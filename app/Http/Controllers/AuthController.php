<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\changePasswordRequest;
use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\UploadAvatarRequest;
use App\Http\Requests\Auth\Verify_2FA_Request;
use App\Http\Requests\VerifyLawyerRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function registerClient(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'full_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'phone_number' => ['required', 'string', 'min:10', 'unique:users,phone'],
                'address'      => ['nullable', 'string', 'max:500'],
                'lat'          => ['nullable', 'numeric', 'between:-90,90'],
                'lng'          => ['nullable', 'numeric', 'between:-180,180'],
                'bio'          => ['nullable', 'string', 'max:1000'],
                'national_id' => ['required', 'string', 'min:14', 'max:14', 'unique:users,national_id'],
                'avatar'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $token = $this->authService->registerClient($request);
            return sendResponse(['Token' => $token], 'Client registration successful.', 201);
        } catch (\Throwable $th) {
            return sendError('Internal Server Error ', ["Message" =>  $th->getMessage()], 500);
        }
    }
    public function verifySyndicate(VerifyLawyerRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $result = $this->authService->verifySyndicate($validatedData);
            return sendResponse($result['data'] ?? [], $result['status'] . ' : ' . $result['message'], $result['code']);
        } catch (\Throwable $th) {
            return sendError('Verification failed.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'lawyer_profile_id' => ['required', 'exists:lawyer_profiles,id'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'phone_number' => ['required', 'string', 'min:10'],
                'address'      => ['nullable', 'string', 'max:500'],
                'lat'          => ['nullable', 'numeric', 'between:-90,90'],
                'lng'          => ['nullable', 'numeric', 'between:-180,180'],
                'bio'          => ['nullable', 'string', 'max:1000'],
                'national_id' => ['required', 'string', 'min:14', 'max:14', 'unique:users,national_id'],
                'avatar'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
                'city' => ['required', 'string', 'max:255'],
                'experience_years' => ['nullable', 'prohibited_if:lawyer_state,trainee', 'integer', 'min:0', 'max:60'],
            ], [
                'lawyer_profile_id.required' => 'حقل ملف المحامي مطلوب',
                'lawyer_profile_id.exists'   => 'ملف المحامي المحدد غير موجود',

                'email.required' => 'حقل البريد الإلكتروني مطلوب',
                'email.email'    => 'يجب إدخال بريد إلكتروني صحيح',
                'email.unique'   => 'البريد الإلكتروني مسجل مسبقاً',

                'password.required'  => 'حقل كلمة المرور مطلوب',
                'password.string'    => 'كلمة المرور يجب أن تكون نصاً',
                'password.min'       => 'كلمة المرور يجب أن تتكون من 8 أحرف على الأقل',
                'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',

                'phone_number.required' => 'حقل رقم الهاتف مطلوب',
                'phone_number.string'   => 'رقم الهاتف يجب أن يكون نصاً',
                'phone_number.min'      => 'رقم الهاتف يجب أن يتكون من 10 أرقام على الأقل',

                'address.string' => 'حقل العنوان يجب أن يكون نصاً',
                'address.max'    => 'العنوان يجب ألا يتجاوز 500 حرف',

                'lat.numeric'  => 'خط العرض يجب أن يكون رقماً',
                'lat.between'  => 'خط العرض يجب أن يكون بين -90 و 90',

                'lng.numeric'  => 'خط الطول يجب أن يكون رقماً',
                'lng.between'  => 'خط الطول يجب أن يكون بين -180 و 180',

                'bio.string' => 'حقل السيرة الذاتية يجب أن يكون نصاً',
                'bio.max'    => 'السيرة الذاتية يجب ألا تتجاوز 1000 حرف',

                'national_id.required' => 'حقل الرقم القومي مطلوب',
                'national_id.string'   => 'الرقم القومي يجب أن يكون نصاً',
                'national_id.min'      => 'الرقم القومي يجب أن يتكون من 14 رقماً',
                'national_id.max'      => 'الرقم القومي يجب أن يتكون من 14 رقماً فقط',
                'national_id.unique'   => 'الرقم القومي مسجل مسبقاً',

                'avatar.image' => 'الملف المرفوع يجب أن يكون صورة',
                'avatar.mimes' => 'صيغة الصورة يجب أن تكون: jpeg, png, jpg, webp',
                'avatar.max'   => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',

                'city.required' => 'حقل المدينة مطلوب',
                'city.string'   => 'حقل المدينة يجب أن يكون نصاً',
                'city.max'      => 'اسم المدينة يجب ألا يتجاوز 255 حرف',

                'experience_years.prohibited_if' => 'لا يمكن إدخال سنوات الخبرة لحالة المتدرب',
                'experience_years.integer'       => 'سنوات الخبرة يجب أن تكون رقماً صحيحاً',
                'experience_years.min'           => 'سنوات الخبرة يجب أن تكون 0 على الأقل',
                'experience_years.max'           => 'سنوات الخبرة يجب ألا تتجاوز 60 سنة',
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $result = $this->authService->register($request);
            return sendResponse($result, 'Registration successful.', 201);
        } catch (\Throwable $th) {
            return sendError('Registration failed.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->all());
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (HttpResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        // return response()->json(["رسالة" => "وصل الغالي عالكونترولر"]);
        try {
            $user = $request->user();

            if ($user) {
                // بدلاً من currentAccessToken()->delete()، سنستخدم الطريقة المباشرة التالية:
                $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }
    public function forgotPassword(ForgetPasswordRequest $request)
    {
        try {
            $result = $this->authService->sendResetLink($request->email);
            return response()->json($result, $result['code']);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function resetPassword(ResetPasswordRequest $request)
    {
        $result = $this->authService->resetPassword($request->all());
        return response()->json($result, $result['code']);
    }
    public function me(Request $request)
    {
        $user = $request->user();
        $formattedUser = [
            'id' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'role' => $user->role ? $user->role->name : null,
        ];
        return response()->json([
            'status' => 'success',
            'data' => [
                'message' => 'Fetch user data successfully',
                'data' => $formattedUser
            ],
        ], 200);
    }











    // Manage Profile 
    // public function verify_2FA(Verify_2FA_Request $request)
    // {
    //     try {
    //         $data = $this->authService->verify2FA($request->all());
    //         return response()->json([
    //             'status' => $data['status'],
    //             'data' => $data['data']
    //         ], $data['code']);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage()
    //         ], 401);
    //     }
    // }
    // public function getSuperAdminProfile()
    // {
    //     try {
    //         $data = $this->authService->getSuperAdminProfile();
    //         return response()->json([
    //             'status' => $data['status'],
    //             'data' => $data['data']
    //         ], $data['code']);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'error_data' => [
    //                 'message' => $e->getMessage(),
    //                 'Line' => $e->getLine()
    //             ],
    //         ], 401);
    //     }
    // }
    // public function updateSuperAdminProfile(UpdateProfileRequest $request)
    // {
    //     try {
    //         $data = $this->authService->updateSuperAdminProfile($request->all());
    //         return response()->json([
    //             'status' => $data['status'],
    //             'data' => $data['data']
    //         ], $data['code']);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'error_data' => [
    //                 'message' => $e->getMessage(),
    //                 'Line' => $e->getLine()
    //             ],
    //         ], 401);
    //     }
    // }
    // public function upload_avatar(UploadAvatarRequest $request)
    // {
    //     try {
    //         $data = $this->authService->upload_avatar($request);
    //         return response()->json([
    //             'status' => $data['status'],
    //             'data' => $data['data']
    //         ], $data['code']);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'error_data' => [
    //                 'message' => $e->getMessage(),
    //                 'Line' => $e->getLine()
    //             ],
    //         ], 500);
    //     }
    // }
    // public function changePassword(changePasswordRequest $request)
    // {
    //     try {
    //         $data = $this->authService->changePassword($request->all());
    //         return response()->json([
    //             'status' => $data['status'],
    //             'data' => $data['data']
    //         ], $data['code']);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'error_data' => [
    //                 'message' => $e->getMessage(),
    //                 'Line' => $e->getLine()
    //             ],
    //         ], 500);
    //     }
    // }
    // public function switch_2FA()
    // {
    //     try {
    //         $data = $this->authService->switch_2FA();
    //         return response()->json([
    //             'status' => $data['status'],
    //             'data' => $data['data']
    //         ], $data['code']);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'error_data' => [
    //                 'message' => $e->getMessage(),
    //                 'Line' => $e->getLine()
    //             ],
    //         ], 500);
    //     }
    // }
}
