<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classes;
use App\Models\AcademicSession;
use App\Models\FeeComponent;
use App\Models\StudentFee;
use App\Models\User;

class FeeStructure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'class_id',
        'academic_session_id',
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the class that this fee structure belongs to.
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the academic session that this fee structure belongs to.
     */
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'academic_session_id');
    }

    /**
     * Get the fee components for this fee structure.
     */
    public function feeComponents()
    {
        return $this->hasMany(FeeComponent::class);
    }

    /**
     * Get the student fees associated with this fee structure.
     */
    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    /**
     * Get the user who created this fee structure.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this fee structure.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
