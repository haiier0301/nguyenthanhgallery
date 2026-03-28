<?php
// Load settings from CMS
use App\Models\Settings;
$settings = Settings::all();
$phone1 = $settings['contactPhone1'] ?? '+84 (028) 3823 8754';
$phone2 = $settings['contactPhone2'] ?? '+84 (0) 919 268 83';
$address = $settings['contactAddress'] ?? '139 Dong Khoi Street, Sai Gon Ward, Ho Chi Minh City, Vietnam';
$email = $settings['contactEmail'] ?? 'nguyenthanhgallerie@gmail.com';
$openingHours = $settings['openingHours'] ?? "Monday – Sunday\n9:00 AM – 7:00 PM";
$mapUrl = $settings['socialLinks']['mapUrl'] ?? 'https://www.google.com/maps/search/139+Dong+Khoi+Street+Sai+Gon+Ward+Ho+Chi+Minh+City+Vietnam';

// Split address for display
$addressLines = explode(',', $address);
$addressDisplay = '';
foreach ($addressLines as $line) {
    $addressDisplay .= trim($line) . '<br>';
}
$addressDisplay = rtrim($addressDisplay, '<br>');
?>

<main class="contact-main">
    <div class="contact-container">
        <h1 class="contact-title">CONTACT</h1>
        <section class="contact-content">
            <div class="contact-map">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.63689847558!2d106.7012!3d10.7769!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f46a640754d%3A0x0!2zMTM5IMSQ4buRbmcgS2hvaSBTdHJlZXQsIELDrG4gTmdow6osIFF14buRYyAxLCBI4buTIEMjaMOhIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1sen!2s!4v1640000000000"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade" title="Nguyen Thanh Gallery Location">
                </iframe>
            </div>
            <div class="contact-info">
                <div class="contact-block">
                    <h2 class="contact-heading">Nguyen Thanh Gallery</h2>
                    <p class="contact-address"><?= $addressDisplay ?></p>
                </div>
                <div class="contact-block">
                    <h2 class="contact-heading">Opening Hours</h2>
                    <p><?= nl2br(htmlspecialchars($openingHours)) ?></p>
                    <p class="contact-note">Out-of-hours visits available upon request.</p>
                </div>
                <div class="contact-block">
                    <h2 class="contact-heading">Contact</h2>
                    <p><a href="mailto:<?= htmlspecialchars($email) ?>"><?= htmlspecialchars($email) ?></a></p>
                    <p><a href="mailto:tnguyentrangartist78@gmail.com">tnguyentrangartist78@gmail.com</a></p>
                    <p>Phone: <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', $phone1) ?>"><?= htmlspecialchars($phone1) ?></a><?php if ($phone2): ?> - <a href="tel:<?= str_replace([' ', '(', ')', '-'], '', $phone2) ?>"><?= htmlspecialchars($phone2) ?></a><?php endif; ?></p>
                </div>
                <div class="contact-links">
                    <a href="mailto:<?= htmlspecialchars($email) ?>" class="contact-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                        <span>JOIN THE MAILING LIST</span>
                    </a>
                    <a href="<?= htmlspecialchars($mapUrl) ?>" target="_blank" rel="noopener noreferrer" class="contact-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        <span>VIEW ON GOOGLE MAPS</span>
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>
