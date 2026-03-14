/**
 * Artworks Management Script
 */

let artworksData = [];
let artistsData = [];

// Load data on page load
document.addEventListener('DOMContentLoaded', () => {
    loadArtworks();
    loadArtistsForDropdown();
});

/**
 * Load all artworks
 */
async function loadArtworks() {
    try {
        artworksData = await loadJSON('data/artworks.json');
        artistsData = await loadJSON('data/artists.json');
        renderArtworksTable();
        populateFilters();
    } catch (error) {
        console.error('Error loading artworks:', error);
        showNotification('Error loading artworks', 'error');
    }
}

/**
 * Load artists for dropdown
 */
async function loadArtistsForDropdown() {
    try {
        const artists = await loadJSON('data/artists.json');
        const select = document.getElementById('artworkArtist');
        const filterSelect = document.getElementById('filterArtist');
        
        artists.forEach(artist => {
            const option = document.createElement('option');
            option.value = artist.id;
            option.textContent = artist.nameDisplay;
            select.appendChild(option);
            
            const filterOption = option.cloneNode(true);
            filterSelect.appendChild(filterOption);
        });
    } catch (error) {
        console.error('Error loading artists:', error);
    }
}

/**
 * Populate filter dropdowns
 */
function populateFilters() {
    const years = [...new Set(artworksData.map(a => a.year).filter(Boolean))].sort((a, b) => b - a);
    const yearSelect = document.getElementById('filterYear');
    
    years.forEach(year => {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearSelect.appendChild(option);
    });
}

/**
 * Filter artworks
 */
function filterArtworks() {
    const selectedArtist = document.getElementById('filterArtist').value;
    const selectedYear = document.getElementById('filterYear').value;
    
    let filtered = artworksData;
    
    if (selectedArtist) {
        filtered = filtered.filter(a => a.artistId === selectedArtist);
    }
    
    if (selectedYear) {
        filtered = filtered.filter(a => a.year == selectedYear);
    }
    
    renderArtworksTable(filtered);
}

/**
 * Render artworks table
 */
function renderArtworksTable(data = artworksData) {
    const tbody = document.getElementById('artworksTableBody');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 40px;">No artworks found</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(artwork => {
        const artist = artistsData.find(a => a.id === artwork.artistId);
        return `
            <tr>
                <td>
                    <img src="${artwork.imagePath}" 
                         alt="${artwork.code}" 
                         class="image-preview"
                         onerror="this.src='../images/assets/placeholder.jpg'">
                </td>
                <td><strong>${artwork.code}</strong></td>
                <td>${artwork.title || '-'}</td>
                <td>${artist ? artist.nameDisplay : artwork.artistId}</td>
                <td>${artwork.medium}</td>
                <td>${artwork.size || '-'}</td>
                <td>${artwork.year || '-'}</td>
                <td>
                    <div class="table-actions">
                        <button class="btn-icon" onclick="editArtwork('${artwork.id}')">Edit</button>
                        <button class="btn-icon delete" onclick="deleteArtwork('${artwork.id}')">Delete</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Open add artwork modal
 */
function openAddArtworkModal() {
    document.getElementById('modalTitle').textContent = 'Add New Artwork';
    document.getElementById('artworkForm').reset();
    document.getElementById('artworkId').value = '';
    document.getElementById('artworkAvailable').checked = true;
    showModal('artworkModal');
}

/**
 * Edit artwork
 */
function editArtwork(artworkId) {
    const artwork = artworksData.find(a => a.id === artworkId);
    if (!artwork) return;
    
    document.getElementById('modalTitle').textContent = 'Edit Artwork';
    document.getElementById('artworkId').value = artwork.id;
    document.getElementById('artworkArtist').value = artwork.artistId;
    document.getElementById('artworkCode').value = artwork.code;
    document.getElementById('artworkTitle').value = artwork.title || '';
    document.getElementById('artworkMedium').value = artwork.medium;
    document.getElementById('artworkSize').value = artwork.size || '';
    document.getElementById('artworkYear').value = artwork.year || '';
    document.getElementById('artworkSeries').value = artwork.seriesYear || '';
    document.getElementById('artworkImage').value = artwork.imagePath;
    document.getElementById('artworkAvailable').checked = artwork.available !== false;
    
    showModal('artworkModal');
}

/**
 * Delete artwork
 */
function deleteArtwork(artworkId) {
    const artwork = artworksData.find(a => a.id === artworkId);
    if (!artwork) return;
    
    if (!confirm(`Are you sure you want to delete ${artwork.code}?`)) return;
    
    artworksData = artworksData.filter(a => a.id !== artworkId);
    saveJSON('data/artworks.json', artworksData);
    renderArtworksTable();
    showNotification(`${artwork.code} deleted successfully`);
}

/**
 * Handle form submission
 */
document.getElementById('artworkForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const artworkId = formData.get('id');
    
    const artworkData = {
        id: artworkId || `artwork-${Date.now()}`,
        artistId: formData.get('artistId'),
        code: formData.get('code'),
        title: formData.get('title') || '',
        medium: formData.get('medium'),
        size: formData.get('size') || '',
        year: parseInt(formData.get('year')) || null,
        seriesYear: formData.get('seriesYear') || '',
        imagePath: formData.get('imagePath'),
        available: document.getElementById('artworkAvailable').checked
    };
    
    if (artworkId) {
        // Update existing artwork
        const index = artworksData.findIndex(a => a.id === artworkId);
        if (index !== -1) {
            artworksData[index] = { ...artworksData[index], ...artworkData };
            showNotification('Artwork updated successfully');
        }
    } else {
        // Add new artwork
        artworksData.push(artworkData);
        showNotification('Artwork added successfully');
    }
    
    await saveJSON('data/artworks.json', artworksData);
    renderArtworksTable();
    hideModal('artworkModal');
});
