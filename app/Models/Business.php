<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['business_type_id', 'name', 'slug', 'phone', 'email', 'address', 'currency'])]
class Business extends Model
{
    //
}
