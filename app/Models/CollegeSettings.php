<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class CollegeSettings extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'college_name',
        'college_code',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'phone',
        'email',
        'website',
        'logo',
        'established_year',
        'accreditation_info',
        'academic_year_start',
        'academic_year_end',
        'grading_system',
        'principal_name',
        'vision_statement',
        'mission_statement',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'established_year' => 'integer',
        'academic_year_start' => 'date',
        'academic_year_end' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the logo URL.
     * 
     * @return string|null
     */
    public function getLogoUrl()
    {
        if (!$this->logo) {
            return null;
        }
        
        return Storage::disk('public')->url($this->logo);
    }
}
