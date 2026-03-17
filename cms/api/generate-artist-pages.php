<?php
/**
 * Generate Individual Artist Pages
 * Creates HTML pages for each artist from CMS data
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

// Load artists data
$artistsFile = '../data/artists.json';
if (!file_exists($artistsFile)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Artists data not found']);
    exit;
}

$artists = json_decode(file_get_contents($artistsFile), true);
if (!$artists) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Invalid artists data']);
    exit;
}

// Load artworks data
$artworksFile = '../data/artworks.json';
$artworks = [];
if (file_exists($artworksFile)) {
    $artworks = json_decode(file_get_contents($artworksFile), true);
}

$generated = [];
$errors = [];

// Generate page for each artist
foreach ($artists as $artist) {
    $slug = $artist['slug'];
    $artistDir = "../../artists/";
    $filename = $artistDir . $slug . '.html';
    
    // Create artists directory if it doesn't exist
    if (!is_dir($artistDir)) {
        mkdir($artistDir, 0755, true);
    }
    
    // Get artist's artworks
    $artistArtworks = array_filter($artworks, function($a) use ($artist) {
        return $a['artistId'] === $artist['id'];
    });
    
    // Group artworks by series year
    $series = [];
    foreach ($artistArtworks as $artwork) {
        $year = $artwork['seriesYear'] ?? 'other';
        if (!isset($series[$year])) {
            $series[$year] = [];
        }
        $series[$year][] = $artwork;
    }
    ksort($series);
    
    // Generate HTML
    $html = generateArtistPage($artist, $series);
    
    // Save file
    if (file_put_contents($filename, $html)) {
        $generated[] = $slug;
    } else {
        $errors[] = $slug;
    }
}

echo json_encode([
    'success' => true,
    'generated' => count($generated),
    'pages' => $generated,
    'errors' => $errors
]);

/**
 * Generate artist page HTML
 */
function generateArtistPage($artist, $series) {
    $name = htmlspecialchars($artist['nameDisplay']);
    $code = htmlspecialchars($artist['code']);
    $born = isset($artist['born']) ? date('Y', strtotime($artist['born'])) : 'N/A';
    $birthPlace = htmlspecialchars($artist['birthPlace'] ?? '');
    $bio = $artist['bio']; // Already contains HTML
    
    // Build series sections
    $seriesSections = '';
    foreach ($series as $year => $artworks) {
        $seriesSections .= generateSeriesSection($year, $artworks);
    }
    
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/assets/favicon.png">
    <title>{$name} - Nguyen Thanh Gallery</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-logo">
            <a href="../index.html">NGUYEN THANH GALLERIE</a>
        </div>
        <ul class="nav-links">
            <li><a href="../index.html">HOME</a></li>
            <li><a href="../about.html">ABOUT</a></li>
            <li><a href="../artists.html">ARTISTS</a></li>
            <li><a href="../exhibitions.html">EXHIBITIONS</a></li>
            <li><a href="../art-fairs.html">ART FAIRS</a></li>
            <li><a href="../publications.html">PUBLICATIONS</a></li>
            <li><a href="../contact.html">CONTACT</a></li>
        </ul>
    </nav>

    <!-- Page Title -->
    <div class="page-title">
        <h1>{$name}</h1>
    </div>

    <!-- Artist Bio -->
    <section class="artist-bio">
        <div class="artist-bio-content">
            <h2>Biography</h2>
            <div class="artist-info">
                <p><strong>Born:</strong> {$born}</p>
                <p><strong>Birthplace:</strong> {$birthPlace}</p>
                <p><strong>Artist Code:</strong> {$code}</p>
            </div>
            <div class="artist-bio-text">
                {$bio}
            </div>
        </div>
    </section>

    {$seriesSections}

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

    <script src="../script.js"></script>
</body>
</html>
HTML;
    
    return $html;
}

/**
 * Generate series section HTML
 */
function generateSeriesSection($year, $artworks) {
    $yearLabel = $year !== 'other' ? $year : 'Other Works';
    
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
    
    return <<<HTML
    <!-- Series: {$yearLabel} -->
    <section class="artist-artworks">
        <h2>{$yearLabel}</h2>
        <div class="artworks-grid">
            {$artworksHtml}
        </div>
    </section>

HTML;
}
?>
