<?php

namespace App\Policies;

use App\Models\CaseModel;
use App\Models\Session;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SessionPolicy
{
    // helper functions
    private function isLisencedLawer(User $user)
    {
        return $user->lawyerProfile->lawyer_state === 'licensed';
    }
    private function isofficeManager(User $user, CaseModel $case)
    {
        return $user->lawyerProfile->ownedOffice && $user->lawyerProfile->ownedOffice->id === $case->office_id;
    }
    private function isCaseTeamMember(User $user, CaseModel $case)
    {
        return $case->team->contains('id', $user->id);
    }
    private function isCaseLeader(User $user, CaseModel $case)
    {
        return $case->lead_lawyer_id === $user->id;
    }
    public function addSession(User $user, CaseModel $case)
    {
        // can create session if the user is a licensed lawyer and he is the office manager of the case or he is a member of the case team
        return
            $this->isLisencedLawer($user) &&
            ($this->isofficeManager($user, $case) ||
                $this->isCaseTeamMember($user, $case)
            );
    }
    public function get_sessions_for_case(User $user, CaseModel $case)
    {
        return
            $this->isLisencedLawer($user) &&
            ($this->isofficeManager($user, $case) ||
                $this->isCaseLeader($user, $case)
            );
    }
    public function  delete_session(User $user, Session $session)
    {
        return
            $this->isofficeManager($user, $session->case) ||
            $this->isCaseLeader($user, $session->case);
    }
    public function update_session(User $user, Session $session)
    {
        return
            $this->isofficeManager($user, $session->case) ||
            $this->isCaseLeader($user, $session->case) ||
            $this->isCaseTeamMember($user, $session->case);
    }
    public function get_sessions_for_lawer()
    {
        return true;
    }
}
