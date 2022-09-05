<?php

namespace App\Http\Controllers;

use App\Services\TagService;

class TagController extends Controller
{
    public function __construct(private TagService $tagService)
    {
    }

    public function get()
    {
        $tags = $this->tagService->all();

        return response()->json([
            'tags' => $tags->toArray(),
        ]);
    }
}
