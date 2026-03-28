<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?= asset('images/assets/favicon.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/assets/favicon.png') ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Nguyen Thanh Gallery') ?></title>
    <link rel="stylesheet" href="<?= asset('style.css') ?>?v=<?= filemtime(ROOT_PATH . 'style.css') ?: 1 ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?= htmlspecialchars($bodyClass ?? '') ?>">
    <header class="header <?= ($bodyClass !== '') ? 'header-light' : '' ?>">
        <div class="header-inner">
            <a href="<?= url('') ?>" class="logo" style="font-size: 32px;">NGUYEN THANH GALLERIE</a>
            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="<?= url('artists') ?>" <?= ($currentPage ?? '') === 'artists' ? 'class="active"' : '' ?>>ARTISTS</a></li>
                    <li><a href="<?= url('exhibitions') ?>" <?= ($currentPage ?? '') === 'exhibitions' ? 'class="active"' : '' ?>>EXHIBITIONS</a></li>
                    <li><a href="<?= url('art-fairs') ?>" <?= ($currentPage ?? '') === 'art-fairs' ? 'class="active"' : '' ?>>ART FAIRS</a></li>
                    <li><a href="<?= url('about') ?>" <?= ($currentPage ?? '') === 'about' ? 'class="active"' : '' ?>>ABOUT US</a></li>
                    <li><a href="<?= url('contact') ?>" <?= ($currentPage ?? '') === 'contact' ? 'class="active"' : '' ?>>CONTACT</a></li>
                </ul>
            </nav>
            <button class="menu-toggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <?= $content ?? '' ?>

    <?php
    // Load settings from CMS for dynamic footer
    use App\Models\Settings;
    $settings = Settings::all();
    $phone1 = $settings['contactPhone1'] ?? '+84 (028) 3823 8754';
    $phone2 = $settings['contactPhone2'] ?? '+84 (0) 919 268 83';
    $address = $settings['contactAddress'] ?? '139 Dong Khoi Street, Sai Gon Ward, Ho Chi Minh City, Vietnam';
    $email = $settings['contactEmail'] ?? 'nguyenthanhgallerie@gmail.com';
    ?>

    <footer class="footer">
        <div class="footer-inner">
            <div class="footer-contact">
                <p><?= htmlspecialchars($address) ?></p>
                <p>
                    <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', $phone1) ?>"><?= htmlspecialchars($phone1) ?></a>
                    <?php if ($phone2): ?>
                     - <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', $phone2) ?>"><?= htmlspecialchars($phone2) ?></a>
                    <?php endif; ?>
                </p>
                <p><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></p>
                <p><a href="mailto:tnguyentrangartist78@gmail.com">tnguyentrangartist78@gmail.com</a></p>
            </div>
            <div class="footer-social">
                <a href="mailto:thanhart2000@yahoo.com" aria-label="Email">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                </a>
                <a href="<?= url('contact') ?>" aria-label="Location">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                </a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>COPYRIGHT © 2026 NGUYEN THANH GALLERY</p>
            <a href="<?= asset('cms/index.html') ?>" style="color: rgba(255,255,255,0.3); text-decoration: none; font-size: 10px;"><p>SITE BY ARTLOGIC | CMS</p></a>
        </div>
    </footer>
    <script src="<?= asset('script.js') ?>?v=<?= filemtime(ROOT_PATH . 'script.js') ?: 1 ?>"></script>
</body>
</html>
