<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnfollowProfileTest extends TestCase
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
        $response = $this->deleteJson('/api/profiles/nonExistentUsername/follow');

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testItThrowsModelNotFoundException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(ModelNotFoundException::class);

        $this->actingAs($this->user)->deleteJson('/api/profiles/nonExistentUsername/follow');
    }

    public function testItReturnsNotFound()
    {
        $response = $this->actingAs($this->user)->deleteJson('/api/profiles/nonExistentUsername/follow');

        $response->assertNotFound();
        $response->assertJson(['message' => 'Profile not found']);
    }

    public function testItReturnsTheUnfollowedProfile()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create(['username' => 'user2']);

        $response = $this->actingAs($this->user)->deleteJson("/api/profiles/{$user->username}/follow");

        $response->assertOk();
        $response->assertJson([
            'profile' => [
                'username' => $user->username,
                'bio' => $user->bio,
                'image' => $user->image,
                'following' => false,
            ],
        ]);
    }

    public function testItDeletesTheRelationBetweenFollowerAndFollowed()
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create(['username' => 'user2']);

        // create follower relationship
        $this->user->follows()->attach($user->id);

        $this->assertDatabaseCount('followers', 1);

        $this->actingAs($this->user)->deleteJson("/api/profiles/{$user->username}/follow");

        $this->assertDatabaseCount('followers', 0);
        $this->assertDatabaseMissing(
            'followers',
            ['follower_id' => $this->user->id, 'followed_id' => $user->id]
        );
    }
}
