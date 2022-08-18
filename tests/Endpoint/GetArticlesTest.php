<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetArticlesTest extends TestCase
{
    use RefreshDatabase;

    public Article $article;

    public function testItReturnsTheArticles()
    {
        $users = User::factory(3)->create();
        $users->each(fn (User $user) => Article::factory(5)->for($user, 'author')->create());

        $response = $this->getJson('/api/articles');

        $response->assertOk();
        $response->assertJson(['articlesCount' => 15]);
        $response->assertJsonStructure([
            'articles' => [
                [
                    'slug',
                    'title',
                    'description',
                    'body',
                    'tagList',
                    'createdAt',
                    'updatedAt',
                    'favorited',
                    'favoritesCount',
                    'author' => [
                        'username',
                        'bio',
                        'image',
                        'following',
                    ]
                ]
            ],
            'articlesCount',
        ]);
    }

    public function testItReturnsTheLimitNumberOfArticles()
    {
        $user = User::factory()->create();
        Article::factory(11)->for($user, 'author')->create();

        $response = $this->getJson('/api/articles?limit=10');

        $response->assertJson(['articlesCount' => 10]);
    }

    public function testItReturnsTheOffsetNumberOfArticles()
    {
        $user = User::factory()->create();
        Article::factory()->for($user, 'author')->createMany([
            ['title' => 'The first title', 'created_at' => '2022-08-18 12:15:00'],
            ['title' => 'The second title', 'created_at' => '2022-08-18 12:15:01'],
            ['title' => 'The last title', 'created_at' => '2022-08-18 12:15:02'],
        ]);

        $response = $this->getJson('/api/articles?offset=2');

        $response->assertJson([
            'articles' => [
                [
                    'title' => 'The first title',
                    // skipping rest of the ArticleResource properties for brevity
                ]
            ],
            'articlesCount' => 1
        ]);
    }

    public function testItReturnsArticlesFromAuthor()
    {
        Article::factory(3)
            ->for(User::factory()->create(), 'author')
            ->create();
        $author = User::factory()->create(['username' => 'username']);
        $authorArticles = Article::factory()->for($author, 'author')->createMany([
            ['title' => 'The first title', 'created_at' => '2022-08-18 12:15:00'],
            ['title' => 'The second title', 'created_at' => '2022-08-18 12:15:01'],
            ['title' => 'The last title', 'created_at' => '2022-08-18 12:15:02'],
        ]);

        $response = $this->getJson('/api/articles?author=username');

        $response->assertOk();

        $sortedArticlesResponse = $authorArticles->sortByDesc('created_at')
            ->pluck('title')
            ->map(fn (string $title) => ['title' => $title])
            ->toArray();
        $response->assertJson([
            'articles' => $sortedArticlesResponse,
            'articlesCount' => $authorArticles->count()
        ]);
    }
}
