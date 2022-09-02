<?php

namespace Tests\Endpoint;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentDeleteTest extends TestCase
{
    use RefreshDatabase;

    private Authenticatable|User $user;

    private Comment $comment;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['username' => 'username']);

        $this->comment = Comment::factory()
            ->for($this->user, 'author')
            ->for(Article::factory()->for($this->user, 'author')->create())
            ->create();
    }

    public function testItDeletesTheComment()
    {
        $this->assertDatabaseHas('comments', ['id' => $this->comment->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/articles/easy-title/comments/{$this->comment->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('comments', ['id' => $this->comment->id]);
    }

    public function testItReturnsForbiddenWhenUserTriesDeletingOtherUsercomment()
    {
        /** @var Authenticatable|User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/articles/easy-title/comments/{$this->comment->id}");

        $response->assertForbidden();
    }

    public function testItThrowsAuthenticationException()
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthenticationException::class);

        $this->deleteJson("/api/articles/easy-title/comments/{$this->comment->id}");
    }

    public function testItReturnsUnauthorizedMessage()
    {
        $response = $this->deleteJson("/api/articles/easy-title/comments/{$this->comment->id}");

        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }
}
