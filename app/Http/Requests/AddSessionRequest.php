<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddSessionRequest extends FormRequest
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
            'date' => 'required|date_format:Y-m-d H:i|after_or_equal:today',
            'location' => 'required',
            'nots' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'case_id.required' => "case_id is required",
            'case_id.exists' => "case_id not found",
            'date.required' => "date is required",
            'location.required' => "location is required",
            'nots.required' => "nots is required",
            'date.date_format' => "date must be in format Y-m-d H:i",
            'date.after_or_equal' => "date must be after or equal to today",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
