<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
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
            'case_id' => 'required|uuid|exists:cases,id',
            // 'lawyer_id' => 'required|uuid|exists:users,id',
            // 'client_id' => 'required|uuid|exists:users,id',
            'total_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'type' => 'in:For_case,salary',
            'items' => 'required|array',
            'items.*.title' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'documents' => 'nullable|array|max:5', // السماح برفع حتى 5 ملفات، ويمكن أن تكون فارغة
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,png,jpeg|max:10240', // كل ملف يجب أن يكون من هذه الصيغ وحجمه أقل من 10 ميجا
        ];
    }

    public function messages(): array
    {
        return [
            'case_id.required' => 'The case ID is required.',
            'case_id.uuid' => 'The case ID must be a valid UUID.',
            'case_id.exists' => 'The specified case does not exist.',
            'lawyer_id.required' => 'The lawyer ID is required.',
            'lawyer_id.uuid' => 'The lawyer ID must be a valid UUID.',
            'lawyer_id.exists' => 'The specified lawyer does not exist.',
            'client_id.required' => 'The client ID is required.',
            'client_id.uuid' => 'The client ID must be a valid UUID.',
            'client_id.exists' => 'The specified client does not exist.',
            'total_amount.required' => 'The total amount is required.',
            'total_amount.numeric' => 'The total amount must be a number.',
            'total_amount.min' => 'The total amount must be at least 0.',
            'status.required' => 'The status is required.',
            'status.string' => 'The status must be a string.',
            'status.in' => 'The status must be one of the following: pending, paid, overdue.',
            'due_date.required' => 'The due date is required.',
            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after_or_equal' => 'The due date must be today or a future date.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.*.title.required' => 'Each item must have a title.',
            'items.*.description.required' => 'Each item must have a description.',
            'items.*.amount.required' => 'Each item must have an amount.',
            'items.*.amount.numeric' => 'Each item amount must be a number.',
            'items.*.amount.min' => 'Each item amount must be at least 0.',
            'documents.max' => 'Cannot upload more than 5 files.',
            'documents.*.mimes' => 'The document must be a file of type: pdf, doc, docx, jpg, png, jpeg.',
            'documents.*.max' => 'The document may not be greater than 10MB.',
        ];
    }
    /**
     * التحقق الإضافي بعد نجاح القواعد الأساسية
     */
    public function after(): array
    {
        return [
            function ($validator) {
                // منع تشغيل الاستعلامات إذا كانت معرفات الـ IDs الأساسية غير صحيحة اصلاً
                if ($validator->errors()->has('client_id') || $validator->errors()->has('lawyer_id')) {
                    return;
                }

                // 1. جلب العميل مع علاقة الدور المفردة (تأكد من كتابة اسم العلاقة 'role' بالمفرد)
                $client = User::with('role')->find($this->client_id);

                // فحص مباشر للنص (String) لأن $client->role هو كائن موديل مفرد وليس مصفوفة
                if ($client && (!$client->role || $client->role->name !== 'client')) {
                    $validator->errors()->add('client_id', 'المستخدم المحدد يجب أن يكون عميلاً (Client).');
                }

                // 2. جلب المحامي مع علاقة الدور المفردة
                $lawyer = User::with('role')->find($this->lawyer_id);

                if ($lawyer) {
                    // نتحقق مما إذا كان اسم الدور موجوداً وضمن القائمة المسموحة ['lawyer', 'admin']
                    $isValidRole = $lawyer->role && in_array($lawyer->role->name, ['lawer', 'admin']);

                    if (!$isValidRole) {
                        $validator->errors()->add('lawyer_id', 'المستخدم المحدد يجب أن يكون محامياً أو مديراً بالنظام.');
                    }
                }
            }
        ];
    }
}
