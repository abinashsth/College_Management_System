<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalGradeSheet extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_session_id',
        'total_marks',
        'percentage',
        'grade',
        'rank',
        'attendance_percentage',
        'eca_average',
        'remarks'
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'eca_average' => 'decimal:2'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }
} 