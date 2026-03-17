<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artwork extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'artist_id',
        'code',
        'title',
        'series_year',
        'medium',
        'size',
        'image_path',
        'year',
        'available',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'year' => 'integer',
        'available' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Get the artist that owns the artwork.
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    /**
     * Scope a query to only include available artworks.
     */
    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    /**
     * Scope a query to filter by series year.
     */
    public function scopeBySeries($query, $year)
    {
        return $query->where('series_year', $year);
    }

    /**
     * Scope a query to filter by artist.
     */
    public function scopeByArtist($query, $artistId)
    {
        return $query->where('artist_id', $artistId);
    }

    /**
     * Get the artwork's caption for display.
     */
    public function getCaptionAttribute()
    {
        return $this->code . ' - ' . $this->medium;
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute()
    {
        return asset($this->image_path);
    }
}
