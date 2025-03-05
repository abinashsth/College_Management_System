<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'section',
        'is_active'
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /**
     * Get the subjects for the class.
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
} 