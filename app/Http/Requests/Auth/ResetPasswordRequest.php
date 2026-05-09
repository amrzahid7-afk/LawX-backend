<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => 'required|email|exists:users,email',
            'code'    => 'required|numeric|exists:verification_codes,code',
            'token'    => 'required|string',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'code is required',
            'code.exists' => 'code not found',
            'code.numeric' => 'code must be numeric',
            'email.required' => 'email is required',
            'email.required' => 'email is required',
            'email.email' => 'invalid email',
            'email.exists' => 'email not found',
            'token.required' => 'token is required',
            'token.string' => 'invalid token',
            'password.required' => 'password is required',
            'password.min' => 'password must be at least 8 characters',
            'password.confirmed' => 'password does not match',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 400));
    }
}
