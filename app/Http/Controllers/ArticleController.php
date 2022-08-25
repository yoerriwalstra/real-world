<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function getOne(string $slug)
    {
        $article = $this->articleService->firstWhere('slug', $slug);
        if (!$article) {
            throw new ModelNotFoundException('Article not found');
        }

        return new ArticleResource($article);
    }

    public function feed(Request $request)
    {
        $limit = $request->query('limit', 20);
        $offset = $request->query('offset', 0);

        $feed = $this->articleService->getFeed($limit, $offset);

        return new ArticleCollection($feed);
    }
}
