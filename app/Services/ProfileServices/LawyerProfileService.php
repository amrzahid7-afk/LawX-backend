<?php

namespace App\Services\ProfileServices;

use App\Contracts\ProfileDataBuilderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LawyerProfileService implements ProfileDataBuilderInterface
{
    public function build($user): array
    {
        $profile = $user->lawyerProfile;

        if (!$profile) {
            throw new \Exception('This user does not have a lawyer profile.');
        }
        $data = [
            'full_name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => $user->avatar_path,
            'bio' => $user->bio,
            'experience_years' => $user->experience_years,
            'national_id' => $profile->national_id,
            'syndicate_card_number' => $profile->syndicate_card_number,
            'syndicate_branch' => $profile->syndicate_branch,
            'lawyer_state' => $profile->lawyer_state,
            'legal_categories' => $user->legalCategories->pluck('name')->toArray(),
            'reviews_comments' =>  $user->reviewsReceived->map(function ($review) {
                return [
                    'comment' =>  $review->comment ?? null,
                    'client' => $review->client->full_name ?? null
                ];
            }),
            'review_stats' => $this->getReviewStats($user->id),
        ];

        // ========== التفرع حسب نوع المحامي ==========

        if ($profile->lawyer_state === 'trainee') {
            $data['is_trainee'] = true;

            // تعديل 2: الوصول لاسم المشرف عبر يوزره ->user->full_name
            $data['supervisor_name'] = $profile->supervisor?->user?->full_name;

            $data['approved_independent_at'] = $profile->trainee_approved_independent_at;

            if ($profile->employed_in_office_id) {
                $data['employed_in_office'] = [
                    'is_employed' => true,
                    'office_name' => $profile->employedOffice?->name,
                ];
            }
        } elseif ($profile->ownedOffice && $profile->ownedOffice->status === 'active') {
            $data['is_office_owner'] = true;
            $data['office_details'] = [
                'name' => $profile->ownedOffice->name,
                'status' => $profile->ownedOffice->status,

                // تعديل 3: وضع حماية لعدم حدوث خطأ إذا لم تكن العلاقة موجودة بعد
                'employees_count' => method_exists($profile->ownedOffice, 'employees')
                    ? $profile->ownedOffice->employees()->count()
                    : 0,
            ];
        } else {
            $data['is_employed'] = true;
            $data['office_name'] = $profile->employedOffice?->name;
            $data['base_salary'] = $profile->base_salary;
        }

        return $data;
    }
    public function update($user, $request)
    {
        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // delete last avatar
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $avatarPath = Storage::disk('public')->url($path);
            $user->update(['avatar_path' => $avatarPath]);
        }
        $user->update([
            'email' => $request->input('email') ?? $user->email,
            'phone' => $request->input('phone') ??  $user->phone,
            'address' => $request->input('address') ?? $user->address,
            'city' => $request->input('city') ?? $user->city,
            'lat' => $request->input('lat') ?? $user->lat,
            'lng' => $request->input('lng') ?? $user->lng,
            'password' => $request->input('password') ? Hash::make($request->input('password')) : $user->password

        ]);
    }
    private function getReviewStats($lawyerId): array
    {
        $cachedStats = Cache::get("lawyer_ratings_stats_{$lawyerId}");

        return $cachedStats ? [
            'overall' => $cachedStats['overall'],
            'professionalism' => $cachedStats['professionalism'],
            'communication' => $cachedStats['communication'],
            'result' => $cachedStats['result'],
            'total_reviews' => $cachedStats['total'],
        ] : [
            'overall' => 0.0,
            'professionalism' => 0.0,
            'communication' => 0.0,
            'result' => 0.0,
            'total_reviews' => 0
        ];
    }
}
