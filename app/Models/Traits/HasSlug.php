<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $slug = self::createSlug($model);
            $model->slug = $slug;
        });

        static::updating(function (Model $model) {
            // only create new slug if title changed to prevent incrementing slug
            if ($model->title !== $model->getOriginal('title')) {
                $slug = self::createSlug($model);
                $model->slug = $slug;
            }
        });
    }

    private static function createSlug(Model $model)
    {
        $slug = Str::slug($model->title);
        if (static::query()->where('slug', $slug)->exists()) {
            $latestSlug = static::query()->where('title', $model->title)->latest()->value('slug');
            $slug = static::incrementSlug($latestSlug);
        }

        return $slug;
    }

    private static function incrementSlug(string $slug)
    {
        $newSlug = "$slug-0";
        $matches = [];
        if (preg_match('/\d+$/', $slug, $matches)) {
            $newSlug = preg_replace('/\d+$/', (int) $matches[0] + 1, $slug);
        }

        return $newSlug;
    }
}
