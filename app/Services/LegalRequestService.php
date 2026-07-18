<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Legal_request;
use App\Models\Proposal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Notifications\Send_Request_Notification;

class LegalRequestService
{
    public function get_legal_requests()
    {
        // lawer ID
        $lawer_id = request()->user()->id;
        $requests  = Legal_request::where(function ($query) use ($lawer_id) {
            $query->where('assigned_lawyer_id', $lawer_id)
                ->where('status', 'open')
                ->where('type', 'private');
        })->orWhere([
            ['type', 'public'],
            ['status', 'open']
        ])->with(['documents', 'client'])->get();
        $groupedData = [];
        // Formatting data
        $requests->each(function ($req) use (&$groupedData) {
            $documentcounts = $req->documents->count();
            // $clientInfo = [
            //     'full_name' => $req->client->full_name ?? null,
            //     'email' => $req->client->email ?? null,
            // ];
            $processedRequest = [
                'id' => $req->id,
                'title_request' => $req->title_request,
                'description_request' => $req->description_request,
                'type_request' => $req->type,
                'location_request' => $req->location_request,
                'created_at' => $req->created_at->format('Y-m-d h'),
                'budget_min' => $req->budget_min,
                'budget_max' => $req->budget_max,
                'status' => $req->status,
                // 'client_info' => $clientInfo,
                'documents_counts' => $documentcounts,
            ];
            $groupedData[] = $processedRequest;
        });
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get legal requests successfully',
                'data' => $groupedData
            ],
            'code' => '200'
        ];
    }
    public function createLegalRequest($data)
    {
        $file = isset($data['file']) ? $data['file'] : null;
        unset($data['file']);
        $lawer = User::with('lawyerProfile')
            ->whereHas('lawyerProfile', function ($q) {
                $q->where('is_suspended', false);
                $q->where('lawyer_state', 'licensed');
            })
            ->find($data['assigned_lawyer_id']);
        if ($lawer == null) {
            return [
                'status' => 'error',
                'data' => [
                    'message' => 'Assigned lawyer not found or not eligible'
                ],
                'code' => '404'
            ];
        }
        $legal_request = Legal_request::create([
            'title_request' => $data['title_request'],
            'description_request' => $data['description_request'],
            'location_request' => $data['location_request'],
            'budget_min' => $data['budget_min'],
            'budget_max' => $data['budget_max'],
            'type' => $data['type'],
            'status' => 'open',
            'created_at' => now(),
            'client_id' => auth('api')->user()->id,
            'assigned_lawyer_id' => $data['assigned_lawyer_id'] ?? null,
        ]);
        // sending notification
        if (isset($data['assigned_lawyer_id']) && $data['assigned_lawyer_id']) {
            $lawyer = User::find($data['assigned_lawyer_id']);
            $lawyer->notify(new Send_Request_Notification($legal_request->toArray()));
        }
        if ($file) {
            $path = $file->store('documents', 'public');
            Document::create([
                'id' => Str::uuid(),
                'file_path' => $path,
                'type' => $file->getClientOriginalExtension(),
                'case_id' => null,
                'legal_request_id' => $legal_request->id,
                'proposal_id' => null,
                'uploaded_by' => auth('api')->user()->id,
            ]);
        }
        $Respose_data = $legal_request->toArray();
        $Respose_data['file_url'] = Storage::disk('public')->url($path);;

        return [
            'status' => 'success',
            'data' => [
                'message' => 'create legal_request successfully',
                'data' => $Respose_data
            ],
            'code' => '200'
        ];
    }
    public function updateLegalRequest($data)
    {
        $legal_request = Legal_request::find($data['request_id']);
        $file = isset($data['file']) ? $data['file'] : null;
        unset($data['file']);
        $legal_request->update($data);
        if ($file) {
            $legal_request->documents()->delete();
            $path = $file->store('documents', 'public');
            Document::create([
                'id' => Str::uuid(),
                'file_path' => $path,
                'type' => 'legal_request_file',
                'case_id' => null,
                'legal_request_id' => $legal_request->id,
                'proposal_id' => null,
                'uploaded_by' => request()->user()->id,
            ]);
        }
        return [
            'status' => 'success',
            'data' => [
                'message' => 'update legal request successfully',
                'data' => $legal_request
            ],
            'code' => '200'
        ];
    }
    public function deleteLegalRequest($request_id)
    {
        $legal_request = Legal_request::find($request_id);
        $legal_request->delete();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'delete legal request successfully',
            ],
            'code' => '200'
        ];
    }

    public function getMyRequests()
    {
        $requests = Legal_request::where('client_id', auth('api')->user()->id)
            ->with('documents')
            ->get();
        $formattedRequests = $requests->map(function ($request) {
            $formattedDocs = $request->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'file_url' => Storage::url($doc->file_path),
                    'file_size' => Storage::size($doc->file_path),
                    'name' => $doc->type,
                    'nots' => $doc->nots
                ];
            });
            $requestData = [
                'id' => $request->id,
                'title_request' => $request->title_request,
                'description_request' => $request->description_request,
                'location_request' => $request->location_request,
                'budget_min' => $request->budget_min,
                'budget_max' => $request->budget_max,
                'type' => $request->type,
                'status' => $request->status,
                'created_at' => $request->created_at->format('Y-m-d h:i A'), // تنسيق التاريخ (12 ساعة مع AM/PM)
                'documents' => $formattedDocs
            ];
            if ($request->type === 'private') {
                $requestData['assigned_lawyer_id'] = $request->assigned_lawyer_id;
            }
            return $requestData;
        });
        $groupedRequests = $formattedRequests->groupBy('type');
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get my requests successfully',
                'data' => $groupedRequests
            ],
            'code' => '200'
        ];
    }
    public function get_offices_or_lawers_available(Request $request)
    {
        // getting lawer & office admin role
        $lawyerRoleId = Role::where('slug', 'lawyer')->first()->id;
        // dd('1');
        $query = User::with('lawyerProfile')
            // ->where('lawyerProfile.lawyer_state', 'independent')
            // ->where('lawyerProfile.is_suspended', false)
            ->whereHas('lawyerProfile', function ($q) {
                $q->where('lawyer_state', 'independent')
                    ->where('is_suspended', false);
            })
            ->whereIn('role_id', [$lawyerRoleId])
            ->where('is_active', true)
            ->with(['legalCategories'])
            ->withAvg('reviewsReceived as rate', 'rating')
            ->withCount('cases as cases_count');
        // dd('1');

        // getting data with city filter
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }


        // getting data with cases category filter
        if ($request->has('category_id')) {
            $query->whereHas('legalCategories', function ($q) use ($request) {
                $q->where('legal_categories.id', $request->category_id);
            });
        }
        // dd('1');
        //  getting data with rate lawers filter
        if ($request->has('min_rate')) {
            $query->having('rate', '>=', $request->min_rate);
        }
        // dd('1');

        $users = $query->get();
        // dd('1');

        // تنسيق البيانات للإرسال
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'avatar' => $user->avatar ?? null,
                'rate' => round($user->rate, 1) ?: 0, // التقييم المحسوب
                'status' => $user->is_active ? 'active' : 'inactive',
                'address' => $user->address ?? null,
                // 'office_id' => $user->office_id ?? null, // للمكتب فقط
                'specialists' => $user->legalCategories->pluck('name'), // أسماء التخصصات
                'role' => $user->role->name // lawyer or office_manager
            ];
        });
        // dd('1');
        // Response
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get lawers , offices successfully',
                'data' => $data
            ],
            'code' => '200'
        ];
    }

    public function getProtofolioForLawerOrOffice($lawyerId)
    {
        $lawyer = User::with(['legalCategories', 'cases', 'reviewsReceived.client', 'role'])
            ->findOrFail($lawyerId);

        $experienceYears = Carbon::parse($lawyer->created_at)->diffInYears(Carbon::now());

        $totalCases = $lawyer->cases->count();
        $wonCases = $lawyer->cases->where('status', 'won')->count();
        $percentWon = $totalCases > 0 ? ($wonCases / $totalCases) * 100 : 0;

        $legalCategories =  $lawyer->legalCategories->map(function ($cat) {
            return [
                'icon' => $cat->icon,
                'name' => $cat->name
            ];
        })->toArray();


        $opinions = $lawyer->reviewsReceived->map(function ($review) {
            return [
                'client_name' => $review->client ? $review->client->full_name : 'Unknown',
                'rate'        => $review->rating,
                'comment'     => $review->comment,
            ];
        })->toArray();

        $data = [
            'lawyer_name'           => $lawyer->full_name,
            'role'                  => $lawyer->role->name ?? 'User',
            'address'               => $lawyer->address,
            'phone_number'          => $lawyer->phone ?? '0968339198',
            'rate'                  => $lawyer->reviewsReceived()->avg('rating') ?? 0,
            'experience_years_count' => $experienceYears,
            'number_of_cases'       => $totalCases,
            'percent_of_cases_won'  => number_format($percentWon, 2),
            'legal_categories'      => $legalCategories,
            'bio_for_lawyer'        => $lawyer->bio,
            'opinion_of_clients'    => $opinions,
        ];

        if ($lawyer->role_id == 1 || (isset($lawyer->role->name) && strtolower($lawyer->role->name) === 'admin')) {
            $data['location_data'] = [
                'latitude'  => $lawyer->latitude,
                'longitude' => $lawyer->longitude,
            ];
        }

        // sending response 
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get protofolio for lawer or office successfully',
                'data' => $data
            ],
            'code' => '200'
        ];
    }
    // public function getOfficesOrLawers()
    // {
    //     $role_id = Role::where('name', 'lawer')->first()->id;
    //     $lawers = User::with('legalCategories')->where([
    //         'role_id' => $role_id,
    //         'is_active' => true
    //     ])->select(
    //         'full_name',
    //         'is_active',
    //         'city',
    //         'address',
    //         'lat',
    //         'lng',
    //         'experience_years',
    //         'bio',
    //     )->get();
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'get offices or lawers successfully',
    //             'data' => $lawers
    //         ],
    //         'code' => '200'
    //     ];
    // }
    // public function getProposals($request_id)
    // {
    //     $proposals = Proposal::where('request_id', $request_id)
    //         ->with(['documents', 'lawyer', 'request'])
    //         ->get();
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'get proposals successfully',
    //             'data' => $proposals
    //         ],
    //         'code' => '200'
    //     ];
    // }

    // public function getProposals($request_id)
    // {
    //     $requests = Proposal::where('request_id', $request_id)
    //         ->with(['lawyer', 'documents', 'request'])
    //         ->get();
    //     $result = [];
    //     foreach ($requests as $request) {
    //         if ($request->proposals->count() > 0) {
    //             $proposalsData = $request->proposals->map(function ($proposal) {
    //                 $documentsData = $proposal->documents->map(function ($doc) {
    //                     return [
    //                         'id' => $doc->id,
    //                         'file_url' => Storage::url($doc->file_path),
    //                         'type' => $doc->type
    //                     ];
    //                 });
    //                 $lawyerInfo = null;
    //                 if ($proposal->lawyer) {
    //                     $lawyerInfo = [
    //                         'id' => $proposal->lawyer->id,
    //                         'name' => $proposal->lawyer->full_name,
    //                         'avatar' => $proposal->lawyer->avatar ?? null
    //                     ];
    //                 }
    //                 return [
    //                     'id' => trim($proposal->id),
    //                     'price' => $proposal->price,
    //                     'currency' => $proposal->price_currency,
    //                     'estimated_days' => $proposal->estimated_days,
    //                     'message' => $proposal->message_for_client,
    //                     'valid_until' => $proposal->valid_until->format('Y-m-d'),
    //                     'payment_terms' => $proposal->payment_terms,
    //                     'status' => $proposal->status,
    //                     'created_at' => $proposal->created_at->format('Y-m-d h:i A'),
    //                     'lawyer' => $lawyerInfo,
    //                     'documents' => $documentsData
    //                 ];
    //             });

    //             $result[$request->title_request] = $proposalsData;
    //         }
    //     }
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'get my proposals successfully',
    //             'data' => $result
    //         ],
    //         'code' => '200'
    //     ];
    // }

    public function getProposals($request_id)
    {
        $proposals = Proposal::where('request_id', $request_id)
            ->with(['lawyer', 'documents', 'request'])
            ->get();

        $result = [];

        if ($proposals->isNotEmpty()) {
            $requestTitle = $proposals->first()->request->title_request;

            $formattedProposals = $proposals->map(function ($proposal) {
                $documentsData = $proposal->documents->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'file_url' => Storage::url($doc->file_path),
                        'type' => $doc->type
                    ];
                });

                $lawyerInfo = null;
                if ($proposal->lawyer) {
                    $lawyerInfo = [
                        'id' => $proposal->lawyer->id,
                        'name' => $proposal->lawyer->full_name,
                        'avatar' => $proposal->lawyer->avatar ?? null
                    ];
                }

                return [
                    'id' => trim($proposal->id),
                    'price' => $proposal->price,
                    'currency' => $proposal->price_currency,
                    'estimated_days' => $proposal->estimated_days,
                    'message' => $proposal->message_for_client,
                    // التصحيح: إزالة ->format لأن valid_until يأتي كنص جاهز من قاعدة البيانات
                    'valid_until' => $proposal->valid_until,
                    'payment_terms' => $proposal->payment_terms,
                    'status' => $proposal->status,
                    'created_at' => $proposal->created_at->format('Y-m-d h:i A'), // هذا يبقى كما هو لأنه datetime
                    'lawyer' => $lawyerInfo,
                    'documents' => $documentsData
                ];
            });

            $result[$requestTitle] = $formattedProposals;
        }

        return [
            'status' => 'success',
            'data' => [
                'message' => 'get proposals successfully',
                'data' => $result
            ],
            'code' => '200'
        ];
    }
    public function getLegalRequestDetails($request_id)
    {
        // GET DATA 
        $legal_request = Legal_request::with(['documents', 'client'])->find($request_id);
        // Format data
        $formatted_data = [
            'id' => $legal_request->id,
            'title_request' => $legal_request->title_request,
            'description_request' => $legal_request->description_request,
            'location_request' => $legal_request->location_request,
            'budget_min' => $legal_request->budget_min,
            'budget_max' => $legal_request->budget_max,
            'created_at' => $legal_request->created_at->format('Y-m-d h:i A'),
            'status' => $legal_request->status,
            'type' => $legal_request->type,
            'documents' => $legal_request->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'file_url' => Storage::url($doc->file_path),
                    'file_size' => Storage::size($doc->file_path),
                    'name' => $doc->type
                ];
            })->toArray(),
            'client_info' => [
                'full_name' => $legal_request->client->full_name ?? null,
                'email' => $legal_request->client->email ?? null,
            ],
        ];
        // Response
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get legal request details successfully',
                'data' => $formatted_data
            ],
            'code' => '200'
        ];
    }
}
