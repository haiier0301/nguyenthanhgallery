<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'year',
        'title',
        'location',
        'description',
        'display_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'display_order' => 'integer',
    ];

    /**
     * Exhibition types.
     */
    const TYPE_SOLO = 'solo';
    const TYPE_GROUP = 'group';
    const TYPE_AWARD = 'award';
    const TYPE_ART_FAIR = 'art-fair';

    /**
     * Scope a query to only include awards.
     */
    public function scopeAwards($query)
    {
        return $query->where('type', self::TYPE_AWARD);
    }

    /**
     * Scope a query to only include exhibitions (solo + group).
     */
    public function scopeExhibitions($query)
    {
        return $query->whereIn('type', [self::TYPE_SOLO, self::TYPE_GROUP]);
    }

    /**
     * Scope a query to only include art fairs.
     */
    public function scopeArtFairs($query)
    {
        return $query->where('type', self::TYPE_ART_FAIR);
    }

    /**
     * Scope a query to order by year descending.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('year', 'desc')
                    ->orderBy('display_order');
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the display title with location.
     */
    public function getFullTitleAttribute()
    {
        return $this->title . ' — ' . $this->location;
    }
}
