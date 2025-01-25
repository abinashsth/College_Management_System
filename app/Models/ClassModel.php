<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassModel extends Model
{
    use HasFactory;
    protected $table = 'classes';

    /**
     * Get the students for the class
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}