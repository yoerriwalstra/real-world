<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Tag;
use Carbon\Carbon;

class TagService
{
    public function all()
    {
        return Tag::all();
    }

    public function firstWhere(string $attribute, string $value): ?Tag
    {
        return Tag::query()->where($attribute, $value)->first();
    }

    public function findOrCreateMany(array $data)
    {
        $tags = Tag::query()->whereIn('name', $data)->get();
        $newData = collect($data)->diff($tags->pluck('name'));

        if ($newData->count()) {
            $now = Carbon::now('utc')->toDateTimeString();
            $newTags = $newData->map(fn (string $name) => [
                'name' => $name,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            Tag::insert($newTags->toArray());

            return Tag::whereIn('name', $data)->get();
        }

        return $tags;
    }

    public function syncArticleTags(Article $article, array $tagData): Article
    {
        $tags = $this->findOrCreateMany($tagData);
        $article->tags()->sync($tags->pluck('id')->toArray());
        $article->save();

        return $article;
    }
}
