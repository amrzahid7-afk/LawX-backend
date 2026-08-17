<?php

namespace App\Policies;

use App\Models\CaseModel;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function create_task(User $user, CaseModel $case, User $assignedToLawyer)
    {
        // return true;
        $isLicensedLawyer = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        // dd($isLicensedLawyer);
        if ($isLicensedLawyer != true) return false;

        $isLeadLawyer = $user->id === $case->lead_lawyer_id;
        $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice && $user->lawyerProfile->ownedOffice->id == $case->office_id;
        // dd(($isLeadLawyer || $isOfficeManager));
        if (($isLeadLawyer || $isOfficeManager) != true) {
            return false;
        }

        // $isAssignedLawyerInTeam = $case->team->contains('user_id', $assignedToLawyer->id);
        $isAssignedLawyerInTeam = $case->team->contains('id', $assignedToLawyer->id);
        $isAssigningToHimself = ($assignedToLawyer->id === $user->id);
        // dd(($isAssignedLawyerInTeam || $isAssigningToHimself));
        // dd($case->team->toArray());
        // dd($isAssignedLawyerInTeam);
        if (($isAssignedLawyerInTeam || $isAssigningToHimself) == true) {
            return true;
        }
    }
    public function get_tasks_for_case(User $user, CaseModel $case)
    {
        $isLicensedLawyer = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        $isLeadLawyer = $user->id === $case->lead_lawyer_id;
        $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice && $user->lawyerProfile->ownedOffice->id == $case->office_id;

        if ($isLeadLawyer || $isOfficeManager) {
            return true;
        }

        $isTeamMember = $case->team->contains('id', $user->id);
        if ($isLicensedLawyer && $isTeamMember) {
            return true;
        }

        if (!$isLicensedLawyer) { // يعني هو متدرب
            return $case->tasks()->where('assigned_to', $user->id)->exists();
        }

        return false;
    }

    public function edit_task(User $user, Task $task)
    {
        $isLicensedLawyer = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        $isLeadLawyer = $user->id === $task->case->lead_lawyer_id;

        $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice && $user->lawyerProfile->ownedOffice->id == $task->case->office_id;

        $isAssignedLawyer = $user->id === $task->assigned_to;

        if (($isLeadLawyer || $isOfficeManager) && $isLicensedLawyer) {
            return true;
        }

        if ($isLicensedLawyer && $isAssignedLawyer) {
            return true;
        }

        if (!$isLicensedLawyer && $isAssignedLawyer) {
            return true;
        }

        return false;
    }

    public function delete_task(User $user, Task $task)
    {
        $isLicensedLawyer = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        $isLeadLawyer = $user->id === $task->case->lead_lawyer_id;
        $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice && $user->lawyerProfile->ownedOffice->id == $task->case->office_id;
        return ($isLeadLawyer || $isOfficeManager) && $isLicensedLawyer;
    }

    public function get_my_tasks(User $user)
    {
        return true;
    }
    public function add_Expense_for_task(User $user, Task $task)
    {
        $isLicensedLawyer = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        $isLeadLawyer = $user->id === $task->case->lead_lawyer_id;
        $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice && $user->lawyerProfile->ownedOffice->id == $task->case->office_id;
        $isAssignedLawyer = $user->id === $task->assigned_to;

        // if (($isLeadLawyer || $isOfficeManager) && $isLicensedLawyer) {
        //     return true;
        // }

        // if ($isLicensedLawyer && $isAssignedLawyer) {
        //     return true;
        // }

        if ($isAssignedLawyer) {
            return true;
        }

        return false;
    }
    public function aprove_expensive_for_task(User $user, Task $task)
    {
        $isLicensedLawyer = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';
        $isLeadLawyer = $user->id === $task->case->lead_lawyer_id;
        $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice && $user->lawyerProfile->ownedOffice->id == $task->case->office_id;

        return ($isLeadLawyer || $isOfficeManager) && $isLicensedLawyer;
    }
    public function get_expenses_for_task(User $user, Task $task)
    {
        $isLicensedLawyer = $user->lawyerProfile && $user->lawyerProfile->lawyer_state === 'licensed';

        $isLeadLawyer = optional($task->case)->lead_lawyer_id === $user->id;
        $isOfficeManager = $user->lawyerProfile && $user->lawyerProfile->ownedOffice && optional($task->case)->office_id == $user->lawyerProfile->ownedOffice->id;
        $isAssignedLawyer = $user->id === $task->assigned_to;

        if (($isLeadLawyer || $isOfficeManager) && $isLicensedLawyer) {
            return true;
        }

        if ($isLicensedLawyer && $isAssignedLawyer) {
            return true;
        }

        if (!$isLicensedLawyer && $isAssignedLawyer) {
            return true;
        }

        return false;
    }
}
