<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Legal_request;
use App\Models\Proposal;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropposalService
{
    public function get_legal_requests()
    {
        // lawer ID
        $lawer_id = auth('api')->user()->id;
        // getting requests data 
        $requests = Legal_request::where(function ($query) use ($lawer_id) {
            $query->where('assigned_lawyer_id', $lawer_id)
                ->where('status', 'open')
                ->where('type', 'private');
        })->orWhere([
            ['type', 'public'],
            ['status', 'open']
        ])->with(['documents', 'client'])->get();
        $groupedData = [];
        // Formatting data
        foreach ($requests as $req) {
            $documentUrls = $req->documents->map(function ($doc) {
                return asset('storage/' . $doc->file_path);
            })->toArray();
            $clientInfo = [
                'full_name' => $req->client->full_name ?? null,
                'email' => $req->client->email ?? null,
            ];
            $processedRequest = [
                'id' => $req->id,
                'title_request' => $req->title_request,
                'description_request' => $req->description_request,
                'location_request' => $req->location_request,
                'budget_min' => $req->budget_min,
                'budget_max' => $req->budget_max,
                'created_at' => $req->created_at->format('Y-m-d h'),
                'client' => $clientInfo,
                'documents' => $documentUrls,
            ];
            if (!isset($groupedData[$req->type])) {
                $groupedData[$req->type] = [];
            }
            if (!isset($groupedData[$req->type][$req->status])) {
                $groupedData[$req->type][$req->status] = [];
            }
            $groupedData[$req->type][$req->status][] = $processedRequest;
        }

        return [
            'status' => 'success',
            'data' => [
                'message' => 'get legal requests successfully',
                'data' => $groupedData
            ],
            'code' => '200'
        ];
    }
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
            'lawer_id' => auth('api')->user()->id,
        ]);
        if ($file) {
            $path = $file->store('documents', 'public');
            Document::create([
                'id' => Str::uuid(),
                'file_path' => $path,
                'type' => $file->getClientOriginalExtension(),
                'case_id' => null,
                'legal_request_id' => null,
                'proposal_id' => $proposal->id,
                'uploaded_by' => auth('api')->user()->id,
            ]);
        }
        $Respose_data = $proposal->makeHidden(['created_at', 'updated_at'])->toArray();
        $Respose_data['file_url'] = Storage::disk('public')->url($path);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'create legal_request successfully',
                'data' => $Respose_data
            ],
            'code' => '200'
        ];
    }
}
