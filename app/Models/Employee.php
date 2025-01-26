<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Specify fillable attributes for mass assignment
    protected $fillable = [
        'name',
        'email',
        'position',
        'salary',
    ];

    // Define relationship with User model
    public function user()
    {
       
        $employeeCount = Employee::count();

        return $this->belongsTo(User::class);
    }

    
}
