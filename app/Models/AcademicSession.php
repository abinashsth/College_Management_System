<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSession extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    // Scope for active session
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get active session
    public static function getActive()
    {
        return static::active()->first();
    }
}
