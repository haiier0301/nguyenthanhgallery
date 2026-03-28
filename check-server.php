<?php
/**
 * SERVER DIAGNOSTIC TOOL
 * Upload this to your server root and visit: yoursite.com/check-server.php
 * This will help diagnose 403 Forbidden errors
 */

// Set proper headers
header('Content-Type: text/html; charset=UTF-8');

// Start output with styling
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Diagnostic - Nguyen Thanh Gallery</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            padding: 40px 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 { font-size: 28px; margin-bottom: 10px; }
        .header p { opacity: 0.9; }
        .content { padding: 30px; }
        .check-item {
            background: #f9f9f9;
            border-left: 4px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .check-item.success { border-left-color: #10b981; background: #f0fdf4; }
        .check-item.error { border-left-color: #ef4444; background: #fef2f2; }
        .check-item.warning { border-left-color: #f59e0b; background: #fffbeb; }
        .check-item h3 {
            font-size: 18px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .icon { font-size: 24px; }
        .code {
            background: #1e293b;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .file-list {
            background: #f1f5f9;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .file-list ul { list-style-position: inside; }
        .file-list li { padding: 4px 0; font-family: monospace; font-size: 13px; }
        .success-text { color: #10b981; font-weight: 600; }
        .error-text { color: #ef4444; font-weight: 600; }
        .warning-text { color: #f59e0b; font-weight: 600; }
        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .action-button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            margin: 10px 10px 0 0;
            transition: background 0.2s;
        }
        .action-button:hover { background: #5568d3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Server Diagnostic Tool</h1>
            <p>Checking server configuration for MVC application</p>
        </div>
        <div class="content">

<?php
$checks = [];
$allPassed = true;

// 1. Check PHP Version
$phpVersion = PHP_VERSION;
$phpOk = version_compare($phpVersion, '7.4.0', '>=');
$checks[] = [
    'title' => 'PHP Version',
    'status' => $phpOk ? 'success' : 'error',
    'message' => $phpOk 
        ? "PHP $phpVersion (OK - requires 7.4+)" 
        : "PHP $phpVersion (ERROR - needs 7.4+)",
];
if (!$phpOk) $allPassed = false;

// 2. Check mod_rewrite
$modRewriteEnabled = in_array('mod_rewrite', apache_get_modules());
$checks[] = [
    'title' => 'Apache mod_rewrite',
    'status' => $modRewriteEnabled ? 'success' : 'error',
    'message' => $modRewriteEnabled 
        ? 'mod_rewrite is ENABLED (OK)' 
        : 'mod_rewrite is DISABLED (ERROR - required for clean URLs)',
];
if (!$modRewriteEnabled) $allPassed = false;

// 3. Check .htaccess exists
$htaccessExists = file_exists(__DIR__ . '/.htaccess');
$checks[] = [
    'title' => '.htaccess File',
    'status' => $htaccessExists ? 'success' : 'error',
    'message' => $htaccessExists 
        ? '.htaccess file EXISTS in root' 
        : '.htaccess file NOT FOUND (ERROR - must upload)',
];
if (!$htaccessExists) $allPassed = false;

// 4. Check index.php exists
$indexExists = file_exists(__DIR__ . '/index.php');
$checks[] = [
    'title' => 'index.php File',
    'status' => $indexExists ? 'success' : 'error',
    'message' => $indexExists 
        ? 'index.php EXISTS in root' 
        : 'index.php NOT FOUND (ERROR - must upload)',
];
if (!$indexExists) $allPassed = false;

// 5. Check app directory
$appExists = is_dir(__DIR__ . '/app');
$checks[] = [
    'title' => 'app/ Directory',
    'status' => $appExists ? 'success' : 'error',
    'message' => $appExists 
        ? 'app/ directory EXISTS' 
        : 'app/ directory NOT FOUND (ERROR - must upload entire app folder)',
];
if (!$appExists) $allPassed = false;

// 6. Check app structure
if ($appExists) {
    $requiredDirs = ['Controllers', 'Models', 'Views', 'Core'];
    $missingDirs = [];
    foreach ($requiredDirs as $dir) {
        if (!is_dir(__DIR__ . '/app/' . $dir)) {
            $missingDirs[] = "app/$dir";
        }
    }
    $checks[] = [
        'title' => 'app/ Structure',
        'status' => empty($missingDirs) ? 'success' : 'error',
        'message' => empty($missingDirs) 
            ? 'All required directories exist' 
            : 'Missing directories: ' . implode(', ', $missingDirs),
    ];
    if (!empty($missingDirs)) $allPassed = false;
}

// 7. Check cms/data directory
$cmsDataExists = is_dir(__DIR__ . '/cms/data');
$checks[] = [
    'title' => 'cms/data/ Directory',
    'status' => $cmsDataExists ? 'success' : 'warning',
    'message' => $cmsDataExists 
        ? 'cms/data/ directory EXISTS' 
        : 'cms/data/ directory NOT FOUND (WARNING - CMS won\'t work)',
];

// 8. Check JSON data files
if ($cmsDataExists) {
    $requiredFiles = ['artists.json', 'artworks.json', 'series.json', 'exhibitions.json'];
    $missingFiles = [];
    foreach ($requiredFiles as $file) {
        if (!file_exists(__DIR__ . '/cms/data/' . $file)) {
            $missingFiles[] = $file;
        }
    }
    $checks[] = [
        'title' => 'JSON Data Files',
        'status' => empty($missingFiles) ? 'success' : 'warning',
        'message' => empty($missingFiles) 
            ? 'All JSON data files exist' 
            : 'Missing files: ' . implode(', ', $missingFiles),
    ];
}

// 9. Check file permissions
$permissions = [];
if ($htaccessExists) {
    $perms = substr(sprintf('%o', fileperms(__DIR__ . '/.htaccess')), -3);
    $permissions['.htaccess'] = $perms;
}
if ($indexExists) {
    $perms = substr(sprintf('%o', fileperms(__DIR__ . '/index.php')), -3);
    $permissions['index.php'] = $perms;
}
if ($appExists) {
    $perms = substr(sprintf('%o', fileperms(__DIR__ . '/app')), -3);
    $permissions['app/'] = $perms;
}

$permOk = true;
foreach ($permissions as $file => $perm) {
    if (strpos($file, '/') !== false) {
        if ($perm !== '755') $permOk = false;
    } else {
        if ($perm !== '644' && $perm !== '755') $permOk = false;
    }
}

$checks[] = [
    'title' => 'File Permissions',
    'status' => $permOk ? 'success' : 'warning',
    'message' => $permOk 
        ? 'Permissions look good' 
        : 'Some permissions may be incorrect',
    'details' => $permissions
];

// 10. Check if we can read artists.json
if (file_exists(__DIR__ . '/cms/data/artists.json')) {
    $jsonContent = @file_get_contents(__DIR__ . '/cms/data/artists.json');
    $jsonData = @json_decode($jsonContent, true);
    $jsonOk = !empty($jsonData);
    $checks[] = [
        'title' => 'JSON Data Reading',
        'status' => $jsonOk ? 'success' : 'error',
        'message' => $jsonOk 
            ? 'Successfully read ' . count($jsonData) . ' artists from JSON' 
            : 'Failed to read artists.json (check file permissions)',
    ];
    if (!$jsonOk) $allPassed = false;
}

// Display results
foreach ($checks as $check) {
    $iconMap = [
        'success' => '✅',
        'error' => '❌',
        'warning' => '⚠️',
    ];
    $icon = $iconMap[$check['status']] ?? '•';
    ?>
    <div class="check-item <?= $check['status'] ?>">
        <h3><span class="icon"><?= $icon ?></span> <?= $check['title'] ?></h3>
        <p class="<?= $check['status'] ?>-text"><?= $check['message'] ?></p>
        <?php if (!empty($check['details'])): ?>
            <div class="file-list">
                <strong>Details:</strong>
                <ul>
                    <?php foreach ($check['details'] as $k => $v): ?>
                    <li><?= $k ?>: <?= $v ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

// Summary
?>
<div class="info-box">
    <h3 style="margin-bottom: 10px;">
        <?php if ($allPassed): ?>
            🎉 All Checks Passed!
        <?php else: ?>
            ⚠️ Issues Found - See Errors Above
        <?php endif; ?>
    </h3>
    <p>
        <?php if ($allPassed): ?>
            Your server configuration looks good. If you're still getting 403 errors, check:
            <ul style="margin-left: 20px; margin-top: 10px;">
                <li>File permissions (files: 644, directories: 755)</li>
                <li>cPanel → Error Log for detailed error messages</li>
                <li>Contact your hosting provider if mod_rewrite is disabled</li>
            </ul>
        <?php else: ?>
            Please fix the errors above and refresh this page to re-check.
        <?php endif; ?>
    </p>
</div>

<h2 style="margin-top: 30px; margin-bottom: 15px;">📋 Server Info</h2>
<div class="code">
PHP Version: <?= PHP_VERSION ?><br>
Server Software: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?><br>
Document Root: <?= $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown' ?><br>
Script Filename: <?= __FILE__ ?><br>
Current User: <?= get_current_user() ?><br>
</div>

<h2 style="margin-top: 30px; margin-bottom: 15px;">🧪 Test MVC Routing</h2>
<div class="info-box">
    <p>If all checks passed above, test these URLs:</p>
    <a href="/artists" class="action-button">Test: /artists</a>
    <a href="/" class="action-button">Test: / (home)</a>
    <a href="/contact" class="action-button">Test: /contact</a>
</div>

<h2 style="margin-top: 30px; margin-bottom: 15px;">🔧 Quick Fixes</h2>

<div class="check-item">
    <h3>Fix File Permissions (via SSH)</h3>
    <div class="code">
# Set correct permissions for all files and directories<br>
find ~/public_html -type d -exec chmod 755 {} \;<br>
find ~/public_html -type f -exec chmod 644 {} \;<br>
<br>
# Make sure index.php is executable by PHP<br>
chmod 644 ~/public_html/index.php<br>
chmod 644 ~/public_html/.htaccess
    </div>
</div>

<div class="check-item">
    <h3>Check Apache Error Log (cPanel)</h3>
    <ol style="margin-left: 20px; margin-top: 10px;">
        <li>Login to cPanel</li>
        <li>Go to: <strong>Metrics → Errors</strong></li>
        <li>Look for recent 403 errors</li>
        <li>Check what file is causing the issue</li>
    </ol>
</div>

<div class="check-item">
    <h3>Enable mod_rewrite (if disabled)</h3>
    <p style="margin-top: 10px;">
        Contact your hosting provider and ask them to enable <strong>mod_rewrite</strong> 
        for your account. This is required for clean URLs (/artists instead of /artists.html).
    </p>
</div>

        </div>
    </div>
</body>
</html>
