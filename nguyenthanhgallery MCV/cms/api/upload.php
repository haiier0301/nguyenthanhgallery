<?php
/**
 * CMS API - File Upload Endpoint
 * Handles image uploads for the media library
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

// Check if file was uploaded
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['file'];

// Build folder: artistId+seriesYear → artists/Name/Year; artistName only → artists/Name; else use folder param
$folder = $_POST['folder'] ?? null;
if ($folder === null && !empty($_POST['artistName'])) {
    $name = preg_replace('/[^a-zA-Z0-9\s\-]/', '', trim($_POST['artistName']));
    $folder = $name !== '' ? 'artists/' . $name : 'artists/Other';
} elseif ($folder === null && !empty($_POST['artistId']) && !empty($_POST['seriesYear'])) {
    $artistsPath = __DIR__ . '/../data/artists.json';
    if (is_file($artistsPath)) {
        $artists = json_decode(file_get_contents($artistsPath), true) ?: [];
        foreach ($artists as $a) {
            if (($a['id'] ?? '') === $_POST['artistId']) {
                $name = $a['name'] ?? $a['nameDisplay'] ?? 'Artist';
                $folder = 'artists/' . $name . '/' . preg_replace('/[^0-9]/', '', $_POST['seriesYear']);
                break;
            }
        }
    }
    if ($folder === null) {
        $folder = 'artists/Other/' . preg_replace('/[^0-9]/', '', $_POST['seriesYear'] ?: date('Y'));
    }
}
$folder = $folder ?? 'uploads';

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file type. Only images allowed.']);
    exit;
}

// Validate file size (max 10MB)
$maxSize = 10 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'File too large. Max 10MB.']);
    exit;
}

// Create upload directory
$uploadDir = __DIR__ . '/../../images/' . $folder . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$baseName = pathinfo($file['name'], PATHINFO_FILENAME);
$baseName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $baseName);
$fileName = $baseName . '.' . $extension;

// Check if file exists, add counter if needed
$counter = 1;
while (file_exists($uploadDir . $fileName)) {
    $fileName = $baseName . '_' . $counter . '.' . $extension;
    $counter++;
}

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to upload file']);
    exit;
}

// Path for JSON / MVC (no leading ../): images/artists/Name/Year/file.jpg
$pathForJson = 'images/' . $folder . '/' . $fileName;
// Path for CMS img src (from cms/): ../images/...
$pathForCms = '../images/' . $folder . '/' . $fileName;

echo json_encode([
    'success' => true,
    'message' => 'File uploaded successfully',
    'path' => $pathForJson,
    'pathCms' => $pathForCms,
    'file' => [
        'name' => $fileName,
        'path' => $pathForJson,
        'pathCms' => $pathForCms,
        'size' => $file['size'],
        'type' => $file['type']
    ]
]);
logActivity('CMS_UPLOAD', "User {$currentUser} uploaded {$pathForJson}");
?>
