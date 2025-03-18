<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'full_name',
        'email',
        'phone',
        'department_id',
        'designation',
        'joining_date',
        'basic_salary',
        'allowances',
        'deductions',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'joining_date' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function getNetSalaryAttribute(): float
    {
        return $this->basic_salary + $this->allowances - $this->deductions;
    }
}