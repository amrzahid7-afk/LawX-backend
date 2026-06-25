<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Http\Requests\GenerateTemplateRequest;
use App\Models\SmartTemplate;
use App\Services\TemplateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    protected TemplateService $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * API 1: رفع قالب جديد
     */
    public function upload(StoreTemplateRequest $request): JsonResponse
    {
        try {
            $response = $this->templateService->uploadTemplate(
                $request->validated(),
                $request->file('file')
            );

            return response()->json([
                'status' => true,
                'message' => ' تم رفع القالب',
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل في رفع القالب',
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'Class' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * API 2: جلب القوالب المتاحة
     */
    public function getTemplates(Request $request): JsonResponse
    {
        try {
            $response = $this->templateService->getTemplates($request->all());
            return response()->json([
                'status' => true,
                'message' => ' تم جلب القالب',
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل في رفع القالب',
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'Class' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * API 3: جلب تفاصيل قالب
     */
    public function show_template_details(): JsonResponse
    {
        try {
            $validaor = Validator::make(['id' => request()->template_id], [
                'id' => 'required|exists:smart_templates,id',
            ]);
            if ($validaor->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'بيانات المدخلات غير صحيحة',
                    'errors' => $validaor->errors(),
                ], 422);
            }
            $response = $this->templateService->getTemplateById(request()->template_id);
            return response()->json([
                'status' => true,
                'message' => ' تم جلب القالب',
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 4: تحديث قالب
     */
    public function update_template(UpdateTemplateRequest $request)
    {
        try {


            $data = $request->validated();

            // إذا تم رفع ملف جديد، أضفه للبيانات
            if ($request->hasFile('file')) {
                $data['file'] = $request->file('file');
            }

            $response = $this->templateService->updateTemplate($data);
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث القالب بنجاح',
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في تحديث القالب',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * API 5: حذف قالب
     */
    public function delete_template()
    {
        try {
            $validator = Validator::make(['template_id'  =>  request()->template_id], [
                'template_id' => 'required|exists:smart_templates,id',
            ], [
                'id.required' => 'Template ID is required',
                'id.exists' => 'Template ID does not exist',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات المدخلات غير صحيحة',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $response = $this->templateService->deleteTemplate();
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في حذف القالب',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API 6: توليد ملف من قالب (FR-LB-02)
     */
    public function generate_template(GenerateTemplateRequest $request)
    {
        try {
            $data = $request->validated();
            $response = $this->templateService->generateFromTemplate($data);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في توليد الملف',
                'error' => $e->getMessage(),
                'Line' => $e->getLine(),
                'File' => $e->getFile(),
            ], 500);
        }
    }
}
