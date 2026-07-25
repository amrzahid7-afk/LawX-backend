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
    public function invite_lawer_to_office(User $user, LawerProfile $invited_lawer_profile): bool
    {
        $profile = $user->lawyerProfile;
        $office = $user->lawyerProfile->ownedOffice;
        // return true;
        return
            // lawer has profile and office 
            // dd($office && $profile);
            $office && $profile &&
            // invited_lawyer profile Is climed and not suspended
            // dd($invited_lawer_profile->is_claimed);
            // dd($invited_lawer_profile->is_claimed && !$invited_lawer_profile->is_suspended);
            $invited_lawer_profile->is_claimed && !$invited_lawer_profile->is_suspended &&
            // the office manager Active his office 
            $office->status === 'active' &&
            // office Manager is licensed Lawer
            $profile->lawyer_state === 'licensed';
    }
    public function my_sended_invitations(User $user, Office $office): bool
    {
        $profile = $user->lawyerProfile;
        return  $office && $profile && $office->status === 'active' && $office->owner_lawyer_profile_id === $profile->id && $profile->lawyer_state === 'licensed';
    }
    public function my_received_invitations(User $user): bool
    {
        // return true;
        $profile = $user->lawyerProfile;
        $office = $user->lawyerProfile->ownedOffice;
        // dd(($office && $profile && $office->status != 'active') || $profile->lawyer_state === 'trainee');
        return ($office && $profile && $office->status != 'active') || $profile->lawyer_state === 'trainee';
    }

    public function accept_invitation(User $user, OfficeInvitation $invitation): bool
    {
        return $invitation->invited_lawyer_profile_id === $user->lawyerProfile->id && $invitation->status === 'pending';
    }
    public function remove_invitation(User $user, OfficeInvitation $invitation): bool
    {
        // dd($user->lawyerProfile->ownedOffice->id === $invitation->office_id && $invitation->status === 'pending');
        return $user->lawyerProfile->ownedOffice->id === $invitation->office_id && $invitation->status === 'pending';
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
