<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagServiceTest extends TestCase
{
    use RefreshDatabase;

    private TagService $tagService;

    public function setUp(): void
    {
        $this->tagService = new TagService();

        parent::setUp();
    }

    public function testItReturnsTheTag()
    {
        Tag::factory()->create(['name' => 'test tag']);

        $tag = $this->tagService->firstWhere('name', 'test tag');

        $this->assertInstanceOf(Tag::class, $tag);
    }

    public function testItReturnsNullWhenTagIsNotFound()
    {
        $tag = $this->tagService->firstWhere('name', 'non existent tag');

        $this->assertNull($tag);
    }

    public function testFindsExistingAndCreatesNewTags()
    {
        Tag::factory()->create(['name' => 'test tag']);

        $tags = $this->tagService->findOrCreateMany(['test tag', 'new test tag']);

        $this->assertTrue($tags->count() === 2);
        $this->assertDatabaseHas(
            'tags',
            ['name' => 'new test tag'],
        );
    }

    public function testItSyncsTheTagsRelatedToArticle()
    {
        $user = User::factory()->create();
        $article = Article::factory()
            ->for($user, 'author')
            ->has(Tag::factory()->count(1))
            ->create();

        $article = $this->tagService->syncArticleTags($article, []);

        $this->assertTrue($article->tags->isEmpty());
    }
}
