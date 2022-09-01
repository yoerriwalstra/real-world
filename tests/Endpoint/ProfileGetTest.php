<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProfileGetTest extends TestCase
{
    use RefreshDatabase;

    public function testItThrowsModelNotFoundException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(ModelNotFoundException::class);

        $this->getJson('/api/profiles/nonExistentUsername');
    }

    public function testItReturnsNotFound()
    {
        $response = $this->getJson('/api/profiles/nonExistentUsername');

        $response->assertNotFound();
        $response->assertJson(['message' => 'Profile not found']);
    }

    public function testItReturnsTheProfile()
    {
        User::factory()->create([
            'username' => 'existingUsername',
        ]);

        $response = $this->getJson('/api/profiles/existingUsername');

        $response->assertOk();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->hasAll(['profile.username', 'profile.bio', 'profile.image', 'profile.following'])
        );
    }
}
