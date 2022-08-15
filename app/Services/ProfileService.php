<?php

namespace App\Services;

use App\Models\User;

class ProfileService
{
    public function isAuthUserFollowing(User $profile): bool
    {
        return $profile->followed->firstWhere(fn (User $user) => $user->id === auth()->id()) !== null;
    }
}
