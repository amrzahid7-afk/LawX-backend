<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LawyerProfileController extends Controller
{
    public function getLawyerProfileData()
    {
        // dd("I am in Contoller");
        try {
            $owned_office = false;
            $employed_in_office = false;
            $user = auth('sanctum')->user();
            // dd($user);
            if (!$user) {
                return sendError('Unauthorized', ['message' => 'User not authenticated'], 401);
            }
            $profile = $user->lawyerProfile;

            if (!$profile) {
                return sendError(
                    'Validation Error',
                    ['message' => 'This user does not have a lawyer profile.'],
                    404
                );
            }
            if ($profile->lawyer_state != 'trainee') {
                $owned_office = !is_null($profile->ownedOffice->status) && $profile->ownedOffice->status === 'active';
            }
            $employed_in_office = !is_null($profile->employedOffice);
            $lawer_profile_data = [
                'lawer_name'         => $user->full_name ?? null,
                'lawer_national_id'  => $profile->national_id ?? null,
                'lawer_email'        => $user->email ?? null,
                'lawer_phone'        => $user->phone ?? null,
                'lawer_address'      => $user->address ?? null,
                'lawer_state'        => $profile->lawyer_state ?? null,
                'lawer_profile_id'   => $profile->id ?? null,
                'Employed_In_Office' => $employed_in_office,
                'Has_Office'         => $owned_office,
            ];
            return sendResponse($lawer_profile_data, 'Lawyer Profile Data Fetched Successfully', 200);
        } catch (\Throwable $th) {
            // dd($th);
            return sendError('Internal Server Error', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    // public function getLawyerProfileData()
    // {
    //     try {
    //         $lawer = User::with([
    //             'lawyerProfile',
    //             'lawyerProfile.employedOffice',
    //             'lawyerProfile.ownedOffice'
    //         ])->find(auth('sanctum')->id());

    //         if (!$lawer->lawyerProfile) {
    //             return sendError('Validation Error', ['message' => 'this User An Registered in the platform yet'], 404);
    //         }
    //         $lawer_profile_data = [
    //             'lawer_name'         => $lawer->full_name ?? null,
    //             'lawer_national_id'  => $lawer->lawyerProfile->national_id ?? null,
    //             'lawer_email'        => $lawer->email ?? null,
    //             'lawer_phone'        => $lawer->phone ?? null, // استخدام حماية في حال عدم وجود الحقل
    //             'lawer_address'      => $lawer->address ?? null,
    //             'lawer_state'        => $lawer->lawyerProfile->lawyer_state ?? null,
    //             'lawer_profile_id'   => $lawer->lawyerProfile->id ?? null,
    //             'Employed_In_Office' => (bool) $lawer->lawyerProfile->employedOffice ?? null,
    //             'Has_Office'         => (bool) $lawer->lawyerProfile->ownedOffice ?? null, // فحص امتلاك المكتب بالشكل الصحيح
    //         ];
    //         return sendResponse($lawer_profile_data, 'Lawyer Profile Data Fetched Successfully', 200);
    //     } catch (\Throwable $th) {
    //         return  sendError('Server Error ', ['message' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()], 500);
    //     }
    // }
}
