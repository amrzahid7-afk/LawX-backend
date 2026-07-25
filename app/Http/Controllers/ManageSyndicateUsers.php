<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLawerRequest;
use App\Http\Requests\ListLawyersRequest;
use App\Services\ManageSyndicateUsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageSyndicateUsers extends Controller
{
    private ManageSyndicateUsersService $service;
    public function __construct(ManageSyndicateUsersService $service)
    {
        $this->service = $service;
    }

    public function createLawyer(CreateLawerRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $result = $this->service->createLawyer($validatedData);
            return sendResponse("", 'Lawyer created successfully.', 201);
        } catch (\Throwable $th) {
            return sendError('Failed to create lawyer.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function get_lawers(ListLawyersRequest $request)
    {
        try {
            $vaidated_data = $request->validated();
            $lawyers = $this->service->getLawyers($vaidated_data);
            return sendResponse($lawyers, 'Lawyers retrieved successfully.', 200);
        } catch (\Throwable $th) {
            return sendError('Failed to retrieve lawyers.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function toggleSuspension($lawyer_id)
    {
        try {
            $validator = Validator::make(['lawyer_id' => $lawyer_id], [
                'lawyer_id' => ['required', 'uuid', Rule::exists('lawyer_profiles', 'id')],
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $this->service->toggleSuspension($lawyer_id);
            return sendResponse(null, 'Lawyer suspension status toggled successfully.', 200);
        } catch (\Throwable $th) {
            return sendError('Failed to toggle lawyer suspension status.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function promoteTrainee($lawer_id)
    {
        try {
            $validator = Validator::make(['lawyer_id' => $lawer_id], [
                'lawyer_id' => ['required', 'uuid', Rule::exists('lawyer_profiles', 'id')],
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $this->service->promoteTrainee($lawer_id);
            return sendResponse(null, 'Lawyer promoted successfully.', 200);
        } catch (\Throwable $th) {
            $code = $th->getCode();

            // التأكد من أن الكود المستخرج هو كود HTTP valid وليس خطأ قاعدة بيانات (مثل الكود النصي أو صفر)
            if (!is_int($code) || $code < 100 || $code > 599) {
                $code = 500;
            }

            // إذا كان الخطأ غير متوقع (500)، نغير الرسالة لرسالة عامة للمستخدم ونترك التفاصيل للـ Debug
            if ($code === 500) {
                $errorMessage = 'حدث خطأ داخلي في الخادم، يرجى المحاولة لاحقاً.';
                $errorDetails = [
                    'message' => $th->getMessage(),
                    'file'    => $th->getFile(),
                    'line'    => $th->getLine()
                ];
            } else {
                // الأخطاء المتوقعة التي قمت برميها في السيرفر (400, 404, 422, 409)
                $errorMessage = $th->getMessage();
                $errorDetails = [];
            }

            return sendError($errorMessage, $errorDetails, $code);
        }
    }
    public function updateLawyer(Request $request, $lawyer_id)
    {
        try {
            // 1. التحقق من وجود معرف المحامي أولاً
            $idValidator = Validator::make(['lawyer_id' => $lawyer_id], [
                'lawyer_id' => ['required', 'uuid', Rule::exists('lawyer_profiles', 'id')],
            ]);

            if ($idValidator->fails()) {
                return sendError('المحامي غير موجود في النظام', $idValidator->errors()->toArray(), 404);
            }

            // 2. التحقق من البيانات القادمة للتعديل 
            // تأكد أن الأسماء هنا تطابق تماماً ما ترجوه من طلب الـ API
            $validator = Validator::make($request->all(), [
                'national_id'           => ['sometimes', 'required', 'string', Rule::unique('lawyer_profiles', 'national_id')->ignore($lawyer_id)],
                'full_name'             => ['sometimes', 'required', 'string', 'max:255'],
                'birth_date'            => ['sometimes', 'required', 'date'],
                'birth_place'           => ['sometimes', 'required', 'string'],
                'father_name'           => ['sometimes', 'required', 'string'],
                'mother_name'           => ['sometimes', 'required', 'string'],
                'mother_family_name'    => ['sometimes', 'required', 'string'],
                'civil_registry_number' => ['sometimes', 'required', 'string'],
                'syndicate_card_number' => ['sometimes', 'required', 'string', Rule::unique('lawyer_profiles', 'syndicate_card_number')->ignore($lawyer_id)],
                'syndicate_branch'      => ['sometimes', 'required', 'string'],
                'lawyer_state'          => ['sometimes', 'required', 'string', Rule::in(['trainee', 'licensed', 'independent'])],
            ]);

            if ($validator->fails()) {
                return sendError('خطأ في التحقق من البيانات المرسلة', $validator->errors()->toArray(), 422);
            }

            // جلب البيانات التي اجتازت الفحص فقط
            $validatedData = $validator->validated();

            // 💡 خطوة فحص أمان مؤقتة: إذا كانت المصفوفة فارغة فهذا يعني أن المدخلات قادمة بأسماء مختلفة عن الفحص
            if (empty($validatedData)) {
                return sendError('لم يتم إرسال أي بيانات مطابقة للتعديل، يرجى التحقق من أسماء الحقول المتغيرة.', [], 400);
            }

            // تمرير البيانات المفلترة إلى السيرفيس
            $this->service->updateLawyer($lawyer_id, $validatedData);

            return sendResponse(null, 'Lawyer updated successfully.', 200);
        } catch (\Throwable $th) {
            return handleException($th);
        }
    }
    public function deleteLawyer($lawyer_id)
    {
        try {
            // validate data 
            $validator = Validator::make(['lawyer_id' => $lawyer_id], [
                'lawyer_id' => ['required', 'uuid', Rule::exists('lawyer_profiles', 'id')],
            ]);
            if ($validator->fails()) {
                return sendError('المحامي المطلوب حذفه غير موجود', $validator->errors()->toArray(), 404);
            }
            // call service
            $this->service->deleteLawyer($lawyer_id);
            // send resposne
            return sendResponse(null, 'Lawyer deleted successfully.', 200);
        } catch (\Throwable $th) {
            return handleException($th);
        }
    }
}
