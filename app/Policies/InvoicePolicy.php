<?php

namespace App\Policies;

use App\Models\CaseModel;
use App\Models\Invoice;
use App\Models\Office;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InvoicePolicy
{
    private function isCaseManager(User $user, CaseModel $case): bool
    {
        $isLeadLawyer = $user->id === $case->lead_lawyer_id;
        $isOfficeOwner = $case->office_id && Office::where('id', $case->office_id)
            ->where('owner_lawyer_profile_id', $user->lawyerProfile->id)->exists();

        return $isLeadLawyer || $isOfficeOwner;
    }
    public function create_invoice(User $user, CaseModel $case): bool
    {
        $isTrainee = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'trainee';
        if ($isTrainee) return false;
        return $this->isCaseManager($user, $case);
    }
    public function get_invoices_for_lawer(User $user): bool
    {
        return true;
    }
    public function get_invoices_for_client(User $user)
    {
        return true;
    }
    public function pay_invoice(User $user, Invoice $invoice): bool
    {
        if ($invoice->type === 'For_case') {
            return $user->id === $invoice->client_id;
        }

        if ($invoice->type === 'salary') {
            // 1. التأكد أن المستخدم لديه بروفايل وأنه يملك مكتب (مدير مكتب)
            $managerProfile = $user->lawyerProfile;
            if (!$managerProfile || !$managerProfile->ownedOffice) {
                return false;
            }

            // 2. جلب الموظف صاحب الراتب (الآن سيرجع User بشكل صحيح بعد تعديل موديل Salary)
            $employee = $invoice->salary->lawyer;
            if (!$employee) {
                return false;
            }

            // 3. جلب بروفايل الموظف للتحقق من مكتبه
            $employeeProfile = $employee->lawyerProfile;
            if (!$employeeProfile) {
                return false;
            }

            return $employeeProfile->employedOffice && $employeeProfile->employedOffice->id === $managerProfile->ownedOffice->id;
        }

        return false;
    }
}
