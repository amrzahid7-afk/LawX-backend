<?php

namespace App\Policies;

use App\Enums\RoleSlug;
use App\Models\User;

class LegislationPolicy
{
    public function create_law(User $user): bool
    {
        return $user->hasRole(RoleSlug::Syndicate);
    }

    public function update_law(User $user): bool
    {
        return $user->hasRole(RoleSlug::Syndicate);
    }

    public function delete_law(User $user): bool
    {
        return $user->hasRole(RoleSlug::Syndicate);
    }
}
