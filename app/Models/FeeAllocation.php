<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeAllocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fee_type_id',
        'applicable_to',
        'applicable_id',
        'amount',
        'academic_year_id',
        'due_date',
        'academic_term',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the fee type associated with this allocation.
     */
    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    /**
     * Get the academic year associated with this allocation.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the creator user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater user.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the applicable entity based on the applicable_to type.
     */
    public function applicable()
    {
        switch ($this->applicable_to) {
            case 'class':
                return $this->belongsTo(SchoolClass::class, 'applicable_id');
            case 'program':
                return $this->belongsTo(Program::class, 'applicable_id');
            case 'section':
                return $this->belongsTo(Section::class, 'applicable_id');
            case 'student':
                return $this->belongsTo(Student::class, 'applicable_id');
            default:
                return null;
        }
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        // If no override amount, use the fee type's amount
        $amount = $this->amount ?? $this->feeType->amount;
        return config('app.currency_symbol', '$') . number_format($amount, 2);
    }

    /**
     * Scope a query to only include active allocations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope a query to filter by term.
     */
    public function scopeForTerm($query, $term)
    {
        return $query->where('academic_term', $term);
    }
} 