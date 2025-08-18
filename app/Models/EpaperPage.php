<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpaperPage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'epaper_id',
        'page_number',
        'image_path',
        'thumbnail_path',
    ];

    /**
     * Relationship: Each page belongs to one Epaper.
     */
    public function epaper(): BelongsTo
    {
        return $this->belongsTo(Epaper::class);
    }

    /**
     * Accessor: Full URL for original image.
     */
    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->image_path, '/'));
    }

    /**
     * Accessor: Full URL for thumbnail image.
     */
    public function getThumbnailUrlAttribute(): string
    {
        return asset('storage/' . ltrim($this->thumbnail_path, '/'));
    }
}
