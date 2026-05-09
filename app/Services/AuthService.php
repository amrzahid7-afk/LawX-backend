<?php

namespace App\Services;

use App\Mail\ResetPasswordMail;
use App\Mail\Send2FaMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class AuthService
{
    public function login(array $credetials)
    {
        // fetching user
        $user = User::where('email', $credetials['email'])->first();
        // check password
        if (!Hash::check($credetials['password'], $user->password)) {
            return [
                'status' => 'error',
                'data' => [
                    'message' => 'invalid password'
                ],
                'code' => 401
            ];
        }
        // // validate role 
        // if ($user->role->name != 'productowner') {
        //     return [
        //         'status' => 'error',
        //         'data' => [
        //             'message' => 'Unauthorized'
        //         ],
        //         'code' => 401
        //     ];
        // }
        // // 2FA senario 
        // if ($user->activation_2FA == true) {
        //     // generate token and code  
        //     $token = Str::random(64);
        //     $code = rand(1000000, 9999999);
        //     // store and token 
        //     PasswordResetToken::updateOrCreate(
        //         [
        //             'email' => $credetials['email'],
        //             'type' => 'login_2FA'
        //         ],
        //         [
        //             'token' => Hash::make($token),
        //             'expired_at' => now()->addMinutes(60),
        //             'created_at' => now(),
        //         ]
        //     );
        //     VerificationCode::updateOrCreate(
        //         [
        //             'user_id' => $user->id,
        //             'type' => 'login_2FA',
        //             'is_used' => false
        //         ],
        //         [
        //             'code' => Hash::make($code),
        //             'expires_at' => Carbon::now()->addMinutes(60),
        //             'is_used' => false
        //         ]
        //     );
        //     // sending otp code via email
        //     Mail::to($user->email)->send(new Send2FaMail($code));
        // // return success response
        // return [
        //     'status' => 'true',
        //     'data' => [
        //         'message' => 'please Enter verfication otp code that we send to your email ',
        //         'temporary_token' => $token,
        //         'user_id' => $user->id
        //     ],
        //     'code' => '200'
        // ];
        // } else {
        // login user
        $token = JWTAuth::attempt($credetials);
        return [
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => [
                    "id" =>  $user["id"],
                    "name" =>   $user["full_name"],
                    "email" =>  $user["email"],
                    "role" =>  $user->role->name
                ],
                'message' => 'login successfully'
            ],
            'code' => '200'
        ];
        // }
    }
    public function verify2FA($data)
    {
        // get user , code , token 
        $user = User::where('id', $data['user_id'])->first();
        $code = VerificationCode::where('user_id', $data['user_id'])->where('type', 'login_2FA')->first();
        $temporary_token = PasswordResetToken::where('email', $user->email)->where('type', 'login_2FA')->first();
        // check temporary token
        if (! Hash::check($data['temporary_token'], $temporary_token->token)) {
            return [
                'status' => 'failed',
                'data' => [
                    'message' => 'invalid temporary token'
                ],
                'code' => '400'
            ];
        }
        /// check verfication code 
        else if (! Hash::check($data['code'], $code->code)) {
            return [
                'status' => 'failed',
                'data' => [
                    'message' => 'invalid code'
                ],
                'code' => '400'
            ];
        } else {
            // generate login_token 
            $token = JWTAuth::fromUser($user);
            // delete temporary token and verfication code 
            $code->delete();
            $temporary_token->delete();
            // send response    
            return [
                'status' => 'success',
                'data' => [
                    'token' => $token,
                    'message' => 'login successfully'
                ],
                'code' => '200'
            ];
        }
    }
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
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
    // Manage profile 
    public function getSuperAdminProfile()
    {
        $user = User::select('full_name', 'email', 'is_active', 'activation_2FA')
            ->where('id', auth('api')->user()->id)
            ->first();
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get profile successfully',
                'user' => $user
            ],
            'code' => 200
        ];
    }
    public function updateSuperAdminProfile($validated_data)
    {
        $user = User::select('id', 'full_name', 'email', 'is_active', 'activation_2FA')
            ->where('id', auth('api')->user()->id)->first();
        $user->update($validated_data);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'update profile successfully',
                'user' => $user
            ],
            'code' => 201
        ];
    }
    public function upload_avatar($request)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        if ($request->hasFile('avatar')) {
            // get avatar
            $file = $request->file('avatar');
            // store avatar and get path
            $path = $file->store('public/avatars');
            // delete last avatar
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->update([
                'avatar' => $path
            ]);
        }
        // return response
        return [
            'status' => 'success',
            'data' => [
                'message' => 'upload avatar successfully',
            ],
            'code' => 201
        ];
    }
    public function deleteImage()
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        if ($user->avatar && Storage::exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->update([
            'avatar' => null
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'delete avatar successfully',
            ],
            'code' => 201
        ];
    }
    // Manage Security Settings 
    public function changePassword($array)
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->update([
            'password' => Hash::make($array['new_password'])
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'change password successfully',
            ],
            'code' => 201
        ];
    }
    public function switch_2FA()
    {
        $user = User::where('id', auth('api')->user()->id)->first();
        $user->update([
            'activation_2FA' => !$user->activation_2FA
        ]);
        return [
            'status' => 'success',
            'data' => [
                'message' => 'switch 2FA successfully',
                'activation_2FA' => $user->activation_2FA,
            ],
            'code' => 201
        ];
    }
}
