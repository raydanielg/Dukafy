<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'slug', 'modules'])]
class BusinessType extends Model
{
    protected function casts(): array
    {
        return [
            'modules' => 'array',
        ];
    }
}
