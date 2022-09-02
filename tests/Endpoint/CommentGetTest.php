<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentGetTest extends TestCase
{
    use RefreshDatabase;

    public function testItGetsCommentsForArticle()
    {
        Carbon::setTestNow(Carbon::now());

        /** @var Authenticatable|User $author */
        $author = User::factory()->create();
        /** @var Authenticatable|User $commenter */
        $commenter = User::factory()->create();
        Article::factory()
            ->for($author, 'author')
            ->has(Comment::factory()->for($commenter, 'author')->count(3))
            ->create(['title' => 'easy title']);

        $response = $this->getJson('/api/articles/easy-title/comments');

        $response->assertOk();
        $response->assertJson(['commentsCount' => 3]);
        $response->assertJsonStructure([
            'comments' => [
                [
                    'id',
                    'body',
                    'createdAt',
                    'updatedAt',
                    'author' => [
                        'username',
                        'bio',
                        'image',
                        'following',
                    ],
                ]
            ],
            'commentsCount'

        ]);
    }

    public function testItThrowsModelNotFoundException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(ModelNotFoundException::class);

        $this->getJson('/api/articles/nonExistentArticle/comments');
    }

    public function testItReturnsNotFound()
    {
        $response = $this->getJson('/api/articles/nonExistentArticle/comments');

        $response->assertNotFound();
        $response->assertJson(['message' => 'Article not found']);
    }
}
