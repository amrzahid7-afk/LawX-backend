<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Verify_2FA_Request extends FormRequest
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
            "temporary_token" => 'required',
            "user_id" => 'required|exists:users,id',
            "code" => 'required|digits:7'
        ];
    }
    public function messages()
    {
        return [
            'temporary_token.required' => 'temporary token is required',
            'user_id.required' => 'user_id is required',
            'user_id.exists' => 'user_id not found',
            'code.requied' => 'code is required',
            'code.digits' => 'code must be 7 digits',
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
