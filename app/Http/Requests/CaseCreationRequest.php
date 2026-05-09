<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CaseCreationRequest extends FormRequest
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
        $clientRoleId = Role::where('name', 'client')->value('id');
        return [
            'title'       => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'client_id'   => [
                'required',
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) use ($clientRoleId) {
                    $query->where('users.role_id', $clientRoleId);
                })
            ],
        ];
    }
    public function messages()
    {
        return [
            'title.required' => "title is required",
            'title.string' => "title must be a string",
            'title.min' => "title must be at least 5 characters",
            'title.max' => "title must be less than 255 characters",

            'description.required' => "description is required",
            'description.string' => "description must be a string",
            'description.min' => "description must be at least 10 characters",

            'client_id.required' => "client_id is required",
            'client_id.uuid' => "client_id must be a uuid",
            'client_id.exists' => "client_id not found",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422));
    }
}
