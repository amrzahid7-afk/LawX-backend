<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class changePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth('api')->user()->password)) {
                        $fail('The current password is Wrong ');
                    }
                },
                'new_password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    Password::min(8)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                ]
            ]
        ];
    }
    public function messages()
    {
        return [
            'current_password.required' => 'current password is required',
            'current_password.string' => 'current password must be string',
            'new_password.required' => 'new password is required',
            'new_password.string' => 'new password must be string',
            'new_password.min' => 'new password must be at least 8 characters',
            'new_password.mixedCase' => 'new password must contain at least one uppercase and one lowercase letter',
            'new_password.numbers' => 'new password must contain at least one number',
            'new_password.symbols' => 'new password must contain at least one symbol',
            'new_password.confirmed' => 'new password does not match',
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
