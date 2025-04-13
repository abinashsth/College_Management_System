<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Get the academic sessions for this academic year.
     */
    public function sessions()
    {
        return $this->hasMany(AcademicSession::class);
    }

    /**
     * Get formatted year range (e.g., 2023-2024)
     */
    public function getYearRangeAttribute()
    {
        return $this->start_date->format('Y') . '-' . $this->end_date->format('Y');
    }
} 