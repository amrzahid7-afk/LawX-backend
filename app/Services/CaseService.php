<?php

namespace App\Services; // ضع النيم سبيس الخاص بك هنا

use App\Models\Case_payment_milestone;
use App\Models\Case_team;
use App\Models\CaseModel;
use App\Models\Office;
use App\Models\Opponent;
use App\Models\User;
use App\Notifications\CaseClosedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class CaseService // أو اسم الكلاس الخاص بك
{

    // ==========================================
    // 1. CREATE CASE
    // ==========================================
    public function createCase(array $data)
    {
        $user = auth('sanctum')->user();
        $profile = $user->lawyerProfile;

        // تحديد حالة المحامي (ABAC): هل هو صاحب مكتب أم مستقل؟
        $myOffice = Office::where('owner_lawyer_profile_id', $profile->id)->where('status', 'active')->first();
        $isOwner = !is_null($myOffice);

        DB::beginTransaction();
        try {
            $caseData = [
                'client_id' => $data['clients'][0]['user_id'],
                'lead_lawyer_id' => $user->id,
                'title' => $data['title'],
                'case_category' => $data['case_category'],
                'description' => $data['description'],
                'court' => $data['court'],
                'price' => $data['price'],
                'importance_stars' => $data['importance_stars'],
                'billing_type' => $data['billing_type'],
                'collection_percentage' => $data['collection_percentage'] ?? null,
                'case_number' => $data['case_number'],
                // إذا كان صاحب مكتب، نضع الـ office_id. إذا كان مستقل، نضع null
                'office_id' => $isOwner ? $myOffice->id : null,
            ];

            $case = CaseModel::create($caseData);

            // ربط العملاء
            foreach ($data['clients'] as $client) {
                $case->clients()->attach($client['user_id'], [
                    'is_primary' => $client['is_primary'],
                    'id' => Str::uuid()->toString()
                ]);
            }

            // ربط الخصوم
            if (isset($data['opponents'])) {
                foreach ($data['opponents'] as $opponentData) {
                    $opponent = Opponent::where('national_id', $opponentData['national_id'] ?? null)->first();
                    if (!$opponent) {
                        $opponent = Opponent::create([
                            'id' => Str::uuid()->toString(),
                            'name' => $opponentData['name'] ?? null,
                            'type' => $opponentData['type'] ?? null,
                            'national_id' => $opponentData['national_id'] ?? null,
                            'phone' => $opponentData['phone'] ?? null,
                            'email' => $opponentData['email'] ?? null,
                        ]);
                    }
                    if ($opponent->id) {
                        $case->opponents()->attach($opponent->id);
                    }
                }
            }

            // ربط الفريق (RBAC + ABAC): الفريق مسموح فقط لصاحب المكتب
            if ($isOwner && isset($data['team'])) {
                foreach ($data['team'] as $teamMember) {
                    $case->team()->attach($teamMember['user_id'], [
                        'role_in_case' => $teamMember['role_in_case'],
                        'id' => Str::uuid()->toString()
                    ]);
                }
            }

            // الأقساط المالية
            if (isset($data['payment_milestones'])) {
                foreach ($data['payment_milestones'] as $milestone) {
                    Case_payment_milestone::create([
                        'case_id' => $case->id,
                        'title' => $milestone['title'],
                        'amount' => $milestone['amount'],
                        'due_date' => $milestone['due_date'],
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();
            return [
                'status' => 'success',
                'data' => [
                    'message' => 'create case successfully',
                    'case_id' => $case->id
                ],
                'code' => '200'
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'status' => 'error',
                'data' => [
                    'message' => 'Debug: ' . $e->getMessage(),
                    'Line' => $e->getLine(),
                ],
                'code' => '500'
            ];
        }
    }

    // ==========================================
    // 2. GET CASES
    // ==========================================
    public function get_cases($request)
    {
        $scope = $request->input('scope', 'private'); // افتراضي خاص
        $user = auth('sanctum')->user();
        $profile = $user->lawyerProfile;

        // تحديد حالة المستخدم (ABAC)
        $myOwnedOffice = Office::where('owner_lawyer_profile_id', $profile->id)->first();
        $isOwner = !is_null($myOwnedOffice);
        $isEmployee = !is_null($profile->employed_in_office_id);

        $cases = null;

        if ($scope == 'private') {
            // الحالة 1 و 2 و 3: القضايا الخاصة (اللي الـ office_id فيها null وأنا الـ lead)
            $cases = CaseModel::where('lead_lawyer_id', $user->id)
                ->whereNull('office_id') // القضايا المستقلة فقط
                ->with(['documents', 'tasks', 'clients', 'opponents', 'team', 'paymentMilestones']);
        } else if ($scope == 'office') {
            if ($isOwner) {
                // الحالة 3 (صاحب مكتب): يرى كل قضايا مكتبه
                $cases = CaseModel::where('office_id', $myOwnedOffice->id)
                    ->with(['documents', 'tasks', 'clients', 'opponents', 'team', 'paymentMilestones']);
            } else if ($isEmployee) {
                // الحالة 2 (موظف): يرى قضايا مكتبه المُسندة إليه فقط
                $cases = CaseModel::where('office_id', $profile->employed_in_office_id)
                    ->whereHas('team', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->with(['documents', 'tasks', 'clients', 'opponents', 'team', 'paymentMilestones']);
            } else {
                // الحالة 1 (مستقل وليس موظف ولا صاحب مكتب): لا يوجد عمل مكتب
                return [
                    'status' => 'success',
                    'data' => ['message' => 'You are not affiliated with any office.', 'cases' => []],
                    'code' => '200'
                ];
            }
        }

        $formattedCases = $cases->get()->map(function ($case) {
            return [
                'id' => $case->id,
                'title' => $case->title,
                'description' => $case->description,
                'status' => $case->status,
                'team' => $case->team->map(function ($member) {
                    return [
                        'user_id' => $member->id,
                        'name' => $member->full_name,
                        'role_in_case' => $member->pivot->role_in_case,
                        'email' => $member->email,
                    ];
                })->values()->all(),
                'documents_count' => $case->documents->count(),
                'created_at' => $case->created_at->format('Y-m-d'),
            ];
        });

        return [
            'status' => 'success',
            'data' => [
                'message' => 'get cases successfully',
                'cases' => $formattedCases
            ],
            'code' => '200'
        ];
    }

    // ==========================================
    // 3. GET CASE DETAILS (مع إخفاء الماليات عن الموظفين)
    // ==========================================
    public function get_case_details($case_id)
    {
        $user = auth('sanctum')->user();
        $profile = $user->lawyerProfile;

        $case = CaseModel::with(['invoices', 'clients', 'opponents', 'team', 'paymentMilestones', 'sessions'])->find($case_id);

        if (!$case) {
            return ['status' => 'error', 'data' => ['message' => 'Case not found'], 'code' => 404];
        }

        // --- ABAC Check: هل يحق له رؤية هذه القضية؟ ---
        $isCaseOwner = ($case->lead_lawyer_id == $user->id);
        $isOfficeCase = !is_null($case->office_id);
        $myOwnedOffice = Office::where('owner_lawyer_profile_id', $profile->id)->first();

        $canView = false;
        $isEmployeeInCase = false;

        if ($isCaseOwner && !$isOfficeCase) {
            $canView = true;
        } // قضية خاصة به
        if ($isOfficeCase && $myOwnedOffice && $myOwnedOffice->id == $case->office_id) {
            $canView = true;
        } // صاحب مكتب القضية
        if ($isOfficeCase && $case->team->contains('id', $user->id)) {
            $canView = true;
            $isEmployeeInCase = true;
        } // موظف في القضية

        if (!$canView) {
            return ['status' => 'error', 'data' => ['message' => 'Unauthorized to view this case'], 'code' => 403];
        }

        try {
            $lastSession = $case->sessions()->where('date', '<=', Carbon::now())->orderBy('date', 'desc')->first();
            $nextSession = $case->sessions()->where('date', '>', Carbon::now())->orderBy('date', 'asc')->first();

            $allInvoices = $case->invoices;
            $paidInvoices = $allInvoices->where('status', 'paid');
            $unpaidInvoices = $allInvoices->where('status', '!=', 'paid');

            $financialSummary = [
                'total_invoices_count' => $allInvoices->count(),
                'total_paid_amount'    => (float) $paidInvoices->sum('total_amount'),
                'total_unpaid_amount'  => (float) $unpaidInvoices->sum('total_amount'),
                'paid_count'           => $paidInvoices->count(),
                'unpaid_count'         => $unpaidInvoices->count(),
                'payment_status'       => $unpaidInvoices->count() > 0 ? 'Pending' : 'Paid'
            ];

            $invoicesFormatted = $allInvoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'total_amount' => (float) $invoice->total_amount,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date,
                    'details' => is_string($invoice->items) ? json_decode($invoice->items, true) : $invoice->items,
                ];
            })->values()->all();

            $formattedCase = [
                'id' => $case->id,
                'title' => $case->title,
                'description' => $case->description,
                'court' => $case->court,
                'price' => $case->price,
                'case_category' => $case->case_category,
                'case_number' => $case->case_number,
                'status' => $case->status,
                'importance_stars' => $case->importance_stars,
                'billing_type' => $case->billing_type,
                'collection_percentage' => $case->collection_percentage,
                'progress_percentage' => $case->progress_percentage,
                'Leader_lawyer_data' => $case->leadLawyer ? $case->leadLawyer->only(['id', 'full_name', 'email']) : null,
                'created_at' => $case->created_at->format('Y-m-d'),
                'clients' => $case->clients->map(function ($client) {
                    return [
                        'user_id' => $client->id,
                        'name' => $client->full_name,
                        'email' => $client->email,
                        'phone' => $client->phone ?? null,
                        'is_primary' => (bool) $client->pivot->is_primary,
                    ];
                })->values()->all(),
                'opponents' => $case->opponents->map(function ($opponent) {
                    return [
                        'id' => $opponent->id,
                        'name' => $opponent->name ?? null,
                        'type' => $opponent->type ?? null,
                        'national_id' => $opponent->national_id ?? null,
                        'phone' => $opponent->phone ?? null,
                        'email' => $opponent->email ?? null,
                    ];
                })->values()->all(),
                'team' => $case->team->map(function ($member) {
                    return [
                        'user_id' => $member->id,
                        'name' => $member->full_name,
                        'role_in_case' => $member->pivot->role_in_case,
                        'email' => $member->email,
                    ];
                })->values()->all(),
                'invoices' => $invoicesFormatted,
                'financialSummary' => $financialSummary,
                'payment_milestones' => $case->paymentMilestones->map(function ($milestone) {
                    return [
                        'id' => $milestone->id,
                        'title' => $milestone->title,
                        'amount' => $milestone->amount,
                        'due_date' => $milestone->due_date,
                        'status' => $milestone->status,
                    ];
                })->values()->all(),
                'last_session' => $lastSession ? $lastSession->toArray() : null,
                'next_session' => $nextSession ? $nextSession->toArray() : null
            ];

            // --- السحر هنا: إخفاء البيانات المالية إذا كان مجرد موظف (ABAC) ---
            if ($isEmployeeInCase) {
                unset($formattedCase['invoices']);
                unset($formattedCase['financialSummary']);
                unset($formattedCase['payment_milestones']);
                unset($formattedCase['price']);
                unset($formattedCase['billing_type']);
                unset($formattedCase['collection_percentage']);
            }

            return [
                'status' => 'success',
                'data' => [
                    'message' => 'get case details successfully',
                    'case' => $formattedCase
                ],
                'code' => '200'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'data' => [
                    'message' => 'Failed to get case details: ' . $e->getMessage(),
                    'Line' => $e->getLine(),
                ],
                'code' => '500'
            ];
        }
    }

    // ==========================================
    // 4. UPDATE CASE (مع حماية البيانات المالية من الموظفين)
    // ==========================================
    public function updateCase($data)
    {
        $user = auth('sanctum')->user();
        $profile = $user->lawyerProfile;

        DB::beginTransaction();
        try {
            $case = CaseModel::find($data['case_id']);
            if (!$case) throw new \Exception("Case not found.");

            // --- ABAC Check: هل يحق له التعديل؟ ---
            $isLeadLawyer = ($case->lead_lawyer_id == $user->id);
            $myOwnedOffice = Office::where('owner_lawyer_profile_id', $profile->id)->first();
            $isOfficeOwner = ($myOwnedOffice && $case->office_id == $myOwnedOffice->id);
            $isEmployeeInCase = $case->team->contains('id', $user->id);

            if (!$isLeadLawyer && !$isOfficeOwner && !$isEmployeeInCase) {
                throw new \Exception("Unauthorized to update this case.");
            }

            // إذا كان موظف، نمنعه من تعديل الحقول المالية والإدارية
            if ($isEmployeeInCase && !$isOfficeOwner && !$isLeadLawyer) {
                // نزيل هذه الحقول من الـ Data لحمايتها
                unset($data['price'], $data['billing_type'], $data['collection_percentage']);
                unset($data['team'], $data['clients'], $data['status']); // حسب رغبتك إذا كان الموظف يقدر يغير الحالة
            }

            // 2. تحديث الحقول الأساسية
            $case->title = $data['title'] ?? $case->title;
            $case->description = $data['description'] ?? $case->description;
            $case->case_number = $data['case_number'] ?? $case->case_number;
            $case->case_category = $data['case_category'] ?? $case->case_category;
            $case->status = $data['status'] ?? $case->status;
            $case->court = $data['court'] ?? $case->court;
            $case->price = isset($data['price']) ? (float) $data['price'] : $case->price;
            $case->importance_stars = $data['importance_stars'] ?? $case->importance_stars;
            $case->billing_type = $data['billing_type'] ?? $case->billing_type;

            if (isset($data['billing_type']) && $data['billing_type'] !== 'percentage_collection') {
                $case->collection_percentage = null;
            } else {
                $case->collection_percentage = $data['collection_percentage'] ?? $case->collection_percentage;
            }

            // فقط صاحب المكتب يقدر يغير المحامي الرئيسي
            if ($isOfficeOwner && isset($data['lead_lawyer_id'])) {
                $case->lead_lawyer_id = $data['lead_lawyer_id'];
            }
            // if the case status is changed to 'closed', send notification to all clients
            if (isset($data['status']) && $data['status'] === 'close') {
                $clients = $case->clients;
                Notification::send($clients, new CaseClosedNotification($case));
            }
            $case->save();

            // 3. تحديث العملاء (فقط لصاحب القضية أو صاحب المكتب)
            if (isset($data['clients']) && is_array($data['clients']) && ($isLeadLawyer || $isOfficeOwner)) {
                Case_team::where('case_id', $case->id)->delete();
                foreach ($data['team'] as $member) {
                    Case_team::create([
                        'case_id'      => $case->id,
                        'user_id'      => $member['user_id'],
                        'role_in_case' => $member['role_in_case']
                        // 'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        // 'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]);
                }
            }

            // 4. تحديث الخصوم
            if (isset($data['opponents']) && is_array($data['opponents']) && ($isLeadLawyer || $isOfficeOwner)) {
                $case->opponents()->delete();
                $opponentsData = [];
                foreach ($data['opponents'] as $opp) {
                    $opponentsData[] = [
                        'name' => $opp['name'],
                        'type' => $opp['type'],
                        'national_id' => $opp['national_id'] ?? null,
                        'phone' => $opp['phone'] ?? null,
                        'email' => $opp['email'] ?? null,
                    ];
                }
                if (!empty($opponentsData)) {
                    $case->opponents()->createMany($opponentsData);
                }
            }

            if (isset($data['team']) && is_array($data['team']) && $isOfficeOwner) {
                Case_team::where('case_id', $case->id)->delete();

                foreach ($data['team'] as $member) {
                    Case_team::create([
                        'case_id'      => $case->id,
                        'user_id'      => $member['user_id'],
                        'role_in_case' => $member['role_in_case'],
                    ]);
                }
            }

            // 6. تحديث الأقساط المالية (فقط لصاحب المكتب أو صاحب القضية المستقلة)
            if (isset($data['payment_milestones']) && is_array($data['payment_milestones']) && ($isOfficeOwner || ($isLeadLawyer && is_null($case->office_id)))) {
                $case->paymentMilestones()->delete();
                $milestonesData = [];
                foreach ($data['payment_milestones'] as $ms) {
                    $milestonesData[] = [
                        'title' => $ms['title'],
                        'amount' => (float) $ms['amount'],
                        'due_date' => $ms['due_date'],
                        'status' => 'pending',
                    ];
                }
                if (!empty($milestonesData)) {
                    $case->paymentMilestones()->createMany($milestonesData);
                }
            }

            DB::commit();
            return [
                'status' => 'success',
                'data' => ['message' => 'update case successfully'],
                'code' => '200'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'data' => [
                    'message' => $e->getMessage(),
                    'Line' => $e->getLine()
                ],
                'code' => 500
            ];
        }
    }

    // ==========================================
    // 5. DELETE CASE
    // ==========================================
    public function delete_case($case_id)
    {
        $user = auth('sanctum')->user();
        $profile = $user->lawyerProfile;

        $case = CaseModel::find($case_id);
        if (!$case) {
            return ['status' => 'error', 'data' => ['message' => 'Case not found'], 'code' => 404];
        }

        // --- ABAC Check: الحذف فقط لصاحب القضية المستقلة أو صاحب المكتب ---
        $isLeadLawyer = ($case->lead_lawyer_id == $user->id && is_null($case->office_id));
        $myOwnedOffice = Office::where('owner_lawyer_profile_id', $profile->id)->first();
        $isOfficeOwner = ($myOwnedOffice && $case->office_id == $myOwnedOffice->id);

        if (!$isLeadLawyer && !$isOfficeOwner) {
            return [
                'status' => 'error',
                'data' => ['message' => 'Unauthorized. Only the case owner or office manager can delete cases.'],
                'code' => 403
            ];
        }

        $case->delete();

        return [
            'status' => 'success',
            'data' => ['message' => 'Delete case successfully'],
            'code' => '200'
        ];
    }
}
 // Old Code
// namespace App\Services;

// use App\Models\Case_payment_milestone;
// use App\Models\CaseModel;
// use App\Models\Opponent;
// use App\Models\Role;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;

// use function Laravel\Prompts\select;

// class CaseService
// {

//     public function createCase(array $data)
//     {
//         $user = auth('sanctum')->user();
//         DB::beginTransaction();
//         try {
//             // create the case
//             $office_manager = User::with('lawerProfile', 'lawerProfile.office')
//                 ->whereHas('lawerProfile', function ($query) {
//                     $query->where('office_id', '!=', null);
//                     $query->where('status', 'active');
//                     $query->wherehas('office', function ($query) {
//                         $query->where('status', 'active');
//                     });
//                 })
//                 ->where('id', auth('sanctum')->user()->id)
//                 ->first();
//             $case = CaseModel::create([
//                 'client_id' => $data['clients'][0]['user_id'], // assuming the first client is the primary one
//                 'lead_lawyer_id' => $user->id,
//                 'title' => $data['title'],
//                 'case_category' => $data['case_category'],
//                 'description' => $data['description'],
//                 'court' => $data['court'],
//                 'price' => $data['price'],
//                 'importance_stars' => $data['importance_stars'],
//                 'billing_type' => $data['billing_type'],
//                 'collection_percentage' => $data['collection_percentage'] ?? null,
//                 'case_number' => $data['case_number'],
//                 'office_id' => $office_manager ? $office_manager->lawerProfile->office->id : null,
//             ]);
//             foreach ($data['clients'] as $client) {
//                 // loop through each client and attach them to the case with their primary status
//                 $case->clients()->attach($client['user_id'], [
//                     'is_primary' => $client['is_primary'],
//                     'id' => Str::uuid()->toString() // Generate a UUID for the pivot table entry
//                     // 'user_id' => $client['user_id'],
//                     // 'case_id' => $case->id
//                 ]);
//             }
//             // check if opponents exist in the request and create them
//             if (isset($data['opponents'])) {
//                 foreach ($data['opponents'] as $opponentData) {
//                     $opponent = Opponent::where('national_id', $opponentData['national_id'] ?? null)->first();
//                     if (!$opponent) {
//                         $opponent = Opponent::create([
//                             'id' => Str::uuid()->toString(),
//                             'name' => $opponentData['name'] ?? null,
//                             'type' => $opponentData['type'] ?? null,
//                             'national_id' => $opponentData['national_id'] ?? null,
//                             'phone' => $opponentData['phone'] ?? null,
//                             'email' => $opponentData['email'] ?? null,
//                         ]); // الحفظ اليدوي يضمن إدخال البيانات وتوليد الـ ID
//                     }

//                     // 3. التأكد من وجود ID قبل الربط
//                     if ($opponent->id) {
//                         $case->opponents()->attach($opponent->id);
//                     } else {
//                         // تسجيل خطأ للتنبيه إذا لم يتم الحفظ بشكل صحيح
//                         \Log::error('Failed to create opponent: ' . json_encode($opponentData));
//                     }
//                 }
//             }
//             // check if team members exist in the request and attach them to the case
//             if (isset($data['team'])) {
//                 // loop through each team member and attach them to the case with their role in the case
//                 foreach ($data['team'] as $team) {
//                     $case->team()->attach($team['user_id'], [
//                         'role_in_case' => $team['role_in_case'],
//                         'id' => Str::uuid()->toString() // Generate a UUID for the pivot table entry
//                     ]);
//                 }
//             }
//             // check if payment milestones exist in the request and create them
//             if (isset($data['payment_milestones'])) {
//                 // loop through each milestone and create it in the database
//                 foreach ($data['payment_milestones'] as $milestone) {
//                     Case_payment_milestone::create([
//                         'case_id' => $case->id,
//                         'title' => $milestone['title'],
//                         'amount' => $milestone['amount'],
//                         'due_date' => $milestone['due_date'],
//                         'status' => 'pending',
//                     ]);
//                 }
//             }
//             DB::commit();
//             return [
//                 'status' => 'success',
//                 'data' => [
//                     'message' => 'create case successfully',
//                     'case_id' => $case->id
//                 ],
//                 'code' => '200'
//             ];
//         } catch (\Exception $e) {
//             DB::rollback();
//             if (config('app.env') === 'production') {
//                 $errorMessage = 'Something went wrong. Please try again later.';
//             } else {
//                 $errorMessage = 'Debug: ' . $e->getMessage();
//             }
//             return [
//                 'status' => 'error',
//                 'data' => [
//                     'message' => $errorMessage,
//                     'Line' => $e->getLine(),

//                 ],
//                 'code' => '500'
//             ];
//         }
//     }
//     public function get_case_details($case_id)
//     {
//         $case = CaseModel::with(['invoices', 'clients', 'opponents', 'team', 'paymentMilestones'])->find($case_id);
//         try {
//             $lastSession = $case->sessions()->where('date', '<=', Carbon::now())
//                 ->orderBy('date', 'desc')
//                 ->first();

//             $nextSession = $case->sessions()->where('date', '>', Carbon::now())
//                 ->orderBy('date', 'asc')
//                 ->first();
//             // get all invoices for the case
//             $allInvoices = $case->invoices;

//             // تجميع الفواتير المدفوعة وغير المدفوعة
//             $paidInvoices = $allInvoices->where('status', 'paid');
//             $unpaidInvoices = $allInvoices->where('status', '!=', 'paid'); // يفترض أن الحالة 'paid' تعني مدفوع بالكامل

//             $financialSummary = [
//                 'total_invoices_count' => $allInvoices->count(),
//                 // 'total_due_amount'     => (float) $allInvoices->sum('total_amount'),
//                 'total_paid_amount'    => (float) $paidInvoices->sum('total_amount'),
//                 'total_unpaid_amount'  => (float) $unpaidInvoices->sum('total_amount'),
//                 'paid_count'           => $paidInvoices->count(),
//                 'unpaid_count'         => $unpaidInvoices->count(),
//                 'payment_status'       => $unpaidInvoices->count() > 0 ? 'Pending' : 'Paid'
//             ];
//             $invoicesFormatted = $allInvoices->map(function ($invoice) {
//                 return [
//                     'id' => $invoice->id,
//                     'total_amount' => (float) $invoice->total_amount,
//                     'status' => $invoice->status,
//                     'due_date' => $invoice->due_date,
//                     'details' => is_string($invoice->items) ? json_decode($invoice->items, true) : $invoice->items,
//                 ];
//             })->values()->all();

//             $formattedCase = [
//                 'id' => $case->id,
//                 'title' => $case->title,
//                 'description' => $case->description,
//                 'court' => $case->court,
//                 'price' => $case->price,
//                 'case_category' => $case->case_category,
//                 'case_number' => $case->case_number,
//                 'status' => $case->status,
//                 'importance_stars' => $case->importance_stars,
//                 'billing_type' => $case->billing_type,
//                 'collection_percentage' => $case->collection_percentage,
//                 // 'financial_status' => $case->financial_status,
//                 'progress_percentage' => $case->progress_percentage,
//                 'Leader_lawyer_data' => $case->leadLawyer ? $case->leadLawyer->only(['id', 'full_name', 'email']) : null,
//                 'created_at' => $case->created_at->format('Y-m-d'),
//                 'clients' => $case->clients->map(function ($client) {
//                     return [
//                         'user_id' => $client->id,
//                         'name' => $client->full_name,
//                         'email' => $client->email,
//                         'phone' => $client->phone ?? null,
//                         'is_primary' => (bool) $client->pivot->is_primary, // البيانات من جدول الربط
//                     ];
//                 })->values()->all(),

//                 'opponents' => $case->opponents->map(function ($opponent) {
//                     return [
//                         'id' => $opponent->id,
//                         'name' => $opponent->name ?? null,
//                         'type' => $opponent->type ?? null,
//                         'national_id' => $opponent->national_id ?? null,
//                         'phone' => $opponent->phone ?? null,
//                         'email' => $opponent->email ?? null,
//                     ];
//                 })->values()->all(),

//                 'team' => $case->team->map(function ($member) {
//                     return [
//                         'user_id' => $member->id,
//                         'name' => $member->full_name,
//                         'role_in_case' => $member->pivot->role_in_case, // البيانات من جدول الربط
//                         'email' => $member->email,
//                     ];
//                 })->values()->all(),
//                 'invoices' => $invoicesFormatted,
//                 'financialSummary' => $financialSummary,
//                 'payment_milestones' => $case->paymentMilestones->map(function ($milestone) {
//                     return [
//                         'id' => $milestone->id,
//                         'title' => $milestone->title,
//                         'amount' => $milestone->amount,
//                         'due_date' => $milestone->due_date, // تحويل التاريخ هنا
//                         'status' => $milestone->status,
//                     ];
//                 })->values()->all(),
//                 'last_session' => $lastSession ? $lastSession->toArray() : null,
//                 'next_session' => $nextSession ? $nextSession->toArray() : null
//             ];

//             return [
//                 'status' => 'success',
//                 'data' => [
//                     'message' => 'get case details successfully',
//                     'case' => $formattedCase
//                 ],
//                 'code' => '200'
//             ];
//         } catch (\Exception $e) {
//             return [
//                 'status' => 'error',
//                 'data' => [
//                     'message' => 'Failed to get case details: ' . $e->getMessage(),
//                     'Line' => $e->getLine(),
//                     'file' => $e->getFile(),
//                 ],
//                 'code' => '500'
//             ];
//         }
//     }
//     public function updateCase($data)
//     {
//         DB::beginTransaction();

//         try {
//             // 1. التحقق من وجود القضية
//             $case = CaseModel::find($data['case_id']);
//             if (!$case) {
//                 throw new \Exception("Case not found.");
//             }

//             // 2. تحديث الحقول الأساسية
//             $case->title = $data['title'] ?? $case->title;
//             $case->description = $data['description'] ?? $case->description;
//             $case->case_number = $data['case_number'] ?? $case->case_number;
//             $case->case_category = $data['case_category'] ?? $case->case_category;
//             $case->status = $data['status'] ?? $case->status;
//             $case->court = $data['court'] ?? $case->court;

//             // تحويل السعر لرقم لضمان عدم وجود مشاكل مع قاعدة البيانات
//             $case->price = isset($data['price']) ? (float) $data['price'] : $case->price;

//             $case->importance_stars = $data['importance_stars'] ?? $case->importance_stars;
//             $case->billing_type = $data['billing_type'] ?? $case->billing_type;

//             // إذا كان نوع الفوترة ليس نسبة، تصفير النسبة
//             if (isset($data['billing_type']) && $data['billing_type'] !== 'percentage_collection') {
//                 $case->collection_percentage = null;
//             } else {
//                 $case->collection_percentage = $data['collection_percentage'] ?? $case->collection_percentage;
//             }

//             $case->lead_lawyer_id = $data['lead_lawyer_id'] ?? $case->lead_lawyer_id;
//             $case->status = $data['status'] ?? $case->status;

//             $case->save();

//             // 3. تحديث العملاء (Clients)
//             if (isset($data['clients']) && is_array($data['clients'])) {
//                 $syncData = [];
//                 foreach ($data['clients'] as $client) {
//                     $syncData[$client['user_id']] = ['is_primary' => $client['is_primary']];
//                 }
//                 $case->clients()->sync($syncData);
//             }

//             // 4. تحديث الخصوم (Opponents) - التأكد من إزالة الـ IDs المرسلة
//             if (isset($data['opponents']) && is_array($data['opponents'])) {
//                 $case->opponents()->delete();

//                 // تجهيز البيانات بدون 'id' لتجنب مشاكل UUID التكرار
//                 $opponentsData = [];
//                 foreach ($data['opponents'] as $opp) {
//                     $opponentsData[] = [
//                         'name' => $opp['name'],
//                         'type' => $opp['type'],
//                         'national_id' => $opp['national_id'] ?? null,
//                         'phone' => $opp['phone'] ?? null,
//                         'email' => $opp['email'] ?? null,
//                     ];
//                 }
//                 if (!empty($opponentsData)) {
//                     $case->opponents()->createMany($opponentsData);
//                 }
//             }

//             // 5. تحديث الفريق (Team)
//             if (isset($data['team']) && is_array($data['team'])) {
//                 $syncTeamData = [];
//                 foreach ($data['team'] as $member) {
//                     $syncTeamData[$member['user_id']] = ['role_in_case' => $member['role_in_case']];
//                 }
//                 $case->team()->sync($syncTeamData);
//             }

//             // 6. تحديث الأقساط الزمنية (Payment Milestones)
//             if (isset($data['payment_milestones']) && is_array($data['payment_milestones'])) {
//                 $case->paymentMilestones()->delete();

//                 $milestonesData = [];
//                 foreach ($data['payment_milestones'] as $ms) {
//                     $milestonesData[] = [
//                         'title' => $ms['title'],
//                         'amount' => (float) $ms['amount'],
//                         'due_date' => $ms['due_date'],
//                         'status' => 'pending', // حالة افتراضية
//                     ];
//                 }
//                 if (!empty($milestonesData)) {
//                     $case->paymentMilestones()->createMany($milestonesData);
//                 }
//             }

//             DB::commit();

//             return [
//                 'status' => 'success',
//                 'data' => [
//                     'message' => 'update case successfully'
//                 ],
//                 'code' => '200'
//             ];
//         } catch (\Exception $e) {
//             DB::rollBack();
//             // يمكن هنا تسجيل الخطأ في السجلات (Log)
//             return [
//                 'status' => 'error',
//                 'data' => [
//                     'message' => $e->getMessage(),
//                     'file' => $e->getFile(),
//                     'line' => $e->getLine()
//                 ],
//                 'code' => 500
//             ];
//         }
//     }
//     public function get_cases(Request $request)
//     {
//         $scope = $request->input('scope');
//         // get auth user
//         $user =
//             User::with('lawyerProfile')
//             ->where('id', auth('sanctum')->user()->id); // ;
//         // get cases
//         // this sitution is for get cases office manager and Indepenedet 
//         if ($scope == 'private') {
//             $cases =
//                 // get cases where lead lawyer id is equal to auth user id                
//                 CaseModel::where('lead_lawyer_id', $user->id)
//                 // and get cases that is in my office
//                 ->where('office_id', $user->lawyerProfile->office_id)
//                 // load realations
//                 ->with(['documents', 'tasks', 'clients', 'opponents', 'team', 'paymentMilestones']);
//             // this sitution for employee cases         
//         } else if ($scope == 'office') {
//             $cases =
//                 // get cases that is in the office that I am employee in it                 
//                 CaseModel::where('office_id', $user->lawyerProfile->employed_In_office_id)
//                 // and the cases that I am in the team of it
//                 ->andWhereHas('team', function ($query) use ($user) {
//                     $query->where('user_id', $user->id);
//                 })
//                 // load realations
//                 ->with(['documents', 'tasks', 'clients', 'opponents', 'team', 'paymentMilestones']);
//         }
//         // formatting data
//         $formattedCases = $cases->get()->map(function ($case) {
//             return [
//                 'id' => $case->id,
//                 'title' => $case->title,
//                 'description' => $case->description,
//                 'status' => $case->status,
//                 'team' => $case->team->map(function ($member) {
//                     return [
//                         'user_id' => $member->id,
//                         'name' => $member->full_name,
//                         'role_in_case' => $member->pivot->role_in_case,
//                         'email' => $member->email,
//                     ];
//                 })->values()->all(),
//                 'documents_count' => $case->documents->count(),
//                 'created_at' => $case->created_at->format('Y-m-d'),
//             ];
//         });
//         return [
//             'status' => 'success',
//             'data' => [
//                 'message' => 'get cases successfully',
//                 'cases' => $formattedCases
//             ],
//             'code' => '200'
//         ];
//     }
//     public function delete_case($case_id)
//     {
//         CaseModel::where('id', $case_id)->delete();
//         return [
//             'status' => 'success',
//             'data' => [
//                 'message' => 'Delete case successfully'
//             ],
//             'code' => '200'
//         ];
//     }
// }
