<?php
/**
 * CMS Health Check
 * Tests if data files are accessible and valid
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Health Check</title>
    <style>
        body {
            font-family: monospace;
            padding: 20px;
            background: #1e1e1e;
            color: #d4d4d4;
            line-height: 1.8;
        }
        .success { color: #4caf50; }
        .error { color: #f44336; }
        .warning { color: #ff9800; }
        .section {
            background: #252526;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        pre {
            background: #1e1e1e;
            padding: 12px;
            border-radius: 4px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        td, th {
            padding: 8px;
            border: 1px solid #444;
            text-align: left;
        }
        th {
            background: #333;
        }
    </style>
</head>
<body>
    <h1>🏥 CMS Health Check</h1>
    <p>Server-side validation of CMS data files</p>

    <div class="section">
        <h2>📍 Server Information</h2>
        <table>
            <tr>
                <td>PHP Version</td>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <td>Server Software</td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
            </tr>
            <tr>
                <td>Document Root</td>
                <td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></td>
            </tr>
            <tr>
                <td>Current Directory</td>
                <td><?php echo __DIR__; ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>📁 File System Check</h2>
        <?php
        $dataDir = __DIR__ . '/data/';
        $files = ['artists.json', 'artworks.json', 'exhibitions.json'];
        
        echo "<table>";
        echo "<tr><th>File</th><th>Exists</th><th>Size</th><th>Readable</th><th>Records</th></tr>";
        
        foreach ($files as $file) {
            $filepath = $dataDir . $file;
            $exists = file_exists($filepath);
            $size = $exists ? filesize($filepath) : 0;
            $readable = $exists && is_readable($filepath);
            
            echo "<tr>";
            echo "<td>{$file}</td>";
            echo "<td>" . ($exists ? "<span class='success'>✓ Yes</span>" : "<span class='error'>✗ No</span>") . "</td>";
            echo "<td>" . ($size > 0 ? number_format($size / 1024, 2) . " KB" : "-") . "</td>";
            echo "<td>" . ($readable ? "<span class='success'>✓ Yes</span>" : "<span class='error'>✗ No</span>") . "</td>";
            
            // Try to decode JSON
            if ($readable) {
                $content = file_get_contents($filepath);
                $json = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "<td><span class='success'>" . count($json) . "</span></td>";
                } else {
                    echo "<td><span class='error'>Invalid JSON</span></td>";
                }
            } else {
                echo "<td>-</td>";
            }
            
            echo "</tr>";
        }
        
        echo "</table>";
        ?>
    </div>

    <div class="section">
        <h2>🔐 Permissions Check</h2>
        <?php
        $dataDir = __DIR__ . '/data/';
        
        if (is_dir($dataDir)) {
            $dirPerms = substr(sprintf('%o', fileperms($dataDir)), -4);
            echo "<p>Data directory permissions: <strong>{$dirPerms}</strong>";
            if ($dirPerms === '0755') {
                echo " <span class='success'>✓ Correct</span></p>";
            } else {
                echo " <span class='warning'>⚠ Should be 0755</span></p>";
            }
            
            echo "<table>";
            echo "<tr><th>File</th><th>Permissions</th><th>Status</th></tr>";
            
            foreach ($files as $file) {
                $filepath = $dataDir . $file;
                if (file_exists($filepath)) {
                    $perms = substr(sprintf('%o', fileperms($filepath)), -4);
                    echo "<tr>";
                    echo "<td>{$file}</td>";
                    echo "<td>{$perms}</td>";
                    if ($perms === '0644') {
                        echo "<td><span class='success'>✓ Correct</span></td>";
                    } else {
                        echo "<td><span class='warning'>⚠ Should be 0644</span></td>";
                    }
                    echo "</tr>";
                }
            }
            
            echo "</table>";
        } else {
            echo "<p class='error'>✗ Data directory not found!</p>";
            echo "<p>Expected location: {$dataDir}</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>📊 Data Preview</h2>
        <?php
        // Show first artist as example
        $artistsFile = $dataDir . 'artists.json';
        if (file_exists($artistsFile) && is_readable($artistsFile)) {
            $artists = json_decode(file_get_contents($artistsFile), true);
            if ($artists && count($artists) > 0) {
                echo "<p class='success'>✓ Successfully loaded " . count($artists) . " artists</p>";
                echo "<p>First artist preview:</p>";
                echo "<pre>" . htmlspecialchars(json_encode($artists[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";
            } else {
                echo "<p class='error'>✗ Artists file is empty or invalid</p>";
            }
        } else {
            echo "<p class='error'>✗ Cannot read artists.json</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>🔧 Suggested Actions</h2>
        <?php
        $issues = [];
        
        // Check if data directory exists
        if (!is_dir($dataDir)) {
            $issues[] = "Data directory does not exist. Create it and upload JSON files.";
        }
        
        // Check if files exist
        foreach ($files as $file) {
            if (!file_exists($dataDir . $file)) {
                $issues[] = "File {$file} is missing. Upload it to cms/data/";
            }
        }
        
        // Check if files are readable
        foreach ($files as $file) {
            $filepath = $dataDir . $file;
            if (file_exists($filepath) && !is_readable($filepath)) {
                $issues[] = "File {$file} exists but is not readable. Run: chmod 644 cms/data/{$file}";
            }
        }
        
        if (count($issues) > 0) {
            echo "<p class='warning'>⚠ Issues found:</p>";
            echo "<ol>";
            foreach ($issues as $issue) {
                echo "<li>{$issue}</li>";
            }
            echo "</ol>";
            
            echo "<h3>Fix Commands (via SSH):</h3>";
            echo "<pre>cd " . dirname(__DIR__) . "\n";
            echo "mkdir -p cms/data\n";
            echo "chmod 755 cms/data\n";
            echo "chmod 644 cms/data/*.json</pre>";
        } else {
            echo "<p class='success'>✓ All checks passed! CMS should work correctly.</p>";
            echo "<p>If you still see issues in the browser:</p>";
            echo "<ol>";
            echo "<li>Clear browser cache (Ctrl+Shift+R)</li>";
            echo "<li>Check browser console for JavaScript errors</li>";
            echo "<li>Test with: <a href='test-connection.html' style='color: #4caf50;'>test-connection.html</a></li>";
            echo "</ol>";
        }
        ?>
    </div>

    <div class="section">
        <h2>🔗 Quick Links</h2>
        <p>
            <a href="artists.html" style="color: #4caf50; text-decoration: none;">→ Go to Artists Management</a><br>
            <a href="dashboard.html" style="color: #4caf50; text-decoration: none;">→ Go to Dashboard</a><br>
            <a href="test-connection.html" style="color: #4caf50; text-decoration: none;">→ Run JavaScript Tests</a><br>
            <a href="TROUBLESHOOTING-404.md" style="color: #4caf50; text-decoration: none;">→ Read Full Troubleshooting Guide</a>
        </p>
    </div>

    <div class="section">
        <p style="text-align: center; color: #666;">
            CMS Health Check v1.0 | <?php echo date('Y-m-d H:i:s'); ?>
        </p>
    </div>
</body>
</html>
