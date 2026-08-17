<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// use App\Services\Profile\ProfileService;
use App\Services\ProfileServices\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function getMyProfile()
    {
        try {
            // get user 
            $user = auth('sanctum')->user();
            // call service
            $profileData = $this->profileService->getMyProfile($user);
            // send resposne 
            return sendResponse($profileData, 'Profile Data Fetched Successfully', 200);
        } catch (\Throwable $th) {
            return sendError('Internal Server Error', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
    public function update_my_profile(Request $request)
    {
        try {
            $validated_data = Validator::make(
                $request->all(),
                [
                    'email' => 'email|unique:users,email|nullable',
                    'phone' => 'string|unique:users,phone|nullable',
                    'avatar' => 'image|mimes:jpeg,png,jpg,webp|nullable',
                    'address' => 'string|nullable',
                    'city' => 'string|nullable',
                    'lat' => 'numeric|nullable',
                    'lng' => 'numeric|nullable',
                    'password' => 'string|min:8|confirmed|nullable',
                ],
                [
                    'email.email' => 'The email must be a valid email address.',
                    'phone.string' => 'The phone must be a string.',
                    'avatar.image' => 'The avatar must be an image file.',
                    'avatar.mimes' => 'The avatar must be a file of type: jpeg, png, jpg, webp.',
                    'avatar.max' => 'The avatar may not be greater than 2MB.',
                    'address.string' => 'The address must be a string.',
                    'city.string' => 'The city must be a string.',
                    'lat.numeric' => 'The latitude must be a number.',
                    'lng.numeric' => 'The longitude must be a number.',
                    'password.string' => 'The password must be a string.',
                    'password.min' => 'The password must be at least 8 characters.',
                    'password.confirmed' => 'The password confirmation does not match.',
                ]
            );
            if ($validated_data->fails()) {
                return sendError('Validation Error', $validated_data->errors()->toArray(), 422);
            }
            // get user 
            $user = auth('sanctum')->user();
            // call service
            $profileData = $this->profileService->updateMyProfile($user, $request);
            // send resposne 
            return sendResponse($profileData, 'Profile Data Updated Successfully', 200);
        } catch (\Throwable $th) {
            return sendError('Internal Server Error', [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ], 500);
        }
    }
}
// Old Profile Controller 
// namespace App\Http\Controllers;

// use App\Models\Review;
// use App\Models\User;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Cache;

// class LawyerProfileController extends Controller
// {
//     public function getLawyerProfileData()
//     {
//         // dd("I am in Contoller");
//         try {
//             $owned_office = false;
//             $employed_in_office = false;
//             $user = auth('sanctum')->user();
//             // dd($user);
//             if (!$user) {
//                 return sendError('Unauthorized', ['message' => 'User not authenticated'], 401);
//             }
//             $profile = $user->lawyerProfile;

//             if (!$profile) {
//                 return sendError(
//                     'Validation Error',
//                     ['message' => 'This user does not have a lawyer profile.'],
//                     404
//                 );
//             }
//             if ($profile->lawyer_state != 'trainee') {
//                 $owned_office = !is_null($profile->ownedOffice->status) && $profile->ownedOffice->status === 'active';
//             }
//             $employed_in_office = !is_null($profile->employedOffice);
//             $lawer_profile_data = [
//                 'lawer_name'         => $user->full_name ?? null,
//                 'lawer_national_id'  => $profile->national_id ?? null,
//                 'lawer_email'        => $user->email ?? null,
//                 'lawer_phone'        => $user->phone ?? null,
//                 'lawer_address'      => $user->address ?? null,
//                 'lawer_state'        => $profile->lawyer_state ?? null,
//                 'lawer_profile_id'   => $profile->id ?? null,
//                 'Employed_In_Office' => $employed_in_office,
//                 'Has_Office'         => $owned_office,
//             ];
//             return sendResponse($lawer_profile_data, 'Lawyer Profile Data Fetched Successfully', 200);
//         } catch (\Throwable $th) {
//             // dd($th);
//             return sendError('Internal Server Error', [
//                 'message' => $th->getMessage(),
//                 'file'    => $th->getFile(),
//                 'line'    => $th->getLine()
//             ], 500);
//         }
//     }
//     public function getMyReviewStats()
//     {
//         try {
//             $lawyerId = auth('sanctum')->user()->id;

//             // ==========================================
//             // تنفيذ US-05: جلب الإحصائيات التفصيلية من الكاش
//             // ==========================================
//             $cachedStats = Cache::get("lawyer_ratings_stats_{$lawyerId}");

//             // إذا كان هناك تقييمات، نرجع البيانات من الكاش. وإذا لم يكن هناك أي تقييم بعد، نرجع أصفار
//             $ratingBreakdown = $cachedStats ? [
//                 'overall'        => $cachedStats['overall'],
//                 'professionalism' => $cachedStats['professionalism'],
//                 'communication'  => $cachedStats['communication'],
//                 'result'         => $cachedStats['result'],
//                 'total_reviews'  => $cachedStats['total']
//             ] : [
//                 'overall'        => 0.0,
//                 'professionalism' => 0.0,
//                 'communication'  => 0.0,
//                 'result'         => 0.0,
//                 'total_reviews'  => 0
//             ];

//             // ==========================================
//             // تنفيذ US-06: جلب التقييمات الفردية مع إخفاء هوية العميل
//             // ==========================================
//             $reviews = Review::where('lawyer_id', $lawyerId)
//                 ->where('is_hidden', false) // لا نعرض التقييمات المحظورة من الادمن حتى للمحامي
//                 ->latest() // الأحدث أولاً
//                 ->paginate(15); // ترقيم الصفحات (Pagination) لأن المحامي قد يكون لديه مئات التقييمات

//             // تحويل البيانات لإخفاء اسم العميل (بدون استخدام map على الـ pagination نفسها للحفاظ على بيانات الـ meta)
//             $reviewsCollection = $reviews->getCollection()->map(function ($review) {
//                 return [
//                     // تم تجاهل client_name تماماً (تطبيق US-06)
//                     'rating'    => $review->rating,
//                     'comment'   => $review->comment,
//                     // إضافة عنوان القضية بدلاً من اسم العميل كسياق للمحامي ليعرف عن أي قضية يتحدث التقييم
//                     'case_title' => $review->case ? $review->case->title : 'قضية محذوفة',
//                     'date'      => $review->created_at->diffForHumans(), // مثال: "منذ يومين"
//                 ];
//             });

//             // إعادة تعيين الـ collection المعدل داخل الـ paginator
//             $reviews->setCollection($reviewsCollection);
//             return sendResponse(['states' => $ratingBreakdown, 'reviews' => $reviews], 'تم جلب إحصائيات التقييمات بنجاح', 200);
//         } catch (\Exception $e) {
//             return sendError('Internal Server Error',  ["Message" =>  $e->getMessage()], 500);
//         }
//     }

//     // public function getLawyerProfileData()
//     // {
//     //     try {
//     //         $lawer = User::with([
//     //             'lawyerProfile',
//     //             'lawyerProfile.employedOffice',
//     //             'lawyerProfile.ownedOffice'
//     //         ])->find(auth('sanctum')->id());

//     //         if (!$lawer->lawyerProfile) {
//     //             return sendError('Validation Error', ['message' => 'this User An Registered in the platform yet'], 404);
//     //         }
//     //         $lawer_profile_data = [
//     //             'lawer_name'         => $lawer->full_name ?? null,
//     //             'lawer_national_id'  => $lawer->lawyerProfile->national_id ?? null,
//     //             'lawer_email'        => $lawer->email ?? null,
//     //             'lawer_phone'        => $lawer->phone ?? null, // استخدام حماية في حال عدم وجود الحقل
//     //             'lawer_address'      => $lawer->address ?? null,
//     //             'lawer_state'        => $lawer->lawyerProfile->lawyer_state ?? null,
//     //             'lawer_profile_id'   => $lawer->lawyerProfile->id ?? null,
//     //             'Employed_In_Office' => (bool) $lawer->lawyerProfile->employedOffice ?? null,
//     //             'Has_Office'         => (bool) $lawer->lawyerProfile->ownedOffice ?? null, // فحص امتلاك المكتب بالشكل الصحيح
//     //         ];
//     //         return sendResponse($lawer_profile_data, 'Lawyer Profile Data Fetched Successfully', 200);
//     //     } catch (\Throwable $th) {
//     //         return  sendError('Server Error ', ['message' => $th->getMessage(), 'file' => $th->getFile(), 'line' => $th->getLine()], 500);
//     //     }
//     // }
// }
