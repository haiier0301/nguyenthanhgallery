<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'name_display',
        'code',
        'slug',
        'born',
        'birth_place',
        'bio',
        'thumbnail_image',
        'featured_image',
        'featured',
        'has_series',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'born' => 'date',
        'featured' => 'boolean',
        'has_series' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get all artworks for this artist.
     */
    public function artworks()
    {
        return $this->hasMany(Artwork::class)
                    ->orderBy('series_year')
                    ->orderBy('display_order')
                    ->orderBy('code');
    }

    /**
     * Get featured artworks only.
     */
    public function featuredArtworks()
    {
        return $this->hasMany(Artwork::class)
                    ->where('available', true)
                    ->limit(6);
    }

    /**
     * Get unique series years for this artist.
     */
    public function series()
    {
        return $this->artworks()
                    ->select('series_year')
                    ->distinct()
                    ->whereNotNull('series_year')
                    ->orderBy('series_year')
                    ->pluck('series_year');
    }

    /**
     * Scope a query to only include featured artists.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to order by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Get the artist's full display name.
     */
    public function getFullNameAttribute()
    {
        return $this->name_display ?: strtoupper($this->name);
    }

    /**
     * Get the birth year only.
     */
    public function getBirthYearAttribute()
    {
        return $this->born ? $this->born->format('Y') : null;
    }
}
