<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'tagList' => $this->tagList,
            'createdAt' => (string) $this->created_at,
            'updatedAt' => (string) $this->updated_at,
            'favorited' => auth()->user()?->favoriteArticles()->where('article_id', $this->id)->exists(),
            'favoritesCount' => $this->favoritedBy()->count(),
            'author' => new ProfileResource($this->author),
        ];
    }
}
