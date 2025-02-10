<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'contact_number',
        'telephone',
        'gender',
        'dob',
        'nationality',
        'state',
        'lga',
        'blood_group',
        'passport_photo',
        'status',
        'class_id'
    ];

    protected $casts = [
        'dob' => 'date',
        'status' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_student')
            ->withPivot('grade', 'remarks')
            ->withTimestamps();
    }
}