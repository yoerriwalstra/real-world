<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleUpdateTest extends TestCase
{
    use RefreshDatabase;

    private Authenticatable|User $user;

    private Article $article;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['username' => 'username']);

        $this->article = Article::factory()
            ->for($this->user, 'author')
            ->has(Tag::factory()->count(2))
            ->create(['title' => 'easy title']);
    }

    public function testItUpdatesTheArticle()
    {
        $data = [
            'article' => [
                'description' => 'updated description',
            ],
        ];

        $response = $this->actingAs($this->user)->putJson('/api/articles/easy-title', $data);

        $response->assertOk();
        $response->assertJson([
            'article' => [
                'slug' => $this->article->slug,
                'title' => $this->article->title,
                'description' => $data['article']['description'],
                'body' => $this->article->body,
                'tagList' => Tag::all()->pluck('name')->toArray(),
                'author' => [
                    'username' => $this->user->username,
                    'bio' => $this->user->bio,
                    'image' => $this->user->image,
                    'following' => false,
                ],
            ],
        ]);
    }

    public function testItUpdatesTheSlugWhenTitleChanges()
    {
        $data = [
            'article' => [
                'title' => 'updated title',
            ],
        ];

        $response = $this->actingAs($this->user)->putJson('/api/articles/easy-title', $data);

        $response->assertOk();
        $response->assertJson([
            'article' => [
                'slug' => Str::slug($data['article']['title']),
                'title' => $data['article']['title'],
            ],
        ]);
    }

    public function testItCreatesNewTags()
    {
        $data = [
            'article' => [
                'tagList' => ['new tag'],
            ],
        ];

        $this->actingAs($this->user)->putJson('/api/articles/easy-title', $data);

        $this->assertDatabaseHas('tags', ['name' => 'new tag']);
    }

    public function testItUpdatesTheRelatedTags()
    {
        $tagIds = Tag::all()->pluck('id')->toArray();

        $data = [
            'article' => [
                'tagList' => ['new tag'],
            ],
        ];

        $this->actingAs($this->user)->putJson('/api/articles/easy-title', $data);

        $newTag = Tag::query()->where('name', 'new tag')->first();
        $this->assertDatabaseHas('article_tag', ['article_id' => $this->article->id, 'tag_id' => $newTag->id]);
        $this->assertDatabaseMissing('article_tag', ['article_id' => $this->article->id, 'tag_id' => $tagIds[0]]);
        $this->assertDatabaseMissing('article_tag', ['article_id' => $this->article->id, 'tag_id' => $tagIds[1]]);
    }

    public function testItReturnsForbiddenWhenUserTriesUpdatingOtherUserArticle()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/articles/easy-title', []);

        $response->assertForbidden();
    }

    public function testItThrowsAuthenticationException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthenticationException::class);

        $this->putJson('/api/articles/easy-title', []);
    }

    public function testItReturnsUnauthorizedMessage()
    {
        $response = $this->putJson('/api/articles/easy-title', []);

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testItReturnsValidationErrors()
    {
        $data = [
            'article' => [
                'title' => 'this is too much'.str_repeat('!', 300),
            ],
        ];

        $response = $this->actingAs($this->user)->putJson('/api/articles/easy-title', $data);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['article.title']);
    }
}
