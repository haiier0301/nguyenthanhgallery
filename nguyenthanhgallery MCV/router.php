<?php
/**
 * Router for PHP built-in server: php -S localhost:8000 router.php
 * Sends non-file requests to index.php. Serves static files (decodes %20 etc.) so paths with spaces work.
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$decoded = rawurldecode($uri);
// Redirect legacy .html pages to MVC routes when running php -S
$legacyTarget = null;
if ($decoded === '/index.html') {
    $legacyTarget = '/';
} elseif (preg_match('#^/(about|artists|exhibitions|art-fairs|contact)\.html$#i', $decoded, $m)) {
    $legacyTarget = '/' . strtolower($m[1]);
} elseif (preg_match('#^/artists/artist-([a-z0-9-]+)\.html$#i', $decoded, $m)) {
    $legacyTarget = '/artists/' . strtolower($m[1]);
} elseif (preg_match('#^/artists/([a-z0-9-]+)/([0-9]{4})\.html$#i', $decoded, $m)) {
    $legacyTarget = '/artists/' . strtolower($m[1]) . '/' . $m[2];
}
if ($legacyTarget !== null) {
    $query = $_SERVER['QUERY_STRING'] ?? '';
    if ($query !== '') {
        $legacyTarget .= '?' . $query;
    }
    header('Location: ' . $legacyTarget, true, 301);
    return;
}
// Block path traversal
if (strpos($decoded, '..') !== false) {
    $decoded = '';
}
$file = __DIR__ . $decoded;
$serveFile = ($uri !== '/' && $decoded !== '' && is_file($file));
if ($serveFile) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        return false; // Let PHP built-in server execute .php files (e.g. cms/api/upload.php)
    }
    $real = @realpath($file);
    $root = realpath(__DIR__);
    if ($real === false || $root === false || strpos($real, $root) !== 0) {
        $serveFile = false;
    }
}
if ($serveFile) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'js' => 'application/javascript',
        'css' => 'text/css',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
    ];
    $mime = $mimes[$ext] ?? null;
    if ($mime === null) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file) ?: 'application/octet-stream';
        // PHP 8.5+: finfo is freed automatically, finfo_close() deprecated
    }
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: public, max-age=3600');
    readfile($file);
    return;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
