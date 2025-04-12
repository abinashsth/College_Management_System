<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsDimension extends Model
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
        'source_table',
        'source_column',
        'value_type',
        'is_active',
        'is_system',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the data points for this dimension.
     */
    public function dataPoints()
    {
        return $this->hasMany(AnalyticsDataPoint::class, 'dimension_id');
    }

    /**
     * Get the distinct values for this dimension.
     */
    public function distinctValues()
    {
        return $this->dataPoints()
                    ->select('dimension_value')
                    ->distinct()
                    ->pluck('dimension_value')
                    ->toArray();
    }

    /**
     * Get the most common values for this dimension.
     */
    public function topValues($limit = 10)
    {
        return $this->dataPoints()
                    ->select('dimension_value')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('dimension_value')
                    ->orderByDesc('count')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Scope a query to only include active dimensions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include system dimensions.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include custom dimensions.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope a query to only include dimensions for a specific entity type.
     */
    public function scopeForEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }

    /**
     * Scope a query to order dimensions by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc');
    }
} 