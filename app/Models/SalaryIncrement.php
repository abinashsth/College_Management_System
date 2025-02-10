<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalaryIncrement extends Model
{
    protected $fillable = [
        'employee_id',
        'increment_amount',
      'increment_date'
    ];  

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }   

    public function getIncrementDateAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }   
}
