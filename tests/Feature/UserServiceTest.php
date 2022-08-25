<?php

namespace Tests\Feature;

use App\Models\User;
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

    public function testItUpdatesAnExistingUserInTheDatabase()
    {
        $user = User::factory()->create();
        $data = [
            'username' => 'updated username',
            'bio' => 'test adding bio to user',
        ];

        $this->userService->update($user->id, $data);

        $this->assertDatabaseHas(
            'users',
            [
                'id' => $user->id,
                'username' => $data['username'],
                'bio' => $data['bio'],
            ]
        );
    }
}
