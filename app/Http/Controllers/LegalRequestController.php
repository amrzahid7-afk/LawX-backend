<?php

namespace App\Http\Controllers;

use App\Http\Requests\LegalRequestRequest;
use App\Http\Requests\UpdateLegalRequestRequest;
use App\Models\Legal_request;
use App\Services\LegalRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class LegalRequestController extends Controller
{
    protected LegalRequestService $legalRequestService;
    public function __construct(LegalRequestService $legalRequestService)
    {
        $this->legalRequestService = $legalRequestService;
    }
    // lawers Function 
    public function get_legal_requests()
    {
        try {
            // validate permission
            if (Gate::denies('get_legal_requests', Legal_request::class)) {
                return sendError("Unauthorized , You do not have permission to view legal requests ", [], 403);
            }
            $data = $this->legalRequestService->get_legal_requests();
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
            // validate data
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
            // validate permission 
            if (Gate::denies('get_legal_request_details', Legal_request::find($request_id))) {
                return sendError("Unauthorized , You do not have permission to show this legal requests ", [], 403);
            }
            // call service
            $data = $this->legalRequestService->getLegalRequestDetails($request_id);
            //retrun response
            return sendResponse($data['data'], $data['status'], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function create(LegalRequestRequest $request)
    {
        try {
            // validate data 
            $validated_data = $request->validated();
            // validate permission 
            if (Gate::denies('create', Legal_request::class)) {
                return sendError("Unauthorized , You do not have permission to create legal requests ", [], 403);
            }
            // call Service
            $data = $this->legalRequestService->createLegalRequest($validated_data);
            // send response 
            return sendResponse($data['data'], $data['status'], $data['code']);
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
            // validate data
            $validated_data = $request->validated();
            // validate permission
            if (Gate::denies('update_request', Legal_request::find($validated_data['request_id']))) {
                return sendError("Unauthorized , You do not have permission to update this legal request ", [], 403);
            }
            // call service
            $data = $this->legalRequestService->updateLegalRequest($validated_data);
            // send response
            return sendResponse($data['data'], $data['status'], $data['code']);
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
            // validate data
            $validator = Validator::make(['request_id' => $request_id], [
                'request_id' => 'required|uuid|exists:legal_requests,id',
            ], [
                'request_id.required' => 'Request ID is required',
                'request_id.uuid' => 'Request ID must be a valid UUID',
                'request_id.exists' => 'Request ID does not exist',
            ]);
            if ($validator->fails()) {
                return sendError('Validation failed', $validator->errors()->toArray(), 422);
            }
            // validate permission 
            if (Gate::denies('delete_request', Legal_request::find($request_id))) {
                return sendError("Unauthorized , You do not have permission to delete this legal request ", [], 403);
            }
            // call service
            $data = $this->legalRequestService->deleteLegalRequest($request_id);
            // send response
            return sendResponse($data['data'], $data['status'], $data['code']);
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
            // validate permission
            if (Gate::denies('get_my_requests', Legal_request::class)) {
                return sendError("Unauthorized , You do not have permission to get your legal requests ", [], 403);
            }
            // call service
            $requests = $this->legalRequestService->getMyRequests();
            // send response
            return sendResponse($requests['data'], $requests['status'], $requests['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
}
