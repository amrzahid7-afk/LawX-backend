<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateLegalRequestRequest extends FormRequest
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
            'request_id' => 'required|uuid|exists:legal_requests,id',
            'title_request' => 'nullable|string|max:255',
            'description_request' => 'nullable|string|max:512',
            'location_request' => 'nullable|string|max:255',
            'budget_min' => 'numeric|min:0',
            'budget_max' => 'numeric|min:0',
            'type' => 'nullable|string|max:255|in:private,public',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'assigned_lawyer_id' => Rule::when($this->input('type') === 'private', 'nullable|exists:users,id', 'nullable'),
        ];
    }
    public function messages()
    {
        return [
            'request_id.required' => "request_id is required",
            'request_id.uuid' => "request_id must be a valid UUID",
            'request_id.exists' => "request_id not found",
            'title_request.string' => "title_request must be a string",
            'title_request.max' => "title_request must be less than 255 characters",
            'description_request.string' => "description_request must be a text",
            'description_request.max' => "description_request must be less than 512 characters",
            'location_request.string' => "location_request must be a string",
            'location_request.max' => "location_request must be less than 255 characters",
            'budget_min.numeric' => "budget_min must be a number",
            'budget_min.min' => "budget_min must be greater than or equal to 0",
            'budget_max.numeric' => "budget_max must be a number",
            'budget_max.min' => "budget_max must be greater than or equal to 0",
            'type.string' => "type must be a string",
            'type.max' => "type must be less than 255 characters",
            'assigned_lawyer_id.exists' => "assigned_lawyer_id not found",
            'file.mimes' => 'File must be a pdf, doc, docx, jpg, jpeg, png',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
