<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated('user');
        $username = data_get($data, 'username');
        $email = data_get($data, 'email');
        $password = data_get($data, 'password');

        $user = $this->userService->create($username, $email, $password);

        $jwt = auth()->attempt(['email' => $email, 'password' => $password]);

        return $this->userWithToken($user, $jwt)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function login(UserLoginRequest $request)
    {
        $data = $request->validated('user');
        $email = data_get($data, 'email');
        $password = data_get($data, 'password');

        $jwt = auth()->attempt(['email' => $email, 'password' => $password]);
        if (! $jwt) {
            throw new AuthenticationException('Unauthorized');
        }

        return $this->userWithToken(auth()->user(), $jwt);
    }

    public function me(Request $request)
    {
        $jwt = str_replace('Token ', '', $request->header('Authorization'));

        return $this->userWithToken(auth()->user(), $jwt);
    }

    public function update(UserUpdateRequest $request)
    {
        $data = $request->validated('user');

        $updated = $this->userService->update(auth()->id(), $data);
        $jwt = str_replace('Token ', '', $request->header('Authorization'));

        return $this->userWithToken($updated, $jwt);
    }

    private function userWithToken(Authenticatable $user, string $token)
    {
        return (new UserResource($user))
            ->additional([
                'user' => ['token' => $token],
            ]);
    }
}
