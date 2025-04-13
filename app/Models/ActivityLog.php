<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'action',
        'module',
        'loggable_type',
        'loggable_id',
        'description',
        'old_values',
        'new_values',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related model.
     */
    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include activities for a specific module.
     */
    public function scopeInModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to only include activities for a specific action.
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include activities by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
