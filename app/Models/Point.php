<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = [
        'file_name',
        'description',
        'day_of_the_route',
        'route_id',
    ];
}
