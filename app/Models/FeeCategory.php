<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $fillable = ['name', 'description'];
    
    public function category()
    {
        return $this->belongsTo(FeeStructure::class);
    }   


    public function feeStructures()
    {

        return $this->hasMany(FeeStructure::class);
    }
}
