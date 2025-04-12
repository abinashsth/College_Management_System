<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'exam_id',
        'is_global',
        'description',
        'is_mandatory',
        'display_order',
        'category',
        'penalty_for_violation',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer'
    ];

    /**
     * Get the exam this rule belongs to.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user who created this rule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all rule categories as an array for dropdown lists.
     */
    public static function getCategories()
    {
        return [
            'general' => 'General',
            'conduct' => 'Conduct',
            'materials' => 'Materials',
            'timing' => 'Timing',
            'grading' => 'Grading',
            'other' => 'Other'
        ];
    }

    /**
     * Scope a query to only include active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include global rules.
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /**
     * Scope a query to only include rules for a specific category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include mandatory rules.
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    /**
     * Scope a query to order rules by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    /**
     * Get all applicable rules for an exam (includes global rules).
     */
    public static function getApplicableRules($examId)
    {
        return self::where(function($query) use ($examId) {
            $query->where('exam_id', $examId)
                ->orWhere('is_global', true);
        })
        ->where('is_active', true)
        ->orderBy('display_order')
        ->get();
    }
}
