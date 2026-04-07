<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'slug', 'description', 'image', 'is_active'])]
class ArticleCategory extends Model
{
    public function articles()
    {
        return $this->hasMany(Article::class, 'article_category_id');
    }
}
