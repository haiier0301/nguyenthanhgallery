<main class="exhibitions-main exhibitions-history-main">
    <div class="exhibitions-history-container">
        <header class="exhibitions-history-header">
            <h1 class="page-title">EXHIBITIONS</h1>
        </header>

        <section class="exhibitions-history-block">
            <h2 class="exhibitions-history-heading">AWARDS</h2>
            <div class="exhibitions-history-list">
                <div class="exhibitions-history-year-group">
                    <p class="exhibitions-year">&nbsp;</p>
                    <ul class="exhibitions-history-items">
                        <?php foreach ($awards as $e): ?>
                        <li><?= htmlspecialchars($e['title'] ?? '') ?> — <?= htmlspecialchars($e['location'] ?? '') ?>, <?= htmlspecialchars($e['year'] ?? '') ?></li>
                        <?php endforeach; ?>
                        <?php if (empty($awards)): ?>
                        <li>Spotlight Award — Red Dot Miami Art Fair, USA, 2025</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>

        <div class="exhibitions-history-divider"></div>

        <section class="exhibitions-history-block">
            <h2 class="exhibitions-history-heading">EXHIBITION HISTORY</h2>
            <div class="exhibitions-history-list">
                <?php
                $byYear = [];
                foreach ($history as $e) {
                    $y = $e['year'] ?? '';
                    if (!isset($byYear[$y])) $byYear[$y] = [];
                    $byYear[$y][] = $e;
                }
                krsort($byYear, SORT_NATURAL);
                foreach ($byYear as $year => $items):
                ?>
                <div class="exhibitions-history-year-group">
                    <p class="exhibitions-year"><?= htmlspecialchars($year) ?></p>
                    <ul class="exhibitions-history-items">
                        <?php foreach ($items as $e): ?>
                        <li><?= htmlspecialchars($e['title'] ?? '') ?><?= !empty($e['location']) ? ' — ' . htmlspecialchars($e['location']) : '' ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
                <div class="exhibitions-history-year-group">
                    <p class="exhibitions-year">2005–Present</p>
                    <ul class="exhibitions-history-items">
                        <li>Selected solo and group exhibitions in Ho Chi Minh City and Hanoi, Vietnam</li>
                    </ul>
                </div>
            </div>
        </section>

        <div class="exhibitions-history-divider"></div>

        <section class="exhibitions-history-block">
            <h2 class="exhibitions-history-heading">COLLABORATIONS &amp; HIGHLIGHTS</h2>
            <div class="exhibitions-history-list">
                <div class="exhibitions-history-year-group">
                    <p class="exhibitions-year">&nbsp;</p>
                    <ul class="exhibitions-history-items">
                        <li>Promenarts Gallery — France<br><span class="exhibitions-note">Ongoing gallery collaboration (since 2024)</span></li>
                        <li>Private collectors in Vietnam, Asia, Europe, and North America</li>
                        <li>Fine art collections commissioned for luxury hotels in Ho Chi Minh City, including: Grand Hotel, Continental Hotel, Palace Hotel, Kimdo Hotel, and Huong Sen Hotel</li>
                    </ul>
                </div>
            </div>
        </section>

        <div class="exhibitions-history-divider"></div>

        <section class="exhibitions-photographic">
            <h2 class="page-title">PHOTOGRAPHIC</h2>
            <div class="photographic-item photographic-item-video">
                <video src="<?= asset('images/art-fair/7582043678369.mp4') ?>" controls playsinline preload="metadata" muted loop>Your browser does not support the video tag.</video>
            </div>
            <div class="photographic-grid">
                <?php
                $photos = ['z7582032220206_e2ade248696a1dda6e2bb369a81cf357.jpg', 'z7582031989513_94f1ec3ddb93d571b57494710cfa1406.jpg', 'z7582032356160_1d25959ef98b3e787283f67d9332da51.jpg', 'z7582032481337_372f6fae2572c08c1ff502aefa0897d4.jpg', 'z7582032898916_a9e49506f1a3f67a7f5b4f5b8d76af38.jpg', 'z7582032618994_2d85441a5091901bdfbe12178d863d56.jpg'];
                foreach ($photos as $i => $p):
                ?>
                <div class="photographic-item">
                    <img src="<?= asset('images/exhibitions/' . $p) ?>" alt="Exhibition Photo <?= $i + 1 ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>
