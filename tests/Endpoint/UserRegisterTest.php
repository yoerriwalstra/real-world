<?php

namespace Tests\Endpoint;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testItReturnsTheRegisteredUserWithToken()
    {
        $newUser = [
            'user' => [
                'username' => 'test',
                'email' => 'test@email.com',
                'password' => 'password',
            ],
        ];

        $response = $this->postJson('/api/users', $newUser);

        $response->assertCreated();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->hasAll(['user.token', 'user.bio', 'user.image'])
                ->missing('user.password')
        );
    }

    public function testItReturnsValidationErrors()
    {
        $noEmailUser = [
            'user' => [
                'username' => 'test',
                'password' => 'password',
            ],
        ];

        $response = $this->postJson('/api/users', $noEmailUser);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('user.email');
    }
}
