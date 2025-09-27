<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Route extends Model
{
    protected $fillable = [
        'start_location',
        'duration',
    ];

    public function points(): BelongsToMany
    {
        return $this->belongsToMany(Point::class, 'route_point')->withPivot('day_of_the_route');
    }
}
