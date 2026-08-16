<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Models\Proposal;
use App\Models\Legal_request;
use App\Services\PropposalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ProposalController extends Controller
{
    protected PropposalService $propposal_service;
    public function __construct(PropposalService $propposal_service)
    {
        $this->propposal_service = $propposal_service;
    }
    public function get_my_proposals()
    {
        try {
            // validate permission
            if (Gate::denies('get_my_proposals', Proposal::class)) {
                return sendError("Unauthorized , You do not have permission to view my proposals ", [], 403);
            }
            $data = $this->propposal_service->get_my_proposals();
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

    public function get_proposal_details($proposal_id)
    {
        try {
            // validate data
            $validator = Validator::make(
                ['proposal_id' => $proposal_id],
                ['proposal_id' => ['required', 'uuid', Rule::exists('proposals', 'id')]],
                [
                    'proposal_id.required' => 'Proposal ID is required',
                    'proposal_id.uuid' => 'Proposal ID must be a valid UUID',
                    'proposal_id.exists' => 'Proposal not found'
                ]
            );
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid proposal ID',
                    'error' => $validator->errors()->first()
                ], 422);
            }
            // valdiate permission
            if (Gate::denies('get_proposal_details', Proposal::find($proposal_id))) {
                return sendError("Unauthorized , You do not have permission to view this proposal ", [], 403);
            }
            // call service
            $data = $this->propposal_service->get_proposal_details($proposal_id);
            //send response
            return sendResponse($data['data'], $data['status'], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function send_proposal(ProposalRequest $request)
    {
        try {
            // validate data 
            $proposal = $request->validated();
            // validate permission
            if (Gate::denies('send_proposal', [Proposal::class, Legal_request::find($proposal['request_id'])])) {
                return sendError("Unauthorized , You do not have permission to send this proposal ", [], 403);
            }
            $data = $this->propposal_service->send_proposal($proposal);
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

    public function update_proposal(UpdateProposalRequest $request)
    {
        try {
            // validate data
            $proposal = $request->validated();
            // validate permission
            if (Gate::denies('update_proposal', Proposal::find($proposal['proposal_id']))) {
                return sendError("Unauthorized , You do not have permission to update this proposal ", [], 403);
            }
            // call service
            $data = $this->propposal_service->update_proposal($proposal);
            //send response
            return sendResponse($data['data'], $data['status'], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'Class' => $e->getFile()
            ], 500);
        }
    }
    public function delete_proposal($proposal_id)
    {
        try {
            // validate data
            $validator = Validator::make(
                ['proposal_id' => $proposal_id],
                ['proposal_id' => ['required', 'uuid', Rule::exists('proposals', 'id')]],
                [
                    'proposal_id.required' => 'Proposal ID is required',
                    'proposal_id.uuid' => 'Proposal ID must be a valid UUID',
                    'proposal_id.exists' => 'Proposal not found'
                ]
            );
            if ($validator->fails()) {
                return sendError('Validation failed', $validator->errors()->toArray(), 422);
            }
            // validate permission
            if (Gate::denies('delete_proposal', Proposal::find($proposal_id))) {
                return sendError("Unauthorized , You do not have permission to delete this proposal ", [], 403);
            }
            // call service
            $data = $this->propposal_service->delete_proposal($proposal_id);
            // send response
            return sendResponse($data['data'], $data['status'], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'Class' => $e->getFile()
            ], 500);
        }
    }
    public function get_proposals($request_id)
    {
        try {
            // validate data
            $validator = Validator::make(
                ['request_id' => $request_id],
                ['request_id' => ['required', 'uuid', Rule::exists('legal_requests', 'id')]],
                [
                    'request_id.required' => 'Request ID is required',
                    'request_id.uuid' => 'Request ID must be a valid UUID',
                    'request_id.exists' => 'Request not found'
                ]
            );
            if ($validator->fails()) {
                return sendError('Validation failed', $validator->errors()->toArray(), 422);
            }
            // validate permission
            if (Gate::denies('get_proposals', [Proposal::class, Legal_request::find($request_id)])) {
                return sendError("Unauthorized , You do not have permission to view Proposals For this Request ", [], 403);
            }
            // call service
            $data = $this->propposal_service->getProposals($request_id);
            // send response
            return sendResponse($data['data'], $data['status'], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine()
            ], 500);
        }
    }
    public function response_proposal(Request $request)
    {
        try {
            // validate data
            $validate = Validator::make($request->all(), [
                'proposal_id' => 'required|exists:proposals,id',
                'status' => 'required|in:accepted,rejected',
            ], [
                'proposal_id.required' => 'Proposal ID is required',
                'proposal_id.exists' => 'Proposal not found',
                'status.required' => 'Status is required',
                'status.in' => 'Status must be accepted or rejected',
            ]);
            if ($validate->fails()) {
                return sendError('Validation failed', $validate->errors()->toArray(), 422);
            }
            $proposal = Proposal::find($request->proposal_id);
            // validate permission
            if (Gate::denies('respone_proposal', $proposal)) {
                return sendError("Unauthorized , You do not have permission to respone this proposal ", [], 403);
            }
            // call service
            $data = $this->propposal_service->response_proposal($request);
            // send response
            return sendResponse($data['data'], $data['status'], $data['code']);
        } catch (\Exception $e) {
            return sendError("Internal Server Error ", [$e->getMessage()], 500);
        }
    }
}
