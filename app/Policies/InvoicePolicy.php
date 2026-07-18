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
    public function get_invoices_for_client(User $user)
    {
        return true;
    }
}
