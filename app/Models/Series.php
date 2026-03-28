<?php
/**
 * Series Model - read from cms/data/series.json
 * Manages artist series/themes by year
 */

namespace App\Models;

class Series
{
    protected static ?array $data = null;

    /**
     * Get all series
     */
    public static function all(): array
    {
        if (self::$data === null) {
            $path = DATA_PATH . 'series.json';
            if (!is_file($path)) {
                return [];
            }
            $json = file_get_contents($path);
            self::$data = json_decode($json, true) ?: [];
        }
        return self::$data;
    }

    /**
     * Find series by ID
     */
    public static function findById(string $id): ?array
    {
        foreach (self::all() as $row) {
            if (($row['id'] ?? '') === $id) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Find series by artist and year
     */
    public static function findByArtistYear(string $artistId, string $year): ?array
    {
        foreach (self::all() as $row) {
            if (($row['artistId'] ?? '') === $artistId && ($row['year'] ?? '') === $year) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Get all series for specific artist
     */
    public static function getByArtist(string $artistId): array
    {
        $result = [];
        foreach (self::all() as $row) {
            if (($row['artistId'] ?? '') === $artistId && ($row['published'] ?? true)) {
                $result[] = $row;
            }
        }
        
        // Sort by year descending
        usort($result, function($a, $b) {
            return (int)($b['year'] ?? 0) - (int)($a['year'] ?? 0);
        });
        
        return $result;
    }

    /**
     * Get published series only
     */
    public static function published(): array
    {
        $result = [];
        foreach (self::all() as $row) {
            if ($row['published'] ?? true) {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * Find series by slug (for any artist)
     */
    public static function findBySlug(string $slug): ?array
    {
        foreach (self::all() as $row) {
            if (($row['slug'] ?? '') === $slug || ($row['year'] ?? '') === $slug) {
                return $row;
            }
        }
        return null;
    }

    /**
     * Get artworks for this series
     */
    public static function getArtworks(string $seriesId): array
    {
        $series = self::findById($seriesId);
        if (!$series) {
            return [];
        }
        
        $artistId = $series['artistId'];
        $year = $series['year'];
        
        return Artwork::getByArtistSeries($artistId, $year);
    }
}
