<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
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

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $username = data_get($data, 'user.username');
        $email = data_get($data, 'user.email');
        $password = data_get($data, 'user.password');

        $user = $this->userService->create($username, $email, $password);

        $jwt = auth()->attempt(['email' => $email, 'password' => $password]);

        return $this->userWithToken($user, $jwt)
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $email = data_get($data, 'user.email');
        $password = data_get($data, 'user.password');

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

    public function update(UpdateUserRequest $request)
    {
        $data = $request->validated();

        $updated = $this->userService->update(auth()->id(), $data['user']);
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
