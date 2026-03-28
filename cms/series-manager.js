/**
 * Series Manager - CRUD operations for artist series/themes
 */

let seriesData = [];
let artistsData = [];

// Initialize
checkAuth();

// Load data on page load
window.addEventListener('DOMContentLoaded', async () => {
    await loadArtists();
    await loadSeries();
});

/**
 * Load all artists for dropdown
 */
async function loadArtists() {
    try {
        artistsData = await loadJSON('data/artists.json');
        
        // Populate artist dropdown
        const select = document.getElementById('artistId');
        if (select) {
            select.innerHTML = '<option value="">Select Artist</option>' +
                artistsData.map(a => `<option value="${a.id}">${a.nameDisplay}</option>`).join('');
        }
    } catch (error) {
        console.error('[Series] Error loading artists:', error);
    }
}

/**
 * Load all series
 */
async function loadSeries() {
    const tbody = document.getElementById('seriesTableBody');
    
    try {
        console.log('[Series] Loading series data...');
        seriesData = await loadJSON('data/series.json');
        console.log('[Series] Loaded', seriesData.length, 'series');
        console.log('[Series] First series:', seriesData[0]);
        renderSeriesTable();
    } catch (error) {
        console.error('[Series] Error loading series:', error);
        console.error('[Series] Error details:', error.message, error.stack);
        
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #d32f2f;">
                        <strong>⚠️ Error loading series data</strong><br>
                        <small style="color: #666; margin-top: 8px; display: block;">${error.message}</small><br>
                        <details style="margin-top: 12px; text-align: left; max-width: 600px; margin-left: auto; margin-right: auto;">
                            <summary style="cursor: pointer; color: #2d5f3f; font-weight: 600;">Show troubleshooting steps</summary>
                            <ul style="margin-top: 8px; font-size: 13px; line-height: 1.6;">
                                <li>Check if <code>data/series.json</code> exists</li>
                                <li>Verify file permissions: should be 644 or 664</li>
                                <li>Check JSON syntax: use <a href="https://jsonlint.com" target="_blank">JSONLint</a></li>
                                <li>Open browser console (F12) for detailed errors</li>
                                <li>Try refreshing the page (Ctrl+F5)</li>
                            </ul>
                        </details>
                    </td>
                </tr>
            `;
        }
    }
}

/**
 * Render series table
 */
function renderSeriesTable() {
    const tbody = document.getElementById('seriesTableBody');
    if (!tbody) return;
    
    if (seriesData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #666;">
                    <strong>No series found</strong><br>
                    <small>Click "+ Add New Series" to create your first series</small>
                </td>
            </tr>
        `;
        return;
    }
    
    // Sort by artist, then year descending
    const sorted = [...seriesData].sort((a, b) => {
        if (a.artistId !== b.artistId) {
            return a.artistId.localeCompare(b.artistId);
        }
        return parseInt(b.year) - parseInt(a.year);
    });
    
    tbody.innerHTML = sorted.map(series => {
        const artist = artistsData.find(a => a.id === series.artistId);
        const artistName = artist ? artist.nameDisplay : series.artistId;
        const statusClass = series.published ? 'status-active' : 'status-inactive';
        const statusText = series.published ? 'Published' : 'Draft';
        
        // Safely escape HTML for attributes
        const safeTitle = (series.title || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        const safeTheme = (series.theme || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        
        return `
            <tr>
                <td>
                    ${series.featuredImage ? 
                        `<img src="../${series.featuredImage}" alt="${safeTitle}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;" onerror="this.style.display='none';this.parentElement.innerHTML='<div style=\\'width:80px;height:60px;background:#f0f0f0;border-radius:4px;\\'></div>'">` 
                        : '<div style="width: 80px; height: 60px; background: #f0f0f0; border-radius: 4px;"></div>'
                    }
                </td>
                <td><strong>${artistName}</strong></td>
                <td><strong>${series.year}</strong></td>
                <td>
                    <strong>${series.title}</strong>
                    ${series.theme ? `<br><small style="color: #666;">${safeTheme}</small>` : ''}
                </td>
                <td>${series.artworkCount || 0} artworks</td>
                <td><span class="status ${statusClass}">${statusText}</span></td>
                <td>
                    <button class="btn-icon" onclick="editSeriesById('${series.id}')" title="Edit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button class="btn-icon btn-danger" onclick="deleteSeries('${series.id}')" title="Delete">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Open add series modal
 */
function openAddSeriesModal() {
    document.getElementById('modalTitle').textContent = 'Add New Series';
    document.getElementById('seriesForm').reset();
    document.getElementById('seriesId').value = '';
    document.getElementById('published').checked = true;
    showModal('seriesModal');
}

/**
 * Edit series by ID (safe for HTML attributes)
 */
function editSeriesById(seriesId) {
    const series = seriesData.find(s => s.id === seriesId);
    if (!series) {
        console.error('Series not found:', seriesId);
        return;
    }
    editSeries(series);
}

/**
 * Edit series (internal function)
 */
function editSeries(series) {
    document.getElementById('modalTitle').textContent = 'Edit Series';
    document.getElementById('seriesId').value = series.id;
    document.getElementById('artistId').value = series.artistId;
    document.getElementById('year').value = series.year;
    document.getElementById('title').value = series.title;
    document.getElementById('theme').value = series.theme || '';
    document.getElementById('slug').value = series.slug;
    document.getElementById('description').value = series.description || '';
    document.getElementById('featuredImage').value = series.featuredImage || '';
    document.getElementById('displayOrder').value = series.displayOrder || 0;
    document.getElementById('published').checked = series.published !== false;
    showModal('seriesModal');
}

/**
 * Delete series
 */
async function deleteSeries(seriesId) {
    if (!confirm('Are you sure you want to delete this series? This action cannot be undone.')) {
        return;
    }
    
    seriesData = seriesData.filter(s => s.id !== seriesId);
    await saveJSON('data/series.json', seriesData);
    showNotification('Series deleted successfully');
    renderSeriesTable();
}

/**
 * Save series (add or update)
 */
document.getElementById('seriesForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    try {
        const seriesId = document.getElementById('seriesId').value;
        const artistId = document.getElementById('artistId').value;
        const year = document.getElementById('year').value.trim();
        const title = document.getElementById('title').value.trim();
        const theme = document.getElementById('theme').value.trim();
        const slug = document.getElementById('slug').value.trim();
        const description = document.getElementById('description').value.trim();
        const featuredImage = document.getElementById('featuredImage').value.trim();
        const displayOrder = parseInt(document.getElementById('displayOrder').value) || 0;
        const published = document.getElementById('published').checked;
        
        // Validation
        if (!artistId) {
            alert('Please select an artist');
            return;
        }
        if (!year || !/^\d{4}$/.test(year)) {
            alert('Please enter a valid 4-digit year');
            return;
        }
        if (!title) {
            alert('Please enter a series title');
            return;
        }
        if (!slug) {
            alert('Please enter a URL slug');
            return;
        }
        
        // Generate ID if new
        const id = seriesId || `${artistId}-${year}`;
        
        // Count artworks for this series
        let artworkCount = 0;
        try {
            const artworks = await loadJSON('data/artworks.json');
            artworkCount = artworks.filter(a => a.artistId === artistId && a.seriesYear === year).length;
            console.log('[Series] Found', artworkCount, 'artworks for', artistId, year);
        } catch (err) {
            console.warn('[Series] Could not count artworks:', err);
        }
        
        const seriesData_item = {
            id,
            artistId,
            year,
            title,
            slug,
            theme: theme || `"${title.toUpperCase()}"`,
            description: description || '',
            featuredImage: featuredImage || '',
            artworkCount,
            published,
            displayOrder,
        };
        
        if (seriesId) {
            // Update existing
            const index = seriesData.findIndex(s => s.id === seriesId);
            if (index !== -1) {
                seriesData[index] = { ...seriesData[index], ...seriesData_item };
                showNotification('Series updated successfully');
                console.log('[Series] Updated:', id);
            }
        } else {
            // Add new
            seriesData.push(seriesData_item);
            showNotification('Series added successfully');
            console.log('[Series] Added:', id);
        }
        
        await saveJSON('data/series.json', seriesData);
        renderSeriesTable();
        hideModal('seriesModal');
        
    } catch (error) {
        console.error('[Series] Save error:', error);
        alert('Error saving series: ' + error.message);
    }
});

/**
 * Auto-generate slug from title
 */
document.getElementById('title').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value || !document.getElementById('seriesId').value) {
        slugInput.value = generateSlug(this.value);
    }
});

/**
 * Auto-generate theme from title
 */
document.getElementById('title').addEventListener('blur', function() {
    const themeInput = document.getElementById('theme');
    if (!themeInput.value) {
        themeInput.value = `"${this.value.toUpperCase()}"`;
    }
});

/**
 * Generate series HTML pages
 */
async function generateSeriesPages() {
    const statusDiv = document.getElementById('generation-status');
    statusDiv.style.display = 'block';
    statusDiv.style.background = '#fff3cd';
    statusDiv.style.borderLeft = '4px solid #ff9800';
    statusDiv.innerHTML = '<strong>⏳ Generating series pages...</strong><p>Please wait...</p>';
    
    try {
        console.log('[Generate Pages] Starting series page generation...');
        
        const response = await fetch('api/generate-series-pages.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        console.log('[Generate Pages] Result:', result);
        
        if (result.success) {
            statusDiv.style.background = '#d4edda';
            statusDiv.style.borderLeft = '4px solid #28a745';
            statusDiv.innerHTML = `
                <strong>✅ Success! Generated ${result.generated} series pages</strong>
                <p>Created pages:</p>
                <ul style="margin: 8px 0; padding-left: 20px;">
                    ${result.pages.map(page => `<li><a href="${page.url}" target="_blank">${page.title} (${page.year})</a></li>`).join('')}
                </ul>
                ${result.errors.length > 0 ? `<p style="color: #d32f2f;">Failed: ${result.errors.join(', ')}</p>` : ''}
                <p style="margin-top: 12px;"><small>Series pages are now live on your website.</small></p>
            `;
            
            showNotification(`Generated ${result.generated} series pages`);
            
            // Auto-hide after 10 seconds
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 10000);
        } else {
            throw new Error(result.message || 'Generation failed');
        }
        
    } catch (error) {
        console.error('[Generate Pages] Error:', error);
        statusDiv.style.background = '#f8d7da';
        statusDiv.style.borderLeft = '4px solid #dc3545';
        statusDiv.innerHTML = `
            <strong>❌ Error generating series pages</strong>
            <p>${error.message}</p>
            <p><small>Make sure:</small></p>
            <ul style="margin: 8px 0; padding-left: 20px; font-size: 13px;">
                <li>PHP is installed and configured</li>
                <li>api/generate-series-pages.php exists</li>
                <li>artists/ folder has write permissions (755)</li>
                <li>data/series.json, artists.json, artworks.json exist</li>
            </ul>
            <p><small>Check browser console (F12) for details.</small></p>
        `;
    }
}

/**
 * Generate slug from text
 */
function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

/**
 * Escape HTML for safe display in attributes
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Safely get text content from HTML string
 */
function stripHtml(html) {
    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
}
