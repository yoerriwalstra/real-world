<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService, private UserService $userService)
    {
    }

    public function get(string $username)
    {
        $profile = $this->userService->firstWhere('username', $username);
        if (!$profile) {
            throw new ModelNotFoundException('Profile not found');
        }

        return new ProfileResource($profile);
    }

    public function follow(string $username)
    {
        $profile = $this->userService->firstWhere('username', $username);
        if (!$profile) {
            throw new ModelNotFoundException('Profile not found');
        }

        // early exit if authenticated user is already following profile to prevent causing SQL duplicate key violation
        if ($this->profileService->isAuthUserFollowing($profile)) {
            return (new ProfileResource($profile));
        }

        auth()->user()->follows()->attach($profile->id);

        $profile = $profile->fresh(['followed']);

        return new ProfileResource($profile);
    }

    public function unfollow(string $username)
    {
        $profile = $this->userService->firstWhere('username', $username);
        if (!$profile) {
            throw new ModelNotFoundException('Profile not found');
        }

        auth()->user()->follows()->detach($profile->id);

        $profile = $profile->fresh(['followed']);

        return new ProfileResource($profile);
    }
}
