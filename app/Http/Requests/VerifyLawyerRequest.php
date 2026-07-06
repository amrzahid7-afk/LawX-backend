<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\Validator;

class VerifyLawyerRequest extends FormRequest
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
            'national_id' => ['required', 'string', 'exists:lawyer_profiles,national_id'],
            'full_name' => ['required', 'string'],
            'father_name' => ['required', 'string'],
            'mother_name' => ['required', 'string'],
            'mother_family_name' => ['required', 'string'],
            'birth_place' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
            'civil_registry_number' => ['required', 'string'],
            'syndicate_card_number' => ['required', 'string', 'exists:lawyer_profiles,syndicate_card_number'],
            'syndicate_branch' => ['required', 'string'],
            'lawyer_state' => ['required', Rule::in(['trainee', 'independent'])],
        ];
    }
    public function messages()
    {
        return [
            'national_id.required' => 'National ID is required.',
            'national_id.string' => 'National ID must be a string.',
            'national_id.unique' => 'National ID must be unique.',
            'full_name.required' => 'Full name is required.',
            'full_name.string' => 'Full name must be a string.',
            'father_name.required' => 'Father name is required.',
            'father_name.string' => 'Father name must be a string.',
            'mother_name.required' => 'Mother name is required.',
            'mother_name.string' => 'Mother name must be a string.',
            'mother_family_name.required' => 'Mother family name is required.',
            'mother_family_name.string' => 'Mother family name must be a string.',
            'birth_place.required' => 'Birth place is required.',
            'birth_place.string' => 'Birth place must be a string.',
            'birth_date.required' => 'Birth date is required.',
            'birth_date.date' => 'Birth date must be a valid date.',
            'civil_registry_number.required' => 'Civil registry number is required.',
            'civil_registry_number.string' => 'Civil registry number must be a string.',
            'syndicate_card_number.required' => 'Syndicate card number is required.',
            'syndicate_card_number.string' => 'Syndicate card number must be a string.',
            'syndicate_card_number.unique' => 'Syndicate card number must be unique.',
            'syndicate_branch.required' => 'Syndicate branch is required.',
            'syndicate_branch.string' => 'Syndicate branch must be a string.',
            'lawyer_state.required' => "Lawyer state is required.",
            "lawyer_state.in" => "Lawyer state must be either trainee or independent.",
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
