<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'short_code',
        'priority',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
