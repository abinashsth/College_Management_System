<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';

    /**
     * Get the students for the class
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}