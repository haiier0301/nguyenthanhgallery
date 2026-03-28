<?php
/**
 * Generate Individual Series Pages
 * Creates HTML pages for each artist series/theme from CMS data
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Load series data
$seriesFile = '../data/series.json';
if (!file_exists($seriesFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Series data not found']);
    exit;
}

$allSeries = json_decode(file_get_contents($seriesFile), true);
if (!$allSeries) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Invalid series data']);
    exit;
}

// Load artists data
$artistsFile = '../data/artists.json';
$artists = [];
if (file_exists($artistsFile)) {
    $artists = json_decode(file_get_contents($artistsFile), true);
}

// Load artworks data
$artworksFile = '../data/artworks.json';
$artworks = [];
if (file_exists($artworksFile)) {
    $artworks = json_decode(file_get_contents($artworksFile), true);
}

$generated = [];
$errors = [];

// Generate page for each published series
foreach ($allSeries as $series) {
    if (!($series['published'] ?? true)) {
        continue; // Skip unpublished series
    }
    
    $artistId = $series['artistId'];
    $year = $series['year'];
    
    // Find artist info
    $artist = null;
    foreach ($artists as $a) {
        if ($a['id'] === $artistId) {
            $artist = $a;
            break;
        }
    }
    
    if (!$artist) {
        $errors[] = $series['id'] . ' (artist not found)';
        continue;
    }
    
    // Get artworks for this series
    $seriesArtworks = array_filter($artworks, function($a) use ($artistId, $year) {
        return $a['artistId'] === $artistId && $a['seriesYear'] === $year;
    });
    
    // Determine folder path
    // For Nguyen Thanh: artists/nguyen-thanh/2020.html
    // For others: artists/artist-name/series-2020.html
    $artistSlug = $artist['slug'];
    $artistDir = "../../artists/" . str_replace('artist-', '', $artistSlug) . "/";
    
    // Create directory if doesn't exist
    if (!is_dir($artistDir)) {
        mkdir($artistDir, 0755, true);
    }
    
    $filename = $artistDir . $year . '.html';
    
    // Generate HTML
    $html = generateSeriesPage($artist, $series, $seriesArtworks);
    
    // Save file
    if (file_put_contents($filename, $html)) {
        $generated[] = [
            'id' => $series['id'],
            'title' => $series['title'],
            'year' => $year,
            'url' => str_replace('../../', '../', $filename)
        ];
    } else {
        $errors[] = $series['id'];
    }
}

echo json_encode([
    'success' => true,
    'generated' => count($generated),
    'pages' => $generated,
    'errors' => $errors
]);

/**
 * Generate series page HTML
 */
function generateSeriesPage($artist, $series, $artworks) {
    $artistName = htmlspecialchars($artist['nameDisplay']);
    $year = htmlspecialchars($series['year']);
    $title = htmlspecialchars($series['title']);
    $theme = $series['theme'];
    $description = $series['description']; // Already contains HTML
    
    // Build artworks grid
    $artworksHtml = '';
    foreach ($artworks as $artwork) {
        $code = htmlspecialchars($artwork['code']);
        $medium = htmlspecialchars($artwork['medium']);
        $size = htmlspecialchars($artwork['size'] ?? '');
        $imagePath = htmlspecialchars($artwork['imagePath']);
        
        $artworksHtml .= <<<HTML
                    <div class="artwork-item">
                        <img src="{$imagePath}" alt="{$code}" class="lightbox-image" data-size="{$size}">
                        <p class="artwork-caption">{$code} - {$medium}</p>
                    </div>
HTML;
    }
    
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../../images/assets/favicon.png">
    <title>{$artistName} - {$year} - Nguyen Thanh Gallery</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-logo">
            <a href="../../index.html">NGUYEN THANH GALLERIE</a>
        </div>
        <ul class="nav-links">
            <li><a href="../../index.html">HOME</a></li>
            <li><a href="../../about.html">ABOUT</a></li>
            <li><a href="../../artists.html">ARTISTS</a></li>
            <li><a href="../../exhibitions.html">EXHIBITIONS</a></li>
            <li><a href="../../art-fairs.html">ART FAIRS</a></li>
            <li><a href="../../publications.html">PUBLICATIONS</a></li>
            <li><a href="../../contact.html">CONTACT</a></li>
        </ul>
    </nav>

    <!-- Page Title -->
    <div class="page-title">
        <h1>{$artistName}</h1>
    </div>

    <!-- Series Bio -->
    <section class="artist-bio">
        <div class="artist-bio-content">
            <div class="artist-bio-header" style="text-align: center; margin-bottom: 32px;">
                <p style="font-size: 14px; color: #666; margin-bottom: 8px;">
                    <a href="../artist-{$artist['slug']}.html" style="color: #2d5f3f; text-decoration: underline;">← BACK TO OVERVIEW</a>
                </p>
                <h2 style="font-size: 20px; color: #2d5f3f; margin-bottom: 16px;">{$year}</h2>
                <p style="font-size: 28px; font-weight: 300; font-style: italic; color: #333; margin-bottom: 24px;">
                    THEME<br>{$theme}
                </p>
            </div>
            
            <div class="artist-bio-text">
                {$description}
            </div>
        </div>
    </section>

    <!-- Artworks Section -->
    <section class="artist-artworks">
        <h2>ARTWORKS</h2>
        <div class="artworks-grid">
            {$artworksHtml}
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>NGUYEN THANH GALLERIE</h3>
                <p class="footer-address">
                    139 Dong Khoi Street<br>
                    Sai Gon Ward<br>
                    Ho Chi Minh City, Vietnam
                </p>
            </div>
            <div class="footer-section">
                <h3>CONTACT</h3>
                <p>
                    <strong>Phone:</strong><br>
                    +84 833 969 939<br>
                    +84 833 969 969
                </p>
                <p><strong>Email:</strong> contact@nguyenthanhgallery.com</p>
            </div>
            <div class="footer-section footer-social">
                <a href="https://maps.google.com" target="_blank" class="social-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>COPYRIGHT © 2026 NGUYEN THANH GALLERY</p>
            <p>SITE BY ARTLOGIC</p>
        </div>
    </footer>

    <script src="../../script.js"></script>
</body>
</html>
HTML;
    
    return $html;
}
?>
