/**
 * Artists Management Script
 */

let artistsData = [];

// Load artists on page load
document.addEventListener('DOMContentLoaded', () => {
    loadArtists();
});

/**
 * Load all artists
 */
async function loadArtists() {
    try {
        artistsData = await loadJSON('data/artists.json');
        renderArtistsTable();
    } catch (error) {
        console.error('Error loading artists:', error);
        showNotification('Error loading artists', 'error');
    }
}

/**
 * Render artists table
 */
function renderArtistsTable() {
    const tbody = document.getElementById('artistsTableBody');
    
    if (!artistsData || artistsData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">No artists found</td></tr>';
        return;
    }
    
    tbody.innerHTML = artistsData.map(artist => `
        <tr>
            <td>
                <img src="${artist.thumbnailImage || artist.featuredImage}" 
                     alt="${artist.name}" 
                     class="image-preview"
                     onerror="this.src='../images/assets/placeholder.jpg'">
            </td>
            <td><strong>${artist.nameDisplay}</strong></td>
            <td>${artist.code}</td>
            <td>${artist.born ? new Date(artist.born).getFullYear() : '-'}</td>
            <td>${artist.birthPlace || '-'}</td>
            <td>
                <span style="display: inline-block; padding: 4px 8px; background: ${artist.featured ? '#4caf50' : '#999'}; color: white; border-radius: 3px; font-size: 11px;">
                    ${artist.featured ? 'Featured' : 'Active'}
                </span>
            </td>
            <td>
                <div class="table-actions">
                    <button class="btn-icon" onclick="editArtist('${artist.id}')">Edit</button>
                    <button class="btn-icon" onclick="viewArtist('${artist.id}')">View</button>
                    <button class="btn-icon delete" onclick="deleteArtist('${artist.id}')">Delete</button>
                </div>
            </td>
        </tr>
    `).join('');
}

/**
 * Open add artist modal
 */
function openAddArtistModal() {
    document.getElementById('modalTitle').textContent = 'Add New Artist';
    document.getElementById('artistForm').reset();
    document.getElementById('artistId').value = '';
    showModal('artistModal');
}

/**
 * Edit artist
 */
function editArtist(artistId) {
    const artist = artistsData.find(a => a.id === artistId);
    if (!artist) return;
    
    document.getElementById('modalTitle').textContent = 'Edit Artist';
    document.getElementById('artistId').value = artist.id;
    document.getElementById('artistName').value = artist.name;
    document.getElementById('artistNameDisplay').value = artist.nameDisplay;
    document.getElementById('artistCode').value = artist.code;
    document.getElementById('artistBorn').value = artist.born || '';
    document.getElementById('artistBirthPlace').value = artist.birthPlace || '';
    document.getElementById('artistBio').value = artist.bio || '';
    document.getElementById('artistFeaturedImage').value = artist.featuredImage || '';
    document.getElementById('artistThumbnail').value = artist.thumbnailImage || '';
    document.getElementById('artistFeatured').checked = artist.featured || false;
    document.getElementById('artistHasSeries').checked = artist.hasSeries || false;
    
    showModal('artistModal');
}

/**
 * Delete artist
 */
function deleteArtist(artistId) {
    const artist = artistsData.find(a => a.id === artistId);
    if (!artist) return;
    
    if (!confirm(`Are you sure you want to delete ${artist.name}?`)) return;
    
    artistsData = artistsData.filter(a => a.id !== artistId);
    saveJSON('data/artists.json', artistsData);
    renderArtistsTable();
    showNotification(`${artist.name} deleted successfully`);
}

/**
 * View artist page
 */
function viewArtist(artistId) {
    const artist = artistsData.find(a => a.id === artistId);
    if (!artist) return;
    
    window.open(`../artists/${artist.slug}.html`, '_blank');
}

/**
 * Handle form submission
 */
document.getElementById('artistForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const artistId = formData.get('id');
    
    const artistData = {
        id: artistId || generateSlug(formData.get('name')),
        name: formData.get('name'),
        nameDisplay: formData.get('nameDisplay'),
        code: formData.get('code'),
        slug: artistId ? artistsData.find(a => a.id === artistId).slug : 'artist-' + generateSlug(formData.get('name')),
        born: formData.get('born') || null,
        birthPlace: formData.get('birthPlace') || '',
        bio: formData.get('bio'),
        featuredImage: formData.get('featuredImage') || '',
        thumbnailImage: formData.get('thumbnailImage') || '',
        featured: document.getElementById('artistFeatured').checked,
        hasSeries: document.getElementById('artistHasSeries').checked
    };
    
    if (artistId) {
        // Update existing artist
        const index = artistsData.findIndex(a => a.id === artistId);
        if (index !== -1) {
            artistsData[index] = { ...artistsData[index], ...artistData };
            showNotification('Artist updated successfully');
        }
    } else {
        // Add new artist
        artistsData.push(artistData);
        showNotification('Artist added successfully');
    }
    
    await saveJSON('data/artists.json', artistsData);
    renderArtistsTable();
    hideModal('artistModal');
});

/**
 * Generate slug from text
 */
function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}
