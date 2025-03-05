<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_code',
        'subject_name',
        'description',
        'credit_hours',
        'course_id',
        'status',
        'created_by'
    ];

    protected $casts = [
        'status' => 'boolean',
        'credit_hours' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($subject) {
            // Set created_by if not set
            if (!$subject->created_by) {
                $subject->created_by = Auth::id() ?? 1;
            }

            // Set status to true if not set
            if (!isset($subject->status)) {
                $subject->status = true;
            }

            // Generate subject code if not set
            if (!$subject->subject_code) {
                $course = Course::find($subject->course_id);
                if ($course) {
                    // Format: COURSECODE-SEQUENCE (e.g., BCA-001)
                    $lastSubject = static::where('course_id', $subject->course_id)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    $sequence = $lastSubject ? (int)substr($lastSubject->subject_code, -3) + 1 : 1;
                    $subject->subject_code = sprintf("%s-%03d", 
                        $course->course_code,
                        $sequence
                    );
                }
            }
        });
    }

    /**
     * Get the course that owns the subject.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user that created the subject.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 