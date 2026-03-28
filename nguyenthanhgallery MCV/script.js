/**
 * Nguyen Thanh Gallery aaa
 * Main JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    initHeader();
    initSmoothScroll();
    initNewsCards();
    initLightbox();
    initVideoAutoplay();
});

/**
 * Header scroll effect & mobile menu
 */
function initHeader() {
    const header = document.querySelector('.header');
    const menuToggle = document.querySelector('.menu-toggle');

    // Scroll effect - add scrolled class
    const handleScroll = () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Initial check

    // Mobile menu toggle
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            header.classList.toggle('menu-open');
            menuToggle.classList.toggle('active');
            document.body.classList.toggle('menu-open', header.classList.contains('menu-open'));
        });

        // Close menu when clicking a link
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                header.classList.remove('menu-open');
                menuToggle.classList.remove('active');
                document.body.classList.remove('menu-open');
            });
        });

        // Close menu on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                header.classList.remove('menu-open');
                menuToggle.classList.remove('active');
                document.body.classList.remove('menu-open');
            }
        });
    }
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

/**
 * News cards - subtle animation on scroll
 */
function initNewsCards() {
    const cards = document.querySelectorAll('.news-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.5s ease ${index * 0.1}s, transform 0.5s ease ${index * 0.1}s`;
        observer.observe(card);
    });
}

/**
 * Lightbox - click image to view full size, click outside to close
 */
function initLightbox() {
    const images = document.querySelectorAll('.artwork-frame img, .exhibition-image img, .photographic-item img');
    if (!images.length) return;

    // Filter out images inside series cards (they should navigate, not open lightbox)
    // Also filter out images inside video containers (photographic-item-video)
    const lightboxImages = Array.from(images).filter(img => 
        !img.closest('.series-card') && !img.closest('.photographic-item-video')
    );
    if (!lightboxImages.length) return;

    // Create lightbox overlay
    const overlay = document.createElement('div');
    overlay.className = 'lightbox-overlay';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML = `
        <div class="lightbox-backdrop"></div>
        <div class="lightbox-content">
            <img src="" alt="">
            <div class="lightbox-caption">
                <p class="lightbox-caption-title"></p>
                <p class="lightbox-caption-medium"></p>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    const backdrop = overlay.querySelector('.lightbox-backdrop');
    const lightboxImg = overlay.querySelector('.lightbox-content img');
    const lightboxCaptionTitle = overlay.querySelector('.lightbox-caption-title');
    const lightboxCaptionMedium = overlay.querySelector('.lightbox-caption-medium');

    function openLightbox(src, alt, title, medium, size) {
        lightboxImg.src = src;
        lightboxImg.alt = alt || '';
        
        // Parse and display caption
        if (title || medium) {
            lightboxCaptionTitle.textContent = title || '';
            if (size) {
                lightboxCaptionMedium.innerHTML = `${(medium || '').toUpperCase()}<span class="lightbox-size">${size}</span>`;
            } else {
                lightboxCaptionMedium.textContent = (medium || '').toUpperCase();
            }
        } else {
            lightboxCaptionTitle.textContent = '';
            lightboxCaptionMedium.textContent = '';
        }
        
        overlay.classList.add('active');
        overlay.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    // Click on backdrop (outside image) to close
    backdrop.addEventListener('click', closeLightbox);
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeLightbox();
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && overlay.classList.contains('active')) {
            closeLightbox();
        }
    });

    // Prevent closing when clicking the image itself
    lightboxImg.addEventListener('click', (e) => e.stopPropagation());

    // Attach click to images (excluding series cards)
    lightboxImages.forEach((img) => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', (e) => {
            e.preventDefault();
            const src = img.src || img.getAttribute('src');
            const alt = img.alt || '';
            
            // Get caption and size from adjacent elements
            const artworkItem = img.closest('.artwork-item, .exhibition-item');
            let title = '';
            let medium = '';
            let size = '';
            const dataSize = (img.getAttribute('data-size') || '').trim();
            
            if (artworkItem) {
                const captionEl = artworkItem.querySelector('.artwork-caption, .exhibition-caption');
                if (captionEl) {
                    // Get visible text only (excludes hidden size span)
                    const captionClone = captionEl.cloneNode(true);
                    const sizeSpan = captionClone.querySelector('.artwork-size');
                    if (sizeSpan) {
                        size = sizeSpan.textContent.trim();
                        sizeSpan.remove();
                    }
                    const caption = captionClone.textContent.trim();
                    const parts = caption.split(' - ').map(part => part.trim()).filter(Boolean);
                    title = parts[0] || '';
                    medium = parts[1] || '';
                    if (!size && parts.length > 2) {
                        size = parts.slice(2).join(' - ');
                    }
                } else {
                    title = alt;
                }
            }
            if (!size && dataSize) {
                size = dataSize;
            }
            
            openLightbox(src, alt, title, medium, size);
        });
    });
}

/**
 * Video autoplay on scroll
 */
function initVideoAutoplay() {
    const videos = document.querySelectorAll('.photographic-item-video video');
    if (!videos.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target;
            if (entry.isIntersecting) {
                video.play().catch(err => {
                    console.log('Video autoplay prevented:', err);
                });
            } else {
                video.pause();
            }
        });
    }, {
        threshold: 0.5,
        rootMargin: '0px'
    });

    videos.forEach(video => {
        observer.observe(video);
    });
}
