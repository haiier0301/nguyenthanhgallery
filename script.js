// ===================================
// MOBILE NAVIGATION
// ===================================
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger) {
    hamburger.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        
        // Animate hamburger
        const spans = hamburger.querySelectorAll('span');
        spans[0].style.transform = navMenu.classList.contains('active') 
            ? 'rotate(45deg) translate(5px, 5px)' 
            : 'none';
        spans[1].style.opacity = navMenu.classList.contains('active') ? '0' : '1';
        spans[2].style.transform = navMenu.classList.contains('active') 
            ? 'rotate(-45deg) translate(7px, -6px)' 
            : 'none';
    });

    // Close menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            const spans = hamburger.querySelectorAll('span');
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        });
    });
}

// ===================================
// HERO SLIDESHOW
// ===================================
const heroSlides = document.querySelectorAll('.hero-slide');
const heroPrev = document.querySelector('.hero-prev');
const heroNext = document.querySelector('.hero-next');
let currentHeroSlide = 0;

function showHeroSlide(n) {
    heroSlides.forEach(slide => slide.classList.remove('active'));
    currentHeroSlide = (n + heroSlides.length) % heroSlides.length;
    heroSlides[currentHeroSlide].classList.add('active');
}

if (heroPrev && heroNext) {
    heroPrev.addEventListener('click', () => showHeroSlide(currentHeroSlide - 1));
    heroNext.addEventListener('click', () => showHeroSlide(currentHeroSlide + 1));

    // Auto-advance slideshow every 5 seconds
    setInterval(() => {
        showHeroSlide(currentHeroSlide + 1);
    }, 5000);
}

// ===================================
// WORKS PAGE - FILTER
// ===================================
const filterButtons = document.querySelectorAll('.filter-btn');
const galleryItems = document.querySelectorAll('.gallery-item');

if (filterButtons.length > 0) {
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Get filter value
            const filterValue = button.getAttribute('data-filter');
            
            // Filter gallery items
            galleryItems.forEach(item => {
                const year = item.getAttribute('data-year');
                const series = item.getAttribute('data-series');
                
                if (filterValue === 'all') {
                    item.style.display = 'block';
                } else if (filterValue === year || filterValue === series) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

// ===================================
// WORKS PAGE - LIGHTBOX
// ===================================
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxTitle = document.getElementById('lightbox-title');
const lightboxDetails = document.getElementById('lightbox-details');
const lightboxClose = document.querySelector('.lightbox-close');
const lightboxPrev = document.querySelector('.lightbox-prev');
const lightboxNext = document.querySelector('.lightbox-next');

let currentLightboxIndex = 0;
let visibleGalleryItems = [];

function updateVisibleItems() {
    visibleGalleryItems = Array.from(galleryItems).filter(item => {
        return item.style.display !== 'none';
    });
}

function openLightbox(index) {
    updateVisibleItems();
    currentLightboxIndex = index;
    const item = visibleGalleryItems[currentLightboxIndex];
    const img = item.querySelector('.gallery-image img');
    const title = item.querySelector('.gallery-info h3').textContent;
    const details = item.querySelector('.artwork-details').textContent;
    
    lightboxImg.src = img.src;
    lightboxImg.alt = img.alt;
    lightboxTitle.textContent = title;
    lightboxDetails.textContent = details;
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    lightbox.classList.remove('active');
    document.body.style.overflow = 'auto';
}

function showLightboxSlide(n) {
    updateVisibleItems();
    currentLightboxIndex = (n + visibleGalleryItems.length) % visibleGalleryItems.length;
    const item = visibleGalleryItems[currentLightboxIndex];
    const img = item.querySelector('.gallery-image img');
    const title = item.querySelector('.gallery-info h3').textContent;
    const details = item.querySelector('.artwork-details').textContent;
    
    lightboxImg.src = img.src;
    lightboxImg.alt = img.alt;
    lightboxTitle.textContent = title;
    lightboxDetails.textContent = details;
}

// Add click event to gallery items
galleryItems.forEach((item, index) => {
    item.addEventListener('click', () => {
        updateVisibleItems();
        const visibleIndex = visibleGalleryItems.indexOf(item);
        if (visibleIndex !== -1) {
            openLightbox(visibleIndex);
        }
    });
});

// Lightbox controls
if (lightboxClose) {
    lightboxClose.addEventListener('click', closeLightbox);
}

if (lightboxPrev) {
    lightboxPrev.addEventListener('click', () => showLightboxSlide(currentLightboxIndex - 1));
}

if (lightboxNext) {
    lightboxNext.addEventListener('click', () => showLightboxSlide(currentLightboxIndex + 1));
}

// Close lightbox on background click
if (lightbox) {
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
}

// Keyboard navigation for lightbox
document.addEventListener('keydown', (e) => {
    if (lightbox && lightbox.classList.contains('active')) {
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft') {
            showLightboxSlide(currentLightboxIndex - 1);
        } else if (e.key === 'ArrowRight') {
            showLightboxSlide(currentLightboxIndex + 1);
        }
    }
});

// ===================================
// CONTACT FORM SUBMISSION
// ===================================
const contactForm = document.getElementById('contactForm');
const formSuccess = document.getElementById('formSuccess');

if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // In a real application, you would send the form data to a server
        // For this demo, we'll just show a success message
        
        // Hide form and show success message
        contactForm.style.display = 'none';
        formSuccess.style.display = 'block';
        
        // Reset form after 3 seconds and show it again
        setTimeout(() => {
            contactForm.reset();
            contactForm.style.display = 'flex';
            formSuccess.style.display = 'none';
        }, 5000);
    });
}

// ===================================
// NEWSLETTER FORM SUBMISSION
// ===================================
const newsletterForms = document.querySelectorAll('.newsletter-form, .newsletter-form-large');

newsletterForms.forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const email = form.querySelector('input[type="email"]').value;
        
        // In a real application, you would send the email to a server
        alert(`Thank you for subscribing with: ${email}`);
        
        form.reset();
    });
});

// ===================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ===================================
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

// ===================================
// SCROLL ANIMATIONS (Fade in on scroll)
// ===================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
const animateElements = document.querySelectorAll('.featured-item, .gallery-item, .exhibition-card, .timeline-item');
animateElements.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// ===================================
// NAVBAR SCROLL EFFECT
// ===================================
let lastScroll = 0;
const navbar = document.querySelector('.navbar');

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll <= 0) {
        navbar.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.05)';
    } else {
        navbar.style.boxShadow = '0 2px 30px rgba(0, 0, 0, 0.1)';
    }
    
    lastScroll = currentScroll;
});

// ===================================
// LOADING ANIMATION
// ===================================
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.5s ease';
        document.body.style.opacity = '1';
    }, 100);
});

console.log('Nguyen Thanh Gallery Website - Initialized');

