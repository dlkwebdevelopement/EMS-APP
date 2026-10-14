<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
        'type',
        'is_restricted'
    ];

    protected $casts = [
        'date' => 'date',
        'is_restricted' => 'boolean'
    ];
}
