<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class LegalRequestRequest extends FormRequest
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
            'title_request' => 'required|string|max:255',
            'description_request' => 'required|string|max:512',
            'location_request' => 'required|string|max:255',
            'budget_min' => 'numeric|min:0',
            'budget_max' => 'numeric|min:0',
            'type' => 'required|string|max:255|in:private,public',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            // 'assigned_lawyer_id' => Rule::when($this->input('type') === 'private', 'required|exists:users,id', 'nullable'),
            'assigned_lawyer_id' => [
                'required_if:type,private',
                'prohibited_if:type,public',
                'uuid',
                'exists:users,id'
            ],
        ];
    }
    public function messages()
    {
        return [
            'title_request.required' => "title_request is required",
            'title_request.string' => "title_request must be a string",
            'title_request.max' => "title_request must be less than 255 characters",
            'description_request.required' => "description_request is required",
            'description_request.string' => "description_request must be a text",
            'description_request.max' => "description_request must be less than 512 characters",
            'location_request.required' => "location_request is required",
            'location_request.string' => "location_request must be a string",
            'location_request.max' => "location_request must be less than 255 characters",
            'budget_min.numeric' => "budget_min must be a number",
            'budget_min.min' => "budget_min must be greater than or equal to 0",
            'budget_max.numeric' => "budget_max must be a number",
            'budget_max.min' => "budget_max must be greater than or equal to 0",
            'type.required' => "type is required",
            'type.string' => "type must be a string",
            'type.max' => "type must be less than 255 characters",
            'assigned_lawyer_id.required' => "assigned_lawyer_id is required",
            'assigned_lawyer_id.exists' => "assigned_lawyer_id not found",
            'file.mimes' => 'File must be a pdf, doc, docx, jpg, jpeg, png',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
