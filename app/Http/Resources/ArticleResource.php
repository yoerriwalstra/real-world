<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Article
 */
class ArticleResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'article';

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'body' => $this->body,
            'tagList' => $this->tags->pluck('name')->toArray(),
            'createdAt' => $this->created_at->toIsoString(),
            'updatedAt' => $this->updated_at->toIsoString(),
            'favorited' => auth()->user()?->favoriteArticles()->where('article_id', $this->id)->exists(),
            'favoritesCount' => $this->favoritedBy()->count(),
            'author' => new ProfileResource($this->author),
        ];
    }
}
