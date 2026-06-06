<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'date' => 'nullable|date:after_or_equal:today',
            'location' => 'nullable|string|max:255',
            'result' => 'nullable|string|max:255',
            'file' => 'nullable|file',
        ];
    }
}
