<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProposalRequest extends FormRequest
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
            'proposal_id' => 'required|uuid|exists:proposals,id',
            'price' => 'nullable|numeric',
            'estimated_days' => 'nullable|string',
            'message_for_client' => 'nullable|string',
            'valid_until' => 'nullable|date',
            'payment_terms' => 'nullable|string',
            'price_currency' => 'nullable|in:EGP,USD,SYP',
            'request_id' => 'nullable|exists:legal_requests,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'proposal_id.required' => "proposal_id is required",
            'proposal_id.uuid' => "proposal_id must be a valid UUID",
            'proposal_id.exists' => "proposal_id not found",
            'price.decimal' => "price must be a number",
            'estimated_days.string' => "estimated_days must be a string",
            'message_for_client.string' => "message_for_client must be a string",
            'valid_until.date' => "valid_until must be a date",
            'payment_terms.string' => "payment_terms must be a string",
            'price_currency.in' => "price_currency must be EGP,USD,SYP",
            'request_id.exists' => "request_id not found",
            'file.mimes' => 'File must be a pdf, doc, docx, jpg, jpeg, png',
            'file.file' => 'File must be a file',
        ];
    }
}
