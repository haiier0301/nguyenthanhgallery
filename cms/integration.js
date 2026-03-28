/**
 * CMS Integration Script
 * Include this in front-end pages to load content dynamically from JSON
 * 
 * Usage:
 * <script src="cms/integration.js"></script>
 * <script>
 *   loadArtistsFromCMS();
 *   loadArtworksByCMS('nguyen-thanh', '2002');
 * </script>
 */

const CMS_DATA_PATH = './cms/data/';

/**
 * Load series data from CMS
 */
async function loadSeriesFromCMS(artistId = null) {
    try {
        console.log('[CMS Integration] Loading series...');
        const response = await fetch(CMS_DATA_PATH + 'series.json');
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: Cannot load series.json`);
        }
        
        let series = await response.json();
        console.log('[CMS Integration] Loaded', series.length, 'series');
        
        // Filter by artist if provided
        if (artistId) {
            series = series.filter(s => s.artistId === artistId && (s.published !== false));
        }
        
        // Sort by year descending
        series.sort((a, b) => parseInt(b.year) - parseInt(a.year));
        
        return series;
    } catch (error) {
        console.error('[CMS Integration] Error loading series:', error);
        return [];
    }
}

/**
 * Render artist series list on artist overview page
 */
async function renderArtistSeries(artistId, artistSlug) {
    try {
        const series = await loadSeriesFromCMS(artistId);
        
        if (series.length === 0) {
            console.log('[CMS Integration] No series found for artist:', artistId);
            return;
        }
        
        console.log('[CMS Integration] Rendering', series.length, 'series for', artistId);
        
        // Find or create series container
        let seriesContainer = document.querySelector('.artist-series-list');
        
        if (!seriesContainer) {
            // Create container if doesn't exist
            const bioSection = document.querySelector('.artist-bio');
            if (bioSection) {
                seriesContainer = document.createElement('section');
                seriesContainer.className = 'artist-series-list';
                bioSection.after(seriesContainer);
            } else {
                console.warn('[CMS Integration] No .artist-bio section found');
                return;
            }
        }
        
        // Build series HTML
        const seriesHtml = `
            <div class="series-section">
                <h2>SERIES & THEMES</h2>
                <div class="series-grid">
                    ${series.map(s => `
                        <a href="${artistSlug}/${s.slug}.html" class="series-item">
                            <div class="series-year">${s.year}</div>
                            <div class="series-title">${s.title}</div>
                            <div class="series-theme">${s.theme}</div>
                            <div class="series-count">${s.artworkCount || 0} artworks</div>
                        </a>
                    `).join('')}
                </div>
            </div>
        `;
        
        seriesContainer.innerHTML = seriesHtml;
        console.log('[CMS Integration] Series rendered successfully');
        
    } catch (error) {
        console.error('[CMS Integration] Error rendering series:', error);
    }
}

/**
 * Load artists and render to page
 */
async function loadArtistsFromCMS() {
    try {
        const response = await fetch(CMS_DATA_PATH + 'artists.json');
        const artists = await response.json();
        
        // Render artist list
        const listContainer = document.querySelector('.artist-list-grid');
        if (listContainer) {
            listContainer.innerHTML = artists.map(artist => `
                <a href="artists/${artist.slug}.html" class="artist-name-link">
                    ${artist.nameDisplay}
                </a>
            `).join('');
        }
        
        // Render artist thumbnails
        const thumbContainer = document.querySelector('.artist-thumbnails');
        if (thumbContainer) {
            thumbContainer.innerHTML = artists.map(artist => `
                <a href="artists/${artist.slug}.html" class="artist-thumbnail-item">
                    <img src="${artist.thumbnailImage}" alt="${artist.name}">
                </a>
            `).join('');
        }
        
        return artists;
    } catch (error) {
        console.error('Error loading artists from CMS:', error);
        return [];
    }
}

/**
 * Load artworks for specific artist and series
 */
async function loadArtworksByCMS(artistId, seriesYear = null) {
    try {
        const response = await fetch(CMS_DATA_PATH + 'artworks.json');
        const allArtworks = await response.json();
        
        // Filter by artist
        let artworks = allArtworks.filter(a => a.artistId === artistId);
        
        // Filter by series year if provided
        if (seriesYear) {
            artworks = artworks.filter(a => a.seriesYear === seriesYear);
        }
        
        // Render artworks grid
        const gridContainer = document.querySelector('.artworks-grid');
        if (gridContainer) {
            gridContainer.innerHTML = artworks.map(artwork => {
                const caption = `${artwork.code} - ${artwork.medium}`;
                const size = artwork.size ? `<span class="artwork-size">${artwork.size}</span>` : '';
                
                return `
                    <div class="artwork-item">
                        <img src="${artwork.imagePath}" 
                             alt="${artwork.code}" 
                             class="lightbox-image">
                        <p class="artwork-caption">${caption} ${size}</p>
                    </div>
                `;
            }).join('');
            
            // Reinitialize lightbox for new images
            if (typeof initLightbox === 'function') {
                initLightbox();
            }
        }
        
        return artworks;
    } catch (error) {
        console.error('Error loading artworks from CMS:', error);
        return [];
    }
}

/**
 * Load exhibitions
 */
async function loadExhibitionsFromCMS(type = null) {
    try {
        const response = await fetch(CMS_DATA_PATH + 'exhibitions.json');
        let exhibitions = await response.json();
        
        // Filter by type if provided
        if (type) {
            exhibitions = exhibitions.filter(e => e.type === type);
        }
        
        // Sort by year descending
        exhibitions.sort((a, b) => b.year - a.year);
        
        return exhibitions;
    } catch (error) {
        console.error('Error loading exhibitions from CMS:', error);
        return [];
    }
}

/**
 * Load artist profile
 */
async function loadArtistProfileFromCMS(artistId) {
    try {
        const response = await fetch(CMS_DATA_PATH + 'artists.json');
        const artists = await response.json();
        const artist = artists.find(a => a.id === artistId);
        
        if (!artist) {
            console.warn('Artist not found:', artistId);
            return null;
        }
        
        // Update page content
        const nameElement = document.querySelector('.artist-name-large');
        if (nameElement) {
            nameElement.textContent = artist.nameDisplay;
        }
        
        const bioElement = document.querySelector('.artist-bio');
        if (bioElement) {
            bioElement.innerHTML = artist.bio;
        }
        
        return artist;
    } catch (error) {
        console.error('Error loading artist profile from CMS:', error);
        return null;
    }
}

/**
 * Initialize CMS integration based on page type
 */
function initCMSIntegration() {
    const path = window.location.pathname;
    
    if (path.includes('artists.html') && !path.includes('artist-')) {
        // Artists listing page
        loadArtistsFromCMS();
    } else if (path.includes('artist-')) {
        // Individual artist page
        const artistId = extractArtistIdFromPath(path);
        if (artistId) {
            loadArtistProfileFromCMS(artistId);
            loadArtworksByCMS(artistId);
        }
    }
}

/**
 * Extract artist ID from URL path
 */
function extractArtistIdFromPath(path) {
    const match = path.match(/artist-([a-z-]+)\.html/);
    return match ? match[1] : null;
}

/**
 * Format artwork caption with code and medium
 */
function formatArtworkCaption(code, medium, size = '') {
    const caption = `${code} - ${medium}`;
    return size ? `${caption} <span class="artwork-size">${size}</span>` : caption;
}

/**
 * Check if CMS data is available
 */
async function isCMSDataAvailable() {
    try {
        const response = await fetch(CMS_DATA_PATH + 'artists.json');
        return response.ok;
    } catch {
        return false;
    }
}

// Auto-initialize if on compatible page
// Uncomment to enable auto-loading:
// document.addEventListener('DOMContentLoaded', initCMSIntegration);
