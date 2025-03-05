<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;




class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'effective_date',
        'payment_type',
        'notes',
    ];

    
    protected $casts = [
        'effective_date' => 'date',
    ];
    
    // ✅ Define the missing relationship
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    
}
