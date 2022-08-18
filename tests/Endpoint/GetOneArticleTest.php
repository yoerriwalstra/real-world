<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetOneArticleTest extends TestCase
{
    use RefreshDatabase;

    public function testItThrowsModelNotFoundException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(ModelNotFoundException::class);

        $this->getJson('/api/articles/nonExistentArticle');
    }

    public function testItReturnsNotFound()
    {
        $response = $this->getJson('/api/articles/nonExistentArticle');

        $response->assertNotFound();
        $response->assertJson(['message' => 'Article not found']);
    }

    public function testItReturnsTheArticle()
    {
        Carbon::setTestNow(Carbon::now());

        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user, 'author')
            ->create(['title' => 'easy title']);

        $response = $this->getJson("/api/articles/{$article->slug}");

        $response->assertOk();
        $response->assertJson([
            'article' => [
                'slug' => $article->slug,
                'title' => $article->title,
                'description' => $article->description,
                'body' => $article->body,
                'tagList' => null,
                'createdAt' => (string) $article->created_at,
                'updatedAt' => (string) $article->updated_at,
                'favorited' => null,
                'favoritesCount' => null,
                'author' => [
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ]
            ]
        ]);
    }
}
