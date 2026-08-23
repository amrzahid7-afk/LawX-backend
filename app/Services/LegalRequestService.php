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
        $lawer_id = auth('sanctum')->user()->id;
        $requests  =
            Legal_request::where(function ($query) use ($lawer_id) {
                $query->where('assigned_lawyer_id', $lawer_id)
                    ->where('status', 'open')
                    ->where('type', 'private');
            })
            ->orWhere([
                ['type', 'public'],
                ['status', 'open'],
                ['assigned_lawyer_id', null],
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
        $lawer = null;
        if (!empty($data['assigned_lawyer_id'])) {
            $lawer = User::with('lawyerProfile')
                ->whereHas('lawyerProfile', function ($q) {
                    $q->where('is_suspended', false);
                    $q->where('lawyer_state', 'licensed');
                })
                ->where('id', $data['assigned_lawyer_id']);
        }
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
            'created_at' => now()->format('Y-m-d'),
            'client_id' => auth('sanctum')->user()->id,
            'assigned_lawyer_id' => $data['assigned_lawyer_id'] ? $data['assigned_lawyer_id'] : null,
        ]);
        // sending notification
        if (isset($data['assigned_lawyer_id']) && $data['assigned_lawyer_id']) {
            $lawyer = User::find($data['assigned_lawyer_id']);
            $lawyer->notify(new Send_Request_Notification($legal_request->toArray()));
        }

        if ($file) {
            // storage 
            // $path = $file->store('documents', 'public');
            // cloudinary 
            $path = app(\Cloudinary\Cloudinary::class)
                ->uploadApi()
                ->upload($file->getRealPath(), [
                    'folder' => 'lawx/documents',
                    'resource_type' => 'auto',
                ])['secure_url'];

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
        // $Respose_data['file_url'] = Storage::disk('public')->url($path);;
        $Respose_data['file_url'] = $file ? $path : null;
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

            // $path = $file->store('documents', 'public');
            $path = app(\Cloudinary\Cloudinary::class)
                ->uploadApi()
                ->upload($file->getRealPath(), [
                    'folder' => 'lawx/documents',
                    'resource_type' => 'auto',
                ])['secure_url'];

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
                    'file_url' => $doc->file_path,
                    'file_size' => "2 MB",
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
                    'file_url' => $doc->file_path,
                    'file_size' => "2 MB",
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
