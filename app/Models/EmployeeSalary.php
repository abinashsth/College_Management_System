<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class EmployeeSalary extends Model
{
    use HasFactory;

    protected $table = 'employee_salary'; // Ensure this matches your database table name
}