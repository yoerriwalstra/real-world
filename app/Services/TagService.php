<?php

namespace App\Services;

use App\Models\Tag;

class TagService
{
    public function firstWhere(string $attribute, string $value): ?Tag
    {
        return Tag::query()->where($attribute, $value)->first();
    }
}
