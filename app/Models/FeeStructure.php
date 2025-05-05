<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'course_id',
        'semester',
        'tuition_fee',
        'development_fee',
        'other_charges',
        'total_amount',
        'is_active',
        'description'
    ];

    protected $casts = [
        'tuition_fee' => 'decimal:2',
        'development_fee' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function calculateTotal()
    {
        return $this->tuition_fee + $this->development_fee + $this->other_charges;
    }
}