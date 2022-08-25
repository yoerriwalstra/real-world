<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testItReturnsTheLoggedInUserWithToken()
    {
        $credentials = [
            'email' => 'test@email.com',
            'password' => 'password',
        ];
        User::factory()->create($credentials);

        $response = $this->postJson('/api/users/login', ['user' => $credentials]);

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->hasAll(['user.token', 'user.bio', 'user.image'])
                ->missing('user.password')
        );
    }

    public function testItThrowsAuthenticationException()
    {
        $this->withoutExceptionHandling();

        $data = [
            'user' => [
                'email' => 'non-existent@user.com',
                'password' => 'password',
            ],
        ];

        $this->expectException(AuthenticationException::class);

        $this->postJson('/api/users/login', $data);
    }

    public function testItReturnsUnauthorizedMessage()
    {
        $data = [
            'user' => [
                'email' => 'non-existent@user.com',
                'password' => 'password',
            ],
        ];

        $response = $this->postJson('/api/users/login', $data);

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthorized']);
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
