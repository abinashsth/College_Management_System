<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'dimension_id',
        'date',
        'metric_value',
        'dimension_value',
        'entity_id',
        'academic_year_id',
        'semester_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
        'metric_value' => 'float',
    ];

    /**
     * Get the metric that owns the data point.
     */
    public function metric()
    {
        return $this->belongsTo(AnalyticsMetric::class);
    }

    /**
     * Get the dimension that owns the data point.
     */
    public function dimension()
    {
        return $this->belongsTo(AnalyticsDimension::class);
    }

    /**
     * Get the academic year that owns the data point.
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the semester that owns the data point.
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    /**
     * Scope a query to only include data points for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include data points for a specific metric.
     */
    public function scopeForMetric($query, $metricId)
    {
        return $query->where('metric_id', $metricId);
    }

    /**
     * Scope a query to only include data points for a specific dimension.
     */
    public function scopeForDimension($query, $dimensionId)
    {
        return $query->where('dimension_id', $dimensionId);
    }

    /**
     * Scope a query to only include data points for a specific dimension value.
     */
    public function scopeForDimensionValue($query, $dimensionValue)
    {
        return $query->where('dimension_value', $dimensionValue);
    }

    /**
     * Scope a query to only include data points for a specific entity.
     */
    public function scopeForEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    /**
     * Scope a query to only include data points for a specific academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope a query to only include data points for a specific semester.
     */
    public function scopeForSemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    /**
     * Get the latest data point for a specific metric and dimension.
     */
    public static function getLatestForMetricAndDimension($metricId, $dimensionId = null, $dimensionValue = null)
    {
        $query = self::forMetric($metricId)
                    ->orderByDesc('date');
        
        if ($dimensionId) {
            $query->forDimension($dimensionId);
            
            if ($dimensionValue) {
                $query->forDimensionValue($dimensionValue);
            }
        }
        
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'dimension_id',
        'dimension_value',
        'metric_value',
        'recorded_at',
        'entity_id',
        'entity_type',
        'data_source',
        'is_calculated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metric_value' => 'float',
        'recorded_at' => 'datetime',
        'is_calculated' => 'boolean',
    ];

    /**
     * Get the metric that owns the data point.
     */
    public function metric()
    {
        return $this->belongsTo(AnalyticsMetric::class, 'metric_id');
    }

    /**
     * Get the dimension that owns the data point.
     */
    public function dimension()
    {
        return $this->belongsTo(AnalyticsDimension::class, 'dimension_id');
    }

    /**
     * Polymorphic relation to the entity this data point belongs to.
     */
    public function entity()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include data points for a specific metric.
     */
    public function scopeForMetric($query, $metricId)
    {
        return $query->where('metric_id', $metricId);
    }

    /**
     * Scope a query to only include data points for a specific dimension.
     */
    public function scopeForDimension($query, $dimensionId)
    {
        return $query->where('dimension_id', $dimensionId);
    }

    /**
     * Scope a query to only include data points with a specific dimension value.
     */
    public function scopeWithDimensionValue($query, $value)
    {
        return $query->where('dimension_value', $value);
    }

    /**
     * Scope a query to only include data points for a specific entity.
     */
    public function scopeForEntity($query, $entityType, $entityId)
    {
        return $query->where('entity_type', $entityType)
                    ->where('entity_id', $entityId);
    }

    /**
     * Scope a query to only include data points recorded after a specific date.
     */
    public function scopeAfter($query, $date)
    {
        return $query->where('recorded_at', '>=', $date);
    }

    /**
     * Scope a query to only include data points recorded before a specific date.
     */
    public function scopeBefore($query, $date)
    {
        return $query->where('recorded_at', '<=', $date);
    }

    /**
     * Scope a query to only include data points recorded between two dates.
     */
    public function scopeBetween($query, $startDate, $endDate)
    {
        return $query->where('recorded_at', '>=', $startDate)
                    ->where('recorded_at', '<=', $endDate);
    }

    /**
     * Scope a query to only include the latest data points for each dimension value.
     */
    public function scopeLatest($query)
    {
        return $query->latest('recorded_at');
    }
} 