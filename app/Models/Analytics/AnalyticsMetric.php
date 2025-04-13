<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'slug',
        'description',
        'data_type',
        'aggregation_type',
        'is_active',
        'display_options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'display_options' => 'json',
    ];

    /**
     * Get the data points for this metric.
     */
    public function dataPoints(): HasMany
    {
        return $this->hasMany(AnalyticsDataPoint::class, 'metric_id');
    }

    /**
     * Scope a query to only include active metrics.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 