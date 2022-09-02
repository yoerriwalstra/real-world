<?php

namespace Tests\Endpoint;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleCreateTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesArticle()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();
        $data = [
            'article' => [
                'title' => 'new title',
                'description' => 'new description',
                'body' => 'new body',
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/articles', $data);

        $response->assertCreated();
        $response->assertJson([
            'article' => [
                'slug' => Str::slug($data['article']['title']),
                'title' => $data['article']['title'],
                'description' => $data['article']['description'],
                'body' => $data['article']['body'],
                'tagList' => [],
                'author' => [
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ],

            ],
        ]);
    }

    public function testItCreatesArticleWithTags()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();
        $data = [
            'article' => [
                'title' => 'new title',
                'description' => 'new description',
                'body' => 'new body',
                'tagList' => ['tag one', 'tag two'],
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/articles', $data);
        $response->assertCreated();
        $response->assertJson([
            'article' => [
                'slug' => Str::slug($data['article']['title']),
                'title' => $data['article']['title'],
                'description' => $data['article']['description'],
                'body' => $data['article']['body'],
                'tagList' => $data['article']['tagList'],
                'author' => [
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ],
            ],
        ]);

        $this->assertDatabaseHas('tags', [
            'name' => 'tag one',
            'name' => 'tag two',
        ]);
    }

    public function testItThrowsAuthenticationException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthenticationException::class);

        $this->postJson('/api/articles', []);
    }

    public function testItReturnsUnauthorizedMessage()
    {
        $response = $this->postJson('/api/articles', []);

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testItReturnsValidationErrors()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/articles', []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['article.title', 'article.description', 'article.body']);
    }
}
