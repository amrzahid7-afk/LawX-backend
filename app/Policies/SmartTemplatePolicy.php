<?php

namespace App\Policies;

use App\Models\User;

class SmartTemplatePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function create_template(User $user)
    {
        return $user->role->slug == 'syndicate';
    }
    public function update_template(User $user)
    {
        return $user->role->slug == 'syndicate';
    }
    public function get_templates(User $user)
    {
        return $user->role->slug == 'syndicate' || $user->role->slug == 'lawyer';
    }
    public function show_template_details(User $user)
    {
        return $user->role->slug == 'syndicate' || $user->role->slug == 'lawyer';
    }
    public function delete_template(User $user)
    {
        return $user->role->slug == 'syndicate';
    }
    public function generate_template(User $user)
    {
        return $user->role->slug == 'syndicate' || $user->role->slug == 'lawyer';
    }
}
