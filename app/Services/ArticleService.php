<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleService
{
    public function firstWhere(string $attribute, string $value): ?Article
    {
        return Article::query()->where($attribute, $value)->first();
    }

    // public function findWhere(array $conditions, int $limit, int $offset): Collection
    // {
    //     return Article::query()->where($conditions)->get();
    // }
}
