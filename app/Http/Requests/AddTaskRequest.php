<?php

namespace App\Http\Requests;

use App\Models\LawerProfile;
use App\Models\Office;
use App\Models\OfficeInvitation;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //auth('api')->user()->role->name == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'case_id' => 'required|exists:cases,id',
            'title' => 'required|string|max:25',
            'description' => 'required|string|max:255',
            'due_date' => 'nullable|date|after_or_equal:today',
            // 'assigned_to' => 'required|exists:users,id',
            'assigned_to' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    // 1. جلب المستخدم المُسند إليه
                    $assignedUser = User::find($value);

                    if (!$assignedUser) {
                        $fail('The assigned user does not exist');
                        return;
                    }

                    // 2. التحقق من دوره باستخدام slug (وليس name)
                    if (!$assignedUser->role || !in_array($assignedUser->role->slug, ['lawyer'])) {
                        $fail('The assigned user must be a lawyer');
                        return;
                    }
                    // 3. جلب بيانات المحامي الحالي
                    $currentUser = $this->user();
                    $lawyerProfileId = $currentUser->lawyerProfile?->id;
                    if (!$lawyerProfileId) {
                        $fail('You do not have a lawyer profile');
                        return;
                    }
                    // 4. جلب مكتب المحامي الحالي
                    $office = Office::where('owner_lawyer_profile_id', $lawyerProfileId)->first();
                    if (!$office) {
                        $fail('You cannot assign tasks because you do not own an office');
                        return;
                    }
                    // 5. السماح للمحامي بتسليم مهمة لنفسه (صاحب المكتب)
                    if ($assignedUser->id === $currentUser->id) {
                        return;
                    }
                    // 6. جلب ملف المحامي الشخصي للمستخدم المُسند إليه
                    $assignedProfile = LawerProfile::where('user_id', $value)->first();
                    if (!$assignedProfile) {
                        $fail('The assigned user does not have a lawyer profile');
                        return;
                    }
                    // 7. التحقق من أنه عضو مقبول في المكتب
                    $isAcceptedMember = OfficeInvitation::where('office_id', $office->id)
                        ->where('invited_lawyer_profile_id', $assignedProfile->id)
                        ->where('status', 'accepted')
                        ->exists();
                    if (!$isAcceptedMember) {
                        $fail('The assigned user is not an active member of your office');
                    }
                }
            ],
        ];
    }
    public function messages()
    {
        return [
            'case_id.required' => "case_id is required",
            'case_id.exists' => "case_id not found",
            'title.required' => "title is required",
            'title.string' => "title must be a string",
            'title.max' => "title must be less than 25 characters",
            'description.required' => "description is required",
            'description.string' => "description must be a text",
            'description.max' => "description must be less than 255 characters",
            'due_date.nullable' => "due_date must be a valid date",
            'due_date.date' => "due_date must be a valid date",
            'due_date.after_or_equal' => "due_date must be a date equal to or after today",
            'assigned_to.required' => "assigned_to is required",
            'assigned_to.exists' => "assigned_to not found",
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
