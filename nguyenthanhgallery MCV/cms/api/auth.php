<?php
/**
 * CMS API - Authentication endpoint
 * Actions: login, logout, status
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/_auth.php';

$action = $_GET['action'] ?? '';

if ($action === 'status') {
    $user = current_cms_user();
    json_response([
        'success' => true,
        'authenticated' => $user !== null,
        'user' => $user
    ]);
}

if ($action === 'logout') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'error' => 'Method not allowed'], 405);
    }
    $user = current_cms_user();
    cms_logout();
    if ($user) {
        logActivity('AUTH_LOGOUT', "User {$user} logged out");
    }
    json_response(['success' => true, 'message' => 'Logged out successfully']);
}

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_response(['success' => false, 'error' => 'Method not allowed'], 405);
    }
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim((string)($input['username'] ?? ''));
    $password = (string)($input['password'] ?? '');

    if ($username === '' || $password === '') {
        json_response(['success' => false, 'error' => 'Username and password are required'], 400);
    }

    [$expectedUser, $expectedPass] = get_cms_admin_credentials();
    if (!hash_equals($expectedUser, $username) || !hash_equals($expectedPass, $password)) {
        logActivity('AUTH_FAILED', "Failed login for user {$username}");
        json_response(['success' => false, 'error' => 'Invalid username or password'], 401);
    }

    cms_login($username);
    logActivity('AUTH_LOGIN', "User {$username} logged in");
    json_response([
        'success' => true,
        'authenticated' => true,
        'user' => $username
    ]);
}

json_response(['success' => false, 'error' => 'Invalid action'], 400);
?>
