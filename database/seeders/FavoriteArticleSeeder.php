<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $articles = Article::inRandomOrder()->limit(15)->get();
        $users = User::all();
        $users->each(function (User $user) use ($articles) {
            $articleIds = $articles->random(2)->pluck('id')->toArray();
            $user->favoriteArticles()->attach($articleIds);
        });
    }
}
