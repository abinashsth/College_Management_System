<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SalaryIncrement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'current_salary',
        'increment_amount', 
        'new_salary',
        'effective_date',
        'status',
        'remarks',
        'created_by',
        'updated_by',
        'amount',
        'reason',
        'approved_by'
    ];

    protected $casts = [
        'effective_date' => 'date'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() 
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getEffectiveDateAttribute($value)
    {
        return Carbon::parse($value);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }
}
