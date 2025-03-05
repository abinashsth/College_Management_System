<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuardianProfile extends Model
{
    protected $fillable = [
        'user_id',
        'relationship',
        'occupation',
        'contact_number',
        'emergency_contact',
        'address',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentRelationships(): HasMany
    {
        return $this->hasMany(StudentGuardianRelationship::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_guardian_relationships')
            ->withPivot('relationship_type', 'is_primary')
            ->withTimestamps();
    }
} 