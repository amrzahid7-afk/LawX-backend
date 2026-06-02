<?php
// new Validation Request for Case Creation

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
        // 1. جلب معرفات الأدوار من قاعدة البيانات
        $clientRoleId = Role::where('name', 'client')->value('id');
        $officeManagerRoleId = Role::where('name', 'office_manager')->value('id'); // افترضت أن اسم دور المدير هو office_manager

        // 2. التحقق من دور المستخدم الحالي (من خلال التوكن)
        $user = $this->user();
        $isOfficeManager = $user && $user->role_id === $officeManagerRoleId;

        // 3. بناء قواعد التحقق ديناميكياً
        return [
            // --- بيانات القضية الأساسية ---
            'title'             => ['required', 'string', 'min:5', 'max:255'],
            'description'       => ['required', 'string', 'min:10'],
            'court'             => ['required', 'string', 'min:3'],
            'price'             => ['required', 'integer', 'min:0'],
            'case_category'     => ['required', 'string', 'min:4'],
            'importance_stars'  => ['required', 'integer', 'min:1', 'max:5'],
            'billing_type'      => ['required', 'in:fixed_proposal,percentage_collection,installments,pay_as_you_go'],
            'lead_lawyer_id'      => ['required', "uuid", Rule::exists('users', 'id')],
            'case_number' => [
                'required',
                'string',
                'min:3',
                'unique:cases,case_number' // <--- هذه القاعدة تمنع التكرار
            ],
            // --- شروط الفوترة ---
            // مطلوب فقط إذا كان النوع نسبة من التحصيل
            'collection_percentage' => [
                'required_if:billing_type,percentage_collection',
                'nullable',
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
                    $query->where('role_id', $clientRoleId); // التأكد أنه عميل فعلاً
                })
            ],
            'clients.*.is_primary' => ['required', 'boolean'],

            // --- الخصوم (Array) ---
            'opponents'              => ['required', 'array', 'min:1'],
            'opponents.*.name'       => ['required', 'string'],
            'opponents.*.type'       => ['required', 'in:individual,company,government_entity'],
            'opponents.*.national_id' => ['nullable', 'string'],
            'opponents.*.phone'      => ['nullable', 'string'],
            'opponents.*.email'      => ['nullable', 'email'],

            // --- الفريق المكلف (Array) ---
            // إذا كان المستخدم مدير مكتب -> مطلوب وأقل شي مصفوفة فيها عنصر واحد (المحامي المسؤول على الأقل)
            // إذا كان مستقل -> ممنوع إرساله تماماً (prohibited)
            'team' => $isOfficeManager ? ['required', 'array', 'min:1'] : ['prohibited'],

            'team.*.user_id'     => [
                'required',
                'uuid',
                Rule::exists('users', 'id') // يمكن إضافة شرط أنه ليس عميل إذا أردت
            ],
            'team.*.role_in_case' => ['required', 'string'],

            // --- الأقساط الزمنية (Array) ---
            // مطلوب فقط إذا كان النوع أقساط
            // ممنوع إرساله إذا كان النوع أي شيء آخر
            // --- الأقساط الزمنية (Array) ---
            // أصبحت nullable (اختيارية) ليستطيع المحامي إضافتها في أي نوع فوترة يريده
            // --- الأقساط الزمنية (Array) ---
            'payment_milestones' => [
                'required_if:billing_type,installments',      // مطلوبة إذا كان النوع أقساط
                'prohibited_unless:billing_type,installments', // ممنوعة إرسالها في أي نوع آخر
                'array',
                'min:1'
            ],
            'payment_milestones.*.title'    => ['required', 'string'],
            'payment_milestones.*.amount'   => ['required', 'numeric', 'min:0'],
            'payment_milestones.*.due_date' => [
                'required',
                'date',
                'after:today' // التاريخ يجب أن يكون بعد تاريخ اليوم بشكل حصري
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
            'description.required' => "description is required",
            'court.required' => "court is required",
            'billing_type.required' => "billing_type is required",
            'billing_type.in' => "billing_type must be one of: fixed_proposal, percentage_collection, installments, pay_as_you_go",

            // الفوترة
            'collection_percentage.required_if' => "collection_percentage is required when billing type is percentage_collection",

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
            'team.*.user_id.exists' => "One of the provided team member IDs does not exist",
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

// namespace App\Http\Requests;

// use App\Models\Role;
// use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Validation\Rule;
// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Http\Exceptions\HttpResponseException;

// class CaseCreationRequest extends FormRequest
// {
//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return true;
//     }
//     /**
//      * Get the validation rules that apply to the request.
//      *
//      * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
//      */
//     public function rules(): array
//     {
//         $clientRoleId = Role::where('name', 'client')->value('id');
//         return [
//             'title'       => ['required', 'string', 'min:5', 'max:255'],
//             'description' => ['required', 'string', 'min:10'],
//             'court' => ['required', 'string', 'min:10'],
//             'importance_stars' => ['required', 'integer', 'min:1', 'max:5'],
//             'billing_type' => ['nullable', 'in:fixed_proposal,percentage_collection,installments,pay_as_you_go'],

//             'collection_percentage' => ['nullable', 'numeric', 'between:0,100'],
//             'client_id'   => [
//                 'required',
//                 'uuid',
//                 Rule::exists('users', 'id')->where(function ($query) use ($clientRoleId) {
//                     $query->where('users.role_id', $clientRoleId);
//                 })
//             ],
//         ];
//     }
//     public function messages()
//     {
//         return [
//             'title.required' => "title is required",
//             'title.string' => "title must be a string",
//             'title.min' => "title must be at least 5 characters",
//             'title.max' => "title must be less than 255 characters",

//             'description.required' => "description is required",
//             'description.string' => "description must be a string",
//             'description.min' => "description must be at least 10 characters",

//             'client_id.required' => "client_id is required",
//             'client_id.uuid' => "client_id must be a uuid",
//             'client_id.exists' => "client_id not found",
//         ];
//     }
//     protected function failedValidation(Validator $validator)
//     {
//         throw new HttpResponseException(response()->json([
//             'success' => false,
//             'message' => 'Validation failed',
//             'errors' => $validator->errors()
//         ], 422));
//     }
// }
