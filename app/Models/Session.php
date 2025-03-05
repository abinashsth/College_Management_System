<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    use HasFactory;

    protected $table = 'academic_sessions';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($session) {
            if ($session->status === 'active') {
                self::where('id', '!=', $session->id)
                    ->where('status', 'active')
                    ->update(['status' => 'inactive']);
            }
        });
    }

    public function faculties(): HasMany
    {
        return $this->hasMany(Faculty::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function gradesheets(): HasMany
    {
        return $this->hasMany(Gradesheet::class);
    }

    public function ledgers(): HasMany
    {
        return $this->hasMany(Ledger::class);
    }

    /**
     * Get the classes for the session.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class);
    }

    /**
     * Get the exams for the session.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}