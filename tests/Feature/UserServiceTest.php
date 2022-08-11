<?php

namespace Tests\Feature;

use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $userService;

    public function setUp(): void
    {
        $this->userService = new UserService();

        parent::setUp();
    }

    public function testItSavesUserToTheDatabase()
    {
        $newUser = [
            'username' => 'test',
            'email' => 'test@email.com',
            'password' => 'password',
        ];

        $this->assertDatabaseMissing('users', ['email' => $newUser['email']]);

        $this->userService->create($newUser['username'], $newUser['email'], $newUser['password']);

        $this->assertDatabaseHas('users', ['email' => $newUser['email']]);
    }
}
