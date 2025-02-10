<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'point',
        'mark_from',
        'mark_to',
        'comment'
    ];

    protected $casts = [
        'point' => 'float',
        'mark_from' => 'integer',
        'mark_to' => 'integer'
    ];
}
