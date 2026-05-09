<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseCreationRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Services\CaseService;
use Exception;
use Illuminate\Http\Request;
use App\Models\CaseModel;

class CaseController extends Controller
{
    public $caseService;
    public function __construct(CaseService $caseService)
    {
        $this->caseService = $caseService;
    }
    public function create(CaseCreationRequest $request)
    {
        try {
            // validated data
            $validated_data = $request->validated();
            // calling case service
            $case = $this->caseService->createCase($validated_data);
            // return response
            return response()->json([
                'status' => $case['status'],
                'data' => $case['data'],
            ], $case['code']);
            // returun error response
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateCaseRequest $request)
    {
        try {
            // validated data
            $validated_data = $request->validated();
            // calling case service
            $case = $this->caseService->updateCase($validated_data);
            // return response
            return response()->json([
                'status' => $case['status'],
                'data' => $case['data'],
            ], $case['code']);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function view_list()
    {
        try {

            $cases = $this->caseService->get_cases();
            return response()->json([
                'status' => 'success',
                'data' => $cases
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
