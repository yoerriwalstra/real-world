<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleCreateRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService)
    {
    }

    public function get(Request $request)
    {
        $author = $request->query('author');
        $favorited = $request->query('favorited');
        $tag = $request->query('tag');
        $limit = $request->query('limit', 20);
        $offset = $request->query('offset', 0);

        $filters = array_filter([
            'author' => $author,
            'favorited' => $favorited,
            'tag' => $tag,
        ]);
        $articles = $this->articleService->findWhere($filters, $limit, $offset);

        return new ArticleCollection($articles);
    }

    public function getOne(Article $article)
    {
        return new ArticleResource($article);
    }

    public function feed(Request $request)
    {
        $limit = $request->query('limit', 20);
        $offset = $request->query('offset', 0);

        $feed = $this->articleService->getFeed($limit, $offset);

        return new ArticleCollection($feed);
    }

    public function create(ArticleCreateRequest $request)
    {
        $article = $this->articleService->create($request->validated('article'));

        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function update(ArticleUpdateRequest $request, Article $article)
    {
        $updated = $this->articleService->update($article, $request->validated('article'));

        return new ArticleResource($updated);
    }

    public function delete(Article $article)
    {
        // TODO: delete related comments after implementing comments
        $article->delete();

        return response('', JsonResponse::HTTP_NO_CONTENT);
    }

    public function favorite(Article $article)
    {
        $this->articleService->favorite($article);

        return new ArticleResource($article);
    }

    public function unfavorite(Article $article)
    {
        $this->articleService->unfavorite($article);

        return new ArticleResource($article);
    }
}
