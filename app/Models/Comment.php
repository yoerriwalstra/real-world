<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public $fillable = [
        'body',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
