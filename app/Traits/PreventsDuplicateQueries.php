<?php

namespace App\Traits;

/**
 * Trait PreventsDuplicateQueries
 * 
 * This trait provides utility methods to prevent N+1 query issues
 * by standardizing eager loading patterns across controllers.
 */
trait PreventsDuplicateQueries
{
    /**
     * Default relationships to eager load for each model type.
     * 
     * @var array
     */
    protected $eagerLoadMap = [
        'AcademicStructure' => ['parent'],
        'Faculty' => ['dean', 'departments'],
        'Department' => ['faculty', 'head', 'programs'],
        'Program' => ['department', 'coordinator', 'courses'],
        'Course' => ['department', 'prerequisites'],
        'Student' => ['program', 'department', 'class', 'academicSession'],
        'Exam' => ['class', 'subject', 'academicSession'],
        'User' => ['roles', 'permissions'],
    ];

    /**
     * Eager load standard relationships for a query builder.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $modelClass The class name of the model (without namespace)
     * @param array $additional Additional relationships to eager load
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function loadStandardRelations($query, $modelClass, array $additional = [])
    {
        $modelClass = class_basename($modelClass);
        
        $relations = isset($this->eagerLoadMap[$modelClass]) 
            ? $this->eagerLoadMap[$modelClass] 
            : [];
            
        $relations = array_merge($relations, $additional);
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query;
    }
    
    /**
     * Apply eager loading to an existing model or collection.
     * 
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection $models
     * @param string $modelClass The class name of the model (without namespace)
     * @param array $additional Additional relationships to eager load
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     */
    protected function eagerLoadRelations($models, $modelClass, array $additional = [])
    {
        $modelClass = class_basename($modelClass);
        
        $relations = isset($this->eagerLoadMap[$modelClass]) 
            ? $this->eagerLoadMap[$modelClass] 
            : [];
            
        $relations = array_merge($relations, $additional);
        
        if (!empty($relations) && $models) {
            $models->load($relations);
        }
        
        return $models;
    }
    
    /**
     * Extend the eager loading map with custom relationships.
     * 
     * @param array $customMap
     * @return void
     */
    protected function extendEagerLoadMap(array $customMap)
    {
        $this->eagerLoadMap = array_merge($this->eagerLoadMap, $customMap);
    }
} 