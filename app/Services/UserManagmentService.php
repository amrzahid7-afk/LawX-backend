<?php

namespace App\Services;

use App\Enums\RoleSlug;
use App\Mail\ResetPasswordMail;
use App\Mail\Send2FaMail;
use App\Models\LawerProfile;
use App\Models\PasswordResetToken;
use App\Models\Role;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserManagmentService
{
    public function get_offices_or_lawers_available(Request $request)
    {
        // getting lawer & office admin role
        $lawyerRoleId = Role::where('slug', 'lawyer')->first()->id;
        // dd('1');
        $query = User::with('lawyerProfile')
            // ->where('lawyerProfile.lawyer_state', 'independent')
            // ->where('lawyerProfile.is_suspended', false)
            ->whereHas('lawyerProfile', function ($q) {
                $q->where('lawyer_state', 'licensed')
                    ->where('is_suspended', false);
            })
            ->whereIn('role_id', [$lawyerRoleId])
            ->where('is_active', true)
            ->with(['legalCategories'])
            ->withAvg('reviewsReceived as rate', 'rating')
            ->withCount('cases as cases_count');
        // dd('1');

        // getting data with city filter
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }
        // getting data with cases category filter
        if ($request->has('category_id')) {
            $query->whereHas('legalCategories', function ($q) use ($request) {
                $q->where('legal_categories.id', $request->category_id);
            });
        }
        // dd('1');
        //  getting data with rate lawers filter
        if ($request->has('min_rate')) {
            $query->having('rate', '>=', $request->min_rate);
        }
        // dd('1');

        $users = $query->get();
        // dd('1');

        // تنسيق البيانات للإرسال
        $data = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'avatar' => $user->avatar ?? null,
                'rate' => round($user->rate, 1) ?: 0, // التقييم المحسوب
                'status' => $user->is_active ? 'active' : 'inactive',
                'address' => $user->address ?? null,
                // 'office_id' => $user->office_id ?? null, // للمكتب فقط
                'specialists' => $user->legalCategories->pluck('name'), // أسماء التخصصات
                'role' => $user->role->slug // lawyer or office_manager
            ];
        });
        // dd('1');
        // Response
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get lawers , offices successfully',
                'data' => $data
            ],
            'code' => '200'
        ];
    }
    // with get cached rating for lawer or office
    public function getProtofolioForLawerOrOffice($lawyerId)
    {
        // 1. جلب بيانات المحامي (لا نحتاج لتحميل reviewsReceived بالكامل هنا لتخفيف الداتا)
        $lawyer = User::with(['legalCategories', 'cases', 'role'])
            ->findOrFail($lawyerId);

        $experienceYears = Carbon::parse($lawyer->created_at)->diffInYears(Carbon::now());

        $totalCases = $lawyer->cases->count();
        $wonCases = $lawyer->cases->where('status', 'won')->count();
        $percentWon = $totalCases > 0 ? ($wonCases / $totalCases) * 100 : 0;

        $legalCategories = $lawyer->legalCategories->map(function ($cat) {
            return [
                'icon' => $cat->icon,
                'name' => $cat->name
            ];
        })->toArray();

        // ==========================================
        // السحر هنا: استخدام الـ Cache بدل الاستعلامات الثقيلة
        // ==========================================
        $cachedRatings = Cache::get("lawyer_ratings_stats_{$lawyerId}");

        // 2. جلب الآراء الفردية (التعليقات فقط، مع إخفاء اسم العميل للخصوصية US-06)
        // نستخدم where('is_hidden', false) لتنفيذ US-07 (إخفاء التقييمات المسيئة)
        $opinions = $lawyer->reviewsReceived()
            ->where('is_hidden', false)
            ->latest() // ترتيب من الأحدث للأقدم
            ->take(10) // نرسل آخر 10 تعليقات فقط لتخفيف الـ Response (يمكنك إزالة take إذا أردت الكل)
            ->get()
            ->map(function ($review) {
                return [
                    // تم إزالة 'client_name' طبقاً لشرط الخصوصية في User Stories
                    'rate'    => $review->rating,
                    'comment' => $review->comment,
                ];
            })->toArray();

        $data = [
            'lawyer_name'           => $lawyer->full_name,
            'role'                  => $lawyer->role->name ?? 'User',
            'address'               => $lawyer->address,
            'phone_number'          => $lawyer->phone ?? '0968339198',

            // تم استبدال الـ avg() الثقيل بالقيمة المحفوظة في الكاش، مع قيمة افتراضية 0 إذا لم يكن هناك كاش بعد
            'rate'                  => $cachedRatings['overall'] ?? 0,

            // إضافة التقييمات التفصيلية (Multi-criteria) التي اتفقنا عليها في FR-12
            'rating_breakdown'      => $cachedRatings ? [
                'professionalism' => $cachedRatings['professionalism'],
                'communication'   => $cachedRatings['communication'],
                'result'          => $cachedRatings['result'],
                'total_reviews'   => $cachedRatings['total']
            ] : null,

            'experience_years_count' => $experienceYears,
            'number_of_cases'       => $totalCases,
            'percent_of_cases_won'  => number_format($percentWon, 2),
            'legal_categories'      => $legalCategories,
            'bio_for_lawyer'        => $lawyer->bio,
            'opinion_of_clients'    => $opinions, // الآن تحتوي فقط على التعليقات والنجوم
        ];

        if ($lawyer->role_id == 1 || (isset($lawyer->role->name) && strtolower($lawyer->role->name) === 'admin')) {
            $data['location_data'] = [
                'latitude'  => $lawyer->latitude,
                'longitude' => $lawyer->longitude,
            ];
        }

        // sending response 
        return [
            'status' => 'success',
            'data' => [
                'message' => 'get protofolio for lawer or office successfully',
                'data' => $data
            ],
            'code' => '200'
        ];
    }
    // public function getProtofolioForLawerOrOffice($lawyerId)
    // {
    //     $lawyer = User::with(['legalCategories', 'cases', 'reviewsReceived.client', 'role'])
    //         ->findOrFail($lawyerId);

    //     $experienceYears = Carbon::parse($lawyer->created_at)->diffInYears(Carbon::now());

    //     $totalCases = $lawyer->cases->count();
    //     $wonCases = $lawyer->cases->where('status', 'won')->count();
    //     $percentWon = $totalCases > 0 ? ($wonCases / $totalCases) * 100 : 0;

    //     $legalCategories =  $lawyer->legalCategories->map(function ($cat) {
    //         return [
    //             'icon' => $cat->icon,
    //             'name' => $cat->name
    //         ];
    //     })->toArray();


    //     $opinions = $lawyer->reviewsReceived->map(function ($review) {
    //         return [
    //             'client_name' => $review->client ? $review->client->full_name : 'Unknown',
    //             'rate'        => $review->rating,
    //             'comment'     => $review->comment,
    //         ];
    //     })->toArray();

    //     $data = [
    //         'lawyer_name'           => $lawyer->full_name,
    //         'role'                  => $lawyer->role->name ?? 'User',
    //         'address'               => $lawyer->address,
    //         'phone_number'          => $lawyer->phone ?? '0968339198',
    //         'rate'                  => $lawyer->reviewsReceived()->avg('rating') ?? 0,
    //         'experience_years_count' => $experienceYears,
    //         'number_of_cases'       => $totalCases,
    //         'percent_of_cases_won'  => number_format($percentWon, 2),
    //         'legal_categories'      => $legalCategories,
    //         'bio_for_lawyer'        => $lawyer->bio,
    //         'opinion_of_clients'    => $opinions,
    //     ];

    //     if ($lawyer->role_id == 1 || (isset($lawyer->role->name) && strtolower($lawyer->role->name) === 'admin')) {
    //         $data['location_data'] = [
    //             'latitude'  => $lawyer->latitude,
    //             'longitude' => $lawyer->longitude,
    //         ];
    //     }

    //     // sending response 
    //     return [
    //         'status' => 'success',
    //         'data' => [
    //             'message' => 'get protofolio for lawer or office successfully',
    //             'data' => $data
    //         ],
    //         'code' => '200'
    //     ];
    // }
}
