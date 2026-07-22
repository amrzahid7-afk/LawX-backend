<?php

namespace App\Policies;

use App\Models\SignatureRequest;
use App\Models\Signer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SignatureRequestPolicy
{
    public function lawyerRequestSignature(User $user): bool
    {
        return true;
    }
    public function getSigningUrl(User $user, Signer $signer): bool
    {
        return $signer->user_id === $user->id && $signer->status === 'pending';
    }

    public function getLawyerFiles(User $user): bool
    {
        return true;
    }

    public function getClientFiles(User $user): bool
    {
        return true;
    }

    public function getSyndicateFiles(User $user): bool
    {
        return true;
    }

    public function reviewFile(User $user, Signer $signer): bool
    {
        return $signer->user_id === $user->id;
    }
}
