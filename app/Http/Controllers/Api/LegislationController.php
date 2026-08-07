<?php
// app/Http/Controllers/Api/LegislationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLegislationRequest;
use App\Http\Requests\UpdateLegislationRequest;
use App\Models\Legal_category;
use App\Models\Legislation;
use App\Services\LegislationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class LegislationController extends Controller
{
    protected LegislationService $legislationService;
    public function __construct(LegislationService $legislationService)
    {
        $this->legislationService = $legislationService;
    }
    public function create_law(StoreLegislationRequest $request): JsonResponse
    {
        try {
            // validate permission 
            if (Gate::denies('create_law', Legislation::class)) {
                return sendError("Unauthorized, You do not have permission to create laws", [], 403);
            }
            // call service
            $response = $this->legislationService->create_law($request->validated());
            // send response
            return sendResponse($response['data'], $response['message'], 201);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }
    public function update_law(UpdateLegislationRequest $request): JsonResponse
    {
        try {
            // validate permission
            if (Gate::denies('update_law', Legislation::class)) {
                return sendError("Unauthorized, You do not have permission to update laws", [], 403);
            }
            // call service to update law
            $response = $this->legislationService->update_law($request->validated());
            // send response
            return sendResponse($response['data'], $response['message'], 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }
    public function delete_law(string $law_id): JsonResponse
    {
        try {
            //  validate data 
            $validator = Validator::make(['id' => $law_id], [
                'id' => 'required|exists:legislations,id',
            ]);
            if ($validator->fails()) {
                return sendError('Validation failed', $validator->errors()->toArray(), 422);
            }
            // validate permission
            if (Gate::denies('delete_law', Legislation::class)) {
                return sendError("Unauthorized, You do not have permission to delete laws", [], 403);
            }
            // call service to delete law
            $response = $this->legislationService->delete_law($law_id);
            // send response 
            return sendResponse($response['data'], $response['message'], 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        try {
            // validate data Filters
            $validator = Validator::make($request->all(), [
                'status' => 'nullable|in:active,amended,repealed',
                'category_id' => 'nullable|string|exists:legal_categories,id',
                'country' => 'nullable|string|max:100',
            ], [
                'category_id.exists' => 'التصنيف غير موجود',
                'status.in' => 'حالة القانون غير صحيحة',
                'country.max' => 'اسم البلد يجب ان يكون اقل من 100 حرف',
            ]);
            if ($validator->fails()) {
                return  sendError('Validation failed', $validator->errors()->toArray(), 422);
            }
            // validate permission
            if (Gate::denies('view_law', Legislation::class)) {
                return sendError("Unauthorized, You do not have permission to view laws", [], 403);
            }
            // dd(Legislation::get());
            // call service
            $reponse = $this->legislationService->getLegislations($request->all());
            // send resposne
            // dd($reponse);

            return sendResponse($reponse['data'], $reponse['message'], 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage() . "   "  . $e->getFile() . "   " . $e->getLine()], 500);
        }
    }
    public function Get_legal_categories()
    {
        // $categories = Legislation::distinct()->pluck('category_id');
        // $countries = Legislation::distinct()->pluck('country');
        $categories = Legal_category::all('id', 'name');
        return response()->json([
            'success' => true,
            'message' => 'تم جلب التصنيفات  والبلدان بنجاح',
            'data' => [
                'categories' => $categories,
            ],
        ]);
    }

    public function get_law_details($id)
    {
        try {
            //  validate data 
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:legislations,id',
            ]);
            if ($validator->fails()) {
                return sendError('Validation failed', $validator->errors()->toArray(), 422);
            }
            // validate permissions 
            if (Gate::denies('view_law', Legislation::class)) {
                return sendError("Unauthorized, You do not have permission to view laws", [], 403);
            }
            $response = $this->legislationService->get_law_details($id);
            return sendResponse($response['data'], $response['message'], 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage() . "   "  . $e->getFile() . "   " . $e->getLine()], 500);
        }
    }
    public function semantic_search(Request $request)
    {
        // validate request
        $request->validate([
            'q' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);
        if ($validator->fails()) {
            return  sendError('Validation failed', $validator->errors()->toArray(), 422);
        }
        // calling service
        $result = $this->legislationService->semantic_search($request->q, $request->limit ?? 10);
        // return response
        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }
}
