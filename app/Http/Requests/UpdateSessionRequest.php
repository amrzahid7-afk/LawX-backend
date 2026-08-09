<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSessionRequest extends FormRequest
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
            'session_id' => 'required|exists:sessions,id',
            'case_id' => 'required|exists:cases,id',
            'date' => 'nullable|date_format:Y-m-d H:i|after_or_equal:today',
            'location' => 'nullable|string|max:255',
            'result' => 'nullable|string|max:255',
            'file' => 'nullable|file',
            'status' => 'nullable|in:pending,completed,cancelled',
            'nots' => 'nullable|string'
        ];
    }
    public function messages()
    {
        return [
            'session_id.required' => "session_id is required",
            'session_id.exists' => "session_id not found",
            'case_id.required' => "case_id is required",
            'case_id.exists' => "case_id not found",
            'date.date_format' => "date must be in format Y-m-d H:i",
            'date.after_or_equal' => "date must be after or equal to today",
            'location.string' => "location must be a string",
            'location.max' => "location must not exceed 255 characters",
            'result.string' => "result must be a string",
            'result.max' => "result must not exceed 255 characters",
            'file.file' => "file must be a valid file",
            'status.in' => "status must be one of pending, completed, cancelled",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
