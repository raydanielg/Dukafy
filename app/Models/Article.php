<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['article_category_id', 'title', 'slug', 'content', 'image', 'is_published'])]
class Article extends Model
{
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    protected function appends(): array
    {
        return ['image_url'];
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
