<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalRequest;
use App\Http\Requests\UpdateProposalRequest;
use App\Services\PropposalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProposalController extends Controller
{
    protected PropposalService $propposal_service;
    public function __construct(PropposalService $propposal_service)
    {
        $this->propposal_service = $propposal_service;
    }
    public function get_legal_requests()
    {
        try {
            $data = $this->propposal_service->get_legal_requests();
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
    public function send_proposal(ProposalRequest $request)
    {
        try {
            $proposal = $request->validated();
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
    public function get_my_proposals()
    {
        try {
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
            $data = $this->propposal_service->get_proposal_details($proposal_id);
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
            $proposal = $request->validated();
            $data = $this->propposal_service->update_proposal($proposal);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
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
            $data = $this->propposal_service->delete_proposal($proposal_id);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'Class' => $e->getFile()
            ], 500);
        }
    }
}
