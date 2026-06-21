<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:docx,doc,pdf|max:10240', // 10MB
            'scope' => 'required|in:public,private',
            'category' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان القالب مطلوب',
            'title.max' => 'عنوان القالب يجب ألا يتجاوز 255 حرف',
            'file.required' => 'ملف القالب مطلوب',
            'file.file' => 'يجب رفع ملف صالح',
            'file.mimes' => 'صيغة الملف يجب أن تكون docx, doc, أو pdf',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
            'scope.required' => 'نطاق القالب مطلوب',
            'scope.in' => 'النطاق يجب أن يكون public أو private',
            'category.required' => 'تصنيف القالب مطلوب',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'بيانات المدخلات غير صحيحة',
            'errors' => $validator->errors(),
        ], 422));
    }
}
