<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function firstWhere(string $attribute, string $value): ?User
    {
        return User::query()->where($attribute, $value)->first();
    }

    public function create(string $username, string $email, string $password): User
    {
        return User::create([
            'username' => $username,
            'email' => $email,
            'password' => $password, // password hashing is done by a mutator on the User model
        ]);
    }

    public function update(int $userId, array $data): User
    {
        $user = User::find($userId);
        $user->fill($data)->save();

        return $user;
    }
}
