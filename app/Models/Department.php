<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';
    
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
        'faculty_id',
        'logo',
        'contact_email',
        'contact_phone',
        'website',
        'address',
        'established_date',
        'type',
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
     * Create a new Eloquent model instance with department type
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Set the default type for department
        $this->attributes['type'] = 'department';
    }

    /**
     * Get the faculty that this department belongs to.
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Get the department head associated with the department.
     */
    public function head()
    {
        return $this->hasOne(DepartmentHead::class);
    }

    /**
     * Get all programs in this department.
     */
    public function programs()
    {
        return $this->hasMany(Program::class, 'department_id');
    }

    /**
     * Get all courses in this department.
     * This includes courses from all programs in the department.
     */
    public function courses()
    {
        // Use a custom query since courses and programs have a many-to-many relationship
        return Course::whereHas('programs', function($query) {
            $query->where('programs.department_id', $this->id);
        });
    }

    /**
     * Get all teachers assigned to this department.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'department_teachers', 'department_id', 'user_id')
            ->withPivot('position', 'start_date', 'end_date', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get all students in this department through programs.
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, Program::class, 'department_id', 'program_id');
    }

    /**
     * Scope query to include only departments.
     * This is a temporary fix until the database schema is updated.
     */
    protected static function boot()
    {
        parent::boot();
        
        // No global scopes that would filter departments
    }
} 