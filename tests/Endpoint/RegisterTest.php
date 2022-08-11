<?php

namespace Tests\Endpoint;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testItReturnsTheRegisteredUserWithToken()
    {
        $newUser = [
            'user' => [
                'username' => 'test',
                'email' => 'test@email.com',
                'password' => 'password',
            ]
        ];

        $response = $this->post('/api/users', $newUser);

        $response->assertStatus(JsonResponse::HTTP_CREATED);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json
                ->has('user.token')
                ->missing('user.password')
        );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testItReturnsValidationErrors()
    {
        $noEmailUser = [
            'user' => [
                'username' => 'test',
                'password' => 'password',
            ]
        ];

        $response = $this->postJson('/api/users', $noEmailUser);

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('user.email');
    }
}
