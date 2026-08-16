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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TemplateController extends Controller
{
    protected TemplateService $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function upload(StoreTemplateRequest $request): JsonResponse
    {
        try {
            // call servcie
            $response = $this->templateService->uploadTemplate(
                $request->validated(),
                $request->file('file')
            );
            // validate permission 
            if (Gate::denies('create_template', SmartTemplate::class)) {
                return sendError("Unauthorized, You do not have permission to create templates", [], 403);
            }
            //send response
            return sendResponse($response, 'تم رفع القالب بنجاح', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage() . "   "  . $e->getFile() . "   " . $e->getLine()], 500);
        }
    }

    public function update_template(UpdateTemplateRequest $request)
    {
        try {

            // validate data
            $data = $request->validated();
            if ($request->hasFile('file')) {
                $data['file'] = $request->file('file');
            }
            // validate permission 
            if (Gate::denies('update_template', SmartTemplate::class)) {
                return sendError("Unauthorized, You do not have permission to create templates", [], 403);
            }
            // call service
            $response = $this->templateService->updateTemplate($data);
            // send response 
            return sendResponse($response, 'تم تحديث القالب بنجاح', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }

    public function getTemplates(Request $request): JsonResponse
    {
        try {
            // Valdiate data 
            $validaor = Validator::make([
                'category' => request()->category,
                'title' => request()->title
            ], [
                'category' => 'nullable|exists:smart_templates,category',
                'title' => 'nullable|string',
            ]);
            if ($validaor->fails()) {
                return sendError('بيانات المدخلات غير صحيحة', $validaor->errors()->toArray(), 422);
            }
            // validate permission 
            if (Gate::denies('get_templates', SmartTemplate::class)) {
                return sendError("Unauthorized, You do not have permission to create templates", [], 403);
            }
            // call service
            $response = $this->templateService->getTemplates($request->all());
            //send response
            return sendResponse($response, 'تم جلب القالب بنجاح', 200);
        } catch (\Exception $e) {
            return sendError("Internal Server Error", ['error' => $e->getMessage()], 500);
        }
    }

    public function show_template_details(): JsonResponse
    {
        try {
            //  validate data 
            $validaor = Validator::make(['id' => request()->template_id], [
                'id' => 'required|exists:smart_templates,id',
            ]);
            if ($validaor->fails()) {
                return sendError('بيانات المدخلات غير صحيحة', $validaor->errors()->toArray(), 422);
            }
            // validate permission 
            if (Gate::denies('show_template_details', SmartTemplate::class)) {
                return sendError("Unauthorized, You do not have permission to create templates", [], 403);
            }
            // call service
            $response = $this->templateService->getTemplateById(request()->template_id);
            return sendResponse($response, 'تم جلب القالب بنجاح', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }

    public function delete_template()
    {
        try {
            // validate permission 
            $validator = Validator::make(['template_id'  =>  request()->template_id], [
                'template_id' => 'required|exists:smart_templates,id',
            ], [
                'id.required' => 'Template ID is required',
                'id.exists' => 'Template ID does not exist',
            ]);
            if ($validator->fails()) {
                return sendResponse("Validation Error", 'بيانات المدخلات غير صحيحة', 422);
            }
            // validate permission 
            if (Gate::denies('delete_template', SmartTemplate::class)) {
                return sendError("Unauthorized, You do not have permission to create templates", [], 403);
            }
            // call service
            $response = $this->templateService->deleteTemplate();
            // send response 
            return sendResponse($response, 'تم حذف القالب بنجاح', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }


    public function generate_template(GenerateTemplateRequest $request)
    {
        try {
            // validate data 
            $data = $request->validated();
            // validate permission 
            if (Gate::denies('generate_template', SmartTemplate::class)) {
                return sendError("Unauthorized, You do not have permission to create templates", [], 403);
            }
            // call service
            $response = $this->templateService->generateFromTemplate($data);
            // send resposne 
            return sendResponse($response, 'تم توليد الملف بنجاح', 200);
        } catch (\Exception $e) {
            return sendError('Internal Server Error', ['error' => $e->getMessage()], 500);
        }
    }
    public function get_templates_categories()
    {
        $categories = SmartTemplate::distinct()->pluck('category');
        return sendResponse($categories, 'تم جلب التصنيفات', 200);
    }
}
