<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'registration_number',
        'first_name',
        'last_name',
        'gender',
        'profile_photo',
        'email',
        'password',
        'student_address',
        'city',
        'state',
        'phone_number',
        'emergency_contact_name',
        'emergency_contact_number',
        'emergency_contact_relationship',
        'dob',
        'date_of_birth',
        'father_name',
        'mother_name',
        'class_id',
        'section_id',
        'program_id',
        'department_id',
        'batch_year',
        'years_of_study',
        'admission_number',
        'admission_date',
        'current_semester',
        'academic_session_id',
        'guardian_name',
        'guardian_relation',
        'guardian_contact',
        'guardian_address',
        'guardian_occupation',
        'previous_education',
        'last_qualification',
        'last_qualification_marks',
        'medical_information',
        'remarks',
        'documents',
        'status',
        'enrollment_status',
        'fee_status',
        'verified_at',
        'documents_verified_at',
        'created_by',
        'photo'
    ];

    protected $casts = [
        'dob' => 'date',
        'admission_date' => 'date',
        'status' => 'boolean',
        'fee_status' => 'boolean',
        'verified_at' => 'datetime',
        'documents_verified_at' => 'datetime',
        'documents' => 'array',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the student's full name.
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Generate a unique student ID based on admission year, program, and auto-increment.
     */
    public static function generateStudentId($programCode, $batchYear, $count)
    {
        // If any parameters are missing, return null
        if (!$programCode || !$batchYear) {
            return null;
        }
        
        // Format: [YY]-[PROGRAM_CODE]-[SEQUENTIAL_NUMBER]
        // Example: 23-CSE-0001
        $year = substr($batchYear, -2); // Last two digits of year
        $sequentialNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "{$year}-{$programCode}-{$sequentialNumber}";
    }

    /**
     * Generate a unique registration number.
     */
    public static function generateRegistrationNumber($departmentCode, $batchYear, $count)
    {
        // Format: REG-[DEPARTMENT_CODE]-[YY]-[SEQUENTIAL_NUMBER]
        // Example: REG-CSE-23-0001
        $year = substr($batchYear, -2); // Last two digits of year
        $sequentialNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "REG-{$departmentCode}-{$year}-{$sequentialNumber}";
    }

    /**
     * Get the class that the student belongs to.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the program that the student is enrolled in.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the department that the student belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the academic session that the student is currently in.
     */
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the user associated with the student.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    /**
     * Get the exams that the student has taken.
     */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_student')
            ->withPivot('grade', 'remarks')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include students with verified documents.
     */
    public function scopeVerifiedDocuments($query)
    {
        return $query->whereNotNull('documents_verified_at');
    }

    /**
     * Scope a query to only include students in a specific program.
     */
    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    /**
     * Scope a query to only include students in a specific department.
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope a query to only include students from a specific batch year.
     */
    public function scopeByBatchYear($query, $batchYear)
    {
        return $query->where('batch_year', $batchYear);
    }

    /**
     * Scope a query to only include students with a specific enrollment status.
     */
    public function scopeByEnrollmentStatus($query, $status)
    {
        return $query->where('enrollment_status', $status);
    }

    /**
     * Scope a query to filter students by name.
     */
    public function scopeSearchByName($query, $name)
    {
        return $query->where(function($q) use ($name) {
            $q->where('first_name', 'like', "%{$name}%")
              ->orWhere('last_name', 'like', "%{$name}%");
        });
    }

    /**
     * Scope a query to order students by their full name.
     */
    public function scopeOrderByName($query, $direction = 'asc')
    {
        return $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$direction}");
    }

    /**
     * Get the records for the student.
     */
    public function records()
    {
        return $this->hasMany(StudentRecord::class);
    }

    /**
     * Log a change to the student record.
     *
     * @param string $recordType The type of record (e.g., 'personal', 'academic', 'enrollment')
     * @param array $data The current data
     * @param array|null $previousData The previous data
     * @param int|null $changedBy The ID of the user who made the change
     * @param string|null $reason The reason for the change
     * @param string|null $notes Additional notes about the change
     * @return StudentRecord
     */
    public function logChange(string $recordType, array $data, ?array $previousData = null, ?int $changedBy = null, ?string $reason = null, ?string $notes = null): StudentRecord
    {
        return $this->records()->create([
            'record_type' => $recordType,
            'record_data' => $data,
            'previous_data' => $previousData,
            'changed_by' => $changedBy ?? auth()->id(),
            'change_reason' => $reason,
            'notes' => $notes,
        ]);
    }

    /**
     * Get the assignments assigned to this student.
     */
    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'student_assignments')
            ->withPivot(['submitted_at', 'submission_file_path', 'submission_text', 'score', 'feedback', 
                        'graded_by', 'graded_at', 'status', 'is_late'])
            ->withTimestamps();
    }

    /**
     * Get the assignment submissions for this student.
     */
    public function assignmentSubmissions()
    {
        return $this->hasMany(StudentAssignment::class);
    }
}