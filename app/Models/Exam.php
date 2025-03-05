<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'session_id',
        'faculty_id',
        'class_id',
        'exam_date',
        'status'
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
} 