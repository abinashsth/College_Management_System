<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TerminalMarksLedger extends Model
{
    protected $table = 'terminal_marks_ledger';

    protected $fillable = [
        'student_id',
        'class_id',
        'academic_session_id',
        'exam_type_id',
        'total_marks',
        'percentage',
        'grade',
        'rank',
        'remarks'
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2'
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

    public function examType(): BelongsTo
    {
        return $this->belongsTo(ExamType::class);
    }
} 