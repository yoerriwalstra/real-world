<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'comment';

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'createdAt' => $this->created_at->toISOString(), // TODO: format date: 2016-02-18T03:22:56.637Z
            'updatedAt' => $this->updated_at->toISOString(), // TODO: format date: 2016-02-18T03:22:56.637Z
            'author' => new ProfileResource($this->author),
        ];
    }
}
