<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['title', 'slug', 'category_id', 'category', 'age_range', 'image', 'excerpt', 'content', 'published_at', 'is_featured'])]
class Article extends Model
{
    protected $appends = ['image_url'];

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
