<?php

namespace App\Models\Analytics;

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsDataPoint extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'metric_id',
        'entity_type',
        'entity_id',
        'academic_year_id',
        'academic_term_id',
        'date',
        'dimensions',
        'value',
        'text_value',
        'additional_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'dimensions' => 'json',
        'value' => 'decimal:2',
        'additional_data' => 'json',
    ];

    /**
     * Get the metric that owns the data point.
     */
    public function metric(): BelongsTo
    {
        return $this->belongsTo(AnalyticsMetric::class, 'metric_id');
    }

    /**
     * Get the academic year that owns the data point.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * Get the academic term that owns the data point.
     */
    public function academicTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicTerm::class, 'academic_term_id');
    }

    /**
     * Scope a query to only include data points for a specific entity.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $entityType
     * @param  int  $entityId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
            ->where('entity_id', $entityId);
    }

    /**
     * Scope a query to only include data points for a specific metric.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $metricId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForMetric($query, $metricId)
    {
        return $query->where('metric_id', $metricId);
    }

    /**
     * Scope a query to only include data points for a specific academic year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $academicYearId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope a query to only include data points for a specific date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $startDate
     * @param  string  $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
} 