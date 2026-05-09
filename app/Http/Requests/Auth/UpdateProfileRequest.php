<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateProfileRequest extends FormRequest
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
            "full_name" => 'nullable|string|max:50',
            "email" => 'nullable|email|max:50',
            "is_active" => 'nullable|boolean',
            "activation_2FA" => 'nullable|boolean',
        ];
    }
    public function messages()
    {
        return [
            'fullname.string' => 'The name must be a string',
            'fullname.max' => 'The name must be less than 50 characters',
            'email.email' => 'The email must be a valid email address',
            'email.max' => 'The email must be less than 50 characters',
            'is_active' => 'The is_active must be a boolean value',
            'activation_2FA' => 'The activation_2FA must be a boolean value',
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(
            [
                'status' => false,
                'errors' => $validator->errors()
            ],
            400
        ));
    }
}
