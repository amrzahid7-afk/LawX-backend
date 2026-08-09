<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GenerateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'case_id' => 'nullable|uuid|exists:cases,id',
            'template_id' => 'required|uuid|exists:smart_templates,id',
            'custom_values' => 'required|array',
            'custom_values.*' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'case_id.exists' => 'القضية غير موجودة',
            'custom_values.required' => 'يجب إرسال قيم المتغيرات',
            'custom_values.array' => 'قيم المتغيرات يجب أن تكون مصفوفة',
            'custom_values.*.string' => 'قيم المتغيرات يجب أن تكون نصية',
            'custom_values.*.max' => 'قيم المتغيرات يجب أن تكون اقل من 1000 حرف',
            'template_id.exists' => 'القالب غير موجود',
            'template_id.required' => 'القالب مطلوب',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
