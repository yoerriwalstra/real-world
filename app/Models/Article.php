<?php

namespace App\Models;

use App\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'title',
        'description',
        'body',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_articles');
    }
}
