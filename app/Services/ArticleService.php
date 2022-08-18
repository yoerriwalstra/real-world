<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Collection;

class ArticleService
{
    public function __construct(private UserService $userService)
    {
    }

    public function firstWhere(string $attribute, string $value): ?Article
    {
        return Article::query()->where($attribute, $value)->first();
    }

    public function findWhere(array $conditions, int $limit, int $offset): Collection
    {
        if (isset($conditions['author'])) {
            return $this->userService->firstWhere('username', $conditions['author'])
                ->articles()
                ->latest()
                ->offset($offset)
                ->limit($limit)
                ->get();
        }
        if (isset($conditions['favorited'])) {
            return $this->userService->firstWhere('username', $conditions['favorited'])
                ->favoriteArticles()
                ->latest()
                ->offset($offset)
                ->limit($limit)
                ->get();
        }
        // if (isset($conditions['tag'])) {
        //     //
        // }

        return Article::query()->where($conditions)->latest()->offset($offset)->limit($limit)->get();
    }
}
