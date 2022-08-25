<?php

namespace App\Services;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class ArticleService
{
    public function __construct(private UserService $userService, private TagService $tagService)
    {
    }

    public function firstWhere(string $attribute, string $value): ?Article
    {
        return Article::query()->where($attribute, $value)->first();
    }

    public function findWhere(array $conditions, int $limit, int $offset): Collection
    {
        if (isset($conditions['author'])) {
            $author = $this->userService->firstWhere('username', $conditions['author']);
            if (!$author) {
                throw new ModelNotFoundException('Author not found');
            }

            return $author
                ->articles()
                ->latest()
                ->offset($offset)
                ->limit($limit)
                ->get();
        }
        if (isset($conditions['favorited'])) {
            $favorited = $this->userService->firstWhere('username', $conditions['favorited']);
            if (!$favorited) {
                throw new ModelNotFoundException('Favorited not found');
            }

            return $favorited
                ->favoriteArticles()
                ->latest()
                ->offset($offset)
                ->limit($limit)
                ->get();
        }
        if (isset($conditions['tag'])) {
            $tag = $this->tagService->firstWhere('name', $conditions['tag']);
            if (!$tag) {
                throw new ModelNotFoundException('Tag not found');
            }

            return $tag
                ->articles()
                ->latest()
                ->offset($offset)
                ->limit($limit)
                ->get();
        }

        return Article::query()->where($conditions)->latest()->offset($offset)->limit($limit)->get();
    }

    public function getFeed(int $limit, int $offset)
    {
        $followingAuthorIds = auth()->user()->follows()->get(['id'])->pluck('id')->toArray();

        return Article::query()
            ->whereIn('author_id', $followingAuthorIds)
            ->latest()
            ->offset($offset)
            ->limit($limit)
            ->get();
    }
}
