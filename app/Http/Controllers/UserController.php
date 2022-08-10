<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function register(RegistrationRequest $request)
    {
        $data = $request->validated();
        $username = data_get($data, 'user.username');
        $email = data_get($data, 'user.email');
        $password = data_get($data, 'user.password');

        $user = $this->userService->create($username, $email, $password);

        $jwt = auth()->attempt(['email' => $email, 'password' => $password]);

        return (new UserResource($user))
            ->additional([
                'user' => ['token' => $jwt]
            ]);
    }
}
