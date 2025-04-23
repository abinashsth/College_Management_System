<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkComponent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mark_id',
        'component_name',
        'marks_obtained',
        'total_marks',
        'weight_percentage',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'weight_percentage' => 'decimal:2',
    ];

    /**
     * Get the mark that owns the component.
     */
    public function mark()
    {
        return $this->belongsTo(Mark::class);
    }

    /**
     * Calculate the weighted marks for this component.
     *
     * @return float|null
     */
    public function getWeightedMarks()
    {
        if ($this->marks_obtained === null || $this->total_marks <= 0) {
            return null;
        }

        $percentage = ($this->marks_obtained / $this->total_marks) * 100;
        return ($percentage * $this->weight_percentage) / 100;
    }

    /**
     * Calculate the percentage of marks obtained.
     *
     * @return float
     */
    public function getPercentageAttribute()
    {
        if ($this->total_marks > 0) {
            return ($this->marks_obtained / $this->total_marks) * 100;
        }
        
        return 0;
    }

    /**
     * Calculate the weighted total marks.
     *
     * @return float
     */
    public function getWeightedTotalAttribute()
    {
        return $this->total_marks * ($this->weight_percentage / 100);
    }
} 