<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'fee_category_id',
        'amount',
        'frequency',
        'is_optional',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'is_optional' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the category that this fee type belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    /**
     * Get the fee allocations for this fee type.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(FeeAllocation::class);
    }

    /**
     * Get the invoice items for this fee type.
     */
    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the user who created this fee type.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this fee type.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include active fee types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->amount, 2);
    }

    /**
     * Get human-readable frequency.
     */
    public function getFrequencyTextAttribute()
    {
        $map = [
            'one_time' => 'One Time',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annually' => 'Semi-Annually',
            'annually' => 'Annually',
        ];

        return $map[$this->frequency] ?? $this->frequency;
    }
} 