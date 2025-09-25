<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POI extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'file_name',
    ];
}
