<main class="exhibitions-main exhibitions-history-main">
    <div class="exhibitions-history-container">
        <header class="exhibitions-history-header">
            <h1 class="page-title">ART FAIRS</h1>
        </header>
        <section class="art-fairs-list">
            <?php if (!empty($artFairs)): ?>
                <?php foreach ($artFairs as $fair): ?>
                    <?php
                        $title = $fair['title'] ?? 'Art Fair';
                        $year = $fair['year'] ?? '';
                        $location = $fair['location'] ?? '';
                        $link = $fair['link'] ?? '';
                        $imagePath = $fair['imagePath'] ?? '';
                        $safeHref = $link !== '' ? htmlspecialchars($link) : '#';
                        $assetPath = $imagePath !== '' ? asset($imagePath) : asset('images/assets/placeholder.jpg');
                    ?>
                    <article class="art-fair-row">
                        <div class="art-fair-row-image">
                            <?php if ($link !== ''): ?><a href="<?= $safeHref ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
                                <img src="<?= htmlspecialchars($assetPath) ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy">
                            <?php if ($link !== ''): ?></a><?php endif; ?>
                        </div>
                        <div class="art-fair-row-info">
                            <?php if ($link !== ''): ?><a href="<?= $safeHref ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
                                <h2 class="art-fair-row-title"><?= htmlspecialchars($title) ?></h2>
                                <p class="art-fair-row-year"><?= htmlspecialchars((string) $year) ?></p>
                                <p class="art-fair-row-subtitle"><?= htmlspecialchars($location) ?></p>
                            <?php if ($link !== ''): ?></a><?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No art fair data available.</p>
            <?php endif; ?>
        </section>
        <section class="art-fairs-media">
            <h2 class="page-title">VIDEO & IMAGES</h2>
            <div class="photographic-grid">
                <?php if (!empty($mediaImages)): ?>
                    <?php foreach ($mediaImages as $img): ?>
                        <?php
                        $rawPath = $img['path'] ?? '';
                        if ($rawPath === '') {
                            continue;
                        }
                        $imgPath = asset($rawPath);
                        ?>
                        <div class="photographic-item">
                            <img src="<?= htmlspecialchars($imgPath) ?>" alt="<?= htmlspecialchars($img['name'] ?? 'Art Fair Image') ?>" loading="lazy">
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No media uploaded yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>
