<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Point extends Model
{
    protected $fillable = [
        'file_name',
        'description',
        'name',
    ];

    public function routes(): BelongsToMany
    {
        return $this->belongsToMany(Route::class, 'route_point')->withPivot('day_of_the_route');
    }
}
