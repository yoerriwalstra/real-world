<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Services\ArticleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService)
    {
    }

    // public function get(Request $request)
    // {
    //     $tag = $request->query('tag');
    //     $author = $request->query('author');
    //     $favorited = $request->query('favorited');
    //     $limit = $request->query('limit', 20);
    //     $offset = $request->query('offset', 0);

    //     $filters = array_filter([
    //         'tag' => $tag,
    //         'author' => $author,
    //         'favorited' => $favorited,
    //     ]);
    //     $articles = $this->articleService->findWhere($filters, $limit, $offset);

    //     return new ArticleResource::collection($articles);
    // }

    public function getOne(string $slug)
    {
        $article = $this->articleService->firstWhere('slug', $slug);
        if (!$article) {
            throw new ModelNotFoundException('Article not found');
        }

        return new ArticleResource($article);
    }
}
