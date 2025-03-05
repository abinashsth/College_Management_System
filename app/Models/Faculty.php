<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the classes for the faculty.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class);
    }

    /**
     * Get the subjects for the faculty.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
} 