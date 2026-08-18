<?php
// New Code
namespace App\Http\Controllers;

use App\Http\Requests\ListLawyersRequest;
use App\Models\LawerProfile;
use App\Models\Office;
use App\Models\OfficeInvitation;
use App\Services\ManageSyndicateUsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Validator;

class ManageOfficeController extends Controller
{
    private ManageSyndicateUsersService $manageSyndicateUsersService;
    public function __construct()
    {
        $this->manageSyndicateUsersService = new ManageSyndicateUsersService();
    }

    public function activate_office(Request $request)
    {
        $profile = auth('sanctum')->user()->lawyerProfile;
        $office = Office::where('owner_lawyer_profile_id', $profile->id)->first();
        if (Gate::denies('activate_office', $office)) {
            return sendError('Un Authorized ,You can not activate office.', [], 403);
        }
        if (!$office) {
            return sendError('You do not own an office to activate.', [], 404);
        }

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
            'salary_expected' => 'required|numeric|min:0'
        ]);
        $invited_lawer_profile = LawerProfile::find($request->input('invited_lawyer_profile_id'));
        $profile = auth('sanctum')->user()->lawyerProfile;
        $my_office = Office::where('owner_lawyer_profile_id', $profile->id)->first();
        if (Gate::denies('invite_lawer_to_office', [Office::class, $invited_lawer_profile])) {
            return sendError(' Un Authorized,You can not invite lawyer to office.', [], 403);
        }
        if ($request->input('invited_lawyer_profile_id') === $profile->id) {
            return sendError('Cannot invite yourself.', [], 400);
        }

        // التأكد إنه موظف فعلي بالفعل
        if ($my_office->employees()->where('lawyer_profiles.id', $request->input('invited_lawyer_profile_id'))->exists()) {
            return sendError('Already a member.', [], 400);
        }

        // التأكد إنه ما عنده دعوة معلقة
        if ($my_office->invitations()->where('invited_lawyer_profile_id', $request->input('invited_lawyer_profile_id'))->where('status', 'pending')->exists()) {
            return sendError('Already invited (pending).', [], 400);
        }
        // التأكد إنه لا يملك مكتبه الخاص 
        if (Office::where('owner_lawyer_profile_id', $request->input('invited_lawyer_profile_id'))->where('status', 'active')->exists()) {
            return sendError('This lawyer owns an office and cannot be an employee.', [], 400);
        }

        // التأكد إنه ليس موظف بمكتب ثاني
        if (LawerProfile::where('id', $request->input('invited_lawyer_profile_id'))->whereNotNull('employed_in_office_id')->exists()) {
            return sendError('This lawyer is already employed in another office.', [], 400);
        }

        $invitation = $my_office->invitations()->create([
            'invited_lawyer_profile_id' => $request->input('invited_lawyer_profile_id'),
            'status' => 'pending',
            'invited_by' => auth('sanctum')->user()->lawyerProfile->id,
            'salary_expected' => $request->input('salary_expected'),
        ]);

        return sendResponse($invitation, 'Invitation sent successfully.', 200);
    }

    public function my_sended_invitations(Request $request)
    {

        $profile = auth('sanctum')->user()->lawyerProfile;
        $office = Office::where('owner_lawyer_profile_id', $profile->id)->first();
        // validate that the user is the owner of the office
        if (Gate::denies('my_sended_invitations', $office)) {
            return sendError('You do not own an active office. Activate your office first.', [], 400);
        }
        // Gate::authorize('manage', $office);
        $invitations = $office->invitations()->with('invitedLawyer')->get();

        $response_data = $invitations->map(function ($invitation) {
            return [
                'invitation_id' => $invitation->id,
                'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
                'status' => $invitation->status,
                'responded_at' => $invitation->responded_at,
                'salary_expected' => $invitation->salary_expected,
                'created_at' => $invitation->created_at->format('Y-m-d'),
            ];
        });

        return sendResponse($response_data, 'Retrieved successfully.', 200);
    }
    public function my_received_invitations(Request $request)
    {
        $profile = auth('sanctum')->user()->lawyerProfile;
        $office = Office::where('owner_lawyer_profile_id', $profile->id)
            ->first() ?? null;
        if (Gate::denies('my_received_invitations', Office::class)) {
            return sendError('Un Authorized ,You can not recieve invitations', [], 400);
        }
        // get the invitations
        $invitations = OfficeInvitation::where('invited_lawyer_profile_id', $profile->id)
            ->with('office.owner')
            ->where('status', 'pending')
            ->get();

        $response_data = $invitations->map(function ($invitation) {
            return [
                'invitation_id' => $invitation->id,
                'office_name' => $invitation->office->name,
                'owner_office_name' => $invitation->office->owner->full_name ?? null,
                'status' => $invitation->status,
                'salary_expected' => $invitation->salary_expected,
                'created_at' => $invitation->created_at->format('Y-m-d'),
            ];
        });
        return sendResponse($response_data, 'Retrieved successfully.', 200);
    }

    public function accept_invitation(Request $request)
    {
        $request->validate(['invitation_id' => 'required|uuid|exists:office_invitations,id']);
        $invitation = OfficeInvitation::find($request->input('invitation_id'));
        if (Gate::denies('accept_invitation', [Office::class, $invitation])) {
            return sendError('Unauthorized . the invitation is not for you or its status is not  pending', [], 403);
        }
        $invitation->update([
            'status' => 'accepted',
            'responded_at' => now()->format('Y-m-d H:i:s')
        ]);

        $lawer_profile = LawerProfile::where('id', $invitation->invited_lawyer_profile_id)
            ->update([
                'employed_in_office_id' => $invitation->office_id,
                'base_salary' => $invitation->salary_expected,
            ]);
        // if ($lawer_profile->lawyer_state === 'trainee') {
        //     $lawer_profile->update([
        //         'supervised_by_lawyer_id' => $invitation->office->owner_lawyer_profile_id
        //     ]);
        // }
        return sendResponse($invitation, 'Accepted successfully.', 200);
    }
    public function reject_invitation(Request $request)
    {
        $request->validate(['invitation_id' => 'required|uuid|exists:office_invitations,id']);
        $invitation = OfficeInvitation::find($request->input('invitation_id'));
        if (! Gate::allows('accept_invitation', [Office::class, $invitation])) {
            return sendError('Unauthorized . the invitation is not for you or its status is not  pending', [], 403);
        }
        $invitation->update(['status' => 'rejected', 'responded_at' => now()]);
        return sendResponse($invitation, 'Rejected successfully.', 200);
    }

    public function remove_invitation(Request $request)
    {
        $request->validate(['invitation_id' => 'required|uuid|exists:office_invitations,id']);
        $invitation = OfficeInvitation::find($request->input('invitation_id'));
        if (! Gate::allows('remove_invitation', [Office::class, $invitation])) {
            return sendError('Unauthorized . the invitation is not for you or its status is not  pending', [], 403);
        }
        $invitation->delete();
        return sendResponse(null, 'Invitation removed successfully.', 200);
    }


    public function remove_lawer_from_office(Request $request)
    {
        try {
            // validate data 
            $request->validate(['lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id']);
            // get data 
            // $office = auth('sanctum')->user()->lawyerProfile->ownedOffice;
            $office = auth('sanctum')->user()->lawyerProfile->ownedOffice;
            $employee_profile = LawerProfile::find($request->input('lawer_profile_id'));
            // check permission 
            if (Gate::denies('remove_lawer_from_office', [Office::class, $employee_profile])) {
                return sendError('You can not remove lawyer from office.', [], 404);
            }
            // delete the record in invitation table between employee and Mnager Lawer
            $invitation = OfficeInvitation::where('office_id', $office->id)
                ->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'))
                ->where('status', 'accepted')
                ->first();
            $invitation->delete();
            // update lawyer profile to be not employeed
            LawerProfile::find($request->input('lawer_profile_id'))->update(['employed_in_office_id' => null]);

            return sendResponse(null, 'Lawyer removed successfully.', 200);
        } catch (\Exception $e) {
            return sendError($e->getMessage() . $e->getFile() . $e->getLine(), [], 400);
        }
    }

    // public function remove_lawer_from_office(Request $request)
    // {
    //     try {
    //         $request->validate(['lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id']);

    //         $managerProfile = auth('sanctum')->user()->lawyerProfile;

    //         if (!$managerProfile) {
    //             return sendError('Your account has no lawyer profile.', [], 403);
    //         }

    //         $office = $managerProfile->ownedOffice;

    //         if (!$office) {
    //             return sendError('You do not own an office.', [], 403);
    //         }

    //         $employee_profile = LawerProfile::find($request->input('lawer_profile_id'));

    //         if (Gate::denies('remove_lawer_from_office', [Office::class, $employee_profile])) {
    //             return sendError('You can not remove lawyer from office.', [], 403);
    //         }

    //         $invitation = OfficeInvitation::where('office_id', $office->id)
    //             ->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'))
    //             ->where('status', 'accepted')
    //             ->first();

    //         if (!$invitation) {
    //             return sendError('No active membership found for this lawyer in your office.', [], 404);
    //         }

    //         $invitation->delete();

    //         $employee_profile->update(['employed_in_office_id' => null]);

    //         return sendResponse(null, 'Lawyer removed successfully.', 200);
    //     } catch (\Exception $e) {
    //         return sendError($e->getMessage() . $e->getFile() . $e->getLine(), [], 400);
    //     }
    // }

    public function get_all_lawers_in_my_office(Request $request)
    {
        $profile = auth('sanctum')->user()->lawyerProfile;
        $office = Office::where('owner_lawyer_profile_id', $profile->id)->first();
        if (Gate::denies('get_all_lawers_in_my_office', $office)) {
            return sendError('Unauthorized , you dont have active office , Or you are trainee lawer', [], 403);
        }
        $lawyers = $office->employees()->get()->map(function ($lawyer) {
            return [
                'user_id' => $lawyer->user_id, //  where return Lawer_Profile ID 
                'lawer_profile_id' => $lawyer->id,
                'full_name' => $lawyer->full_name,
                'national_id' => $lawyer->national_id,
                'syndicate_card_number' => $lawyer->syndicate_card_number,
            ];
        });

        return sendResponse($lawyers, 'Retrieved successfully.', 200);
    }
    public function search_for_lawers(ListLawyersRequest $request)
    {
        try {
            // validate data
            $vaidated_data = $request->validated();
            // get office Manager ID 
            $myOfficeId = Office::where('owner_lawyer_profile_id', auth('sanctum')->user()->lawyerProfile->id)->value('id');
            // call service
            $lawyers = $this->manageSyndicateUsersService->getLawyers($vaidated_data, $myOfficeId);
            // send response
            return sendResponse($lawyers, 'Lawyers retrieved successfully.', 200);
        } catch (\Throwable $th) {
            return sendError('Failed to retrieve lawyers.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function edit_salary_for_lawer(Request $request)
    {
        try {
            // validate data
            $vaidated_data = Validator::make($request->all(), [
                'salary' => 'required|numeric',
                'lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
            ], [
                'salary.required' => 'Salary is required.',
                'salary.numeric' => 'Salary must be a number.',
                'lawer_profile_id.required' => 'Lawer profile ID is required.',
                'lawer_profile_id.exists' => 'Lawer profile does not exist.',
            ]);
            // get office Manager ID 
            $lawerProfile = LawerProfile::find($request->input('lawer_profile_id'));
            $lawerProfile->update([
                'base_salary' => $request->input('salary')
            ]);
            // send response
            return sendResponse("Done", 'Update Salary For Lawer ' . $lawerProfile->full_name . ' successfully.', 200);
        } catch (\Throwable $th) {
            return sendError('Failed to retrieve lawyers.', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    // public function search_for_lawers(ListLawyersRequest $request)
    // {
    //     try {
    //         $vaidated_data = $request->validated();
    //         $lawyers = $this->manageSyndicateUsersService->getLawyers($vaidated_data);
    //         return sendResponse($lawyers, 'Lawyers retrieved successfully.', 200);
    //     } catch (\Throwable $th) {
    //         return sendError('Failed to retrieve lawyers.', [
    //             'message' => $th->getMessage(),
    //             'file'    => $th->getFile(),
    //             'line'    => $th->getLine()
    //         ], 500);
    //     }
    // }
}
// old code to be deleted
// namespace App\Http\Controllers;

// use App\Models\LawerProfile;
// use App\Models\Office;
// use App\Models\OfficeInvitation;
// use Illuminate\Http\Request;

// class ManageOfficeController extends Controller
// {
//     public function activate_office(Request $request)
//     {
//         $request->validate([
//             'lawer_profile_id' => 'required|uuid|exists:offices,owner_lawyer_profile_id',
//         ]);
//         $office = Office::where('owner_lawyer_profile_id', $request->input('lawer_profile_id'))->first();
//         if ($office->status === 'active') {
//             return sendError('Office is already active.', [], 400);
//         }
//         $office->status = 'active';
//         $office->save();
//         return sendResponse($office, 'Office activated successfully.', 200);
//     }
//     public function invite_lawer_to_office(Request $request)
//     {
//         $request->validate([
//             'invited_lawyer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
//         ]);
//         $my_office = Office::where('owner_lawyer_profile_id', auth('sanctum')->user()->lawyerProfile->id)->first();
//         // process the conflict that lawer invite him self to his own office
//         if ($request->input('invited_lawyer_profile_id') === $my_office->owner_lawyer_profile_id) {
//             return sendError('Cannot invite yourself to your own office.', [], 400);
//         }
//         // process the conflict that lawer invite another lawer who is already in the office
//         if ($my_office->employees()->where('lawyer_profiles.id', $request->input('invited_lawyer_profile_id'))->exists()) {
//             return sendError('The invited lawyer is already a member of this office.', [], 400);
//         }
//         // process the conflict that lawer invite another lawer who is already invited to the office
//         if ($my_office->invitations()->where('invited_lawyer_profile_id', $request->input('invited_lawyer_profile_id'))->where('status', 'pending')->exists()) {
//             return sendError('The invited lawyer has already been invited to this office.', [], 400);
//         }
//         // process the conflict that lawer invite another lawer who has its one office and he is not allowed to join another office
//         $invitedLawyer = $my_office->invitations()->where('invited_lawyer_profile_id', $request->input('invited_lawyer_profile_id'))->first();
//         if ($invitedLawyer && $invitedLawyer->status === 'accepted') {
//             return sendError('The invited lawyer is already a member of another office.', [], 400);
//         }
//         // check if the office is active or not
//         if ($my_office->status !== 'active') {
//             return sendError('Cannot invite lawyers to a suspended office.', [], 400);
//         }
//         $invitation = $my_office->invitations()->create([
//             'invited_lawyer_profile_id' => $request->input('invited_lawyer_profile_id'),
//             'status' => 'pending',
//         ]);
//         return sendResponse($invitation, 'Invitation sent successfully.', 200);
//     }
//     public function my_sended_invitations(Request $request)
//     {
//         $request->validate([
//             'lawer_profile_id' => 'required|uuid|exists:offices,owner_lawyer_profile_id',
//         ]);
//         $office = Office::where('owner_lawyer_profile_id', $request->input('lawer_profile_id'))->first();
//         $invitations = $office->invitations()->get();
//         $response_data = $invitations->map(function ($invitation) {
//             return [
//                 'invitation_id' => $invitation->id,
//                 'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
//                 'status' => $invitation->status,
//                 'responded_at' => $invitation->responded_at,
//                 'created_at' => $invitation->created_at,
//                 'updated_at' => $invitation->updated_at,
//             ];
//         });
//         return sendResponse($response_data, 'My sended invitations retrieved successfully.', 200);
//     }
//     public function my_received_invitations(Request $request)
//     {
//         $request->validate([
//             'lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
//         ]);
//         $invitations = Office::whereHas('invitations', function ($query) use ($request) {
//             $query->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'));
//         })->with(['invitations' => function ($query) use ($request) {
//             $query->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'));
//         }])->get();
//         $response_data = [];
//         foreach ($invitations as $office) {
//             foreach ($office->invitations as $invitation) {
//                 $response_data[] = [
//                     'invitation_id' => $invitation->id,
//                     'office_name' => $office->name,
//                     'owner_office_name' => $office->owner->full_name,
//                     'status' => $invitation->status,
//                     'responded_at' => $invitation->responded_at,
//                     'created_at' => $invitation->created_at,
//                     'updated_at' => $invitation->updated_at,
//                 ];
//             }
//         }
//         return sendResponse($response_data, 'My received invitations retrieved successfully.', 200);
//     }
//     public function accept_invitation(Request $request)
//     {
//         $request->validate([
//             'invitation_id' => 'required|uuid|exists:office_invitations,id',
//         ]);
//         $invitation = OfficeInvitation::find($request->input('invitation_id'));
//         if ($invitation->status !== 'pending') {
//             return sendError('This invitation has already been responded to.', [], 400);
//         }
//         $invitation->accept();
//         $response_data = [
//             'invitation_id' => $invitation->id,
//             'office_name' => $invitation->office->name,
//             'owner_office_name' => $invitation->office->owner->full_name,
//             'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
//             'status' => $invitation->status,
//             'responded_at' => $invitation->responded_at,
//             'created_at' => $invitation->created_at,
//             'updated_at' => $invitation->updated_at,
//         ];
//         return sendResponse($response_data, 'Invitation accepted successfully.', 200);
//     }
//     public function remove_lawer_from_office(Request $request)
//     {
//         $request->validate([
//             'lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
//         ]);

//         $lawer_id = auth('sanctum')->user()->id;
//         $lawer_profile = LawerProfile::where('user_id', $lawer_id)->first();

//         if (!$lawer_profile) {
//             return sendError('Lawyer profile not found.', [], 404);
//         }

//         $lawer_office = Office::where('owner_lawyer_profile_id', $lawer_profile->id)->first();

//         if (!$lawer_office) {
//             return sendError('You are not the owner of any office.', [], 400);
//         }
//         // proceess the conflict that the 
//         if ($request->input('lawer_profile_id') === $lawer_profile->id) {
//             return sendError('You cannot remove yourself from your office.', [], 400);
//         }

//         // 1. البحث عن الدعوة في الجدول الوسيط التي حالتها 'accepted' فقط
//         $invitation = \App\Models\OfficeInvitation::where('office_id', $lawer_office->id)
//             ->where('invited_lawyer_profile_id', $request->input('lawer_profile_id'))
//             ->where('status', 'accepted')
//             ->first();

//         if (!$invitation) {
//             return sendError('This lawyer is not an active employee in your office.', [], 404);
//         }

//         // 2. حذف سجل الدعوة (أو يمكنك تغيير حالته إلى 'removed' إذا كنت تريد الاحتفاظ بسجل تاريخي)
//         $invitation->delete();

//         // 3. مهم جداً: تفريغ عمود التوظيف في جدول lawyer_profiles
//         $removed_lawer_data = LawerProfile::find($request->input('lawer_profile_id'));
//         $removed_lawer_data->update([
//             'employed_in_office_id' => null
//         ]);

//         $lawyer = [
//             'id' => $removed_lawer_data->id,
//             'full_name' => $removed_lawer_data->full_name,
//             'national_id' => $removed_lawer_data->national_id,
//         ];

//         return sendResponse($lawyer, 'Lawyer removed from office successfully.', 200);
//     }
//     public function reject_invitation(Request $request)
//     {
//         $request->validate([
//             'invitation_id' => 'required|uuid|exists:office_invitations,id',
//         ]);
//         $invitation = OfficeInvitation::find($request->input('invitation_id'));
//         if ($invitation->status !== 'pending') {
//             return sendError('This invitation has already been responded to.', [], 400);
//         }
//         $invitation->reject();
//         $response_data = [
//             'invitation_id' => $invitation->id,
//             'office_name' => $invitation->office->name,
//             'owner_office_name' => $invitation->office->owner->full_name,
//             'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
//             'status' => $invitation->status,
//             'responded_at' => $invitation->responded_at,
//             'created_at' => $invitation->created_at,
//             'updated_at' => $invitation->updated_at,
//         ];
//         return sendResponse($response_data, 'Invitation rejected successfully.', 200);
//     }
//     public function remove_invitation(Request $request)
//     {
//         $request->validate([
//             'invitation_id' => 'required|uuid|exists:office_invitations,id',
//         ]);
//         $invitation = OfficeInvitation::find($request->input('invitation_id'));
//         $invitation->cancelled();
//         $response_data = [
//             'invitation_id' => $invitation->id,
//             'office_name' => $invitation->office->name,
//             'owner_office_name' => $invitation->office->owner->full_name,
//             'Lawer_that_invited_name' => $invitation->invitedLawyer->full_name,
//             'status' => $invitation->status,
//             'responded_at' => $invitation->responded_at,
//             'created_at' => $invitation->created_at,
//             'updated_at' => $invitation->updated_at,
//         ];
//         return sendResponse($response_data, 'Invitation removed successfully.', 200);
//     }
//     // public function remove_lawer_from_office(Request $request)
//     // {
//     //     $request->validate([
//     //         'lawer_profile_id' => 'required|uuid|exists:lawyer_profiles,id',
//     //     ]);
//     //     $lawer_id = auth('sanctum')->user()->id;
//     //     $lawer_profile = LawerProfile::where('user_id', $lawer_id)->first();
//     //     $lawer_office = Office::where('owner_lawyer_profile_id', $lawer_profile->id)->first();
//     //     // process the conflict that lawer try to remove another lawer from his office but he is not the owner of the office
//     //     if (!$lawer_office) {
//     //         return sendError('You are not the owner of any office.', [], 400);
//     //     }
//     //     // process the conflict that lawer try to remove himself from his own office
//     //     if ($request->input('lawer_profile_id') === $lawer_profile->id) {
//     //         return sendError('You cannot remove yourself from your office.', [], 400);
//     //     }
//     //     $lawer_office->employees()->detach($request->input('lawer_profile_id'));
//     //     $removed_lawer_data = LawerProfile::find($request->input('lawer_profile_id'));
//     //     $lawyer = [
//     //         'id' => $removed_lawer_data->id,
//     //         'full_name' => $removed_lawer_data->full_name,
//     //         'national_id' => $removed_lawer_data->national_id,
//     //     ];
//     //     return sendResponse($lawyer, 'Lawer removed from office successfully.', 200);
//     // }
//     public function get_all_lawers_in_my_office(Request $request)
//     {
//         $lawer = auth('sanctum')->user();
//         $lawer_profile = LawerProfile::where('user_id', $lawer->id)->first();
//         if (!$lawer_profile) {
//             return sendError('Lawyer profile not found.', [], 404);
//         }
//         $office = Office::where('owner_lawyer_profile_id', $lawer_profile->id)->first();
//         if (!$office) {
//             return sendError('Office not found.', [], 404);
//         }
//         $lawyers = $office->employees()->get();
//         $lawyers = $lawyers->map(function ($lawyer) {
//             return [
//                 'id' => $lawyer->id,
//                 'full_name' => $lawyer->full_name,
//                 'national_id' => $lawyer->national_id,
//                 'syndicate_card_number' => $lawyer->syndicate_card_number,
//                 'syndicate_branch' => $lawyer->syndicate_branch,
//                 'birth_date' => $lawyer->birth_date,
//                 'birth_place' => $lawyer->birth_place,
//                 'father_name' => $lawyer->father_name,
//                 'mother_name' => $lawyer->mother_name,
//                 'mother_family_name' => $lawyer->mother_family_name,
//             ];
//         });
//         return sendResponse($lawyers, 'All lawyers in office retrieved successfully.', 200);
//     }
// }
