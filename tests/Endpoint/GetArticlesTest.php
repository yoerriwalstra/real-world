<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\Tag;
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

        $sortedArticlesResponse = $authorArticles->sortByDesc('created_at')
            ->pluck('title')
            ->map(fn (string $title) => ['title' => $title])
            ->toArray();
        $response->assertJson([
            'articles' => $sortedArticlesResponse,
            'articlesCount' => $authorArticles->count()
        ]);
    }

    public function testItReturnsArticlesFavoritedByUsername()
    {
        $user = User::factory()->create(['username' => 'username']);
        $users = User::factory(2)->create();
        $users->concat($user);

        // create articles
        $users->each(fn (User $user) => Article::factory(5)->for($user, 'author')->create());
        // favorite random articles
        $users->each(function (User $user) {
            $articleIds = Article::inRandomOrder()->limit(3)->get(['id'])->pluck('id')->toArray();
            $user->favoriteArticles()->attach($articleIds);
        });

        $articlesResponse = $user->favoriteArticles()
            ->latest()
            ->get()
            ->pluck('title')
            ->map(fn (string $title) => ['title' => $title]);

        $response = $this->getJson('/api/articles?favorited=username');

        $response->assertJson([
            'articles' => $articlesResponse->toArray(),
            'articlesCount' => $articlesResponse->count()
        ]);
    }

    public function testItReturnsArticlesByTag()
    {
        $articles = Article::factory(5)->for(User::factory()->create(), 'author')->create();
        $tags = Tag::factory(2)->create();
        $articles->each(function (Article $article) use ($tags) {
            $article->tags()->attach($tags->random()->id);
        });

        $articlesResponse = $tags->first()->articles()
            ->latest()
            ->get()
            ->pluck('title')
            ->map(fn (string $title) => ['title' => $title]);

        $response = $this->getJson("/api/articles?tag={$tags->first()->name}");

        $response->assertJson([
            'articles' => $articlesResponse->toArray(),
            'articlesCount' => $articlesResponse->count()
        ]);
    }

    public function testItReturnsNotFoundForUnknownUserOrTag()
    {
        $response = $this->getJson('api/articles?author=nonExistent');
        $response->assertNotFound();
        $response->assertJson(['message' => 'Author not found']);

        $response = $this->getJson('api/articles?favorited=nonExistent');
        $response->assertNotFound();
        $response->assertJson(['message' => 'Favorited not found']);

        $response = $this->getJson('api/articles?tag=nonExistent');
        $response->assertNotFound();
        $response->assertJson(['message' => 'Tag not found']);
    }
}
