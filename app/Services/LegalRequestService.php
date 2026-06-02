<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Legal_request;
use App\Models\Proposal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LegalRequestService
{

    public function createLegalRequest($data)
    {
        $file = isset($data['file']) ? $data['file'] : null;
        unset($data['file']);
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
                    'type' => $doc->type
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
    public function getOfficesOrLawers()
    {
        $role_id = Role::where('name', 'lawer')->first()->id;
        $lawers = User::where([
            'role_id' => $role_id,
            'is_active' => true
        ])->select('full_name', 'email')
            ->get();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get offices or lawers successfully',
                'data' => $lawers
            ],
            'code' => '200'
        ];
    }
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
                    'type' => $doc->type
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
