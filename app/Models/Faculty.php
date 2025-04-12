<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'faculties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'logo',
        'contact_email',
        'contact_phone',
        'website',
        'address',
        'established_date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'established_date' => 'date',
        'status' => 'boolean',
    ];

    /**
     * Create a new Eloquent model instance with faculty type
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Set the default type for faculty
        $this->attributes['type'] = 'faculty';
    }

    /**
     * Get all departments in this faculty.
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'faculty_id');
    }

    /**
     * Get all events for this faculty.
     */
    public function events()
    {
        return $this->hasMany(FacultyEvent::class);
    }

    /**
     * Get all staff assigned to this faculty.
     */
    public function staff()
    {
        return $this->belongsToMany(User::class, 'faculty_staff', 'faculty_id', 'user_id')
            ->withPivot('position', 'start_date', 'end_date', 'is_active')
            ->withTimestamps();
    }

    /**
     * Scope query to include only faculties.
     * This is a temporary fix until the database schema is updated.
     */
    protected static function boot()
    {
        parent::boot();
        
        // No global scopes that would filter faculties
    }
} 