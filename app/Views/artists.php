<main class="artists-main">
    <div class="artists-container">
        <section class="artists-list-section">
            <h2 class="artists-section-heading">LIST ARTISTS</h2>
            <div class="artists-grid artists-grid-2x5">
                <?php foreach ($artists as $a): ?>
                <a href="<?= url('artists/' . ($a['slug'] ?? '')) ?>" class="artist-name"><?= htmlspecialchars($a['nameDisplay'] ?? $a['name'] ?? '') ?></a>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="artists-thumbnails-section">
            <h2 class="artists-section-heading">THUMBNAILS</h2>
            <div class="thumbnails-grid">
                <?php foreach ($artists as $a):
                    $thumb = $a['thumbnailImage'] ?? $a['featuredImage'] ?? '';
                    if (strpos($thumb, '../') === 0) {
                        $thumb = substr($thumb, 3);
                    }
                ?>
                <a href="<?= url('artists/' . ($a['slug'] ?? '')) ?>" class="artist-thumb">
                    <div class="artist-thumb-image">
                        <img src="<?= asset($thumb) ?>" alt="<?= htmlspecialchars($a['nameDisplay'] ?? '') ?>">
                    </div>
                    <span class="artist-thumb-name"><?= htmlspecialchars($a['nameDisplay'] ?? '') ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>
