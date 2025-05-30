<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_id',
        'type',
        'amount',
        'description',
    ];

            
        public function salary()
        {
            return $this->belongsTo(Salary::class);
        }

}

