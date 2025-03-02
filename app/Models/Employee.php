<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employee';

    protected $fillable = [
        'employee_id',
        'name', 
        'email',
        'department',
        'designation',
        'contact',
        'salary',
        'status',
        'verified_at'
    ];

    protected $casts = [
        'status' => 'boolean',
        'verified_at' => 'datetime',
        'salary' => 'decimal:2'
    ];

    public function salaryIncrements()
    {
        return $this->hasMany(SalaryIncrement::class);
    }

    public function getStatusLabelAttribute()
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function getDepartmentLabelAttribute()
    {
        $departments = [
            'HR' => 'Human Resources',
            'IT' => 'Information Technology', 
            'Finance' => 'Finance',
            'Marketing' => 'Marketing',
            'Operations' => 'Operations'
        ];

        return $departments[$this->department] ?? $this->department;
    }
}
