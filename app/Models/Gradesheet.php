<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gradesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'session_id',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'remarks',
    ];

    /**
     * Get the student that owns the gradesheet.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class that owns the gradesheet.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the session that owns the gradesheet.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
} 