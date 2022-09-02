<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $articles = Article::all();
        $articles->each(function (Article $article) use ($users) {
            $comments = [];
            for ($i = 0; $i <= rand(1, 5); $i++) {
                $comments[] = Comment::factory()->for($users->random(), 'author')->make();
            }
            $article->comments()->saveMany($comments);
        });
    }
}
