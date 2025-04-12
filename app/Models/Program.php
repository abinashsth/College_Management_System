<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'programs';

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
        'department_id',
        'coordinator_id',
        'duration',
        'duration_unit',
        'credit_hours',
        'degree_level',
        'admission_requirements',
        'curriculum',
        'tuition_fee',
        'max_students',
        'start_date',
        'end_date',
        'type',
        'status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'integer',
        'credit_hours' => 'integer',
        'max_students' => 'integer',
        'tuition_fee' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Create a new Eloquent model instance
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        // Only set the type attribute if the column exists in the table
        // $this->attributes['type'] = 'program';
    }

    /**
     * Get the department that this program belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the coordinator of this program.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * Get all courses in this program.
     * 
     * Uses the same relationship structure as Course::programs() for consistency.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'program_courses')
            ->withPivot('semester', 'year', 'is_elective', 'status')
            ->withTimestamps();
    }

    /**
     * Get all students enrolled in this program.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Get all subjects in this program.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'program_subject')
            ->withPivot('semester', 'year', 'is_core', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Remove the global scope to avoid the SQL error.
     * This is a temporary fix until the database schema is updated.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Comment out the global scope that causes the SQL error
        /*
        static::addGlobalScope('program', function ($query) {
            $query->where('type', 'program');
        });
        */
    }
} 