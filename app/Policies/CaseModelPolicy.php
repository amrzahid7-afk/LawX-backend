<?php

namespace App\Policies;

use App\Models\CaseModel;
use App\Models\Office;
use App\Models\User;

class CaseModelPolicy
{
    // check if the account for this lawer susoened or the lawyer is Trainee yet 
    public function create_case(User $user)
    {
        return  !$user->lawyerProfile->is_suspended && $user->lawyerProfile->lawyer_state != 'trainee';
    }
    // check if the lawer in team for this case or is leader for this case 
    public function get_case_details(User $user, CaseModel $case)
    {
        return $user->id === $case->lead_lawyer_id || $case->team->contains('id', $user->id) || $user->id === $case->office->owner->user_id;
    }
    // only leader can update the case
    public function update(User $user, CaseModel $case)
    {
        return
            $user->id === $case->lead_lawyer_id ||
            $user->id === $case->office->owner->user_id;
    }
    // All true
    public function view_list(User $user)
    {
        return true;
    }
    // only leader can delete the case
    public function delete_case(User $user, CaseModel $case)
    {
        return $case->lead_lawyer_id == $user->id;
    }
}
