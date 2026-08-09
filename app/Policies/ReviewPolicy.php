<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\CaseModel;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReviewPolicy
{
    use HandlesAuthorization;
    public function give_Rate(User $user, CaseModel $case): bool
    {
        $isClient = $case->clients()->where('user_id', $user->id)->exists();

        if (!$isClient) {
            return false;
        }

        if ($case->status !== 'close') {
            return false;
        }
        $alreadyReviewed = $case->reviews()->where('client_id', $user->id)->exists();

        return !$alreadyReviewed;
    }
}
