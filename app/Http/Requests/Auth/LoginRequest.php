<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'email' => 'required|string|email|min:11|max:100|exists:users,email',
            'password' => 'required|string|min:8|max:20'
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'email is required',
            'email.exists' => 'email not found',
            'email.email' => 'invalid email',
            'email.min' => 'email must be at least 11 characters',
            'email.max' => 'email must be less than 100 characters',
            'email.string' => 'email must be a string',
            'password.requied' => 'password is required',
            'password.min' => 'password must be at least 8 characters',
            'password.max' => 'password must be less than 20 characters',
            'password.string' => 'password must be a string',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 400));
    }
}
