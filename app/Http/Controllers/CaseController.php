<?php

namespace App\Http\Controllers;

use App\Http\Requests\CaseCreationRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\CaseModel;
use App\Services\CaseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CaseController extends Controller
{
    public CaseService $caseService;
    public function __construct(CaseService $caseService)
    {
        $this->caseService = $caseService;
    }
    public function create(CaseCreationRequest $request)
    {
        try {
            // validated data
            // dd("done");
            $validated_data = $request->validated();
            // dd("done");
            // calling case service
            $case = $this->caseService->createCase($validated_data);
            // dd("done");
            // return response
            return response()->json([
                'status' => $case['status'],
                'data' => $case['data'],
            ], $case['code']);
            // dd("done");

            // returun error response
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'something went wrong',
                'error'   => $e->getMessage(),
                'Line'   => $e->getLine(),
                'Class'   => $e->getFile()
            ], 500);
        }
    }
    public function get_case_details($case_id)
    {
        try {
            // validate case_id is uuid and exists in cases table
            $validate = Validator::make(
                ['case_id' => $case_id],
                [
                    'case_id' => ['required', 'uuid', Rule::exists('cases', 'id')]
                ],
                [
                    'case_id.required' => 'Case ID is required',
                    'case_id.uuid' => 'Case ID must be a valid UUID',
                    'case_id.exists' => 'Case not found'
                ]
            );
            if ($validate->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid case ID',
                    'error'   => $validate->errors()
                ], 422);
            }
            // calling case service
            $case = $this->caseService->get_case_details($case_id);;
            // return response
            return response()->json([
                'status' => $case['status'],
                'data' => $case['data'],
            ], $case['code']);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'something went wrong',
                'error'   => $e->getMessage(),
                'Line'   => $e->getLine(),
                'Class'   => $e->getFile()
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
    public function view_list(Request $request)
    {
        try {

            $cases = $this->caseService->get_cases($request);
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
    public function delete_case($case_id)
    {
        try {
            // validate case_id is uuid and exists in cases table
            $validate = Validator::make(
                ['case_id' => $case_id],
                [
                    'case_id' => ['required', 'uuid', Rule::exists('cases', 'id')]
                ],
                [
                    'case_id.required' => 'Case ID is required',
                    'case_id.uuid' => 'Case ID must be a valid UUID',
                    'case_id.exists' => 'Case not found'
                ]
            );
            if ($validate->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid case ID',
                    'error'   => $validate->errors()
                ], 422);
            }
            // calling case service
            $cases = $this->caseService->delete_case($case_id);
            // return response
            return response()->json([
                'status' => $cases['status'],
                'data' => $cases['data']
            ], $cases['code']);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'something went wrong',
                'error'   => $e->getMessage(),
                'Line'   => $e->getLine()
            ], 500);
        }
    }
}
