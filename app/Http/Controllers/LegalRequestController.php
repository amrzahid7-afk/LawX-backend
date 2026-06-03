<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegalRequestRequest;
use App\Http\Requests\UpdateLegalRequestRequest;
use App\Models\Legal_request;
use App\Services\LegalRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LegalRequestController extends Controller
{
    protected LegalRequestService $legalRequestService;
    public function __construct(LegalRequestService $legalRequestService)
    {
        $this->legalRequestService = $legalRequestService;
    }
    public function create(LegalRequestRequest $request)
    {
        try {
            $validated_data = $request->validated();
            $data = $this->legalRequestService->createLegalRequest($validated_data);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function update_request(UpdateLegalRequestRequest $request)
    {
        try {
            $validated_data = $request->validated();
            $data = $this->legalRequestService->updateLegalRequest($validated_data);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function delete_request($request_id)
    {
        try {
            $validator = Validator::make(['request_id' => $request_id], [
                'request_id' => 'required|uuid|exists:legal_requests,id',
            ], [
                'request_id.required' => 'Request ID is required',
                'request_id.uuid' => 'Request ID must be a valid UUID',
                'request_id.exists' => 'Request ID does not exist',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid request ID',
                    'error' => $validator->errors()->first()
                ], 422);
            }
            $data = $this->legalRequestService->deleteLegalRequest($request_id);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function get_my_requests(Request $request)
    {
        try {
            // $client_id = $request->user()->id;
            // dd(auth('api')->user());
            // dd($request->user());
            $requests = $this->legalRequestService->getMyRequests();
            return response()->json([
                'status' => $requests['status'],
                'data' => $requests['data']
            ], $requests['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function get_offices_or_lawers(Request $request)
    {
        try {
            $data = $this->legalRequestService->get_offices_or_lawers_available($request);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'class' => $e->getFile()
            ], 500);
        }
    }
    public function get_proposals($request_id)
    {
        try {
            $data = $this->legalRequestService->getProposals($request_id);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function get_legal_request_details($request_id)
    {
        try {
            $validator = Validator::make(
                ['request_id' => $request_id],
                [
                    'request_id' => ['required', 'exists:legal_requests,id'],
                ],
                [
                    'request_id.required' => 'Request ID is required',
                    'request_id.exists' => 'Request ID does not exist',
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid request ID',
                    'error' => $validator->errors()->first()
                ], 422);
            }
            $data = $this->legalRequestService->getLegalRequestDetails($request_id);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function get_protofolio_for_lawer_or_office($lawer_id)
    {
        try {
            $validator = Validator::make(
                ['lawer_id' => $lawer_id],
                [
                    'lawer_id' => ['required', 'exists:users,id'],
                ],
                [
                    'lawer_id.required' => 'Lawyer/Office ID is required',
                    'lawer_id.exists' => 'Lawyer/Office ID does not exist',
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid Lawyer/Office ID',
                    'error' => $validator->errors()->first()
                ], 422);
            }
            $data = $this->legalRequestService->getProtofolioForLawerOrOffice($lawer_id);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'class' => $e->getFile()
            ], 500);
        }
    }
}
