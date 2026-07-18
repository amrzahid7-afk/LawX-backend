<?php

namespace App\Policies;

use App\Models\Legal_request;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class Legal_requestPolicy
{
    public function get_legal_requests(User $user)
    {
        // lawer will be lisenced To get requests 
        return $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        // return true;
    }
    public function get_legal_request_details(User $user, Legal_request $legal_request)
    {
        $is_public = $legal_request->type === 'public';
        $is_request_for_me = $legal_request->assigned_lawyer_id === $user->id;
        return $this->get_legal_requests($user) && ($is_public || $is_request_for_me);
    }
}
