<?php
$slugUrl = str_replace('artist-', '', $artist['slug'] ?? '');
if ($slugUrl === ($artist['slug'] ?? '')) {
    $slugUrl = $artist['id'] ?? $slugUrl;
}
$nameDisplay = $artist['nameDisplay'] ?? $artist['name'] ?? '';
?>
<main class="artist-detail-main">
    <div class="artist-detail-container">
        <section class="artist-profile">
            <div class="artist-profile-left">
                <h1 class="artist-name-large"><?= htmlspecialchars($nameDisplay) ?></h1>
                <h2 class="artist-series-subtitle"><?= htmlspecialchars($year) ?></h2>
                <nav class="artist-sub-nav">
                    <a href="<?= url('artists/' . $slugUrl) ?>" class="artist-sub-nav-link">← BACK TO OVERVIEW</a>
                </nav>
            </div>
            <div class="artist-profile-right">
                <div class="artist-intro-block">
                    <div class="theme-name">
                        <h3>THEME</h3>
                        <h2>"In Full Bloom"</h2>
                    </div>
                    <div class="artist-bio">
                        <p>With a rich diversity of themes and an unrelenting dedication over his 25-year artistic journey, <strong><?= htmlspecialchars($artist['name'] ?? '') ?></strong> has continuously explored, learned, and reinvented himself, creating ever-new, distinctive bodies of work that captivate viewers and lead them from one emotional state to another.</p>
                        <p>Through these concerns and contemplations, the artist persistently sends out messages of tranquility — stories of a green world and a vision of peace for humankind.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="artist-artworks" id="artworks">
            <h2 class="artist-section-title">ARTWORKS</h2>
            <div class="artworks-grid">
                <?php foreach ($artworks as $aw): ?>
                <?php
                $imgPath = $aw['imagePath'] ?? '';
                if (strpos($imgPath, '../../') === 0) {
                    $imgPath = substr($imgPath, 6);
                }
                $caption = ($aw['code'] ?? '') . ' - ' . ($aw['medium'] ?? '');
                if (!empty($aw['size'])) {
                    $caption .= ' - ' . $aw['size'];
                }
                ?>
                <div class="artwork-item">
                    <div class="artwork-frame">
                        <img src="<?= asset($imgPath) ?>" alt="<?= htmlspecialchars($aw['title'] ?? $caption) ?>">
                    </div>
                    <p class="artwork-caption"><?= htmlspecialchars($caption) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <div class="back-link-wrap">
            <a href="<?= url('artists/' . $slugUrl) ?>" class="back-link">← BACK TO <?= htmlspecialchars(strtoupper($nameDisplay)) ?></a>
        </div>
    </div>
</main>
