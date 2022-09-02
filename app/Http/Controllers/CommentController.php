<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentCreateRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Services\ArticleService;
use App\Services\CommentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService, private ArticleService $articleService)
    {
    }

    public function get(string $slug)
    {
        $article = $this->articleService->firstWhere('slug', $slug);
        if (! $article) {
            throw new ModelNotFoundException('Article not found');
        }

        return new CommentCollection($article->comments);
    }

    public function create(CommentCreateRequest $request, string $slug)
    {
        $article = $this->articleService->firstWhere('slug', $slug);
        if (! $article) {
            throw new ModelNotFoundException('Article not found');
        }

        $comment = $this->commentService->create($request->validated('comment'), $article);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }
}
