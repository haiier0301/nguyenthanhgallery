<?php
/**
 * ArtFair Model - read from cms/data/art-fairs.json
 */

namespace App\Models;

class ArtFair
{
    protected static ?array $data = null;

    public static function all(): array
    {
        if (self::$data === null) {
            $path = DATA_PATH . 'art-fairs.json';
            if (!is_file($path)) {
                self::$data = [];
                return self::$data;
            }
            self::$data = json_decode(file_get_contents($path), true) ?: [];
        }
        return self::$data;
    }

    public static function sortedByYearDesc(): array
    {
        $items = self::all();
        usort($items, function ($a, $b) {
            return (int)($b['year'] ?? 0) <=> (int)($a['year'] ?? 0);
        });
        return $items;
    }
}

