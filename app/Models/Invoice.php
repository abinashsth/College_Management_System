<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_no',
        'student_id',
        'academic_year_id',
        'term',
        'issue_date',
        'due_date',
        'total_amount',
        'paid_amount',
        'discount_amount',
        'discount_note',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the student associated with this invoice.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic year associated with this invoice.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the items for this invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the payments for this invoice.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user who created this invoice.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this invoice.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the due amount.
     */
    public function getDueAmountAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount - $this->discount_amount);
    }

    /**
     * Get the formatted total amount with currency.
     */
    public function getFormattedTotalAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->total_amount, 2);
    }

    /**
     * Get the formatted paid amount with currency.
     */
    public function getFormattedPaidAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->paid_amount, 2);
    }

    /**
     * Get the formatted due amount with currency.
     */
    public function getFormattedDueAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->due_amount, 2);
    }

    /**
     * Get the formatted discount amount with currency.
     */
    public function getFormattedDiscountAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->discount_amount, 2);
    }

    /**
     * Check if the invoice is overdue.
     */
    public function isOverdue()
    {
        return $this->due_date < Carbon::today() && $this->status != 'paid';
    }

    /**
     * Update the status based on payment.
     */
    public function updateStatus()
    {
        if ($this->due_amount <= 0) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } elseif ($this->due_date < Carbon::today()) {
            $this->status = 'overdue';
        } else {
            $this->status = 'pending';
        }

        $this->save();
    }

    /**
     * Scope a query to only include invoices for a specific student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to only include invoices for a specific academic year.
     */
    public function scopeForAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    /**
     * Scope a query to only include invoices for a specific term.
     */
    public function scopeForTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'partial', 'overdue']);
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())
            ->whereIn('status', ['pending', 'partial']);
    }
} 