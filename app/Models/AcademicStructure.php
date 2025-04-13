<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicStructure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type', // faculty, department, program
        'code',
        'description',
        'parent_id',
        'is_active',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the parent academic structure.
     * 
     * This relationship lets us traverse up the academic hierarchy.
     * For example, departments can access their parent faculty.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(AcademicStructure::class, 'parent_id');
    }

    /**
     * Get the children academic structures.
     * 
     * This relationship lets us traverse down the academic hierarchy.
     * For example, faculties can access their child departments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(AcademicStructure::class, 'parent_id');
    }

    /**
     * Scope a query to only include faculties.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFaculties($query)
    {
        // Check if we're using the faculties table directly
        if ($this->getTable() === 'faculties') {
            return $query;
        }
        
        // Otherwise, use the type column for filtering
        return $query->where('type', 'faculty');
    }

    /**
     * Scope a query to only include departments.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDepartments($query)
    {
        // Check if we're using the departments table directly
        if ($this->getTable() === 'departments') {
            return $query;
        }
        
        // Otherwise, use the type column for filtering
        return $query->where('type', 'department');
    }

    /**
     * Scope a query to only include programs.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrograms($query)
    {
        // Check if we're using the programs table directly
        if ($this->getTable() === 'programs') {
            return $query;
        }
        
        // Otherwise, use the type column for filtering
        return $query->where('type', 'program');
    }

    /**
     * Scope a query to only include active structures.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 