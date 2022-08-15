<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    public function testItReturnsTheUpdatedUser()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();

        $data = [
            'user' => [
                'email' => 'new@email.com',
            ]
        ];

        $response = $this->actingAs($user)->putJson('/api/user', $data);

        $response->assertOk();
        $response->assertJson($data);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json
                ->hasAll(['user.token', 'user.bio', 'user.image'])
                ->missing('user.password')
        );
    }

    public function testItReturnsUnauthenticated()
    {
        $response = $this->putJson('/api/user');

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
