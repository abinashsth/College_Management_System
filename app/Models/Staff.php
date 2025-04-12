<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'staff_id',
        'department_id',
        'position',
        'employment_type',
        'employment_start_date',
        'employment_end_date',
        'qualifications',
        'specializations',
        'contact_number',
        'emergency_contact',
        'address',
        'photo',
        'status',
        'bio',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'employment_start_date' => 'date',
        'employment_end_date' => 'date',
        'qualifications' => 'array',
        'specializations' => 'array',
    ];

    /**
     * Get the user associated with the staff member.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department this staff belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the faculty this staff is associated with through department.
     */
    public function faculty()
    {
        return $this->department ? $this->department->faculty : null;
    }

    /**
     * Get the faculties this staff is directly associated with.
     */
    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class, 'faculty_staff');
    }

    /**
     * Get the teaching loads assigned to this staff.
     */
    public function teachingLoads(): HasMany
    {
        return $this->hasMany(TeachingLoad::class);
    }

    /**
     * Get the subjects taught by this staff.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
            ->withTimestamps();
    }

    /**
     * Get the leave applications submitted by this staff.
     */
    public function leaveApplications(): HasMany
    {
        return $this->hasMany(LeaveApplication::class);
    }

    /**
     * Get the attendance records for this staff.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class);
    }

    /**
     * Get the performances evaluations for this staff.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(StaffEvaluation::class);
    }

    /**
     * Get the full name of the staff member.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->name : '';
    }

    /**
     * Get the email of the staff member.
     */
    public function getEmailAttribute(): string
    {
        return $this->user ? $this->user->email : '';
    }

    /**
     * Scope for active staff members.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for teaching staff.
     */
    public function scopeTeaching($query)
    {
        return $query->whereIn('position', ['teacher', 'lecturer', 'professor', 'assistant professor', 'associate professor']);
    }

    /**
     * Scope for administrative staff.
     */
    public function scopeAdministrative($query)
    {
        return $query->whereIn('position', ['administrator', 'clerk', 'officer', 'coordinator']);
    }
} 