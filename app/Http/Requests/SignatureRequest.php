<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignatureRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,docx',
            'title' => 'required|string',
            'signers' => 'required|array|min:1',
            'signers.*.user_id' => 'required|exists:users,id',
            'signers.*.role' => 'required|in:client,syndicate,lawyer',
            'signers.*.is_seal' => 'boolean',
            // 'signers.*.anchor_text' => 'required|string',
            'signers.*.page_number' => 'integer',
            'signers.*.x_position' => 'required|integer',
            'signers.*.y_position' => 'required|integer',
            'signers.*.width' => 'required|integer',
            'signers.*.height' => 'required|integer',
            'signers.*.sign_type' => 'required|in:seal,signature',

        ];
    }
    public function messages(): array
    {
        return [
            'file.required' => 'File is required',
            'file.file' => 'File must be a file',
            'file.mimes' => 'File must be a pdf, docx',
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'signers.required' => 'Signers are required',
            'signers.array' => 'Signers must be an array',
            'signers.min' => 'At least one signer is required',
            'signers.*.user_id.required' => 'User ID is required',
            'signers.*.user_id.exists' => 'User ID does not exist',
            'signers.*.role.required' => 'Role is required',
            'signers.*.role.in' => 'Role must be one of the following: client, syndicate, lawyer',
            'signers.*.is_seal.boolean' => 'Is seal must be a boolean',
            'signers.*.anchor_text.required' => 'Anchor text is required',
            'signers.*.anchor_text.string' => 'Anchor text must be a string',
            'signers.*.page_number.integer' => 'Page number must be an integer',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
