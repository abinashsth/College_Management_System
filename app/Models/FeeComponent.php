<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeeStructure;
use App\Models\StudentFee;
use App\Models\User;

class FeeComponent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fee_structure_id',
        'name',
        'description',
        'amount',
        'is_optional',
        'is_recurring',
        'recurrence_period',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'is_optional' => 'boolean',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the fee structure that this component belongs to.
     */
    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    /**
     * Get the student fees associated with this component.
     */
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    /**
     * Get the user who created this fee component.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
