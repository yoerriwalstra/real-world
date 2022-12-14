<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentCreateTest extends TestCase
{
    use RefreshDatabase;

    public function testItCreatesComment()
    {
        Carbon::setTestNow(Carbon::now());

        /** @var Authenticatable|User $user */
        $user = User::factory()->create();
        Article::factory()->for($user, 'author')->create(['title' => 'easy title']);

        $data = [
            'comment' => [
                'body' => 'new body',
            ],
        ];

        $response = $this->actingAs($user)->postJson('/api/articles/easy-title/comments', $data);

        $comment = Comment::first();

        $response->assertCreated();
        $response->assertJson([
            'comment' => [
                'id' => $comment->id,
                'body' => $data['comment']['body'],
                'createdAt' => $comment->created_at->toISOString(),
                'updatedAt' => $comment->updated_at->toISOString(),
                'author' => [
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ],

            ],
        ]);
    }

    public function testItCannotAddCommentToNonExistentArticle()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/articles/non-existent-article/comments');

        $response->assertNotFound();
    }

    public function testItReturnsValidationErrors()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();
        Article::factory()->for($user, 'author')->create(['title' => 'easy title']);

        $response = $this->actingAs($user)->postJson('/api/articles/easy-title/comments');

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['comment.body']);
    }
}
