<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Comment;

class CommentService
{
    public function firstWhere(string $attribute, $value): ?Comment
    {
        //
    }

    public function create(array $data, Article $article): Comment
    {
        $comment = new Comment($data);
        $comment->author()->associate(auth()->user());
        $comment->article()->associate($article);
        $comment->save();

        return $comment;
    }
}
