<?php
/**
 * Artwork Model - read from cms/data/artworks.json
 */

namespace App\Models;

class Artwork
{
    protected static ?array $data = null;

    public static function all(): array
    {
        if (self::$data === null) {
            $path = DATA_PATH . 'artworks.json';
            if (!is_file($path)) {
                return [];
            }
            self::$data = json_decode(file_get_contents($path), true) ?: [];
        }
        return self::$data;
    }

    public static function byArtist(string $artistId): array
    {
        $out = [];
        foreach (self::all() as $row) {
            if (($row['artistId'] ?? '') === $artistId) {
                $out[] = $row;
            }
        }
        return $out;
    }

    public static function byArtistAndYear(string $artistId, string $year): array
    {
        $out = [];
        foreach (self::all() as $row) {
            if (($row['artistId'] ?? '') === $artistId && (string)($row['seriesYear'] ?? '') === (string)$year) {
                $out[] = $row;
            }
        }
        usort($out, function ($a, $b) {
            return strcmp($a['code'] ?? '', $b['code'] ?? '');
        });
        return $out;
    }
}
