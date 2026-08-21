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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Cloudinary;

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
            return [
                'message' => 'لا يوجد سجلات مطابقة لبيانات النقابة المدخلة.',
                'status' => 'error',
                'code' => 404
            ];
            // return sendError('البيانات المدخلة غير مطابقة لسجلات النقابة.', [], 404);
        }
        // if there are exist account 
        if ($lawer_profile->is_claimed) {
            return [
                'message' => 'هذا الملف الشخصي مسجل بالفعل في النظام مسبقاً.',
                'status' => 'error',
                'code' => 400
            ];
            return sendError('هذا الملف الشخصي مسجل بالفعل في النظام مسبقاً.', [], 400);
        }
        return [
            'status' => 'success',
            'data' => $lawer_profile->id,
            'message' => 'تم التحقق من بيانات النقابة بنجاح، يمكنك إكمال عملية التسجيل.',
            'code' => 200
        ];
        return sendResponse([
            'lawyer_profile_id' => $lawer_profile->id,
            'message' => 'تم التحقق من بيانات النقابة بنجاح، يمكنك إكمال عملية التسجيل.'
        ], 'Verification successful.');
    }
    public function register(Request $request)
    {
        DB::beginTransaction();
        $profile = LawerProfile::findOrFail($request['lawyer_profile_id']);
        if ($profile->is_claimed) {
            return sendError('هذا الحساب تم تفعيله مسبقاً.', [], 400);
        }
        // ===== OLD (comment this) =====
        // $avatarPath = null;
        // if ($request->hasFile('avatar')) {
        //     $path = $request->file('avatar')->store('avatars', 'public');
        //     $avatarPath = Storage::disk('public')->url($path);
        // }
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $result = app(Cloudinary::class)
                ->uploadApi()
                ->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'lawx/avatars',
                ]);
            $avatarPath = $result['secure_url'];
        }
        // dd($avatarPath);
        $user = User::create([
            'full_name'       => $profile->full_name,
            'email'           => $request['email'],
            'password'        => Hash::make($request['password']),
            'role_id'         => Role::where('slug', RoleSlug::Lawyer->value)->firstOrFail()->id,
            'phone_number'    => $request['phone_number'],
            'address'         => $request['address'] ?? null,
            'lat'             => $request['lat'] ?? null,
            'lng'             => $request['lng'] ?? null,
            'bio'             => $request['bio'] ?? null,
            'national_id'     => $request['national_id'],
            'avatar_path'          => $avatarPath,
            'city'            => $request['city'],
            'experience_years' => $request['experience_years'] ?? 0,
        ]);
        $profile->update([
            'user_id' => $user->id,
            'is_claimed' => true,
            'claimed_at' => now()->format('Y-m-d'),
        ]);
        $profile->save();
        $token = $user->createToken('LawyerToken')->plainTextToken;
        DB::commit();
        return sendResponse(['token' => $token,], 'Registration successful.', 201);
    }
    public function registerClient(Request $request)
    {
        // ===== OLD (comment this) =====
        // $avatarPath = null;
        // if ($request->hasFile('avatar')) {
        //     $path = $request->file('avatar')->store('avatars', 'public');
        //     $avatarPath = Storage::disk('public')->url($path);
        // }
        // dd($avatarPath);
        // $avatarPath = null;
        // if ($request->hasFile('avatar')) {
        //     $avatarPath = Cloudinary::upload(
        //         $request->file('avatar')->getRealPath(),
        //         ['folder' => 'lawx/avatars']
        //     )->getSecurePath();
        // }
        // dd($avatarPath);
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $result = app(Cloudinary::class)
                ->uploadApi()
                ->upload($request->file('avatar')->getRealPath(), [
                    'folder' => 'lawx/avatars',
                ]);
            $avatarPath = $result['secure_url'];
        }
        // dd($avatarPath);
        $user = User::create([
            'full_name' => $request->input('full_name'),
            'email'     => $request->input('email'),
            'password'  => Hash::make($request->input('password')),
            'role_id'   => Role::where('slug', RoleSlug::Client->value)->firstOrFail()->id,
            'address'   => $request->input('address'),
            'lat'       => $request->input('lat'),
            'lng'       => $request->input('lng'),
            'bio'       => $request->input('bio'),
            'avatar_path'    => $avatarPath,
            'phone'     => $request->input('phone_number'),
            'city'      => $request->input('city'),
            'national_id' => $request->input('national_id')

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
            'role' => $user->role->slug,
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
}
