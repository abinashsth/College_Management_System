<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'session_id',
        'fee_type',
        'amount',
        'paid_amount',
        'due_amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'remarks',
    ];

    /**
     * Get the student that owns the ledger.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the session that owns the ledger.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
} 