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
        if (!$this->image) {
            return null;
        }

        // If already a full URL
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        // If the image is stored directly under /public
        if (file_exists(public_path($this->image))) {
            return url($this->image);
        }

        // Default: stored in /storage (public disk)
        return asset('storage/' . $this->image);
    }
}
