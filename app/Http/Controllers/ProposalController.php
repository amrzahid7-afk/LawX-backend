<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalRequest;
use App\Services\PropposalService;
use Illuminate\Http\Request;

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
}
