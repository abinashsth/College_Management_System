<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'department_id',
        'designation',
        'basic_salary',
        'allowances',
        'deductions',
        'email',
        'phone',
        'join_date',
    ];

    protected $casts = [
        'join_date' => 'date',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function getNetSalaryAttribute()
    {
        return $this->basic_salary + $this->allowances - $this->deductions;
    }
}