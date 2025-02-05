<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'basic_salary', 'allowances', 'deductions',
        'status', 'payment_date', 'payment_method'
    ];
    
    // ✅ Define the missing relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
