<?php
/**
 * Application configuration - MVC Nguyen Thanh Gallery
 */

define('ROOT_PATH', dirname(__DIR__) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('DATA_PATH', ROOT_PATH . 'cms/data/');
define('VIEWS_PATH', APP_PATH . 'Views/');
define('IMAGES_PATH', ROOT_PATH . 'images/');

// Base URL (no trailing slash). Empty string when at doc root, or e.g. '/nguyenthanhgallery'
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseUrl = str_replace('\\', '/', dirname($scriptName));
if ($baseUrl === '/' || $baseUrl === '\\') {
    $baseUrl = '';
}
define('BASE_URL', $baseUrl);

// Helper: full URL for asset
function asset($path) {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

// Helper: URL for route (page)
function url($path) {
    $path = ltrim($path, '/');
    if ($path === '' || $path === 'index') {
        return BASE_URL ?: '/';
    }
    return BASE_URL . '/' . $path;
}
