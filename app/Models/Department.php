<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [ 
        'name',
        'description',
        'status'
    ];

    public function salarySheets()
    {
            return $this->hasMany(SalarySheet::class);
    }
}