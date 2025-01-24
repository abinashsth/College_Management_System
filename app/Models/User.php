<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the URL of the user's profile photo.
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Update the user's profile photo.
     */
    public function updateProfilePhoto($photo)
    {
        if ($this->profile_photo_path) {
            Storage::delete($this->profile_photo_path);
        }

        $path = $photo->store('profile-photos', 'public');
        $this->update(['profile_photo_path' => $path]);
    }

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