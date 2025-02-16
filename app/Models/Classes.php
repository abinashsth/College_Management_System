<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classes extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_name',
        'section',
        'is_active'
    ];

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->is_active = $model->is_active ?? true;
        });
    }
} 