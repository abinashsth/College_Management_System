<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'student_id',
        'class_id', 
        'academic_year',
        'tuition_fee',
        'admission_fee',
        'exam_fee',
        'status'
    ];

    protected $casts = [
        'tuition_fee' => 'float',
        'admission_fee' => 'float', 
        'exam_fee' => 'float',
        
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function academicYear() 
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getTotalFeeAttribute()
    {
        return $this->tuition_fee + 
               $this->admission_fee + 
               $this->exam_fee + 
               $this->lab_fee +
               $this->library_fee +
               $this->sports_fee + 
               $this->transport_fee +
               $this->hostel_fee;
    }
}
