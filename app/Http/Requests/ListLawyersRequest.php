<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ListLawyersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lawyer_state' => ['nullable', Rule::in(['trainee', 'independent'])],
            'is_suspended' => ['nullable', 'boolean'],
            'search_fullname' => ['nullable', 'string', 'max:100'],
            'search_syndicate_card_number' => ['nullable', 'string', 'max:100'],
            'search_national_id' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
    public function messages()
    {
        return [
            'lawyer_state.in' => 'The lawyer state must be either trainee or independent.',
            'is_suspended.boolean' => 'The is_suspended field must be true or false.',
            'is_office_owner.boolean' => 'The is_office_owner field must be true or false.',
            'search.string' => 'The search field must be a string.',
            'search.max' => 'The search field may not be greater than 100 characters.',
            'per_page.integer' => 'The per_page field must be an integer.',
            'per_page.min' => 'The per_page field must be at least 1.',
            'per_page.max' => 'The per_page field may not be greater than 100.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
