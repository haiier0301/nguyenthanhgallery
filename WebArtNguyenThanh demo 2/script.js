// Minimal JavaScript for smooth scrolling, image placeholders, and lightbox

document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Handle image placeholders
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        // Add error handler to show gray placeholder
        img.addEventListener('error', function() {
            this.style.backgroundColor = '#e8e8e8';
            this.style.minHeight = '200px';
        });

        // Check if image is already loaded
        if (img.complete && img.naturalHeight === 0) {
            img.style.backgroundColor = '#e8e8e8';
        }
    });

    // Lazy loading images (for better performance when images exist)
    const imageOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px 50px 0px'
    };

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    }, imageOptions);

    images.forEach(img => {
        imageObserver.observe(img);
    });

    // Lightbox functionality for artwork images
    initLightbox();

    // Series preview hover effect (for artworks page)
    initSeriesPreview();

    // Mobile menu handling
    const createMobileMenu = () => {
        if (window.innerWidth <= 768) {
            const nav = document.querySelector('nav');
            if (nav && !document.querySelector('.mobile-menu-toggle')) {
                // Mobile navigation is handled through CSS
                // No additional JavaScript needed for minimal design
            }
        }
    };

    // Initialize
    createMobileMenu();

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            createMobileMenu();
        }, 250);
    });
});

// Lightbox Gallery Function
function initLightbox() {
    // Create lightbox element
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    lightbox.innerHTML = `
        <span class="lightbox-close">&times;</span>
        <div class="lightbox-content">
            <img src="" alt="" class="lightbox-image">
        </div>
        <div class="lightbox-caption"></div>
    `;
    document.body.appendChild(lightbox);

    const lightboxImg = lightbox.querySelector('.lightbox-image');
    const lightboxCaption = lightbox.querySelector('.lightbox-caption');
    const closeBtn = lightbox.querySelector('.lightbox-close');

    // Add click event to all artwork images
    const artworkImages = document.querySelectorAll('.artwork-item img, .series-hero img, .series-hero-small img');
    
    artworkImages.forEach(img => {
        img.addEventListener('click', function() {
            lightbox.classList.add('active');
            lightboxImg.src = this.src;
            lightboxImg.alt = this.alt;
            
            // Get caption from artwork-caption if exists
            const caption = this.parentElement.querySelector('.artwork-caption');
            if (caption) {
                lightboxCaption.innerHTML = caption.innerHTML;
            } else {
                lightboxCaption.innerHTML = `<p>${this.alt}</p>`;
            }
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        });
    });

    // Close lightbox on click outside image
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox || e.target === closeBtn) {
            closeLightbox();
        }
    });

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            closeLightbox();
        }
    });

    // Prevent click on image from closing lightbox
    lightboxImg.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Series Preview Function (for Artworks page)
function initSeriesPreview() {
    const previewImage = document.getElementById('preview-image');
    const seriesItems = document.querySelectorAll('.series-item');
    
    if (!previewImage || seriesItems.length === 0) {
        return; // Exit if not on artworks page
    }

    seriesItems.forEach(item => {
        // On hover, change preview image
        item.addEventListener('mouseenter', function() {
            const heroImage = this.getAttribute('data-hero-image');
            if (heroImage) {
                // Remove active class from all items
                seriesItems.forEach(i => i.classList.remove('active'));
                // Add active class to current item
                this.classList.add('active');
                
                // Change preview image with fade effect
                previewImage.style.opacity = '0.3';
                setTimeout(() => {
                    previewImage.src = heroImage;
                    previewImage.style.opacity = '1';
                }, 250);
            }
        });

        // On click, still navigate to the series page
        item.addEventListener('click', function(e) {
            // Allow default link behavior
        });
    });
}


