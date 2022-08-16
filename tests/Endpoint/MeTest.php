<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MeTest extends TestCase
{
    use RefreshDatabase;

    public function testItThrowsUnauthenticatedException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthenticationException::class);

        $this->getJson('/api/user');
    }

    public function testItReturnsUnauthenticated()
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testItReturnsTheLoggedInUserWithToken()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create([
            'email' => 'test@email.com',
        ]);

        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json
                ->hasAll(['user.token', 'user.bio', 'user.image'])
                ->missing('user.password')
        );
    }
}
