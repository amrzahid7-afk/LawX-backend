<?php

namespace App\Services;

use App\Models\Case_payment_milestone;
use App\Models\CaseModel;
use App\Models\Opponent;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\select;

class CaseService
{
    public function createCase(array $data)
    {
        $user = auth('sanctum')->user();
        DB::beginTransaction();
        try {
            // create the case
            $case = CaseModel::create([
                'client_id' => $data['clients'][0]['user_id'], // assuming the first client is the primary one
                'lead_lawyer_id' => $data['lead_lawyer_id'],
                'title' => $data['title'],
                'case_category' => $data['case_category'],
                'description' => $data['description'],
                'court' => $data['court'],
                'price' => $data['price'],
                'importance_stars' => $data['importance_stars'],
                'billing_type' => $data['billing_type'],
                'collection_percentage' => $data['collection_percentage'] ?? null,
                'case_number' => $data['case_number'],
            ]);
            foreach ($data['clients'] as $client) {
                // loop through each client and attach them to the case with their primary status
                $case->clients()->attach($client['user_id'], [
                    'is_primary' => $client['is_primary'],
                    'id' => Str::uuid()->toString() // Generate a UUID for the pivot table entry
                    // 'user_id' => $client['user_id'],
                    // 'case_id' => $case->id
                ]);
            }
            // check if opponents exist in the request and create them
            if (isset($data['opponents'])) {
                foreach ($data['opponents'] as $opponentData) {
                    // 1. البحث أولاً
                    $opponent = Opponent::where('national_id', $opponentData['national_id'] ?? null)->first();

                    // 2. الإنشاء إذا لم يكن موجوداً (باستخدام الطريقة الآمنة)
                    if (!$opponent) {
                        $opponent = Opponent::create([
                            'id' => Str::uuid()->toString(),
                            'name' => $opponentData['name'] ?? null,
                            'type' => $opponentData['type'] ?? null,
                            'national_id' => $opponentData['national_id'] ?? null,
                            'phone' => $opponentData['phone'] ?? null,
                            'email' => $opponentData['email'] ?? null,
                        ]); // الحفظ اليدوي يضمن إدخال البيانات وتوليد الـ ID
                    }

                    // 3. التأكد من وجود ID قبل الربط
                    if ($opponent->id) {
                        $case->opponents()->attach($opponent->id, [
                            'id' => Str::uuid()->toString()
                        ]);
                    } else {
                        // تسجيل خطأ للتنبيه إذا لم يتم الحفظ بشكل صحيح
                        \Log::error('Failed to create opponent: ' . json_encode($opponentData));
                    }
                }
            }
            // check if team members exist in the request and attach them to the case
            if (isset($data['team'])) {
                // loop through each team member and attach them to the case with their role in the case
                foreach ($data['teams'] as $team) {
                    $case->team()->attach($team['user_id'], [
                        'role_in_case' => $team['role_in_case'],
                        'id' => Str::uuid()->toString() // Generate a UUID for the pivot table entry
                    ]);
                }
            }
            // check if payment milestones exist in the request and create them
            if (isset($data['payment_milestones'])) {
                // loop through each milestone and create it in the database
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
            if (config('app.env') === 'production') {
                $errorMessage = 'Something went wrong. Please try again later.';
            } else {
                $errorMessage = 'Debug: ' . $e->getMessage();
            }
            return [
                'status' => 'error',
                'data' => [
                    'message' => $errorMessage,
                    'Line' => $e->getLine(),

                ],
                'code' => '500'
            ];
        }
    }
    public function get_case_details($case_id)
    {
        // جلب القضية مع العلاقات
        $case = CaseModel::with(['clients', 'opponents', 'team', 'paymentMilestones'])->find($case_id);
        // بدء عملية التنسيق (Formatting)
        try {
            $lastSession = $case->sessions()->where('date', '<=', Carbon::now())
                ->orderBy('date', 'desc')
                ->first();

            $nextSession = $case->sessions()->where('date', '>', Carbon::now())
                ->orderBy('date', 'asc')
                ->first();
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
                'financial_status' => $case->financial_status,
                'progress_percentage' => $case->progress_percentage,
                'lawyer_id' => $case->lead_lawyer_id,
                'created_at' => $case->created_at->format('Y-m-d'),
                'clients' => $case->clients->map(function ($client) {
                    return [
                        'user_id' => $client->id,
                        'name' => $client->full_name,
                        'email' => $client->email,
                        'phone' => $client->phone ?? null,
                        'is_primary' => (bool) $client->pivot->is_primary, // البيانات من جدول الربط
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
                        'name' => $member->name,
                        'role_in_case' => $member->pivot->role_in_case, // البيانات من جدول الربط
                        'email' => $member->email,
                    ];
                })->values()->all(),

                'payment_milestones' => $case->paymentMilestones->map(function ($milestone) {
                    return [
                        'id' => $milestone->id,
                        'title' => $milestone->title,
                        'amount' => $milestone->amount,
                        'due_date' => $milestone->due_date, // تحويل التاريخ هنا
                        'status' => $milestone->status,
                    ];
                })->values()->all(),
                'last_session' => $lastSession ? $lastSession->toArray() : null,
                'next_session' => $nextSession ? $nextSession->toArray() : null

                // 'last_session' => $case->sessions
                //     ->where('date', '<', Carbon::now())->map(function ($session) {
                //         return [
                //             'id' => $session->id,
                //             'title' => $session->title,
                //             'date' => $session->date,
                //             'description' => $session->description,
                //         ];
                //     })->first()
                //     ?? null,
                // 'next_session' => $case->sessions
                // ->where('date', '>=', Carbon::now())->map(function ($session) {
                //     return [
                //         'id' => $session->id,
                //         'title' => $session->title,
                //         'date' => $session->date,
                //         'description' => $session->description,
                //     ];
                // })->first()
                // ?? null,
            ];

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
                    'file' => $e->getFile(),
                ],
                'code' => '500'
            ];
        }
    }
    public function updateCase($data)
    {
        $updated_case = CaseModel::where("id", $data['case_id'])->first();;
        $updated_case['status'] = $data['status'] ?? $updated_case['status'];
        $updated_case['title'] = $data['title'] ?? $updated_case['title'];
        $updated_case['description'] = $data['description'] ?? $updated_case['description'];
        $updated_case->save();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'update case successfully'
            ],
            'code' => '200'
        ];
    }
    public function get_cases(Request $request)
    {
        // get auth user
        $user = $request->user();
        // get cases
        $cases = CaseModel::with(['documents', 'tasks', 'clients', 'opponents', 'team', 'paymentMilestones']);
        // formatting data
        $formattedCases = $cases->get()->map(function ($case) {
            return [
                'id' => $case->id,
                'title' => $case->title,
                'description' => $case->description,
                'status' => $case->status,
                'team' => $case->team->map(function ($member) {
                    return [
                        'user_id' => $member->id,
                        'name' => $member->name,
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
    public function delete_case($case_id)
    {
        CaseModel::where('id', $case_id)->delete();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'Delete case successfully'
            ],
            'code' => '200'
        ];
    }
}
