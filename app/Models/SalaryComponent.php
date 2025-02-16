<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryComponent extends Model
{
    use HasFactory;

    protected $table = 'salary_Components';
    protected $fillable = [
        'name',
        'type',
        'status',
        'description'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function employeeSalaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    
    public function scopeFixed($query)
    {
        return $query->where('type', 'Fixed');
    }
    public function scopeAllowances($query)
    {
        return $query->where('type', 'Allowance');
    }

    public function scopeDeductions($query)
    {
        return $query->where('type', 'Deduction');
    }
}
