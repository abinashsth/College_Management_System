<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    protected $fillable = [
        'name',
        'address',
        'contact_number',
        'dob',
        'email',
        'password',
        'status',
        'class_id'
    ];

    protected $casts = [
        'dob' => 'date',
        'verified_at' => 'datetime',
        'status' => 'boolean'
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}