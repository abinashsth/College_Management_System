<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'fee_type_id',
        'fee_name',
        'amount',
        'discount',
        'tax',
        'total',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns this item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the fee type associated with this item.
     */
    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class);
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->amount, 2);
    }

    /**
     * Get the formatted discount with currency.
     */
    public function getFormattedDiscountAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->discount, 2);
    }

    /**
     * Get the formatted tax with currency.
     */
    public function getFormattedTaxAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->tax, 2);
    }

    /**
     * Get the formatted total with currency.
     */
    public function getFormattedTotalAttribute()
    {
        return config('app.currency_symbol', '$') . number_format($this->total, 2);
    }

    /**
     * Calculate and update the total amount.
     */
    public function calculateTotal()
    {
        $this->total = $this->amount + $this->tax - $this->discount;
        return $this;
    }
} 