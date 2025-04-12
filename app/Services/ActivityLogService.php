<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an activity.
     *
     * @param string $action The action performed (e.g., 'created', 'updated', 'deleted')
     * @param string $module The module where the action was performed (e.g., 'users', 'students')
     * @param Model $model The model that was affected
     * @param string|null $description Optional description of the activity
     * @param array|null $oldValues Optional array of old values (for updates)
     * @param array|null $newValues Optional array of new values (for updates)
     * @return ActivityLog
     */
    public static function log(
        string $action,
        string $module,
        Model $model,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'action' => $action,
            'module' => $module,
            'loggable_type' => get_class($model),
            'loggable_id' => $model->getKey(),
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    /**
     * Log a create activity.
     *
     * @param string $module The module where the action was performed
     * @param Model $model The model that was created
     * @param string|null $description Optional description
     * @return ActivityLog
     */
    public static function logCreate(string $module, Model $model, ?string $description = null): ActivityLog
    {
        return self::log('created', $module, $model, $description, null, $model->toArray());
    }

    /**
     * Log an update activity.
     *
     * @param string $module The module where the action was performed
     * @param Model $model The model that was updated
     * @param array $oldValues The original values
     * @param string|null $description Optional description
     * @return ActivityLog
     */
    public static function logUpdate(
        string $module,
        Model $model,
        array $oldValues,
        ?string $description = null
    ): ActivityLog {
        return self::log('updated', $module, $model, $description, $oldValues, $model->toArray());
    }

    /**
     * Log a delete activity.
     *
     * @param string $module The module where the action was performed
     * @param Model $model The model that was deleted
     * @param string|null $description Optional description
     * @return ActivityLog
     */
    public static function logDelete(string $module, Model $model, ?string $description = null): ActivityLog
    {
        return self::log('deleted', $module, $model, $description, $model->toArray(), null);
    }

    /**
     * Log a login activity.
     *
     * @param Model $user The user that logged in
     * @return ActivityLog
     */
    public static function logLogin(Model $user): ActivityLog
    {
        return self::log('login', 'authentication', $user, 'User logged in');
    }

    /**
     * Log a logout activity.
     *
     * @param Model $user The user that logged out
     * @return ActivityLog
     */
    public static function logLogout(Model $user): ActivityLog
    {
        return self::log('logout', 'authentication', $user, 'User logged out');
    }
} 