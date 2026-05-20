<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProposalRequest extends FormRequest
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
            'price' => 'required|numeric',
            'estimated_days' => 'required|string',
            'message_for_client' => 'required|string',
            'valid_until' => 'required|date',
            'payment_terms' => 'required|string',
            //  'status' => 'required|in:pending,accepted,rejected',
            'price_currency' => 'required|in:EGP,USD,SYP',
            'request_id' => 'required|exists:legal_requests,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'price.required' => "price is required",
            'price.decimal' => "price must be a number",
            'estimated_days.required' => "estimated_days is required",
            'estimated_days.string' => "estimated_days must be a string",
            'message_for_client.required' => "message_for_client is required",
            'message_for_client.string' => "message_for_client must be a string",
            'valid_until.required' => "valid_until is required",
            'valid_until.date' => "valid_until must be a date",
            'payment_terms.required' => "payment_terms is required",
            'payment_terms.string' => "payment_terms must be a string",
            'status.required' => "status is required",
            'status.in' => "status must be pending,accepted,rejected",
            'price_currency.required' => "price_currency is required",
            'price_currency.in' => "price_currency must be EGP,USD,SYP",
            'request_id.required' => "request_id is required",
            'request_id.exists' => "request_id not found",
            'file.mimes' => 'File must be a pdf, doc, docx, jpg, jpeg, png',
            'file.file' => 'File must be a file',
            'file.max' => 'File must be less than 2MB',
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
