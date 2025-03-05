<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'established_year',
        'registration_number',
        'principal_name',
        'school_type',
        'is_active'
    ];

    /**
     * Get the active school.
     *
     * @return \App\Models\School|null
     */
    public static function getActive()
    {
        return self::where('is_active', true)->latest()->first();
    }

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function faculty()
    {
        return $this->hasMany(Faculty::class);
    }

    public function classes()
    {
        return $this->hasMany(Classes::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }

    public function gradesheets()
    {
        return $this->hasMany(Gradesheet::class);
    }

    public function ledgers()
    {
        return $this->hasMany(Ledger::class);
    }
}
