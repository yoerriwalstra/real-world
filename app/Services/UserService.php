<?php

namespace App\Services;

use App\Models\User;
use Hash;

class UserService
{
    public function create(string $username, string $email, string $password)
    {
        return User::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
