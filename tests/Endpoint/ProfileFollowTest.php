<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileFollowTest extends TestCase
{
    use RefreshDatabase;

    public Authenticatable $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['username' => 'username']);
    }

    public function testItReturnsUnauthenticated()
    {
        $response = $this->postJson('/api/profiles/nonExistentUsername/follow');

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testItThrowsModelNotFoundException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(ModelNotFoundException::class);

        $this->actingAs($this->user)->postJson('/api/profiles/nonExistentUsername/follow');
    }

    public function testItReturnsNotFound()
    {
        $response = $this->actingAs($this->user)->postJson('/api/profiles/nonExistentUsername/follow');

        $response->assertNotFound();
        $response->assertJson(['message' => 'Profile not found']);
    }

    public function testItReturnsForbiddenWhenUserTriesFollowingSelf()
    {
        $response = $this->actingAs($this->user)->postJson("/api/profiles/{$this->user->username}/follow");

        $response->assertForbidden();
    }

    public function testItReturnsTheFollowedProfile()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create(['username' => 'user2']);

        $response = $this->actingAs($this->user)->postJson("/api/profiles/{$user->username}/follow");

        $response->assertOk();
        $response->assertJson([
            'profile' => [
                'username' => $user->username,
                'bio' => $user->bio,
                'image' => $user->image,
                'following' => true,
            ],
        ]);
    }

    public function testItCreatesTheRelationBetweenFollowerAndFollowed()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create(['username' => 'user2']);

        $this->assertDatabaseCount('followers', 0);

        $this->actingAs($this->user)->postJson("/api/profiles/{$user->username}/follow");

        $this->assertDatabaseCount('followers', 1);
        $this->assertDatabaseHas(
            'followers',
            ['follower_id' => $this->user->id, 'followed_id' => $user->id]
        );
    }
}
