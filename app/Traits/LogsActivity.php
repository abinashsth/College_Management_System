<?php

namespace App\Traits;

use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootLogsActivity()
    {
        static::created(function (Model $model) {
            if (method_exists($model, 'isLoggingEnabled') && !$model->isLoggingEnabled()) {
                return;
            }
            
            $module = static::getModuleName();
            ActivityLogService::logCreate($module, $model);
        });

        static::updated(function (Model $model) {
            if (method_exists($model, 'isLoggingEnabled') && !$model->isLoggingEnabled()) {
                return;
            }
            
            $module = static::getModuleName();
            $oldValues = $model->getOriginal();
            
            // Only log if there were actual changes
            if (count($model->getDirty()) > 0) {
                ActivityLogService::logUpdate($module, $model, $oldValues);
            }
        });

        static::deleted(function (Model $model) {
            if (method_exists($model, 'isLoggingEnabled') && !$model->isLoggingEnabled()) {
                return;
            }
            
            $module = static::getModuleName();
            ActivityLogService::logDelete($module, $model);
        });
    }

    /**
     * Get the module name for the model.
     *
     * @return string
     */
    protected static function getModuleName()
    {
        // By default, use the lowercase plural form of the class name
        return isset(static::$logModule) ? static::$logModule : strtolower(class_basename(static::class)) . 's';
    }

    /**
     * Disable activity logging for the current operation.
     *
     * @param callable $callback
     * @return mixed
     */
    public static function withoutLogging(callable $callback)
    {
        $wasLogging = static::$loggingEnabled ?? true;
        static::$loggingEnabled = false;

        try {
            return $callback();
        } finally {
            static::$loggingEnabled = $wasLogging;
        }
    }

    /**
     * Check if logging is enabled.
     *
     * @return bool
     */
    public function isLoggingEnabled()
    {
        return static::$loggingEnabled ?? true;
    }

    /**
     * Get the activity logs for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activityLogs()
    {
        return $this->morphMany('App\Models\ActivityLog', 'loggable');
    }
} 