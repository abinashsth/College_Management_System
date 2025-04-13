<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExamMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'title',
        'type',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'is_for_students',
        'is_for_teachers',
        'is_confidential',
        'release_date',
        'is_active',
        'version',
        'created_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'is_for_students' => 'boolean',
        'is_for_teachers' => 'boolean',
        'is_confidential' => 'boolean',
        'release_date' => 'datetime',
        'is_active' => 'boolean',
        'version' => 'integer',
        'file_size' => 'integer',
        'approved_at' => 'datetime'
    ];

    /**
     * Get the exam this material belongs to.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user who created this material.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this material.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all material types as an array for dropdown lists.
     */
    public static function getTypes()
    {
        return [
            'question_paper' => 'Question Paper',
            'answer_sheet' => 'Answer Sheet',
            'supplementary' => 'Supplementary Material',
            'instruction' => 'Instruction',
            'resource' => 'Resource',
            'marking_scheme' => 'Marking Scheme',
            'other' => 'Other'
        ];
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute()
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' KB';
        } elseif ($this->file_size < 1048576) {
            return round($this->file_size / 1024, 2) . ' MB';
        } else {
            return round($this->file_size / 1048576, 2) . ' GB';
        }
    }

    /**
     * Get file URL.
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    /**
     * Scope a query to only include active materials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include materials for students.
     */
    public function scopeForStudents($query)
    {
        return $query->where('is_for_students', true);
    }

    /**
     * Scope a query to only include materials for teachers.
     */
    public function scopeForTeachers($query)
    {
        return $query->where('is_for_teachers', true);
    }

    /**
     * Scope a query to only include confidential materials.
     */
    public function scopeConfidential($query)
    {
        return $query->where('is_confidential', true);
    }

    /**
     * Scope a query to only include materials of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include materials that are released.
     */
    public function scopeReleased($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('release_date')
                    ->orWhere('release_date', '<=', now());
            });
    }

    /**
     * Approve this material.
     */
    public function approve($userId)
    {
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->save();
        
        return $this;
    }

    /**
     * Check if the material is released.
     */
    public function isReleased()
    {
        return $this->is_active && 
            (!$this->release_date || $this->release_date <= now());
    }

    /**
     * Check if the material can be viewed by a user.
     */
    public function canBeViewedBy(User $user)
    {
        if (!$this->is_active) {
            return false;
        }
        
        if ($user->hasRole(['Super Admin', 'Admin'])) {
            return true;
        }
        
        if ($this->is_for_teachers && $user->hasRole('Teacher')) {
            return !$this->is_confidential || $user->id === $this->created_by || $user->id === $this->approved_by;
        }
        
        if ($this->is_for_students && $user->hasRole('Student')) {
            return !$this->is_confidential && $this->isReleased();
        }
        
        return false;
    }
}
