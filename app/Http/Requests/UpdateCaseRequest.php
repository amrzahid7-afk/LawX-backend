<?php

namespace App\Http\Requests;

use App\Models\LawerProfile;
use App\Models\Office;
use App\Models\OfficeInvitation;
use App\Models\Role;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateCaseRequest extends FormRequest
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
        $clientRoleId = Role::where('slug', 'client')->value('id');
        $currentUser = $this->user();
        $lawyerProfileId = $currentUser->lawyerProfile?->id;

        $hasTeam = false;

        if ($lawyerProfileId) {
            $office = Office::where('owner_lawyer_profile_id', $lawyerProfileId)->first();

            if ($office) {
                $hasTeam = OfficeInvitation::where('office_id', $office->id)
                    ->where('status', 'accepted')
                    ->exists();
            }
        }

        return [
            'case_id'           => ['required', 'uuid', Rule::exists('cases', 'id')],
            'title'             => ['nullable', 'string', 'min:5', 'max:255'],
            'description'       => ['nullable', 'string', 'min:10'],
            'court'             => ['nullable', 'string', 'min:3'],
            'price'             => ['nullable', 'numeric', 'min:0'],
            'case_category'     => ['nullable', 'string', 'min:4'], // افترضنا أنه UUID ويوجد في جدول التصنيفات
            'importance_stars'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'billing_type'      => ['nullable', 'in:fixed_proposal,percentage_collection,installments,pay_as_you_go'],
            // 'lead_lawyer_id'    => ['nullable', "uuid", Rule::exists('users', 'id')],
            'status'            => ['nullable', 'in:open,close,pending,archived'],
            'case_number' => [
                'nullable',
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
            'clients'           => ['nullable', 'array', 'min:1'],
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
            'opponents'              => ['nullable', 'array', 'min:1'],
            'opponents.*.name'       => ['required', 'string'],
            'opponents.*.type'       => ['nullable', 'in:individual,company,government_entity'],
            'opponents.*.national_id' => ['nullable', 'string'],
            'opponents.*.phone'      => ['nullable', 'string'],
            'opponents.*.email'      => ['nullable', 'email'],

            // --- الفريق المكلف (Array) ---
            'team' => $hasTeam ? ['nullable', 'array'] : ['prohibited'],

            'team.*.user_id'     => [
                'required_with:team',
                'uuid',
                'exists:users,id'
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
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $team = $this->input('team');

            // إذا لم يكن هناك فريق أو كانت هناك أخطاء سابقة، تخطي هذا التحقق
            if (empty($team) || $validator->errors()->any()) {
                return;
            }

            $currentUser = $this->user();
            $lawyerProfileId = $currentUser->lawyerProfile?->id;

            if (!$lawyerProfileId) {
                return;
            }

            // جلب مكتب المحامي
            $office = Office::where('owner_lawyer_profile_id', $lawyerProfileId)->first();

            if (!$office) {
                return;
            }

            // جلب معرفات الملفات الشخصية للمحامين المقبولين في المكتب
            $acceptedProfileIds = OfficeInvitation::where('office_id', $office->id)
                ->where('status', 'accepted')
                ->pluck('invited_lawyer_profile_id')
                ->toArray();

            // تحويلها إلى معرفات المستخدمين
            $validUserIds = LawerProfile::whereIn('id', $acceptedProfileIds)
                ->pluck('user_id')
                ->toArray();

            // التحقق من كل عضو في الفريق
            foreach ($team as $index => $member) {
                $userId = $member['user_id'] ?? null;

                if (!$userId) {
                    continue;
                }

                if (!in_array($userId, $validUserIds)) {
                    $validator->errors()->add(
                        "team.{$index}.user_id",
                        "المحامي المحدد ليس عضواً نشطاً في فريق مكتبك."
                    );
                }
            }
        });
    }
    public function messages()
    {
        return [
            // بيانات أساسية
            'price.numeric' => "The price must be a number.",
            'case_category.exists' => "The selected category is invalid.",
            'billing_type.in' => "billing_type must be one of: fixed_proposal, percentage_collection, installments, pay_as_you_go",
            // الفوترة
            'collection_percentage.required_if' => "collection_percentage is required when billing type is percentage_collection",
            'collection_percentage.prohibited_unless' => "The collection_percentage field is only allowed when billing type is percentage_collection.",
            // العملاء
            'clients.*.user_id.exists' => "One of the provided client IDs does not exist or is not a client",
            'clients.*.is_primary.required' => "is_primary flag is required for each client",
            // الخصوم
            'opponents.*.name.required' => "Opponent name is required",
            'opponents.*.type.in' => "Opponent type must be individual, company, or government_entity",
            // الفريق

            'team.prohibited' => "You cannot assign a team because you don't have active members in your office.",
            'team.*.user_id.exists' => "The selected user does not exist.",
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
        throw new HttpResponseException(sendError('Validation failed', $validator->errors()->toArray(), 422));
    }
}
