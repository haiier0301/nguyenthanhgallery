<?php
/**
 * CMS Configuration
 * Central configuration file for the CMS
 */

// Database configuration (if using MySQL in future)
define('DB_HOST', 'localhost');
define('DB_NAME', 'gallery_cms');
define('DB_USER', 'root');
define('DB_PASS', '');

// File paths
define('DATA_PATH', __DIR__ . '/data/');
define('UPLOAD_PATH', __DIR__ . '/../images/uploads/');
define('BACKUP_PATH', __DIR__ . '/data/backups/');

// Upload settings
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Security
define('SESSION_TIMEOUT', 8 * 60 * 60); // 8 hours
define('ENABLE_BACKUP', true);
define('CMS_ADMIN_USERNAME', getenv('CMS_ADMIN_USERNAME') ?: 'admin');
define('CMS_ADMIN_PASSWORD', getenv('CMS_ADMIN_PASSWORD') ?: 'admin123');

// Gallery settings
define('GALLERY_NAME', 'NGUYEN THANH GALLERIE');
define('GALLERY_EMAIL', 'nguyenthanhgallerie@gmail.com');
define('GALLERY_PHONE_1', '+84 (028) 3823 8754');
define('GALLERY_PHONE_2', '+84 (0) 919 268 888');
define('GALLERY_ADDRESS', '139 Dong Khoi Street, Sai Gon Ward, Ho Chi Minh City, Vietnam');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Get absolute path to data file
 */
function getDataPath($file) {
    return DATA_PATH . $file;
}

/**
 * Get relative path for web access
 */
function getWebPath($path) {
    return str_replace(__DIR__ . '/../', '', $path);
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '-', $filename);
    $filename = preg_replace('/-+/', '-', $filename);
    return trim($filename, '-');
}

/**
 * Create backup of file
 */
function createBackup($filepath) {
    if (!ENABLE_BACKUP || !file_exists($filepath)) {
        return false;
    }
    
    $backupDir = BACKUP_PATH;
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $filename = basename($filepath);
    $backupFile = $backupDir . $filename . '.' . date('Y-m-d_H-i-s') . '.bak';
    
    return copy($filepath, $backupFile);
}

/**
 * Log activity
 */
function logActivity($action, $details) {
    $logFile = DATA_PATH . 'activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[{$timestamp}] {$action}: {$details}\n";
    file_put_contents($logFile, $entry, FILE_APPEND);
}
?>
