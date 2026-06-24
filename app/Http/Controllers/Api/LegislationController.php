<?php
// app/Http/Controllers/Api/LegislationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLegislationRequest;
use App\Http\Requests\UpdateLegislationRequest;
use App\Models\Legislation;
use App\Services\LegislationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LegislationController extends Controller
{
    protected LegislationService $legislationService;
    public function __construct(LegislationService $legislationService)
    {
        $this->legislationService = $legislationService;
    }
    public function index(Request $request)
    {
        try {
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
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات المدخلات غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $reponse = $this->legislationService->getLegislations($request->all());
            return response()->json([
                'success' => true,
                'message' => 'تم جلب التشريعات بنجاح',
                'data' => $reponse['data'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'Class' => $e->getFile()
            ], 500);
        }
    }
    public function get_law_details($id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:legislations,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات المدخلات غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $response = $this->legislationService->get_law_details($id);

            return response()->json([
                'success' => true,
                'message' => $response['message'],
                'data' => $response['data'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
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
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        // calling service
        $result = $this->legislationService->semantic_search($request->q, $request->limit ?? 10);
        // return response
        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }
    public function create_law(StoreLegislationRequest $request): JsonResponse
    {
        try {
            $response = $this->legislationService->create_law($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء التشريع بنجاح',
                'data' => $response['data'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update_law(UpdateLegislationRequest $request): JsonResponse
    {
        try {
            $response = $this->legislationService->update_law($request->validated());

            return response()->json([
                'success' => $response['success'],
                'message' => $response['message'],
                'data'    => $response['data'],
            ], $response['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function delete_law(string $law_id): JsonResponse
    {
        try {
            $validator = Validator::make(['id' => $law_id], [
                'id' => 'required|exists:legislations,id',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'Errors' => $validator->errors(),
                ], 422);
            }

            // 3. استدعاء الـ Service
            $response = $this->legislationService->delete_law($law_id);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التشريع بنجاح',
                'data' => $response['data'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
