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
                'phone_number' => ['required', 'string', 'min:10'],
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $token = $this->authService->registerClient($request->all());
            return sendResponse(['Token' => $token], 'Client registration successful.', 201);
        } catch (\Throwable $th) {
            return sendError('Client registration failed.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
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
            ]);
            if ($validator->fails()) {
                return sendError('Validation Error :', $validator->errors()->toArray(), 422);
            }
            $result = $this->authService->register($request->all());
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
