<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        // سنترك التحقق من الصلاحيات للـ Policy لاحقاً
        return true;
    }

    public function rules(): array
    {
        return [
            'case_id' => 'required|exists:cases,id',
            'professionalism_rating' => 'required|integer|min:1|max:5',
            'communication_rating'   => 'required|integer|min:1|max:5',
            'result_rating'          => 'required|integer|min:1|max:5',
            'comment'                => 'nullable|string|max:1000',
        ];
    }


    public function messages(): array
    {
        return [
            'case_id.required' => 'معرف القضية مطلوب.',
            'case_id.exists' => 'القضية المحددة غير موجودة.',
            'professionalism_rating.required' => 'تقييم الاحترافية مطلوب.',
            'professionalism_rating.min' => 'أقل تقييم هو 1.',
            'professionalism_rating.max' => 'أعلى تقييم هو 5.',
            'communication_rating.required' => 'تقييم الاتصال مطلوب.',
            'communication_rating.min' => 'أقل تقييم هو 1.',
            'communication_rating.max' => 'أعلى تقييم هو 5.',
            'result_rating.required' => 'تقييم النتيجة مطلوب.',
            'result_rating.min' => 'أقل تقييم هو 1.',
            'result_rating.max' => 'أعلى تقييم هو 5.',
            'comment.string' => 'التعليق يجب ان يكون نص.',
            'comment.max' => 'التعليق يجب ألا يتجاوز 1000 حرف.',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
