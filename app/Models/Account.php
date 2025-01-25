<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    // Specify fillable attributes for mass assignment
    protected $fillable = [
        'name',
        'email',
        'balance',
        'user_id',
    ];

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}
