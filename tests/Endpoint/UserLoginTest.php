<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function testItReturnsTheLoggedInUserWithToken()
    {
        $credentials = [
            'email' => 'test@email.com',
            'password' => 'password',
        ];
        $user = User::factory()->create($credentials);

        $response = $this->postJson('/api/users/login', ['user' => $credentials]);

        $this->assertAuthenticatedAs($user);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->hasAll(['user.token', 'user.bio', 'user.image'])
                ->missing('user.password')
        );
    }

    public function testItReturnsValidationErrors()
    {
        $data = [
            'user' => [
                'email' => 'test@email.com',
            ],
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('user.password');
    }
}
