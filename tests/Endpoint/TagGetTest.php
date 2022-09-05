<?php

namespace Tests\Endpoint;

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagGetTest extends TestCase
{
    use RefreshDatabase;

    public function testItReturnsTheTags()
    {
        Tag::factory(5)->create();

        $response = $this->getJson('/api/tags');

        $response->assertOk();
        $response->decodeResponseJson()->assertCount(5, 'tags');
    }
}
