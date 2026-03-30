<?php
/**
 * CMS API shared auth/session helpers.
 */

require_once __DIR__ . '/../config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Session persists for 8 hours (even after closing browser)
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT, // 8 hours (28800 seconds)
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function current_cms_user(): ?string
{
    $auth = $_SESSION['cms_authenticated'] ?? false;
    $user = $_SESSION['cms_user'] ?? null;
    if ($auth !== true || !is_string($user) || $user === '') {
        return null;
    }

    $loginTime = (int) ($_SESSION['cms_login_time'] ?? 0);
    if ($loginTime <= 0) {
        return null;
    }

    if ((time() - $loginTime) > SESSION_TIMEOUT) {
        cms_logout();
        return null;
    }

    return $user;
}

function require_cms_auth(): string
{
    $user = current_cms_user();
    if ($user === null) {
        json_response([
            'success' => false,
            'error' => 'Unauthorized'
        ], 401);
    }
    return $user;
}

function cms_login(string $username): void
{
    $_SESSION['cms_authenticated'] = true;
    $_SESSION['cms_user'] = $username;
    $_SESSION['cms_login_time'] = time();
}

function cms_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }
    session_destroy();
}

function get_cms_admin_credentials(): array
{
    $username = getenv('CMS_ADMIN_USERNAME');
    $password = getenv('CMS_ADMIN_PASSWORD');

    if (!is_string($username) || $username === '') {
        $username = defined('CMS_ADMIN_USERNAME') ? CMS_ADMIN_USERNAME : 'admin';
    }
    if (!is_string($password) || $password === '') {
        $password = defined('CMS_ADMIN_PASSWORD') ? CMS_ADMIN_PASSWORD : 'admin123';
    }

    return [$username, $password];
}

