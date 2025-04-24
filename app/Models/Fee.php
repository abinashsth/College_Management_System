<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_type',
        'amount',
        'due_date',
        'status',
        'academic_session_id',
        'payment_date',
        'payment_method',
        'transaction_id',
        'remarks'
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }
}