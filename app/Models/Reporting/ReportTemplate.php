<?php

namespace App\Models\Reporting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'report_type',
        'frequency',
        'parameters',
        'columns',
        'filters',
        'visualizations',
        'sql_query',
        'is_system',
        'is_active',
        'export_formats',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'parameters' => 'json',
        'columns' => 'json',
        'filters' => 'json',
        'visualizations' => 'json',
    ];

    /**
     * Get the user who created the report template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the generated reports for this template.
     */
    public function generatedReports(): HasMany
    {
        return $this->hasMany(GeneratedReport::class, 'report_template_id');
    }

    /**
     * Scope a query to only include active report templates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include system report templates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include report templates of a specific type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Get available export formats as an array.
     *
     * @return array
     */
    public function getExportFormatsArray(): array
    {
        return explode(',', $this->export_formats);
    }
} 