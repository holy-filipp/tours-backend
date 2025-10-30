<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'starts_at',
        'capacity',
        'min_age',
        'price',
        'route_id',
        'archived',
    ];
}
