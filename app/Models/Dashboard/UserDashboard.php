<?php

namespace App\Models\Dashboard;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserDashboard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'is_default',
        'is_shared',
        'layout',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_shared' => 'boolean',
        'layout' => 'json',
    ];

    /**
     * Get the user that owns the dashboard.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the widget instances for this dashboard.
     */
    public function widgetInstances(): HasMany
    {
        return $this->hasMany(DashboardWidgetInstance::class, 'dashboard_id');
    }

    /**
     * Get the widgets for this dashboard through the widget instances.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function widgets()
    {
        return $this->hasManyThrough(
            DashboardWidget::class,
            DashboardWidgetInstance::class,
            'dashboard_id',
            'id',
            'id',
            'widget_id'
        );
    }

    /**
     * Scope a query to only include default dashboards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefaultDashboards($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include shared dashboards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    /**
     * Scope a query to only include dashboards for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include dashboards visible to a specific user.
     * This includes dashboards owned by the user and shared dashboards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Models\User  $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisibleToUser($query, User $user)
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('is_shared', true);
        });
    }
} 