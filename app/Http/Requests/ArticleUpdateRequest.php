<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'article.title' => 'string|max:255',
            'article.description' => 'string',
            'article.body' => 'string',
            'article.tagList' => 'array',
            'article.tagList.*' => 'string',
        ];
    }
}
