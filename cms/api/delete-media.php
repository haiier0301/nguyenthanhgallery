<?php
/**
 * CMS API - Delete Media File Endpoint
 * Removes a media file under /images and returns JSON status.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/_auth.php';
$currentUser = require_cms_auth();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$path = isset($input['path']) ? trim((string)$input['path']) : '';

if ($path === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing media path']);
    exit;
}

if (strpos($path, 'images/') !== 0 || strpos($path, '..') !== false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid media path']);
    exit;
}

$imagesRoot = realpath(__DIR__ . '/../../images');
if ($imagesRoot === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Images root not found']);
    exit;
}

$targetFile = realpath(__DIR__ . '/../../' . $path);
if ($targetFile === false || strpos($targetFile, $imagesRoot) !== 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Media file not found']);
    exit;
}

if (!is_file($targetFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Media file not found']);
    exit;
}

if (!unlink($targetFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to delete media file']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Media file deleted successfully',
    'path' => $path
]);
logActivity('CMS_DELETE_MEDIA', "User {$currentUser} deleted {$path}");
?>
