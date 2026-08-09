<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {

        return true;
    }

    public function rules(): array
    {

        return [
            'template_id' => 'required|uuid|exists:smart_templates,id',
            'title' => 'sometimes|required|string|max:255',
            'file' => 'nullable|file|mimes:docx,doc,pdf|max:10240',
            // 'scope' => 'sometimes|required|in:public,private'
            'template_description' => 'nullable|string',
            'category' => 'sometimes|required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان القالب مطلوب',
            'file.mimes' => 'صيغة الملف يجب أن تكون docx, doc, أو pdf',
            'scope.in' => 'النطاق يجب أن يكون public أو private',
            'template_id.required' => 'معرف القالب مطلوب',
            'template_id.exists' => 'معرف القالب غير صحيح',
            'template_id.uuid' => 'معرف القالب غير صحيح',
            'template_description.string' => 'وصف القالب يجب ان يكون نص',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
