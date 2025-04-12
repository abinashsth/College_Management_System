<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsMetric extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'entity_type',
        'calculation_method',
        'calculation_query',
        'display_format',
        'unit',
        'aggregation_type',
        'is_active',
        'is_system',
        'target_value',
        'refresh_frequency',
        'last_calculated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'target_value' => 'float',
        'last_calculated_at' => 'datetime',
    ];

    /**
     * Get the data points for this metric.
     */
    public function dataPoints()
    {
        return $this->hasMany(AnalyticsDataPoint::class, 'metric_id');
    }

    /**
     * Get dimensions that can be applied to this metric.
     */
    public function dimensions()
    {
        return AnalyticsDimension::where('entity_type', $this->entity_type)
                                ->where('is_active', true)
                                ->get();
    }

    /**
     * Get the latest data points for this metric (one per dimension value).
     */
    public function latestDataPoints()
    {
        // This is a simplified approach; in a real system, you might need a more sophisticated query
        return $this->dataPoints()->latest('recorded_at')->get();
    }

    /**
     * Get the current value of this metric (most recent data point).
     */
    public function currentValue()
    {
        return $this->dataPoints()->latest('recorded_at')->first()?->metric_value;
    }

    /**
     * Calculate the trend percentage comparing current value to previous period.
     */
    public function trendPercentage($currentPeriodStart, $previousPeriodStart)
    {
        $currentValue = $this->dataPoints()
                            ->where('recorded_at', '>=', $currentPeriodStart)
                            ->avg('metric_value') ?? 0;
        
        $previousValue = $this->dataPoints()
                            ->where('recorded_at', '>=', $previousPeriodStart)
                            ->where('recorded_at', '<', $currentPeriodStart)
                            ->avg('metric_value') ?? 0;
        
        if ($previousValue == 0) {
            return null; // Cannot calculate percentage
        }
        
        return (($currentValue - $previousValue) / $previousValue) * 100;
    }

    /**
     * Get the dashboards that include this metric.
     */
    public function dashboards()
    {
        return $this->belongsToMany(Dashboard::class, 'dashboard_metrics')
                    ->withPivot('widget_type', 'position_x', 'position_y', 'width', 'height', 'widget_config')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active metrics.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include system metrics.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include custom metrics.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope a query to only include metrics for a specific entity type.
     */
    public function scopeForEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to order metrics by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }
} 