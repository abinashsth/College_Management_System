<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_code',
        'name',
        'email',
        'department',
        'salary',
        'position',
        'join_date',
         'is_active'
    ];

    protected $dates = [
        'join_date'
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}