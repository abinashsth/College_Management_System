<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GradeSystem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'pass_percentage',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'pass_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // When creating a new default grade system, ensure it's the only default
        static::creating(function ($gradeSystem) {
            if ($gradeSystem->is_default) {
                self::where('is_default', true)->update(['is_default' => false]);
            }
        });

        // When updating a grade system to be default, ensure it's the only default
        static::updating(function ($gradeSystem) {
            if ($gradeSystem->isDirty('is_default') && $gradeSystem->is_default) {
                self::where('id', '!=', $gradeSystem->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the scales defined in this grade system.
     */
    public function scales(): HasMany
    {
        return $this->hasMany(GradeScale::class);
    }

    /**
     * Get the user who created this grade system.
     */
    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * Get the default grade system.
     */
    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Set this grade system as the default.
     */
    public function setAsDefault(): self
    {
        // First, remove default status from all other grade systems
        static::where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);

        return $this;
    }

    /**
     * Find the appropriate grade scale for a given percentage.
     *
     * @param float $percentage The percentage to find a grade for
     * @return GradeScale|null The matching grade scale, or null if none found
     */
    public function findGradeForPercentage(float $percentage): ?GradeScale
    {
        return $this->scales()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();
    }

    /**
     * Calculate GPA and grade for a given percentage.
     *
     * @param float $percentage Student's percentage
     * @return array{gpa: float, grade: string, remarks: string|null, is_passed: bool}
     */
    public function calculateGradeData(float $percentage): array
    {
        $scale = $this->findGradeForPercentage($percentage);

        if (!$scale) {
            // Fallback to the lowest grade
            $scale = $this->scales()->orderBy('min_percentage', 'asc')->first();
        }

        return [
            'gpa' => $scale ? $scale->grade_point : 0.0,
            'grade' => $scale ? $scale->grade : 'F',
            'remarks' => $scale ? $scale->remarks : 'Failed',
            'is_passed' => $scale ? !$scale->isFailing() : false,
        ];
    }

    /**
     * Get the user who last updated this grade system.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get an array of grades for dropdown menus.
     *
     * @return array
     */
    public function getGradesForDropdown(): array
    {
        return $this->scales()
            ->pluck('grade', 'id')
            ->toArray();
    }

    /**
     * Check if this grade system has at least one grade scale defined.
     *
     * @return bool
     */
    public function hasScales(): bool
    {
        return $this->scales()->count() > 0;
    }

    /**
     * Get the results that use this grade system.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get the grade rules for the grade system.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gradeRules()
    {
        return $this->hasMany(GradeRule::class);
    }

    /**
     * Calculate GPA from percentage using this grade system.
     *
     * @param float $percentage
     * @return float|null
     */
    public function calculateGPA($percentage)
    {
        $rule = $this->gradeRules()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        return $rule ? $rule->grade_point : null;
    }

    /**
     * Get grade from percentage using this grade system.
     *
     * @param float $percentage
     * @return string|null
     */
    public function getGrade($percentage)
    {
        $rule = $this->gradeRules()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        return $rule ? $rule->grade : null;
    }

    /**
     * Check if a percentage passes according to this grading system.
     *
     * @param float $percentage
     * @return bool
     */
    public function isPassing($percentage)
    {
        if ($percentage < $this->pass_percentage) {
            return false;
        }

        $rule = $this->gradeRules()
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();

        return $rule ? $rule->is_pass : false;
    }

    /**
     * Get the active grade system.
     *
     * @return self|null
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
} 