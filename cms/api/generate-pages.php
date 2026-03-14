<?php
/**
 * CMS API - Page Generator
 * Generates HTML pages from JSON data
 */

// Load JSON data
function loadJSON($file) {
    $filepath = __DIR__ . '/../data/' . $file;
    if (!file_exists($filepath)) {
        return [];
    }
    return json_decode(file_get_contents($filepath), true);
}

// Generate artist page
function generateArtistPage($artist, $artworks) {
    $slug = $artist['slug'];
    $name = $artist['nameDisplay'];
    $bio = $artist['bio'];
    $code = $artist['code'];
    
    // Filter artworks for this artist
    $artistArtworks = array_filter($artworks, function($artwork) use ($artist) {
        return $artwork['artistId'] === $artist['id'];
    });
    
    // Generate artworks HTML
    $artworksHtml = '';
    foreach ($artistArtworks as $artwork) {
        $caption = $artwork['code'] . ' - ' . $artwork['medium'];
        $size = $artwork['size'] ? '<span class="artwork-size">' . $artwork['size'] . '</span>' : '';
        
        $artworksHtml .= <<<HTML
        <div class="artwork-item">
            <img src="{$artwork['imagePath']}" alt="{$artwork['code']}" class="lightbox-image">
            <p class="artwork-caption">{$caption} {$size}</p>
        </div>
HTML;
    }
    
    // Generate full page HTML
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$name} - NGUYEN THANH GALLERIE</title>
    <link rel="stylesheet" href="../../style.css">
    <link rel="icon" type="image/png" href="../../images/assets/favicon.png">
</head>
<body>
    <!-- Navigation (copy from template) -->
    <nav class="main-nav">
        <div class="nav-logo">
            <a href="../../index.html">NGUYEN THANH GALLERIE</a>
        </div>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <ul class="nav-links" id="navLinks">
            <li><a href="../../index.html">HOME</a></li>
            <li><a href="../../artists.html">ARTISTS</a></li>
            <li><a href="../../exhibitions.html">EXHIBITIONS</a></li>
            <li><a href="../../contact.html">CONTACT</a></li>
        </ul>
    </nav>

    <main>
        <section class="page-header">
            <h1 class="artist-name-large">{$name}</h1>
        </section>

        <section class="artist-intro-block">
            <div class="artist-bio">
                {$bio}
            </div>
        </section>

        <section class="artist-content">
            <h2 class="section-title">ARTWORKS</h2>
            <div class="artworks-grid">
                {$artworksHtml}
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-contact">
            <h3>NGUYEN THANH GALLERIE</h3>
            <p>139 Dong Khoi Street, Sai Gon Ward, Ho Chi Minh City, Vietnam</p>
            <p>+84 (028) 3823 8754 | +84 (0) 919 268 888</p>
            <p><a href="../../contact.html">nguyenthanhgallerie@gmail.com</a></p>
        </div>
    </footer>

    <script src="../../script.js"></script>
</body>
</html>
HTML;
    
    return $html;
}

// Main execution
if (isset($_GET['action']) && $_GET['action'] === 'generate-all') {
    header('Content-Type: application/json');
    
    $artists = loadJSON('artists.json');
    $artworks = loadJSON('artworks.json');
    $generated = [];
    
    foreach ($artists as $artist) {
        $html = generateArtistPage($artist, $artworks);
        $filepath = __DIR__ . '/../../artists/' . $artist['slug'] . '.html';
        
        // Create directory if needed
        $dir = dirname($filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        file_put_contents($filepath, $html);
        $generated[] = $artist['slug'] . '.html';
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Pages generated successfully',
        'files' => $generated
    ]);
} else {
    echo '<h1>CMS Page Generator</h1>';
    echo '<p><a href="?action=generate-all">Generate All Pages</a></p>';
}
?>
