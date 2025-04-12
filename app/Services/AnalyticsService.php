<?php

namespace App\Services;

use App\Models\Analytics\AnalyticsDataPoint;
use App\Models\Analytics\AnalyticsDimension;
use App\Models\Analytics\AnalyticsMetric;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Store a data point.
     *
     * @param string $metricSlug
     * @param Model|string|null $entity
     * @param float|int|string|null $value
     * @param array $dimensions
     * @param array $options
     * @return AnalyticsDataPoint|null
     */
    public function recordDataPoint($metricSlug, $entity = null, $value = null, array $dimensions = [], array $options = [])
    {
        // Find the metric
        $metric = AnalyticsMetric::where('slug', $metricSlug)->first();
        
        if (!$metric) {
            return null;
        }
        
        // Prepare entity information
        $entityType = null;
        $entityId = null;
        
        if ($entity instanceof Model) {
            $entityType = get_class($entity);
            $entityId = $entity->id;
        } elseif (is_string($entity)) {
            $entityType = $entity;
        }
        
        // Set up default options
        $date = $options['date'] ?? Carbon::today();
        $academicYearId = $options['academic_year_id'] ?? null;
        $academicTermId = $options['academic_term_id'] ?? null;
        $additionalData = $options['additional_data'] ?? null;
        
        // Create the data point
        $dataPoint = new AnalyticsDataPoint();
        $dataPoint->metric_id = $metric->id;
        $dataPoint->entity_type = $entityType;
        $dataPoint->entity_id = $entityId;
        $dataPoint->academic_year_id = $academicYearId;
        $dataPoint->academic_term_id = $academicTermId;
        $dataPoint->date = $date;
        $dataPoint->dimensions = $dimensions;
        
        // Set the value based on the metric's data type
        switch ($metric->data_type) {
            case 'integer':
                $dataPoint->value = (int) $value;
                break;
            case 'decimal':
                $dataPoint->value = (float) $value;
                break;
            case 'percentage':
                $dataPoint->value = (float) $value;
                break;
            case 'text':
                $dataPoint->text_value = (string) $value;
                break;
            default:
                $dataPoint->value = $value;
        }
        
        $dataPoint->additional_data = $additionalData;
        $dataPoint->save();
        
        return $dataPoint;
    }

    /**
     * Get time series data for a specific metric.
     *
     * @param string $metricSlug
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param string $interval
     * @param array $filters
     * @return Collection
     */
    public function getTimeSeriesData($metricSlug, Carbon $startDate, Carbon $endDate, $interval = 'day', array $filters = [])
    {
        $metric = AnalyticsMetric::where('slug', $metricSlug)->first();
        
        if (!$metric) {
            return collect();
        }
        
        // Build the query
        $query = AnalyticsDataPoint::where('metric_id', $metric->id)
            ->whereBetween('date', [$startDate, $endDate]);
        
        // Apply entity filter if provided
        if (isset($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
            
            if (isset($filters['entity_id'])) {
                $query->where('entity_id', $filters['entity_id']);
            }
        }
        
        // Apply academic year filter if provided
        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        
        // Apply academic term filter if provided
        if (isset($filters['academic_term_id'])) {
            $query->where('academic_term_id', $filters['academic_term_id']);
        }
        
        // Apply dimension filters if provided
        if (isset($filters['dimensions']) && is_array($filters['dimensions'])) {
            foreach ($filters['dimensions'] as $dimension => $value) {
                $query->whereJsonContains("dimensions->{$dimension}", $value);
            }
        }
        
        // Apply date grouping based on interval
        switch ($interval) {
            case 'hour':
                $dateFormat = 'Y-m-d H:00:00';
                break;
            case 'day':
                $dateFormat = 'Y-m-d';
                break;
            case 'week':
                $dateFormat = 'Y-W';
                break;
            case 'month':
                $dateFormat = 'Y-m';
                break;
            case 'quarter':
                $dateFormat = 'Y-n';
                break;
            case 'year':
                $dateFormat = 'Y';
                break;
            default:
                $dateFormat = 'Y-m-d';
        }
        
        // Apply aggregation based on metric's aggregation type
        $query->select(
            DB::raw("DATE_FORMAT(date, '{$dateFormat}') as time_period")
        );
        
        switch ($metric->aggregation_type) {
            case 'sum':
                $query->addSelect(DB::raw('SUM(value) as value'));
                break;
            case 'avg':
                $query->addSelect(DB::raw('AVG(value) as value'));
                break;
            case 'min':
                $query->addSelect(DB::raw('MIN(value) as value'));
                break;
            case 'max':
                $query->addSelect(DB::raw('MAX(value) as value'));
                break;
            case 'count':
                $query->addSelect(DB::raw('COUNT(*) as value'));
                break;
            default:
                $query->addSelect('value');
        }
        
        $query->groupBy('time_period');
        $query->orderBy('time_period');
        
        return $query->get();
    }

    /**
     * Get aggregated data for multiple metrics.
     *
     * @param array $metricSlugs
     * @param array $filters
     * @return Collection
     */
    public function getAggregatedData(array $metricSlugs, array $filters = [])
    {
        $metrics = AnalyticsMetric::whereIn('slug', $metricSlugs)->get();
        
        if ($metrics->isEmpty()) {
            return collect();
        }
        
        $result = collect();
        
        foreach ($metrics as $metric) {
            // Build the query
            $query = AnalyticsDataPoint::where('metric_id', $metric->id);
            
            // Apply date range filter if provided
            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
            }
            
            // Apply entity filter if provided
            if (isset($filters['entity_type'])) {
                $query->where('entity_type', $filters['entity_type']);
                
                if (isset($filters['entity_id'])) {
                    $query->where('entity_id', $filters['entity_id']);
                }
            }
            
            // Apply academic year filter if provided
            if (isset($filters['academic_year_id'])) {
                $query->where('academic_year_id', $filters['academic_year_id']);
            }
            
            // Apply academic term filter if provided
            if (isset($filters['academic_term_id'])) {
                $query->where('academic_term_id', $filters['academic_term_id']);
            }
            
            // Apply dimension filters if provided
            if (isset($filters['dimensions']) && is_array($filters['dimensions'])) {
                foreach ($filters['dimensions'] as $dimension => $value) {
                    $query->whereJsonContains("dimensions->{$dimension}", $value);
                }
            }
            
            // Apply aggregation based on metric's aggregation type
            switch ($metric->aggregation_type) {
                case 'sum':
                    $value = $query->sum('value');
                    break;
                case 'avg':
                    $value = $query->avg('value');
                    break;
                case 'min':
                    $value = $query->min('value');
                    break;
                case 'max':
                    $value = $query->max('value');
                    break;
                case 'count':
                    $value = $query->count();
                    break;
                case 'latest':
                    $latest = $query->latest('date')->first();
                    $value = $latest ? $latest->value : 0;
                    break;
                default:
                    $value = $query->avg('value');
            }
            
            $result->push([
                'metric' => $metric->name,
                'slug' => $metric->slug,
                'value' => $value,
                'data_type' => $metric->data_type,
                'display_options' => $metric->display_options,
            ]);
        }
        
        return $result;
    }

    /**
     * Get data grouped by a dimension.
     *
     * @param string $metricSlug
     * @param string $dimensionSlug
     * @param array $filters
     * @return Collection
     */
    public function getDataByDimension($metricSlug, $dimensionSlug, array $filters = [])
    {
        $metric = AnalyticsMetric::where('slug', $metricSlug)->first();
        $dimension = AnalyticsDimension::where('slug', $dimensionSlug)->first();
        
        if (!$metric || !$dimension) {
            return collect();
        }
        
        // Build the query
        $query = AnalyticsDataPoint::where('metric_id', $metric->id);
        
        // Apply date range filter if provided
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }
        
        // Apply entity filter if provided
        if (isset($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
            
            if (isset($filters['entity_id'])) {
                $query->where('entity_id', $filters['entity_id']);
            }
        }
        
        // Apply academic year filter if provided
        if (isset($filters['academic_year_id'])) {
            $query->where('academic_year_id', $filters['academic_year_id']);
        }
        
        // Apply academic term filter if provided
        if (isset($filters['academic_term_id'])) {
            $query->where('academic_term_id', $filters['academic_term_id']);
        }
        
        // Group by the selected dimension
        $query->select(
            DB::raw("dimensions->'$.{$dimensionSlug}' as dimension_value")
        );
        
        // Apply aggregation based on metric's aggregation type
        switch ($metric->aggregation_type) {
            case 'sum':
                $query->addSelect(DB::raw('SUM(value) as value'));
                break;
            case 'avg':
                $query->addSelect(DB::raw('AVG(value) as value'));
                break;
            case 'min':
                $query->addSelect(DB::raw('MIN(value) as value'));
                break;
            case 'max':
                $query->addSelect(DB::raw('MAX(value) as value'));
                break;
            case 'count':
                $query->addSelect(DB::raw('COUNT(*) as value'));
                break;
            default:
                $query->addSelect('value');
        }
        
        $query->whereNotNull(DB::raw("dimensions->'$.{$dimensionSlug}'"));
        $query->groupBy('dimension_value');
        $query->orderBy('value', 'desc');
        
        return $query->get();
    }

    /**
     * Get comparison data between two time periods.
     *
     * @param string $metricSlug
     * @param Carbon $currentStartDate
     * @param Carbon $currentEndDate
     * @param Carbon $previousStartDate
     * @param Carbon $previousEndDate
     * @param array $filters
     * @return array
     */
    public function getComparisonData($metricSlug, Carbon $currentStartDate, Carbon $currentEndDate, Carbon $previousStartDate, Carbon $previousEndDate, array $filters = [])
    {
        $metric = AnalyticsMetric::where('slug', $metricSlug)->first();
        
        if (!$metric) {
            return [
                'current_value' => 0,
                'previous_value' => 0,
                'change' => 0,
                'change_percentage' => 0,
            ];
        }
        
        // Get current period data
        $currentFilters = array_merge($filters, [
            'start_date' => $currentStartDate,
            'end_date' => $currentEndDate,
        ]);
        
        $currentData = $this->getAggregatedData([$metricSlug], $currentFilters)->first();
        
        // Get previous period data
        $previousFilters = array_merge($filters, [
            'start_date' => $previousStartDate,
            'end_date' => $previousEndDate,
        ]);
        
        $previousData = $this->getAggregatedData([$metricSlug], $previousFilters)->first();
        
        // Calculate changes
        $currentValue = $currentData['value'] ?? 0;
        $previousValue = $previousData['value'] ?? 0;
        $change = $currentValue - $previousValue;
        $changePercentage = $previousValue != 0 ? ($change / abs($previousValue)) * 100 : 0;
        
        return [
            'current_value' => $currentValue,
            'previous_value' => $previousValue,
            'change' => $change,
            'change_percentage' => $changePercentage,
            'metric' => $metric->name,
            'slug' => $metric->slug,
            'data_type' => $metric->data_type,
            'display_options' => $metric->display_options,
        ];
    }
} 