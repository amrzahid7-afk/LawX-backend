<?php

namespace App\Services\ProfileServices;

use App\Models\User;
use App\Services\ProfileServices\ClientProfileService;
use App\Services\ProfileServices\LawyerProfileService;
use App\Services\ProfileServices\SyndicateProfileService;
use Illuminate\Http\Request;

class ProfileService
{
    public function getMyProfile(User $user): array
    {
        $roleName = $user->role->slug;
        return match ($roleName) {
            'client'    => app(ClientProfileService::class)->build($user),
            'lawyer'    => app(LawyerProfileService::class)->build($user),
            'syndicate' => app(SyndicateProfileService::class)->build($user),
            default     => throw new \Exception('Unsupported User Role'),
        };
    }
    public function updateMyProfile(User $user,  $request)
    {
        $roleName = $user->role->slug;
        return match ($roleName) {
            'client'    => app(ClientProfileService::class)->update($user, $request),
            'lawyer'    => app(LawyerProfileService::class)->update($user, $request),
            'syndicate' => app(SyndicateProfileService::class)->update($user, $request),
            default     => throw new \Exception('Unsupported User Role'),
        };
    }
}
