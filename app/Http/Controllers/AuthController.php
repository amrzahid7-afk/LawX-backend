<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgetPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\Verify_2FA_Request;
use App\Http\Requests\Auth\UploadAvatarRequest;
use App\Http\Requests\Auth\changePasswordRequest;
use App\Services\AuthService;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->all());
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function verify_2FA(Verify_2FA_Request $request)
    {
        try {
            $data = $this->authService->verify2FA($request->all());
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }
    public function logout()
    {
        $this->authService->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Logout successfully'
        ]);
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
    // Manage Profile  
    public function getSuperAdminProfile()
    {
        try {
            $data = $this->authService->getSuperAdminProfile();
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error_data' => [
                    'message' => $e->getMessage(),
                    'Line' => $e->getLine()
                ],
            ], 401);
        }
    }
    public function updateSuperAdminProfile(UpdateProfileRequest $request)
    {
        try {
            $data = $this->authService->updateSuperAdminProfile($request->all());
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error_data' => [
                    'message' => $e->getMessage(),
                    'Line' => $e->getLine()
                ],
            ], 401);
        }
    }
    public function upload_avatar(UploadAvatarRequest $request)
    {
        try {
            $data = $this->authService->upload_avatar($request);
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error_data' => [
                    'message' => $e->getMessage(),
                    'Line' => $e->getLine()
                ],
            ], 500);
        }
    }
    public function changePassword(changePasswordRequest $request)
    {
        try {
            $data = $this->authService->changePassword($request->all());
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error_data' => [
                    'message' => $e->getMessage(),
                    'Line' => $e->getLine()
                ],
            ], 500);
        }
    }
    public function switch_2FA()
    {
        try {
            $data = $this->authService->switch_2FA();
            return response()->json([
                'status' => $data['status'],
                'data' => $data['data']
            ], $data['code']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error_data' => [
                    'message' => $e->getMessage(),
                    'Line' => $e->getLine()
                ],
            ], 500);
        }
    }
}
