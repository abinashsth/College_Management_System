<?php

namespace App\Models\Analytics;

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
        'slug',
        'description',
        'entity_type',
        'field_name',
        'is_active',
        'grouping_options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'grouping_options' => 'json',
    ];

    /**
     * Scope a query to only include active dimensions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get dimensions for a specific entity type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $entityType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForEntityType($query, $entityType)
    {
        return $query->where('entity_type', $entityType);
    }
} 