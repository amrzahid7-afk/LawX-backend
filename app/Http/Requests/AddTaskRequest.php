<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\User;

class AddTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //auth('api')->user()->role->name == 'admin';
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
            'title' => 'required|string|max:25',
            'description' => 'required|string|max:255',
            'due_date' => 'nullable|date|after_or_equal:today',
            // 'assigned_to' => 'required|exists:users,id',
            'assigned_to' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || !in_array($user->role->name, ['lawer', 'admin', 'office_manager', 'independent_lawyer'])) {
                        $fail('The assigned user must be a lawer');
                    }
                }
            ],
        ];
    }
    public function messages()
    {
        return [
            'case_id.required' => "case_id is required",
            'case_id.exists' => "case_id not found",
            'title.required' => "title is required",
            'title.string' => "title must be a string",
            'title.max' => "title must be less than 25 characters",
            'description.required' => "description is required",
            'description.string' => "description must be a text",
            'description.max' => "description must be less than 255 characters",
            'due_date.nullable' => "due_date must be a valid date",
            'due_date.date' => "due_date must be a valid date",
            'due_date.after_or_equal' => "due_date must be a date equal to or after today",
            'assigned_to.required' => "assigned_to is required",
            'assigned_to.exists' => "assigned_to not found",
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
