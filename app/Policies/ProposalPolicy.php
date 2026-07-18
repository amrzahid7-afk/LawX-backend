<?php

namespace App\Policies;

use App\Models\Legal_request;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProposalPolicy
{
    public function get_my_proposals(User $user)
    {

        return $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
    }
    public function get_proposal_details(User $user, Proposal $proposal)
    {
        // Proposal Is For this lawer 
        $is_for_me = $user->id === $proposal->lawer_id;
        return $this->get_my_proposals($user) && $is_for_me;
    }
    public function send_proposal(User $user, Legal_request $legal_request)
    {
        // the request that send Proposal For it Is For this lawer
        $request_for_me = $legal_request->assigned_lawyer_id === $user->id;
        $public_request = $legal_request->type === 'public';
        return $this->get_my_proposals($user) && ($request_for_me || $public_request);
        // return true;
    }
    public function update_proposal(User $user, Proposal $proposal)
    {
        // Proposal Is For this lawer 
        $is_for_me = $user->id === $proposal->lawer_id;
        return $this->get_my_proposals($user) && $is_for_me;
    }
    public function delete_proposal(User $user, Proposal $proposal)
    {
        // Proposal Is For this lawer 
        $is_for_me = $user->id === $proposal->lawer_id;
        return $this->get_my_proposals($user) && $is_for_me;
    }
}
