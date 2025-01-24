<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'total_theory_marks',
        'total_practical_marks',
        'passing_marks'
    ];

    protected $casts = [
        'status' => 'boolean',
        'total_theory_marks' => 'decimal:2',
        'total_practical_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2'
    ];

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_subject', 'subject_id', 'class_id')
            ->withTimestamps();
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    public function examinerAssignments()
    {
        return $this->hasMany(ExaminerAssignment::class);
    }

    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }
}
