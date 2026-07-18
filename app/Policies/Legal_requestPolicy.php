<?php

namespace App\Policies;

use App\Models\Legal_request;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class Legal_requestPolicy
{
    // lawer permissions 
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
    // client permissions
    public function create(User $user)
    {
        $is_active_client = $user->role->slug === 'client' && $user->is_active;
        return $is_active_client;
    }
    public function update_request(User $user, Legal_request $request)
    {
        $is_request_for_me = $request->client_id === $user->id;
        return $this->create($user) && $is_request_for_me;
    }
    public function delete_request(User $user, Legal_request $request)
    {
        return $this->update_request($user, $request);
    }
    public function get_my_requests(User $user)
    {
        return   $this->create($user);
    }
}
