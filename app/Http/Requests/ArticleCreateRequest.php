<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'article.title' => 'required|string|max:255',
            'article.description' => 'required|string',
            'article.body' => 'required|string',
            'article.tagList' => 'nullable|array',
            'article.tagList.*' => 'string',
        ];
    }
}
