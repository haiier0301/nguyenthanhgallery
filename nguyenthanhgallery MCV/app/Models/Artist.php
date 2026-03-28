<?php
/**
 * Artist Model - read from cms/data/artists.json
 */

namespace App\Models;

class Artist
{
    protected static ?array $data = null;

    public static function all(): array
    {
        if (self::$data === null) {
            $path = DATA_PATH . 'artists.json';
            if (!is_file($path)) {
                return [];
            }
            $json = file_get_contents($path);
            self::$data = json_decode($json, true) ?: [];
        }
        return self::$data;
    }

    public static function findBySlug(string $slug): ?array
    {
        foreach (self::all() as $row) {
            $rowSlug = $row['slug'] ?? '';
            if ($rowSlug === $slug || $rowSlug === 'artist-' . $slug || ($row['id'] ?? '') === $slug) {
                return $row;
            }
        }
        return null;
    }

    public static function findById(string $id): ?array
    {
        foreach (self::all() as $row) {
            if (($row['id'] ?? '') === $id) {
                return $row;
            }
        }
        return null;
    }

    /** Series years for artists who have series (e.g. Nguyen Thanh) */
    public static function getSeriesYears(string $artistId): array
    {
        $path = DATA_PATH . 'artworks.json';
        if (!is_file($path)) {
            return [];
        }
        $artworks = json_decode(file_get_contents($path), true) ?: [];
        $years = [];
        foreach ($artworks as $a) {
            if (($a['artistId'] ?? '') === $artistId && !empty($a['seriesYear'])) {
                $years[$a['seriesYear']] = true;
            }
        }
        krsort($years);
        return array_keys($years);
    }
}
