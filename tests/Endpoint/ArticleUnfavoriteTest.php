<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleUnfavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function testItUnfavoritesArticle()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();

        $article = Article::factory()->for($user, 'author')->create(['title' => 'easy title']);
        $article->favoritedBy()->attach($user);

        $this->assertDatabaseHas('favorite_articles', ['article_id' => $article->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson('/api/articles/easy-title/favorite');

        $response->assertOk();
        $response->assertJson([
            'article' => [
                'favorited' => false,
                'favoritesCount' => 0,
            ],
        ]);

        $this->assertDatabaseMissing('favorite_articles', ['article_id' => $article->id, 'user_id' => $user->id]);
    }

    public function testItThrowsAuthenticationException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthenticationException::class);

        $this->postJson('/api/articles/easy-title/favorite');
    }

    public function testItReturnsUnauthorizedMessage()
    {
        $response = $this->postJson('/api/articles/easy-title/favorite');

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
