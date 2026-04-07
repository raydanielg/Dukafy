<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['business_type_id', 'logo', 'name', 'slug', 'phone', 'email', 'address', 'currency'])]
class Business extends Model
{
    protected function appends(): array
    {
        return ['logo_url'];
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}
