<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacultyDean extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'faculty_id',
        'user_id',
        'appointment_date',
        'end_date',
        'appointment_reference',
        'job_description',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the faculty that the dean belongs to.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    /**
     * Get the user who is the dean.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this dean is currently active.
     *
     * @return bool
     */
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($this->end_date && now()->gt($this->end_date)) {
            return false;
        }
        
        return true;
    }
} 