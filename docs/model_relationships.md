# Model Relationships Best Practices

## Overview

This document outlines the standardized approach for defining and using model relationships in the College Management System. Following these guidelines ensures consistency, prevents N+1 query problems, and makes the codebase more maintainable.

## Table of Contents

1. [Relationship Naming Conventions](#relationship-naming-conventions)
2. [Foreign Key Naming Conventions](#foreign-key-naming-conventions)
3. [Relationship Method Documentation](#relationship-method-documentation)
4. [Eager Loading to Prevent N+1 Queries](#eager-loading-to-prevent-n+1-queries)
5. [Academic Structure Hierarchy](#academic-structure-hierarchy)
6. [Common Relationship Patterns](#common-relationship-patterns)

## Relationship Naming Conventions

### One-to-Many Relationships

- Use the plural form of the related model name when defining a hasMany relationship
- Use the singular form or the specific relationship name for belongsTo relationships

```php
// In User model (One user has many posts)
public function posts()
{
    return $this->hasMany(Post::class);
}

// In Post model (A post belongs to one user)
public function user()
{
    return $this->belongsTo(User::class);
}

// When relationship name differs from model name
public function author()
{
    return $this->belongsTo(User::class, 'user_id');
}
```

### Many-to-Many Relationships

- Use the plural form of the related model name
- Include pivot data with withPivot() when needed
- Always include withTimestamps() when pivot table has timestamps

```php
// In Course model
public function programs()
{
    return $this->belongsToMany(Program::class, 'program_courses')
        ->withPivot('semester', 'year', 'is_elective', 'status')
        ->withTimestamps();
}

// In Program model - use the same structure for consistency
public function courses()
{
    return $this->belongsToMany(Course::class, 'program_courses')
        ->withPivot('semester', 'year', 'is_elective', 'status')
        ->withTimestamps();
}
```

## Foreign Key Naming Conventions

- Use `model_id` naming convention for foreign keys (e.g., `user_id`, `course_id`)
- For polymorphic relationships, use `model_type` and `model_id` convention
- For pivot tables, name them alphabetically (e.g., `course_program` instead of `program_course`)
- Always explicitly specify foreign key names in relationship methods for clarity

```php
// Standard foreign key
public function department()
{
    return $this->belongsTo(Department::class, 'department_id');
}

// Pivot table with explicit foreign keys
public function courses()
{
    return $this->belongsToMany(Course::class, 'program_courses', 'program_id', 'course_id')
        ->withPivot('semester', 'year')
        ->withTimestamps();
}
```

## Relationship Method Documentation

All relationship methods should be documented with:

1. A clear description of the relationship
2. The return type annotation
3. Any special considerations or constraints

```php
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
```

## Eager Loading to Prevent N+1 Queries

### In Controllers

```php
// BAD - N+1 query problem
$academicStructures = AcademicStructure::all();
foreach ($academicStructures as $structure) {
    echo $structure->parent->name; // Triggers a new query for each structure
}

// GOOD - Eager loading
$academicStructures = AcademicStructure::with('parent')->get();
foreach ($academicStructures as $structure) {
    echo $structure->parent->name; // Uses preloaded data
}
```

### Nested Relationships

For nested relationships, use dot notation:

```php
$faculties = Faculty::with(['departments.programs', 'departments.head'])->get();
```

### Dynamic Relationship Loading

When a relationship needs to be loaded conditionally:

```php
$program = Program::find($id);
if ($request->includeCourses) {
    $program->load('courses');
}
```

### Query Optimization for Specific Columns

When you only need certain columns:

```php
$users = User::with(['roles:id,name', 'permissions:id,name'])->get();
```

## Academic Structure Hierarchy

Our system uses a hierarchical structure:

- Faculties contain Departments
- Departments contain Programs
- Programs contain Courses (through many-to-many)

Always respect this hierarchy when defining relationships:

```php
// In Department model
public function faculty()
{
    return $this->belongsTo(Faculty::class, 'parent_id');
}

// In Program model
public function department()
{
    return $this->belongsTo(Department::class, 'parent_id');
}
```

## Common Relationship Patterns

### Self-Referential Relationships

Used in the AcademicStructure model for parent-child relationships:

```php
public function parent()
{
    return $this->belongsTo(AcademicStructure::class, 'parent_id');
}

public function children()
{
    return $this->hasMany(AcademicStructure::class, 'parent_id');
}
```

### User Associations with Timestamps

When tracking when a user performs an action:

```php
public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}

public function updatedBy()
{
    return $this->belongsTo(User::class, 'updated_by');
}
```

### Pivot Tables with Additional Data

Always document pivot table columns when using withPivot():

```php
/**
 * Get teachers assigned to this department.
 * 
 * Pivot data includes:
 * - position: The position of the teacher in the department
 * - start_date: When the teacher started in this department
 * - end_date: When the teacher left the department (null if current)
 * - is_active: Whether the assignment is active
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
 */
public function teachers()
{
    return $this->belongsToMany(User::class, 'department_teachers', 'department_id', 'user_id')
        ->withPivot('position', 'start_date', 'end_date', 'is_active')
        ->withTimestamps();
}
``` 