<?php
/**
 * Exhibition Model - read from cms/data/exhibitions.json
 */

namespace App\Models;

class Exhibition
{
    protected static ?array $data = null;

    public static function all(): array
    {
        if (self::$data === null) {
            $path = DATA_PATH . 'exhibitions.json';
            if (!is_file($path)) {
                return [];
            }
            self::$data = json_decode(file_get_contents($path), true) ?: [];
        }
        return self::$data;
    }

    public static function byType(string $type): array
    {
        $out = [];
        foreach (self::all() as $row) {
            if (($row['type'] ?? '') === $type) {
                $out[] = $row;
            }
        }
        return $out;
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
}
