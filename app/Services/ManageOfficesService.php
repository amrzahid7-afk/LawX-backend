<?php

namespace App\Services;

use App\Mail\ResetPasswordMail;
use App\Mail\Send2FaMail;
use App\Models\Office;
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

class ManageOfficesService
{
    public function getOffices()
    {
        $offices = Office::select(
            'id',
            'name',
            'logo_url',
            'phone',
            'address',
            'country',
            'currency'
        )->get();
        return [
            'status' => true,
            'data' => [
                'message' => 'getting offices success',
                'offices' => $offices
            ],
            'code' => 200
        ];
    }
    public function getOffice($id)
    {
        $office = Office::select(
            'id',
            'name',
            'logo_url',
            'phone',
            'address',
            'country',
            'currency'
        )->where('id', $id)->first();
        $user = User::where('id', $office->user_id)->first();
        return [
            'status' => true,
            'data' => [
                'message' => 'getting office success',
                'office' => $office,
                'user' => $user
            ],
            'code' => 200
        ];
    }
}
