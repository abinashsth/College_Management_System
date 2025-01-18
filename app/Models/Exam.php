<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'exam_date',
        'class_id',
        'subject',
        'total_marks',
        'passing_marks',
        'status'
    ];

    protected $casts = [
        'exam_date' => 'datetime',
        'status' => 'boolean',
        'total_marks' => 'integer',
        'passing_marks' => 'integer'
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'exam_student')
            ->withPivot('grade', 'remarks')
            ->withTimestamps();
    }
} 