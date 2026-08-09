<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadDocumentRequest extends FormRequest
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
            'case_id' => 'required|exists:cases,id',
            'type' => 'required|string|max:10|min:3',
            'file' => 'required|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'parent_id' => 'nullable|uuid|exists:documents,id',
        ];
    }
    public function messages()
    {
        return [
            'case_id.required' => 'Case ID is required',
            'case_id.exists' => 'Case ID does not exist',
            'type.required' => 'Type is required',
            'type.string' => 'Type must be a string',
            'type.max' => 'Type must be less than 10 characters',
            'type.min' => 'Type must be at least 3 characters',
            'file.required' => 'File is required',
            'file.mimes' => 'File must be a pdf, doc, docx, jpg, jpeg, png',
            'file.max' => 'File must be less than 2MB',
            'nots.string' => 'Notes must be a string',
            'nots.max' => 'Notes must be less than 255 characters',
            'has_summary.required' => 'Has Summary is required',
            'has_summary.boolean' => 'Has Summary must be a boolean value',
            'has_versions.required' => 'Has Versions is required',
            'has_versions.boolean' => 'Has Versions must be a boolean value',
            'parent_id.uuid' => 'Parent ID must be a valid UUID',
            'parent_id.exists' => 'Parent ID does not exist',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
