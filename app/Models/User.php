<?php
namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, Notifiable, LogsActivity;

    /**
     * The module name for activity logs.
     */
    protected static $logModule = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Check if user has permission through role or is super-admin
     */
    public function checkPermission($permission): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->hasPermissionTo($permission);
    }
}