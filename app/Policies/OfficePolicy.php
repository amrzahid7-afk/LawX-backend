<?php

namespace App\Policies;

use App\Models\LawerProfile;
use App\Models\Office;
use App\Models\OfficeInvitation;
use App\Models\User;

class OfficePolicy
{
    public function manage(User $user, Office $office): bool
    {
        $profile = $user->lawyerProfile;
        return $profile && $office->owner_lawyer_profile_id === $profile->id; // && $profile->lawyer_state === 'licensed';
    }
    public function activate_office(User $user, Office $office): bool
    {
        $profile = $user->lawyerProfile;
        return  $office && $profile && $office->owner_lawyer_profile_id === $profile->id && $profile->lawyer_state === 'licensed';
    }
    public function invite_lawer_to_office(User $user, Office $office): bool
    {
        $profile = $user->lawyerProfile;
        return  $office && $profile && $office->status === 'active' && $office->owner_lawyer_profile_id === $profile->id && $profile->lawyer_state === 'licensed';
    }
    public function my_sended_invitations(User $user, Office $office): bool
    {
        $profile = $user->lawyerProfile;
        return  $office && $profile && $office->status === 'active' && $office->owner_lawyer_profile_id === $profile->id && $profile->lawyer_state === 'licensed';
    }
    public function my_received_invitations(User $user, Office $office): bool
    {
        $profile = $user->lawyerProfile;
        return ($office && $profile && $office->status != 'active') || $profile->lawyer_state === 'trainee';
    }
    public function accept_invitation(User $user, OfficeInvitation $invitation): bool
    {
        return $invitation->invited_lawyer_profile_id === $user->lawyerProfile->id && $invitation->status === 'pending';
    }
    public function remove_invitation(User $user, OfficeInvitation $invitation): bool
    {
        return $user->lawyerProfile()->ownedOffice()->id === $invitation->office_id && $invitation->status === 'pending';
    }
    public function remove_lawer_from_office(User $user, LawerProfile $lawer): bool
    {
        return $user->lawyerProfile()->ownedOffice()->id === $lawer->employed_In_office_id;
    }
    public function get_all_lawers_in_my_office(User $user, Office $office)
    {
        if (!$user->lawyerProfile) {
            return false;
        }

        return $user->lawyerProfile->ownedOffice &&
            $user->lawyerProfile->ownedOffice->id === $office->id &&
            $user->lawyerProfile->ownedOffice->status === 'active';
    }
}
