<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    protected $fillable = [
        'student_id',
        'fee_type',
        'amount',
        'due_date',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}