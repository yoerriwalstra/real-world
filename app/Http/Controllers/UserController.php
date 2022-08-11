<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\RegisterResource;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;

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

        return (new RegisterResource($user))
            ->additional([
                'user' => ['token' => $jwt]
            ]);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $email = data_get($data, 'user.email');
        $password = data_get($data, 'user.password');

        $jwt = auth()->attempt(['email' => $email, 'password' => $password]);
        if (!$jwt) {
            throw new AuthenticationException('Unauthorized');
        }

        return (new UserResource(auth()->user()))
            ->additional([
                'user' => ['token' => $jwt]
            ]);
    }
}
