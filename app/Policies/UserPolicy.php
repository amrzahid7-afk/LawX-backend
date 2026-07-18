<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function get_clients(User $user)
    {
        // 
        $is_syndicate = $user->role->slug === 'syndicate';
        $is_lawer = $user->role->slug === 'lawyer';
        $is_liseced = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        $lawer_profile_is_not_suspended = $user->lawyerProfile && !$user->lawyerProfile->is_suspended;
        return $is_syndicate || ($is_lawer && $is_liseced && $lawer_profile_is_not_suspended);
    }
}
