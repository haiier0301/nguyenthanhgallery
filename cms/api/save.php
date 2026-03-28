<?php
/**
 * CMS API - Save Data Endpoint
 * Handles saving JSON data for artists, artworks, exhibitions, art fairs, media
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/_auth.php';
$currentUser = require_cms_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$file = $data['file'];
// Support both 'content' and 'data' keys for backward compatibility
$content = $data['content'] ?? $data['data'] ?? null;

if ($content === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing content or data field']);
    exit;
}

// Validate file path (security check)
$allowedFiles = ['artists.json', 'artworks.json', 'series.json', 'exhibitions.json', 'art-fairs.json', 'media.json', 'settings.json'];
if (!in_array($file, $allowedFiles)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid file: ' . $file . '. Allowed: ' . implode(', ', $allowedFiles)]);
    exit;
}

// Save to file
$filepath = __DIR__ . '/../data/' . $file;

// Backup current file
if (file_exists($filepath)) {
    $backupDir = __DIR__ . '/../data/backups/';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    $backupFile = $backupDir . $file . '.' . date('Y-m-d_H-i-s') . '.bak';
    copy($filepath, $backupFile);
}

// Write new data
$jsonContent = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
$result = file_put_contents($filepath, $jsonContent);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save file']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Data saved successfully',
    'file' => $file,
    'timestamp' => date('Y-m-d H:i:s')
]);
logActivity('CMS_SAVE', "User {$currentUser} saved {$file}");
?>
