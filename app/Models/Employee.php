<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

     // Add this to your Employee model if the table name is singular
        protected $table = 'employee';


    // Specify fillable attributes for mass assignment
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

    public function salaryIncrements()
    {
        return $this->hasMany(SalaryIncrement::class);
    }
    

    
}
