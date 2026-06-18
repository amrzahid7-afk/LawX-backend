<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLegislationRequest extends FormRequest
{
    /**
     * تحديد ما إذا كان المستخدم مخولاً لتقديم هذا الطلب
     */
    public function authorize(): bool
    {
        return true; // لاحقاً سنغيرها للتحقق من صلاحيات الأدمن
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'content'        => 'nullable|string',
            'law_number'     => 'required|string|unique:legislations,law_number',
            'category_id'    => 'nullable|uuid|exists:legal_categories,id',
            'status'         => 'nullable|in:active,amended,repealed',
            'effective_date' => 'nullable|date',
            'country'        => 'required|string|max:100',
            'level'          => 'required|in:chapter,section,article,paragraph',
            'parent_id'      => 'nullable|uuid|exists:legislations,id',
        ];
    }

    /**
     * رسائل التحقق المخصصة
     */
    public function messages(): array
    {
        return [
            'title.required'        => 'عنوان التشريع مطلوب',
            'title.max'             => 'عنوان التشريع يجب ألا يتجاوز 255 حرف',
            'law_number.required'   => 'رقم القانون مطلوب',
            'law_number.unique'     => 'رقم القانون موجود مسبقاً في النظام',
            'category_id.exists'    => 'التصنيف المختار غير موجود',
            'status.in'             => 'حالة القانون يجب أن تكون: active, amended, أو repealed',
            'effective_date.date'   => 'تاريخ السريان يجب أن يكون تاريخاً صحيحاً',
            'country.required'      => 'حقل الدولة مطلوب',
            'country.max'           => 'اسم الدولة يجب ألا يتجاوز 100 حرف',
            'level.required'        => 'مستوى التشريع مطلوب',
            'level.in'              => 'مستوى التشريع يجب أن يكون أحد: chapter, section, article, paragraph',
            'parent_id.exists'      => 'التشريع الأب المختار غير موجود',
        ];
    }

    /**
     * التعامل مع فشل التحقق وإرجاع استجابة JSON مخصصة
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'بيانات المدخلات غير صحيحة',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
