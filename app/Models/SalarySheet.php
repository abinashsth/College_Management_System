<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use App\Models\Employee;    

class SalarySheet extends Model
{
    protected $fillable = [
        'month',
        'payment_date',
        'department_id',
        'employee_id',
        'basic_salary',
        'allowance',
        'deduction',
        'total_salary',
        'status'
        ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
