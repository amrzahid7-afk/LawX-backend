<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Legal_request;
use App\Models\Proposal;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Notifications\Send_Proposal_Notification;

class PropposalService
{
    public function send_proposal($data)
    {
        $file = isset($data['file']) ? $data['file'] : null;
        unset($data['file']);
        $proposal = Proposal::create([
            'price' => $data['price'],
            'estimated_days' => $data['estimated_days'],
            'message_for_client' => $data['message_for_client'],
            'request_id' => $data['request_id'],
            'valid_until' => $data['valid_until'],
            'payment_terms' => $data['payment_terms'],
            'price_currency' => $data['price_currency'],
            'status' => 'pending',
            'lawer_id' => auth('sanctum')->user()->id,
        ]);
        if ($file) {
            $path = $file->store('documents/proposals', 'public');
            Document::create([
                'id' => Str::uuid(),
                'file_path' => $path,
                'type' => $file->getClientOriginalExtension(),
                'case_id' => null,
                'legal_request_id' => null,
                'proposal_id' => $proposal->id,
                'uploaded_by' => auth('sanctum')->user()->id,
            ]);
        }
        // $Respose_data = $proposal->makeHidden(['created_at', 'updated_at'])->toArray();
        // $Respose_data['file_url'] = Storage::disk('public')->url($path);
        // sending notification to client 
        $client = $proposal->request->client;
        $client->notify(new Send_Proposal_Notification($proposal->id, $client->id));
        // return response
        return [
            'status' => 'success',
            'data' => [
                'message' => 'create proposal successfully',
                'proposal_id' => $proposal->id,
            ],
            'code' => '200'
        ];
    }
    public function get_my_proposals()
    {
        $lawer_id = request()->user()->id;
        $proposals = Proposal::with(['request', 'documents'])
            ->where('lawer_id', $lawer_id)->get();
        $formattedProposals = $proposals->map(function ($proposal) {
            return [
                'proposal_id' => $proposal->id,
                'title_request' => $proposal->request->title_request ?? null,
                'title_description' => $proposal->request->description_request ?? null,
                'price' => $proposal->price,
                'price_currency' => strtoupper($proposal->price_currency),
                'estimated_days' => $proposal->estimated_days,
                'created_at' => $proposal->created_at->format('Y-m-d h'),
                'status' => $proposal->status,
                'valid_until' => $proposal->valid_until->format('Y-m-d'),
                // 'message_for_client' => $proposal->message_for_client,
            ];
        });
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get my proposals successfully',
                'data' => $formattedProposals
            ],
            'code' => '200'
        ];
    }
    // public function get_proposal_details($proposal_id)
    // {
    //     $proposal = Proposal::with(['request', 'documents', 'lawyer'])->find($proposal_id);
    //     $formattedProposal = [
    //         'lawyer_name' => $proposal->lawyer->full_name ?? null,
    //         'lawyer_address' => $proposal->lawyer->address ?? null,
    //         'lawyer_legalCategories' => $proposal->lawyer->legalCategories ?? null,
    //         'lawyer_review' => $proposal->lawyer->reviewsReceived ?? null,
    //         'title_request' => $proposal->request->title_request ?? null,
    //         'title_description' => $proposal->request->description_request ?? null,
    //         'price' => $proposal->price,
    //         'estimated_days' => $proposal->estimated_days,
    //         'message_for_client' => $proposal->message_for_client,
    //         'status' => $proposal->status,
    //         'created_at' => $proposal->created_at->format('Y-m-d h'),
    //         'valid_until' => $proposal->valid_until->format('Y-m-d'),
    //         'payment_terms' => $proposal->payment_terms,
    //         'price_currency' => $proposal->price_currency,
    //         'documents' => $proposal->documents->map(function ($doc) {
    //             return asset('storage/' . $doc->file_path);
    //         })->toArray(),
    //     ];
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'get proposal details successfully',
    //             'data' => $formattedProposal
    //         ],
    //         'code' => 200
    //     ];
    // }

    public function get_proposal_details($proposal_id)
    {
        $proposal = Proposal::with([
            'request',
            'documents',
            'lawyer',
            'lawyer.legalCategories',
            'lawyer.reviewsReceived'
        ])->find($proposal_id);

        $formattedProposal = [
            'lawyer_name' => $proposal->lawyer->full_name ?? null,
            'lawyer_address' => $proposal->lawyer->address ?? null,
            'lawyer_legalCategories' => $proposal->lawyer->legalCategories->map(function ($cat) {
                return [
                    'icon' => $cat->icon,
                    'name' => $cat->name
                ];
            })->toArray(),
            'lawyer_review' => $proposal->lawyer->reviewsReceived->avg('rating') ?? 0,
            'title_request' => $proposal->request->title_request ?? null,
            'title_description' => $proposal->request->description_request ?? null,
            'price' => $proposal->price,
            'estimated_days' => $proposal->estimated_days,
            'message_for_client' => $proposal->message_for_client,
            'status' => $proposal->status,
            'created_at' => $proposal->created_at->format('Y-m-d h'),
            'valid_until' => $proposal->valid_until->format('Y-m-d'),
            'payment_terms' => $proposal->payment_terms,
            'price_currency' => strtoupper($proposal->price_currency),
            'documents' => $proposal->documents->map(function ($doc) {
                return [
                    "url" => Storage::url($doc->file_path),
                    "type" => $doc->type,
                    "size" => floor(Storage::size($doc->file_path) / 1024) . "  MB"
                ];
            })->toArray(),
        ];
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get proposal details successfully',
                'data' => $formattedProposal
            ],
            'code' => 200
        ];
    }
    public function update_proposal($data)
    {
        // استخدام findOrFail للحماية
        $proposal = Proposal::findOrFail($data['proposal_id']);

        $file = isset($data['file']) ? $data['file'] : null;
        unset($data['file']);

        $proposal->update($data);

        if ($file) {
            $proposal->documents()->delete();
            $folderName = 'Proposal_files_' . $proposal->id;
            $path = $file->store('Documents/Proposal_files/' . $folderName, 'public');
            \App\Models\Document::create([
                'file_path'        => $path,
                'type'             => $file->getClientOriginalExtension(),
                'case_id'          => null, // أو اتركه فارغاً حسب الحاجة
                'legal_request_id' => $proposal->request_id, // ربطه بالطلب القانوني التابع له
                'proposal_id'      => $proposal->id,
                'uploaded_by'      => auth('sanctum')->user()->id,
            ]);
        }
        return [
            'status' => 'success',
            'data' => [
                'message' => 'update proposal successfully',
            ],
            'code' => 200
        ];
    }
    public function delete_proposal($proposal_id)
    {
        $proposal = Proposal::findOrFail($proposal_id);
        $proposal->delete();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'delete proposal successfully',
            ],
            'code' => 200
        ];
    }
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
}
