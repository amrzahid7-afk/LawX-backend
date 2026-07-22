<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLegislationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                     => 'required|string|max:255',
            'content'                   => 'nullable|string',
            'law_number'                => 'required|string|unique:legislations,law_number',
            'category_id'               => 'required|uuid|exists:legal_categories,id',
            'effective_date'            => 'nullable|date:after_or_equal:today',
            'country'                   => 'required|string|max:100',
            'book'                      => 'nullable|string|max:255',
            'part'                      => 'nullable|string|max:255',
            'chapter'                   => 'nullable|string|max:255',
            'related_legislation_ids'   => 'nullable|array',
            'related_legislation_ids.*' => 'uuid|exists:legislations,id',
            'articles'                  => 'nullable|array',
            'articles.*.title'          => 'required_with:articles|string|max:255',
            'articles.*.content'        => 'required_with:articles|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'                => 'عنوان التشريع مطلوب',
            'title.max'                     => 'عنوان التشريع يجب ألا يتجاوز 255 حرف',
            'law_number.required'           => 'رقم القانون مطلوب',
            'law_number.unique'             => 'رقم القانون موجود مسبقاً في النظام',
            'category_id.exists'            => 'التصنيف المختار غير موجود',
            'status.in'                     => 'حالة القانون يجب أن تكون: active, amended, أو repealed',
            'effective_date.date'           => 'تاريخ السريان يجب أن يكون تاريخاً صحيحاً',
            'effective_date.after_or_equal' => 'تاريخ السريان يجب ان يكون بعد تاريخ اليوم',
            'country.required'              => 'حقل الدولة مطلوب',
            'country.max'                   => 'اسم الدولة يجب ألا يتجاوز 100 حرف',
            'related_legislation_ids.array' => 'التشريعات المرتبطة يجب أن تكون مصفوفة',
            'articles.array'                => 'مواد القانون يجب أن تكون مصفوفة متكاملة',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
