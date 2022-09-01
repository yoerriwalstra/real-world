<?php

namespace App\Services;

use App\Models\Article;
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
            if (! $author) {
                throw new ModelNotFoundException('Author not found');
            }

            return $author->articles()->latest()->offset($offset)->limit($limit)->get();
        }
        if (isset($conditions['favorited'])) {
            $favorited = $this->userService->firstWhere('username', $conditions['favorited']);
            if (! $favorited) {
                throw new ModelNotFoundException('Favorited not found');
            }

            return $favorited->favoriteArticles()->latest()->offset($offset)->limit($limit)->get();
        }
        if (isset($conditions['tag'])) {
            $tag = $this->tagService->firstWhere('name', $conditions['tag']);
            if (! $tag) {
                throw new ModelNotFoundException('Tag not found');
            }

            return $tag->articles()->latest()->offset($offset)->limit($limit)->get();
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

    public function create(array $data)
    {
        $article = new Article($data);
        $article->author()->associate(auth()->user());
        // save article here because we need an `article.id` to create the relationship with the tags
        $article->save();

        if (! empty($data['tagList'])) {
            $tags = $this->tagService->findOrCreateMany($data['tagList']);
            $article->tags()->attach($tags->pluck('id')->toArray());
            $article->save();
        }

        return $article;
    }
}
