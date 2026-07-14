<?php

namespace App\Http\Controllers;

use App\Models\LawerProfile;
use App\Models\Office;
use App\Models\OfficeInvitation;
use Illuminate\Http\Request;

class ManageOfficeController extends Controller
{
    public function activate_office(Request $request)
    {
        $request->validate([
            'lawer_profile_id' => 'required|uuid|exists:offices,owner_lawyer_profile_id',
        ]);
        $office = Office::where('owner_lawyer_profile_id', $request->input('lawer_profile_id'))->first();
        if ($office->status === 'active') {
            return sendError('Office is already active.', [], 400);
        }
        $office->status = 'active';
        $office->save();
        return sendResponse($office, 'Office activated successfully.', 200);
    }
    public function invite_lawer_to_office(Request $request)
    {
        $request->validate([
            'invited_lawyer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
        ]);
        $my_office = Office::where('owner_lawyer_profile_id', auth('sanctum')->user()->lawyerProfile->id)->first();
        // process the conflict that lawer invite him self to his own office
        if ($request->input('invited_lawyer_profile_id') === $my_office->owner_lawyer_profile_id) {
            return sendError('Cannot invite yourself to your own office.', [], 400);
        }
        // process the conflict that lawer invite another lawer who is already in the office
        if ($my_office->employees()->where('lawyer_profiles.id', $request->input('invited_lawyer_profile_id'))->exists()) {
            return sendError('The invited lawyer is already a member of this office.', [], 400);
        }
        // process the conflict that lawer invite another lawer who is already invited to the office
        if ($my_office->invitations()->where('invited_lawyer_profile_id', $request->input('invited_lawyer_profile_id'))->where('status', 'pending')->exists()) {
            return sendError('The invited lawyer has already been invited to this office.', [], 400);
        }
        // process the conflict that lawer invite another lawer who has its one office and he is not allowed to join another office
        $invitedLawyer = $my_office->invitations()->where('invited_lawyer_profile_id', $request->input('invited_lawyer_profile_id'))->first();
        if ($invitedLawyer && $invitedLawyer->status === 'accepted') {
            return sendError('The invited lawyer is already a member of another office.', [], 400);
        }
        // check if the office is active or not
        if ($my_office->status !== 'active') {
            return sendError('Cannot invite lawyers to a suspended office.', [], 400);
        }
        $invitation = $my_office->invitations()->create([
            'invited_lawyer_profile_id' => $request->input('invited_lawyer_profile_id'),
            'status' => 'pending',
        ]);
        return sendResponse($invitation, 'Invitation sent successfully.', 200);
    }
    public function my_sended_invitations(Request $request)
    {
        $request->validate([
            'lawer_profile_id' => 'required|uuid|exists:offices,owner_lawyer_profile_id',
        ]);
        $office = Office::where('owner_lawyer_profile_id', $request->input('lawer_profile_id'))->first();
        $invitations = $office->invitations()->get();
        $response_data = $invitations->map(function ($invitation) {
            return [
                'invitation_id' => $invitation->id,
                'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
                'status' => $invitation->status,
                'responded_at' => $invitation->responded_at,
                'created_at' => $invitation->created_at,
                'updated_at' => $invitation->updated_at,
            ];
        });
        return sendResponse($response_data, 'My sended invitations retrieved successfully.', 200);
    }
    public function my_received_invitations(Request $request)
    {
        $request->validate([
            'lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
        ]);
        $invitations = Office::whereHas('invitations', function ($query) use ($request) {
            $query->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'));
        })->with(['invitations' => function ($query) use ($request) {
            $query->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'));
        }])->get();
        $response_data = [];
        foreach ($invitations as $office) {
            foreach ($office->invitations as $invitation) {
                $response_data[] = [
                    'invitation_id' => $invitation->id,
                    'office_name' => $office->name,
                    'owner_office_name' => $office->owner->full_name,
                    'status' => $invitation->status,
                    'responded_at' => $invitation->responded_at,
                    'created_at' => $invitation->created_at,
                    'updated_at' => $invitation->updated_at,
                ];
            }
        }
        return sendResponse($response_data, 'My received invitations retrieved successfully.', 200);
    }
    public function accept_invitation(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|uuid|exists:office_invitations,id',
        ]);
        $invitation = OfficeInvitation::find($request->input('invitation_id'));
        if ($invitation->status !== 'pending') {
            return sendError('This invitation has already been responded to.', [], 400);
        }
        $invitation->accept();
        $response_data = [
            'invitation_id' => $invitation->id,
            'office_name' => $invitation->office->name,
            'owner_office_name' => $invitation->office->owner->full_name,
            'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
            'status' => $invitation->status,
            'responded_at' => $invitation->responded_at,
            'created_at' => $invitation->created_at,
            'updated_at' => $invitation->updated_at,
        ];
        return sendResponse($response_data, 'Invitation accepted successfully.', 200);
    }
    public function remove_lawer_from_office(Request $request)
    {
        $request->validate([
            'lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
        ]);

        $lawer_id = auth('sanctum')->user()->id;
        $lawer_profile = LawerProfile::where('user_id', $lawer_id)->first();

        if (!$lawer_profile) {
            return sendError('Lawyer profile not found.', [], 404);
        }

        $lawer_office = Office::where('owner_lawyer_profile_id', $lawer_profile->id)->first();

        if (!$lawer_office) {
            return sendError('You are not the owner of any office.', [], 400);
        }
        // proceess the conflict that the 
        if ($request->input('lawer_profile_id') === $lawer_profile->id) {
            return sendError('You cannot remove yourself from your office.', [], 400);
        }

        // 1. البحث عن الدعوة في الجدول الوسيط التي حالتها 'accepted' فقط
        $invitation = \App\Models\OfficeInvitation::where('office_id', $lawer_office->id)
            ->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'))
            ->where('status', 'accepted')
            ->first();

        if (!$invitation) {
            return sendError('This lawyer is not an active employee in your office.', [], 404);
        }

        // 2. حذف سجل الدعوة (أو يمكنك تغيير حالته إلى 'removed' إذا كنت تريد الاحتفاظ بسجل تاريخي)
        $invitation->delete();

        // 3. مهم جداً: تفريغ عمود التوظيف في جدول lawyer_profiles
        $removed_lawer_data = LawerProfile::find($request->input('lawer_profile_id'));
        $removed_lawer_data->update([
            'employed_in_office_id' => null
        ]);

        $lawyer = [
            'id' => $removed_lawer_data->id,
            'full_name' => $removed_lawer_data->full_name,
            'national_id' => $removed_lawer_data->national_id,
        ];

        return sendResponse($lawyer, 'Lawyer removed from office successfully.', 200);
    }
    public function reject_invitation(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|uuid|exists:office_invitations,id',
        ]);
        $invitation = OfficeInvitation::find($request->input('invitation_id'));
        if ($invitation->status !== 'pending') {
            return sendError('This invitation has already been responded to.', [], 400);
        }
        $invitation->reject();
        $response_data = [
            'invitation_id' => $invitation->id,
            'office_name' => $invitation->office->name,
            'owner_office_name' => $invitation->office->owner->full_name,
            'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
            'status' => $invitation->status,
            'responded_at' => $invitation->responded_at,
            'created_at' => $invitation->created_at,
            'updated_at' => $invitation->updated_at,
        ];
        return sendResponse($response_data, 'Invitation rejected successfully.', 200);
    }
    public function remove_invitation(Request $request)
    {
        $request->validate([
            'invitation_id' => 'required|uuid|exists:office_invitations,id',
        ]);
        $invitation = OfficeInvitation::find($request->input('invitation_id'));
        $invitation->cancelled();
        $response_data = [
            'invitation_id' => $invitation->id,
            'office_name' => $invitation->office->name,
            'owner_office_name' => $invitation->office->owner->full_name,
            'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
            'status' => $invitation->status,
            'responded_at' => $invitation->responded_at,
            'created_at' => $invitation->created_at,
            'updated_at' => $invitation->updated_at,
        ];
        return sendResponse($response_data, 'Invitation removed successfully.', 200);
    }
    // public function remove_lawer_from_office(Request $request)
    // {
    //     $request->validate([
    //         'lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
    //     ]);
    //     $lawer_id = auth('sanctum')->user()->id;
    //     $lawer_profile = LawerProfile::where('user_id', $lawer_id)->first();
    //     $lawer_office = Office::where('owner_lawyer_profile_id', $lawer_profile->id)->first();
    //     // process the conflict that lawer try to remove another lawer from his office but he is not the owner of the office
    //     if (!$lawer_office) {
    //         return sendError('You are not the owner of any office.', [], 400);
    //     }
    //     // process the conflict that lawer try to remove himself from his own office
    //     if ($request->input('lawer_profile_id') === $lawer_profile->id) {
    //         return sendError('You cannot remove yourself from your office.', [], 400);
    //     }
    //     $lawer_office->employees()->detach($request->input('lawer_profile_id'));
    //     $removed_lawer_data = LawerProfile::find($request->input('lawer_profile_id'));
    //     $lawyer = [
    //         'id' => $removed_lawer_data->id,
    //         'full_name' => $removed_lawer_data->full_name,
    //         'national_id' => $removed_lawer_data->national_id,
    //     ];
    //     return sendResponse($lawyer, 'Lawer removed from office successfully.', 200);
    // }
    public function get_all_lawers_in_my_office(Request $request)
    {
        $lawer = auth('sanctum')->user();
        $lawer_profile = LawerProfile::where('user_id', $lawer->id)->first();
        if (!$lawer_profile) {
            return sendError('Lawyer profile not found.', [], 404);
        }
        $office = Office::where('owner_lawyer_profile_id', $lawer_profile->id)->first();
        if (!$office) {
            return sendError('Office not found.', [], 404);
        }
        $lawyers = $office->employees()->get();
        $lawyers = $lawyers->map(function ($lawyer) {
            return [
                'id' => $lawyer->id,
                'full_name' => $lawyer->full_name,
                'national_id' => $lawyer->national_id,
                'syndicate_card_number' => $lawyer->syndicate_card_number,
                'syndicate_branch' => $lawyer->syndicate_branch,
                'birth_date' => $lawyer->birth_date,
                'birth_place' => $lawyer->birth_place,
                'father_name' => $lawyer->father_name,
                'mother_name' => $lawyer->mother_name,
                'mother_family_name' => $lawyer->mother_family_name,
            ];
        });
        return sendResponse($lawyers, 'All lawyers in office retrieved successfully.', 200);
    }
}
