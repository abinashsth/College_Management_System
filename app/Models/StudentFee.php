<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\FeeComponent;
use App\Models\User;

class StudentFee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'fee_component_id',
        'amount',
        'due_date',
        'paid_amount',
        'discount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'status',
        'remarks',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    /**
     * Get the student that this fee belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the fee structure that this fee belongs to.
     */
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Get the fee component that this fee belongs to.
     */
    public function feeComponent()
    {
        return $this->belongsTo(FeeComponent::class);
    }

    /**
     * Get the user who created this fee.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the remaining amount to be paid.
     */
    public function getRemainingAmountAttribute()
    {
        return $this->amount - $this->paid_amount - $this->discount;
    }

    /**
     * Check if the fee is fully paid.
     */
    public function getIsFullyPaidAttribute()
    {
        return $this->remaining_amount <= 0;
    }
}
