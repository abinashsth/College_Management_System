<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_name',
        'father_name',
        'mother_name',
        'date_of_birth',
        'gender',
        'address',
        'phone',
        'email',
        'roll_no',
        'admission_number',
        'admission_date',
        'class_id',
        'session_id',
        'faculty_id',
        'course_id',
        'status',
        'verified_at'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'status' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($student) {
            // Get the class details with course
            $class = Classes::with(['course', 'faculty'])->find($student->class_id);
            if (!$class) return;

            // Set course_id from the class if not set
            if (!$student->course_id) {
                $student->course_id = $class->course_id;
            }

            // Set faculty_id from the class if not set
            if (!$student->faculty_id) {
                $student->faculty_id = $class->faculty_id;
            }

            // Set status to true if not set
            if (!isset($student->status)) {
                $student->status = true;
            }

            // Set created_by if not set
            if (!$student->created_by) {
                $student->created_by = Auth::id() ?? 1;
            }

            // Generate admission number if not set
            if (!$student->admission_number) {
                $year = date('Y');
                
                // Get the last sequence number for this year and course
                $lastStudent = static::where('admission_number', 'like', $year . '-%')
                    ->where('course_id', $student->course_id)
                    ->orderBy('id', 'desc')
                    ->first();

                $sequence = $lastStudent ? (int)substr($lastStudent->admission_number, -3) + 1 : 1;
                
                // Format: YYYY-COURSE_CODE-XXX
                $student->admission_number = sprintf("%s-%s-%03d", 
                    $year, 
                    $class->course->course_code ?? 'COURSE',
                    $sequence
                );
            }
        });
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function examResults(): HasMany
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
}