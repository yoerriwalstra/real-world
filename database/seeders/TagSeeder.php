<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::factory()->create(['name' => 'dragons']); // needed to pass Postman tests
        $tags = Tag::factory(30)->create();
        $articles = Article::all();
        $articles->each(function (Article $article) use ($tags) {
            $article->tags()->attach($tags->random(4));
        });
    }
}
