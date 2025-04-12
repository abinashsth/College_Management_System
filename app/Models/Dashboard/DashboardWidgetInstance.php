<?php

namespace App\Models\Dashboard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidgetInstance extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dashboard_id',
        'widget_id',
        'position_x',
        'position_y',
        'width',
        'height',
        'instance_config',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'instance_config' => 'json',
    ];

    /**
     * Get the dashboard that owns the widget instance.
     */
    public function dashboard(): BelongsTo
    {
        return $this->belongsTo(UserDashboard::class, 'dashboard_id');
    }

    /**
     * Get the widget that owns the widget instance.
     */
    public function widget(): BelongsTo
    {
        return $this->belongsTo(DashboardWidget::class, 'widget_id');
    }

    /**
     * Scope a query to only include widget instances for a specific dashboard.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $dashboardId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForDashboard($query, $dashboardId)
    {
        return $query->where('dashboard_id', $dashboardId);
    }

    /**
     * Scope a query to only include widget instances for a specific widget.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $widgetId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForWidget($query, $widgetId)
    {
        return $query->where('widget_id', $widgetId);
    }

    /**
     * Get the effective configuration for this widget instance,
     * merging the instance-specific configuration with the widget's default configuration.
     *
     * @return array
     */
    public function getEffectiveConfig(): array
    {
        $widgetConfig = $this->widget->visualization_config ?? [];
        $instanceConfig = $this->instance_config ?? [];

        return array_merge((array) $widgetConfig, (array) $instanceConfig);
    }
} 