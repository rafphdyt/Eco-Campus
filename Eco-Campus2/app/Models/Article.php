<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $primaryKey = 'article_id';
    protected $fillable = ['category_id', 'title', 'description', 'content', 'author', 'url',
                           'thumbnail_url', 'published_at', 'reading_time'];

    public function category() { return $this->belongsTo(Category::class, 'category_id'); }
}