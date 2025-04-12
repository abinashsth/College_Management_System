<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Subject;
use App\Models\AcademicSession;
use App\Models\User;

class StudentSubject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'subject_id',
        'academic_session_id',
        'enrollment_date',
        'status',
        'attendance_percentage',
        'marks_obtained',
        'grade',
        'remarks',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enrollment_date' => 'date',
        'attendance_percentage' => 'decimal:2',
        'marks_obtained' => 'decimal:2',
    ];

    /**
     * Get the student that this enrollment belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject that this enrollment belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the academic session that this enrollment belongs to.
     */
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the user who created this enrollment.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
