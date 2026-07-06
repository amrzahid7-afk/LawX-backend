<?php

namespace App\Services;

use App\Enums\RoleSlug;
use App\Mail\ResetPasswordMail;
use App\Mail\Send2FaMail;
use App\Models\LawerProfile;
use App\Models\PasswordResetToken;
use App\Models\Role;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    public function verifySyndicate($data)
    {
        $lawer_profile = LawerProfile::where('national_id', $data['national_id'])
            ->where('full_name', $data['full_name'])
            ->where('father_name', $data['father_name'])
            ->where('mother_name', $data['mother_name'])
            ->where('mother_family_name', $data['mother_family_name'])
            ->where('birth_place', $data['birth_place'])
            ->where('birth_date', $data['birth_date'])
            ->where('civil_registry_number', $data['civil_registry_number'])
            ->where('syndicate_card_number', $data['syndicate_card_number'])
            ->where('syndicate_branch', $data['syndicate_branch'])
            ->where('lawyer_state', $data['lawyer_state'])
            ->first();
        // if no matched data 
        if (!$lawer_profile) {
            return sendError('البيانات المدخلة غير مطابقة لسجلات النقابة.', [], 404);
        }
        // if there are exist account 
        if ($lawer_profile->is_claimed) {
            return sendError('هذا الملف الشخصي مسجل بالفعل في النظام مسبقاً.', [], 400);
        }
        return sendResponse([
            'lawyer_profile_id' => $lawer_profile->id,
            'message' => 'تم التحقق من بيانات النقابة بنجاح، يمكنك إكمال عملية التسجيل.'
        ], 'Verification successful.');
    }
    public function register(array $data)
    {
        DB::beginTransaction();
        $profile = LawerProfile::findOrFail($data['lawyer_profile_id']);
        if ($profile->is_claimed) {
            return sendError('هذا الحساب تم تفعيله مسبقاً.', [], 400);
        }
        $user = User::create([
            'full_name' => $profile->full_name, // نأخذ الاسم من ملف النقابة مباشرة لضمان الدقة
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => Role::where('slug', RoleSlug::Lawyer->value)->firstOrFail()->id
        ]);
        $profile->update([
            'user_id' => $user->id,
            'is_claimed' => true,
            'claimed_at' => now()->format('Y-m-d'),
        ]);

        $token = $user->createToken('LawyerToken')->plainTextToken;
        DB::commit();
        return sendResponse(['token' => $token,], 'Registration successful.', 201);
    }
    public function registerClient($data)
    {
        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role_id' => Role::where('slug', RoleSlug::Client->value)->firstOrFail()->id
        ]);
        $token = $user->createToken('ClientToken')->plainTextToken;
        return $token;
    }
    public function login(array $credentials)
    {
        $user = User::where([
            'email' => $credentials['email'],
            // 'password' => bcrypt($credentials['password'])
        ])->first();
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw new HttpResponseException(response()->json([
                'status' => 'Error',
                'message' => 'The provided credentials do not match our records.'
            ], 400));
        }
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;
        $formatted_data = [
            'ID' => $user->id,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'role' => $user->role->name,
            'is_active' => $user->is_active,
            'created_at' => $user->created_at->format('y m d'),
        ];
        return [
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => $formatted_data
            ],
            'code' => 200
        ];
    }
    public function log_out(Request $request)
    {
        try {
            $user = auth('sanctum')->user();
            $user = $request->user();
            $user->currentAccessToken()?->delete();
            return [
                'status' => 'success',
                'message' => 'Logged out successfully',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
                'Line' => $e->getLine(),
            ];
        }
    }

    public function sendResetLink($email)
    {
        // get user
        $user = User::where('email', $email)->first();
        // check the role 
        // if ($user->role->name !== 'productowner') {
        //     return [
        //         'status' => false,
        //         'message' => 'Forbidden',
        //         'code' => 403
        //     ];
        // }
        // generate tokenand code  
        $token = Str::random(64);
        $code = rand(1000000, 9999999);
        // store and hash the token and code
        PasswordResetToken::updateOrCreate(
            ['email' => $email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );
        VerificationCode::updateOrCreate([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'reset_password',
            'expires_at' => Carbon::now()->addMinutes(60),
            'is_used' => false
        ]);
        // an comment when react develope page 
        // // react page url
        // $reactPageUrl = "http://localhost:3000/reset-password";
        // // full reset link
        // $fullResetLink = $reactPageUrl . "?token=" . $token . "&email=" . $email;

        // // send mail
        // Mail::to($email)->send(new ResetPasswordMail($fullResetLink, $email));
        // send mail via mailtrap
        Mail::to($email)->send(new ResetPasswordMail($token, $email, $code));
        // return response success
        return [
            'status' => true,
            'data' => [
                'message' => 'we send mail to your email to reset password',
                'reset-token' => $token,
                'code' => $code
            ],
            'code' => 200
        ];
    }
    public function resetPassword(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        $token = PasswordResetToken::where('email', $data['email'])->first();
        $code = VerificationCode::where([
            'user_id' => $user->id,
            'type' => 'reset_password'
        ])->first();
        // token validation
        if (!$token || !Hash::check($data['token'], $token->token)) {
            return [
                'status' => false,
                'message' => 'invalid token',
                'code' => 400
            ];
        }
        // token expiration
        if (Carbon::parse($token->created_at)->addMinutes(60)->isPast()) {
            $token->delete();
            return [
                'status' => false,
                'message' => 'token is expired',
                'code' => 400
            ];
        }
        // code validation
        if (!$code || $code->is_used) {
            return [
                'status' => false,
                'message' => 'invalid code',
                'code' => 400
            ];
        }
        // code expiration
        if (Carbon::parse($code->expires_at)->isPast()) {
            $code->delete();
            return [
                'status' => false,
                'message' => 'code is expired',
                'code' => 400
            ];
        }
        // update password  
        User::where('email', $data['email'])->update([
            'password' => Hash::make($data['password'])
        ]);

        $token->delete();
        $code->delete();
        return [
            'status' => true,
            'message' => 'updating password successfully',
            'code' => 200
        ];
    }
    // public function changePassword($array)
    // {
    //     $user = auth('sanctum')->user();

    //     $user->update([
    //         'password' => Hash::make($array['new_password'])
    //     ]);

    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'change password successfully',
    //         ],
    //         'code' => 200
    //     ];
    // }
    // public function verify2FA($data)
    // {
    //     // get user , code , token 
    //     $user = User::where('id', $data['user_id'])->first();
    //     $code = VerificationCode::where('user_id', $data['user_id'])->where('type', 'login_2FA')->first();
    //     $temporary_token = PasswordResetToken::where('email', $user->email)->where('type', 'login_2FA')->first();
    //     // check temporary token
    //     if (! Hash::check($data['temporary_token'], $temporary_token->token)) {
    //         return [
    //             'status' => 'failed',
    //             'data' => [
    //                 'message' => 'invalid temporary token'
    //             ],
    //             'code' => '400'
    //         ];
    //     }
    //     /// check verfication code 
    //     else if (! Hash::check($data['code'], $code->code)) {
    //         return [
    //             'status' => 'failed',
    //             'data' => [
    //                 'message' => 'invalid code'
    //             ],
    //             'code' => '400'
    //         ];
    //     } else {
    //         // generate login_token 
    //         $token = $user->createToken('api-token')->plainTextToken;
    //         // delete temporary token and verfication code 
    //         $code->delete();
    //         $temporary_token->delete();
    //         // send response    
    //         return [
    //             'status' => 'success',
    //             'data' => [
    //                 'token' => $token,
    //                 'message' => 'login successfully'
    //             ],
    //             'code' => '200'
    //         ];
    //     }
    // }
    // public function switch_2FA()
    // {
    //     $user = auth()->user();

    //     $user->update([
    //         'activation_2FA' => !$user->activation_2FA
    //     ]);

    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'switch 2FA successfully',
    //             'activation_2FA' => $user->activation_2FA,
    //         ],
    //         'code' => 200
    //     ];
    // }
    // public function login($credentials)
    // {
    //     if (!Auth::attempt($credentials)) {
    //         return response()->json([
    //             'message' => 'Invalid credentials'
    //         ], 401);
    //     }

    //     $user = auth('api')->user();

    //     // حذف أي tokens قديمة (اختياري)
    //     $user->tokens()->delete();

    //     $token = $user->createToken('api-token')->plainTextToken;
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'token' => $token,
    //             'user' => auth('sanctum')->user()
    //         ],
    //         'code' => 200
    //     ];
    // }
    // login jwt 
    // public function login(array $credetials)
    // {
    //     // if (!$token = auth('api')->attempt($credetials)) {
    //     //     return [
    //     //         'status' => 'error',
    //     //         'data' => [
    //     //             'message' => 'invalid credentials'
    //     //         ],
    //     //         'code' => 401
    //     //     ];
    //     // }

    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'token' => $token,
    //     //         'user' => auth('api')->user()
    //     //     ],
    //     //     'code' => 200
    //     // ];
    //     // // // validate role 
    //     // // if ($user->role->name != 'productowner') {
    //     // //     return [
    //     // //         'status' => 'error',
    //     // //         'data' => [
    //     // //             'message' => 'Unauthorized'
    //     // //         ],
    //     // //         'code' => 401
    //     // //     ];
    //     // // }
    //     // // // 2FA senario 
    //     // // if ($user->activation_2FA == true) {
    //     // //     // generate token and code  
    //     // //     $token = Str::random(64);
    //     // //     $code = rand(1000000, 9999999);
    //     // //     // store and token 
    //     // //     PasswordResetToken::updateOrCreate(
    //     // //         [
    //     // //             'email' => $credetials['email'],
    //     // //             'type' => 'login_2FA'
    //     // //         ],
    //     // //         [
    //     // //             'token' => Hash::make($token),
    //     // //             'expired_at' => now()->addMinutes(60),
    //     // //             'created_at' => now(),
    //     // //         ]
    //     // //     );
    //     // //     VerificationCode::updateOrCreate(
    //     // //         [
    //     // //             'user_id' => $user->id,
    //     // //             'type' => 'login_2FA',
    //     // //             'is_used' => false
    //     // //         ],
    //     // //         [
    //     // //             'code' => Hash::make($code),
    //     // //             'expires_at' => Carbon::now()->addMinutes(60),
    //     // //             'is_used' => false
    //     // //         ]
    //     // //     );
    //     // //     // sending otp code via email
    //     // //     Mail::to($user->email)->send(new Send2FaMail($code));
    //     // // // return success response
    //     // // return [
    //     // //     'status' => 'true',
    //     // //     'data' => [
    //     // //         'message' => 'please Enter verfication otp code that we send to your email ',
    //     // //         'temporary_token' => $token,
    //     // //         'user_id' => $user->id
    //     // //     ],
    //     // //     'code' => '200'
    //     // // ];
    //     // // } else {
    //     // // login user
    //     // $token = JWTAuth::attempt($credetials);
    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'token' => $token,
    //     //         'user' => [
    //     //             "id" =>  $user["id"],
    //     //             "name" =>   $user["full_name"],
    //     //             "email" =>  $user["email"],
    //     //             "role" =>  $user->role->name
    //     //         ],
    //     //         'message' => 'login successfully'
    //     //     ],
    //     //     'code' => '200'
    //     // ];
    //     // // }
    // }
    // public function logout(Request $request)
    // {
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'message' => 'Logged out successfully'
    //     ]);
    // }
    // jwt logout 
    // public function logout()
    // {
    //     // JWTAuth::invalidate(JWTAuth::getToken());
    // }


    // Manage profile 
    // public function getSuperAdminProfile()
    // {
    //     // $user = User::select('full_name', 'email', 'is_active', 'activation_2FA')
    //     //     ->where('id', auth('api')->user()->id)
    //     //     ->first();
    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'message' => 'get profile successfully',
    //     //         'user' => $user
    //     //     ],
    //     //     'code' => 200
    //     // ];
    // }
    // public function updateSuperAdminProfile($validated_data)
    // {
    //     // $user = User::select('id', 'full_name', 'email', 'is_active', 'activation_2FA')
    //     //     ->where('id', auth('api')->user()->id)->first();
    //     // $user->update($validated_data);
    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'message' => 'update profile successfully',
    //     //         'user' => $user
    //     //     ],
    //     //     'code' => 201
    //     // ];
    // }
    // public function updateSuperAdminProfile($validated_data)
    // {
    //     $user = auth()->user();

    //     $user->update($validated_data);

    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'update profile successfully',
    //             'user' => $user
    //         ],
    //         'code' => 200
    //     ];
    // }
    // public function getSuperAdminProfile()
    // {
    //     $user = auth()->user();

    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'get profile successfully',
    //             'user' => $user
    //         ],
    //         'code' => 200
    //     ];
    // }
    // public function upload_avatar($request)
    // {
    //     // // $user = User::where('id', auth('api')->user()->id)->first();
    //     // // if ($request->hasFile('avatar')) {
    //     // //     // get avatar
    //     // //     $file = $request->file('avatar');
    //     // //     // store avatar and get path
    //     // //     $path = $file->store('public/avatars');
    //     // //     // delete last avatar
    //     // //     if ($user->avatar && Storage::exists($user->avatar)) {
    //     // //         Storage::disk('public')->delete($user->avatar);
    //     // //     }
    //     // //     $user->update([
    //     // //         'avatar' => $path
    //     // //     ]);
    //     // }
    //     // // return response
    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'message' => 'upload avatar successfully',
    //     //     ],
    //     //     'code' => 201
    //     // ];
    // }
    // use Illuminate\Support\Facades\Storage;

    // public function upload_avatar($request)
    // {
    //     $user = auth()->user();

    //     if ($request->hasFile('avatar')) {

    //         $file = $request->file('avatar');

    //         $path = $file->store('avatars', 'public');

    //         if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
    //             Storage::disk('public')->delete($user->avatar);
    //         }

    //         $user->update([
    //             'avatar' => $path
    //         ]);
    //     }

    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'upload avatar successfully',
    //         ],
    //         'code' => 200
    //     ];
    // }
    // public function deleteImage()
    // {
    //     // $user = User::where('id', auth('api')->user()->id)->first();
    //     // if ($user->avatar && Storage::exists($user->avatar)) {
    //     //     Storage::disk('public')->delete($user->avatar);
    //     // }
    //     // $user->update([
    //     //     'avatar' => null
    //     // ]);
    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'message' => 'delete avatar successfully',
    //     //     ],
    //     //     'code' => 201
    //     // ];
    // }
    // Manage Security Settings 
    // public function changePassword($array)
    // {
    //     // $user = User::where('id', auth('api')->user()->id)->first();
    //     // $user->update([
    //     //     'password' => Hash::make($array['new_password'])
    //     // ]);
    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'message' => 'change password successfully',
    //     //     ],
    //     //     'code' => 201
    //     // ];
    // }

    // public function switch_2FA()
    // {
    //     // $user = User::where('id', auth('api')->user()->id)->first();
    //     // $user->update([
    //     //     'activation_2FA' => !$user->activation_2FA
    //     // ]);
    //     // return [
    //     //     'status' => 'success',
    //     //     'data' => [
    //     //         'message' => 'switch 2FA successfully',
    //     //         'activation_2FA' => $user->activation_2FA,
    //     //     ],
    //     //     'code' => 201
    //     // ];
    // }

}
