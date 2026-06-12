<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CaseCreationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // تحسين: استخدام value('id') لتجنب الخطأ في حال عدم وجود الرول (Avoid error if role not found)
        $clientRoleId = Role::where('name', 'client')->value('id');

        // تحسين: التحقق من أن المستخدم له رول قبل الوصول لاسمه
        $userRole = $this->user()->role?->name ?? '';
        $isOfficeManager = $userRole === 'admin' ? true : false;

        return [
            'title'             => ['required', 'string', 'min:5', 'max:255'],
            'description'       => ['required', 'string', 'min:10'],
            'court'             => ['required', 'string', 'min:3'],

            'price'             => ['required', 'numeric', 'min:0'],

            'case_category'     => ['required', 'string', 'min:4'], // افترضنا أنه UUID ويوجد في جدول التصنيفات
            'importance_stars'  => ['required', 'integer', 'min:1', 'max:5'],
            'billing_type'      => ['required', 'in:fixed_proposal,percentage_collection,installments,pay_as_you_go'],
            'lead_lawyer_id'    => ['required', "uuid", Rule::exists('users', 'id')],

            'case_number' => [
                'required',
                'string',
                'min:3',
                'unique:cases,case_number'
            ],

            'collection_percentage' => [
                'required_if:billing_type,percentage_collection', // مطلوب فقط إذا كان النوع نسبة تحصيل
                'prohibited_unless:billing_type,percentage_collection', // ممنوع إرساله ما لم يكن النوع نسبة تحصيل (هذا هو الحل)
                'nullable', // اختياري لتفادي مشاكل null، لكن prohibited سيتولى الأمر
                'numeric',
                'min:0',
                'max:100'
            ],

            // --- العملاء (Array) ---
            'clients'           => ['required', 'array', 'min:1'],
            'clients.*.user_id' => [
                'required',
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) use ($clientRoleId) {
                    if ($clientRoleId) {
                        $query->where('role_id', $clientRoleId);
                    }
                })
            ],
            'clients.*.is_primary' => ['required', 'boolean'],

            // --- الخصوم (Array) ---
            'opponents'              => ['required', 'array', 'min:1'],
            'opponents.*.name'       => ['required', 'string'],
            'opponents.*.type'       => ['required', 'in:individual,company,government_entity'],
            'opponents.*.national_id' => ['nullable', 'string', 'distinct'],
            'opponents.*.phone'      => ['nullable', 'string'],
            'opponents.*.email'      => ['nullable', 'email'],

            // --- الفريق المكلف (Array) ---
            'team' => $isOfficeManager ? ['required', 'array', 'min:1'] : ['prohibited'],

            'team.*.user_id'     => [
                'required',
                'uuid',
                // تحسين: التأكد من أن عضو الفريق ليس عميلاً (كما هو مطلوب في التعليق)
                Rule::exists('users', 'id')->where(function ($query) use ($clientRoleId) {
                    if ($clientRoleId) {
                        $query->where('role_id', '!=', $clientRoleId);
                    }
                })
            ],
            'team.*.role_in_case' => ['required', 'string'],

            // --- الأقساط الزمنية (Array) ---
            'payment_milestones' => [
                'required_if:billing_type,installments',
                'prohibited_unless:billing_type,installments',
                'array',
                'min:1'
            ],
            'payment_milestones.*.title'    => ['required', 'string'],
            'payment_milestones.*.amount'   => ['required', 'numeric', 'min:0'],
            'payment_milestones.*.due_date' => [
                'required',
                'date',
                'after:today'
            ],
        ];
    }
    /**
     * Custom error messages for better UX
     */
    public function messages()
    {
        return [
            // بيانات أساسية
            'title.required' => "title is required",
            'price.numeric' => "The price must be a number.", // رسالة الخطأ الجديدة
            'case_category.exists' => "The selected category is invalid.",

            'description.required' => "description is required",
            'court.required' => "court is required",
            'billing_type.required' => "billing_type is required",
            'billing_type.in' => "billing_type must be one of: fixed_proposal, percentage_collection, installments, pay_as_you_go",

            // الفوترة
            'collection_percentage.required_if' => "collection_percentage is required when billing type is percentage_collection",
            'collection_percentage.prohibited_unless' => "The collection_percentage field is only allowed when billing type is percentage_collection.",
            // العملاء
            'clients.required' => "clients array is required",
            'clients.*.user_id.exists' => "One of the provided client IDs does not exist or is not a client",
            'clients.*.is_primary.required' => "is_primary flag is required for each client",

            // الخصوم
            'opponents.required' => "opponents array is required",
            'opponents.*.name.required' => "Opponent name is required",
            'opponents.*.type.in' => "Opponent type must be individual, company, or government_entity",

            // الفريق
            'team.required' => "team array is required for office managers",
            'team.prohibited' => "Independent lawyers cannot assign a team",
            'team.*.user_id.exists' => "One of the provided team member IDs does not exist or is invalid",
            'team.*.role_in_case.required' => "role_in_case is required for each team member",

            // الأقساط
            'payment_milestones.required_if' => "payment_milestones are required when billing type is installments",
            'payment_milestones.prohibited_unless' => "payment_milestones are only allowed when billing type is installments",
            'payment_milestones.*.amount.required' => "Milestone amount is required",
            'payment_milestones.*.due_date.required' => "Milestone due date is required",
            'payment_milestones.*.due_date.after' => "The due date must be strictly after today's date",
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
