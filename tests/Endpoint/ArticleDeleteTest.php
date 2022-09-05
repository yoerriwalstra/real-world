<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleDeleteTest extends TestCase
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
            ->create(['title' => 'easy title']);
    }

    public function testItDeletesTheArticle()
    {
        $this->assertDatabaseHas('articles', ['id' => $this->article->id]);

        $response = $this->actingAs($this->user)->deleteJson('/api/articles/easy-title');

        $response->assertNoContent();

        $this->assertDatabaseMissing('articles', ['id' => $this->article->id]);
    }

    public function testItReturnsForbiddenWhenUserTriesDeletingOtherUserArticle()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/articles/easy-title');

        $response->assertForbidden();
    }
}
