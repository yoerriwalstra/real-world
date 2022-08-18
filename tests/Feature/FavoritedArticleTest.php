<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritedArticleTest extends TestCase
{
    use RefreshDatabase;

    public function testItShowsArticleFavoritedNullForUnauthenticatedUser()
    {
        $user = User::factory()->create();
        $article = Article::factory()->for($user, 'author')->create(['title' => 'easy title']);

        $response = $this->getJson("api/articles/$article->slug");

        $response->assertJson(
            [
                'article' => [
                    'favorited' => null
                ]
            ],
            true
        );
    }

    public function testItShowsArticleFavoritedForAuthenticatedUser()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $article = Article::factory()->for($user, 'author')->create(['title' => 'easy title']);
        $user->favoriteArticles()->attach($article->id);

        $response = $this->actingAs($user)->getJson("api/articles/$article->slug");

        $response->assertJson([
            'article' => [
                'favorited' => true
            ]
        ]);
    }
}
