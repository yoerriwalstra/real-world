<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;

class RegisterResource extends UserResource
{
    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->status(JsonResponse::HTTP_CREATED);
    }
}
