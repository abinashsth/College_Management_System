<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mask extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'pattern',
        'placeholder',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created the mask.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the mask.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get built-in mask types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            'phone' => 'Phone Number',
            'date' => 'Date (MM/DD/YYYY)',
            'time' => 'Time (HH:MM)',
            'currency' => 'Currency',
            'percentage' => 'Percentage',
            'email' => 'Email Address',
            'custom' => 'Custom Pattern',
        ];
    }

    /**
     * Get mask pattern based on type.
     *
     * @return string|null
     */
    public function getDefaultPattern()
    {
        switch ($this->type) {
            case 'phone':
                return '(000) 000-0000';
            case 'date':
                return '00/00/0000';
            case 'time':
                return '00:00';
            case 'currency':
                return '#,##0.00';
            case 'percentage':
                return '##0.00%';
            case 'email':
                return null; // Uses standard validation
            default:
                return $this->pattern;
        }
    }
} 