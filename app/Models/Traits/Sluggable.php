<?php

namespace App\Models\Traits;

use App\Exceptions\SluggableException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait Sluggable
{
    protected static function bootSluggable()
    {
        static::creating(function (Model $model) {
            if (!$model->getAttribute('title')) {
                throw new SluggableException('Missing required attribute title to create slug');
            }

            $slug = self::createSlug($model);
            $model->setAttribute('slug', $slug);
        });

        static::updating(function (Model $model) {
            $title = $model->getAttribute('title');
            $originalTitle = $model->getOriginal('title');
            if (!$title || !$originalTitle) {
                throw new SluggableException('Missing required attribute title to create slug');
            }

            // only create new slug if title changed to prevent incrementing slug
            if ($title !== $originalTitle) {
                $slug = self::createSlug($model);
                $model->setAttribute('slug', $slug);
            }
        });
    }

    private static function createSlug(Model $model)
    {
        $title = $model->getAttribute('title');
        $slug = Str::slug($title);
        if (static::query()->where('slug', $slug)->exists()) {
            $latestSlug = static::query()->where('title', $title)->latest()->value('slug');
            $slug = self::incrementSlug($latestSlug);
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
