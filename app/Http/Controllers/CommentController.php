<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentCreateRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use App\Services\ArticleService;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(private CommentService $commentService, private ArticleService $articleService)
    {
    }

    public function get(Article $article)
    {
        return new CommentCollection($article->comments);
    }

    public function create(CommentCreateRequest $request, Article $article)
    {
        $comment = $this->commentService->create($request->validated('comment'), $article);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function delete($article, Comment $comment)
    {
        $comment->delete();

        return response('', JsonResponse::HTTP_NO_CONTENT);
    }
}
