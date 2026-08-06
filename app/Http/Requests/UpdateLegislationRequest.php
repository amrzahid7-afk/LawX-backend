<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateLegislationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'law_id'                    => 'required|uuid|exists:legislations,id',
            'title'                     => 'sometimes|required|string|max:255',
            'content'                   => 'nullable|string',
            'law_number'                => [
                'sometimes',
                'required',
                'string',
                Rule::unique('legislations', 'law_number')->ignore($this->law_id),
            ],
            'category_id'               => 'nullable|uuid|exists:legal_categories,id',
            'status'                    => 'nullable|in:active,amended,repealed',
            'effective_date'            => 'nullable|date',
            'country'                   => 'sometimes|required|string|max:100',
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
            'law_id.required'               => 'معرف القانون مطلوب لإتمام العملية',
            'title.required'                => 'عنوان التشريع لا يمكن أن يكون فارغاً',
            'law_number.unique'             => 'رقم القانون موجود مسبقاً في النظام',
            'category_id.exists'            => 'التصنيف المختار غير موجود',
            'status.in'                     => 'حالة القانون يجب أن تكون: active, amended, أو repealed',
            'country.required'              => 'حقل الدولة مطلوب',
            'related_legislation_ids.array' => 'التشريعات المرتبطة يجب أن تكون مصفوفة',
            'articles.array'                => 'مواد القانون يجب أن تكون مصفوفة متكاملة',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
