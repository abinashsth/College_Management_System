<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentRecord extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_id',
        'record_type',
        'record_data',
        'previous_data',
        'changed_by',
        'change_reason',
        'notes',
        'title',
        'description',
        'attachment_path',
        'attachment_name',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'record_data' => 'array',
        'previous_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The record types available.
     *
     * @var array
     */
    public static $recordTypes = [
        'personal' => 'Personal Information',
        'academic' => 'Academic Record',
        'enrollment' => 'Enrollment Status',
        'attendance' => 'Attendance Record',
        'disciplinary' => 'Disciplinary Record',
        'achievement' => 'Achievement',
        'medical' => 'Medical Record',
        'notes' => 'General Notes',
    ];

    /**
     * Get the student that owns the record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the record type display name.
     *
     * @return string
     */
    public function getRecordTypeNameAttribute()
    {
        return self::$recordTypes[$this->record_type] ?? $this->record_type;
    }

    /**
     * Get the URL to the attachment if exists.
     *
     * @return string|null
     */
    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment_path) {
            return asset('storage/' . $this->attachment_path);
        }
        
        return null;
    }

    /**
     * Get the color class based on record type.
     *
     * @return string
     */
    public function getRecordColorAttribute()
    {
        switch ($this->record_type) {
            case 'personal':
                return 'info';
            case 'academic':
                return 'primary';
            case 'enrollment':
                return 'dark';
            case 'attendance':
                return 'secondary';
            case 'disciplinary':
                return 'danger';
            case 'achievement':
                return 'success';
            case 'medical':
                return 'warning';
            case 'notes':
                return 'light';
            default:
                return 'secondary';
        }
    }

    /**
     * Get the record type icon class.
     *
     * @return string
     */
    public function getRecordIconAttribute()
    {
        switch ($this->record_type) {
            case 'personal':
                return 'fas fa-user';
            case 'academic':
                return 'fas fa-graduation-cap';
            case 'enrollment':
                return 'fas fa-id-card';
            case 'attendance':
                return 'fas fa-calendar-check';
            case 'disciplinary':
                return 'fas fa-exclamation-triangle';
            case 'achievement':
                return 'fas fa-trophy';
            case 'medical':
                return 'fas fa-notes-medical';
            case 'notes':
                return 'fas fa-sticky-note';
            default:
                return 'fas fa-file-alt';
        }
    }

    /**
     * Scope a query to only include records of a given type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('record_type', $type);
    }

    /**
     * Scope a query to only include confidential medical records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfidentialMedical($query)
    {
        return $query->where('record_type', 'medical')
                     ->whereRaw("JSON_EXTRACT(record_data, '$.confidential') = true");
    }

    /**
     * Scope a query to exclude confidential medical records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonConfidential($query)
    {
        return $query->where(function($query) {
            $query->where('record_type', '!=', 'medical')
                  ->orWhereRaw("(JSON_EXTRACT(record_data, '$.confidential') IS NULL OR JSON_TYPE(JSON_EXTRACT(record_data, '$.confidential')) = 'NULL')")
                  ->orWhereRaw("JSON_EXTRACT(record_data, '$.confidential') != true");
        });
    }
} 