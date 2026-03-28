<?php
$slug = $artist['slug'] ?? '';
$slugUrl = str_replace('artist-', '', $slug);
if ($slugUrl === $slug && ($artist['id'] ?? '') !== $slug) {
    $slugUrl = $artist['id'] ?? $slug;
}
$nameDisplay = $artist['nameDisplay'] ?? $artist['name'] ?? '';
$artworksByArtist = $artworks ?? [];
$hasSeries = (bool)($artist['hasSeries'] ?? false);
?>
<main class="artist-detail-main">
    <div class="artist-detail-container">
        <section class="artist-profile">
            <div class="artist-profile-left">
                <h1 class="artist-name-large"><?= htmlspecialchars($nameDisplay) ?></h1>
                <nav class="artist-sub-nav">
                    <a href="#about" class="artist-sub-nav-link active">ABOUT</a>
                    <a href="#artworks" class="artist-sub-nav-link"><?= $hasSeries ? 'SERIES' : 'ARTWORKS' ?></a>
                </nav>
            </div>
            <div class="artist-profile-right" id="about">
                <div class="artist-intro-block">
                    <div class="artist-bio">
                        <?= $artist['bio'] ?? '' ?>
                    </div>
                </div>
            </div>
        </section>
        <section class="artist-artworks" id="artworks">
            <h2 class="artist-section-title"><?= $hasSeries ? 'SERIES' : 'ARTWORKS' ?></h2>
            <?php if ($hasSeries && !empty($series)): ?>
            <div class="serial-coming-soon">
                <h3>New Works – coming soon</h3>
                <h4>Theme: For a World of Peace & Serenity</h4>
            </div>
            <div class="artworks-grid">
                <?php foreach ($series as $s): ?>
                <?php
                $year = $s['year'] ?? '';
                $title = $s['title'] ?? $year;
                $theme = $s['theme'] ?? '';
                $count = $s['artworkCount'] ?? 0;
                
                // Get first artwork image for this series
                $firstArtwork = null;
                foreach ($artworksByArtist as $aw) {
                    if (($aw['seriesYear'] ?? '') === (string)$year) {
                        $firstArtwork = $aw;
                        break;
                    }
                }
                $imgSrc = $s['featuredImage'] ?? ($firstArtwork['imagePath'] ?? '');
                if (strpos($imgSrc, '../../') === 0) {
                    $imgSrc = substr($imgSrc, 6);
                }
                if (!$imgSrc) {
                    $imgSrc = 'images/artists/Nguyen Thanh/' . $year . '/' . $year . '_1.jpg';
                }
                
                // Count actual artworks if not set
                if (!$count) {
                    foreach ($artworksByArtist as $aw) {
                        if (($aw['seriesYear'] ?? '') === (string)$year) $count++;
                    }
                }
                ?>
                <a href="<?= url('artists/' . $slugUrl . '/' . $year) ?>" class="artwork-item series-card">
                    <div class="artwork-frame">
                        <img src="<?= asset($imgSrc) ?>" alt="<?= htmlspecialchars($title) ?>">
                    </div>
                    <h3 class="series-card-title"><?= htmlspecialchars($year) ?></h3>
                    <p class="series-card-count" style="font-weight: 600; color: #333; margin-bottom: 4px;"><?= htmlspecialchars($title) ?></p>
                    <?php if ($theme): ?>
                    <p class="series-card-count" style="font-style: italic; color: #666;"><?= htmlspecialchars($theme) ?></p>
                    <?php endif; ?>
                    <p class="series-card-count"><?= $count ?> works</p>
                </a>
                <?php endforeach; ?>
            </div>
            <?php elseif (!empty($artworksByArtist)): ?>
            <div class="artworks-grid">
                <?php foreach ($artworksByArtist as $aw): ?>
                <?php
                $imgSrc = $aw['imagePath'] ?? '';
                if (strpos($imgSrc, '../../') === 0) {
                    $imgSrc = substr($imgSrc, 6);
                } elseif (strpos($imgSrc, '../') === 0) {
                    $imgSrc = substr($imgSrc, 3);
                }
                $caption = trim(($aw['code'] ?? '') . ' - ' . ($aw['medium'] ?? ''), ' -');
                ?>
                <div class="artwork-item">
                    <div class="artwork-frame">
                        <img src="<?= asset($imgSrc) ?>" alt="<?= htmlspecialchars($aw['title'] ?? $caption) ?>">
                    </div>
                    <p class="artwork-caption"><?= htmlspecialchars($caption) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="artworks-grid">
                <p class="artist-no-series"><?= $hasSeries ? 'No series yet.' : 'No artworks yet.' ?></p>
            </div>
            <?php endif; ?>
        </section>
    </div>
</main>
