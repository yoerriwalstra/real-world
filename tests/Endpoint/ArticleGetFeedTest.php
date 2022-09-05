<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleGetFeedTest extends TestCase
{
    use RefreshDatabase;

    public function testItReturnsUnauthenticated()
    {
        $response = $this->getJson('/api/articles/feed');

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function testItReturnsTheAuthenticatedUserArticleFeed()
    {
        Article::factory(2)->for(User::factory()->create(), 'author')->create();

        $authors = User::factory(2)->create();
        $articles = Article::factory()->for($authors->first(), 'author')->createMany([
            // setting created_at to enforce sorted order
            ['title' => '1', 'created_at' => '2022-08-18 12:15:00'],
            ['title' => '2', 'created_at' => '2022-08-18 12:15:01'],
        ]);
        $more = Article::factory()->for($authors->last(), 'author')->createMany([
            // setting created_at to enforce sorted order
            ['title' => '3', 'created_at' => '2022-08-18 12:15:02'],
            ['title' => '4', 'created_at' => '2022-08-18 12:15:03'],
        ]);
        $articles = $articles->concat($more);

        /** @var Authenticatable|User $follower */
        $follower = User::factory()->create();
        $follower->follows()->attach($authors->map(fn ($author) => $author->id));

        $response = $this->actingAs($follower)->getJson('/api/articles/feed');

        $sortedArticlesResponse = $articles->sortByDesc('created_at')
            ->pluck('title')
            ->map(fn (string $title) => ['title' => $title])
            ->toArray();
        $response->assertOk();
        $response->assertJson([
            'articles' => $sortedArticlesResponse,
            'articlesCount' => $articles->count(),
        ]);
    }

    public function testItReturnsTheLimitNumberOfArticles()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();
        Article::factory(3)->for($user, 'author')->create();

        /** @var Authenticatable|User $follower */
        $follower = User::factory()->create();
        $follower->follows()->attach($user->id);

        $response = $this->actingAs($follower)->getJson('/api/articles/feed?limit=2');

        $response->assertJson(['articlesCount' => 2]);
    }

    public function testItReturnsTheOffsetNumberOfArticles()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();
        Article::factory()->for($user, 'author')->createMany([
            // setting created_at to enforce sorted order
            ['title' => 'The first title', 'created_at' => '2022-08-18 12:15:00'],
            ['created_at' => '2022-08-18 12:15:01'],
            ['created_at' => '2022-08-18 12:15:02'],
        ]);

        /** @var Authenticatable|User $follower */
        $follower = User::factory()->create();
        $follower->follows()->attach($user->id);

        $response = $this->actingAs($follower)->getJson('/api/articles/feed?offset=2');

        $response->assertJson([
            'articles' => [
                [
                    'title' => 'The first title',
                    // skipping rest of the ArticleResource properties for brevity
                ],
            ],
            'articlesCount' => 1,
        ]);
    }
}
