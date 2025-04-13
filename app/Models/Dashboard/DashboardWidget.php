<?php

namespace App\Models\Dashboard;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DashboardWidget extends Model
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
        'widget_type',
        'data_source',
        'visualization_config',
        'refresh_interval',
        'is_system',
        'is_active',
        'query',
        'permissions',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_source' => 'json',
        'visualization_config' => 'json',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'permissions' => 'json',
    ];

    /**
     * Get the user who created the widget.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the widget instances.
     */
    public function instances(): HasMany
    {
        return $this->hasMany(DashboardWidgetInstance::class, 'widget_id');
    }

    /**
     * Scope a query to only include active widgets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include system widgets.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include widgets of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('widget_type', $type);
    }

    /**
     * Scope a query to only include widgets that a user has permission to see.
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
            $q->where('created_by', $user->id)
                ->orWhere('is_system', true)
                ->orWhereNull('permissions')
                ->orWhere('permissions', '[]')
                ->orWhereRaw("JSON_CONTAINS(permissions, ?)", [json_encode($user->roles->pluck('name')->toArray())]);
        });
    }
} 