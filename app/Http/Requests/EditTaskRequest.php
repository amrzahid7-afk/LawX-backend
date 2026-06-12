<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Contracts\Validation\Validator;

class EditTaskRequest extends FormRequest
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
            'task_id' => 'required|exists:tasks,id',
            'title' => 'nullable|string|max:25',
            'status' => 'nullable|in:pending,in_progress,completed',
            'description' => 'nullable|string|max:255',
            'due_date' => 'nullable|date|after_or_equal:today',
            'assigned_to' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $user = \App\Models\User::find($value);
                        if (!$user || !in_array($user->role->name, ['lawer', 'admin', 'office_manager', 'independent_lawyer'])) {
                            $fail('The assigned user must be a lawer');
                        }
                    }
                }
            ],
        ];
    }
    public function messages()
    {
        return [
            'task_id.required' => "task_id is required",
            'task_id.exists' => "task_id not found",
            'title.string' => "title must be a string",
            'title.max' => "title must be less than 25 characters",
            'description.string' => "description must be a text",
            'description.max' => "description must be less than 255 characters",
            'due_date.nullable' => "due_date must be a valid date",
            'due_date.date' => "due_date must be a valid date",
            'due_date.after_or_equal' => "due_date must be a date equal to or after today",
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
