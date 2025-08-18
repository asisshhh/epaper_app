<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Epaper extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'publication_date',
        'city',
        'pdf_path',
        'total_pages',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'publication_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship: An epaper has many pages.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(EpaperPage::class)->orderBy('page_number');
    }

    /**
     * Accessor: Formatted publication date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->publication_date->format('d-M-Y');
    }

    /**
     * Scope: Filter by city.
     */
    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope: Filter by specific date.
     */
    public function scopeByDate($query, string|Carbon $date)
    {
        return $query->whereDate('publication_date', $date);
    }

    /**
     * Scope: Only active epapers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
