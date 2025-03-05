<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'designation',
        'joining_date',
        'qualification',
        'specialization',
        'experience',
        'contact_number',
        'emergency_contact',
        'address',
        'is_active'
    ];

    protected $casts = [
        'joining_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 