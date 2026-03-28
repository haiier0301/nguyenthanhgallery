<?php
/**
 * Media Model - read from cms/data/media.json
 */

namespace App\Models;

class Media
{
    protected static ?array $data = null;

    public static function all(): array
    {
        if (self::$data === null) {
            $path = DATA_PATH . 'media.json';
            if (!is_file($path)) {
                self::$data = [];
                return self::$data;
            }
            self::$data = json_decode(file_get_contents($path), true) ?: [];
        }
        return self::$data;
    }

    public static function byFolder(string $folder): array
    {
        $items = [];
        foreach (self::all() as $row) {
            $currentFolder = $row['folder'] ?? '';
            $path = $row['path'] ?? '';
            if (($currentFolder === $folder || strpos($path, 'images/' . $folder . '/') === 0) && $path !== '') {
                $items[] = $row;
            }
        }

        if (empty($items)) {
            $items = self::scanFromImagesFolder($folder);
        }

        usort($items, function ($a, $b) {
            return strcmp((string)($b['uploadedAt'] ?? ''), (string)($a['uploadedAt'] ?? ''));
        });
        return $items;
    }

    protected static function scanFromImagesFolder(string $folder): array
    {
        $baseDir = ROOT_PATH . 'images/' . $folder;
        if (!is_dir($baseDir)) {
            return [];
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $out = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }
            $ext = strtolower(pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                continue;
            }

            $fullPath = $fileInfo->getPathname();
            $relative = str_replace(ROOT_PATH, '', $fullPath);
            $relative = str_replace('\\', '/', $relative);

            $out[] = [
                'id' => 'fs-' . md5($relative),
                'name' => $fileInfo->getFilename(),
                'path' => $relative,
                'folder' => $folder,
                'size' => (string)$fileInfo->getSize(),
                'uploadedAt' => date('c', $fileInfo->getMTime())
            ];
        }

        return $out;
    }
}

