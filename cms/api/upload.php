<?php
/**
 * CMS API - File Upload Endpoint
 * Handles image uploads for the media library
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

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
$folder = $_POST['folder'] ?? 'uploads';

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

// Return success with file info
$relativePath = '../images/' . $folder . '/' . $fileName;

echo json_encode([
    'success' => true,
    'message' => 'File uploaded successfully',
    'file' => [
        'name' => $fileName,
        'path' => $relativePath,
        'size' => $file['size'],
        'type' => $file['type']
    ]
]);
?>
