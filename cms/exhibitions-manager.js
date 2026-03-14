/**
 * Exhibitions Management Script
 */

let exhibitionsData = [];

document.addEventListener('DOMContentLoaded', () => {
    loadExhibitions();
});

/**
 * Load all exhibitions
 */
async function loadExhibitions() {
    try {
        exhibitionsData = await loadJSON('data/exhibitions.json');
        renderExhibitionsTable();
    } catch (error) {
        console.error('Error loading exhibitions:', error);
        showNotification('Error loading exhibitions', 'error');
    }
}

/**
 * Render exhibitions table
 */
function renderExhibitionsTable() {
    const tbody = document.getElementById('exhibitionsTableBody');
    
    if (!exhibitionsData || exhibitionsData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px;">No exhibitions found</td></tr>';
        return;
    }
    
    // Sort by year descending
    const sorted = [...exhibitionsData].sort((a, b) => b.year - a.year);
    
    tbody.innerHTML = sorted.map(exhibition => {
        const typeColors = {
            'solo': '#2196f3',
            'group': '#4caf50',
            'award': '#ff9800',
            'art-fair': '#9c27b0'
        };
        
        return `
            <tr>
                <td><strong>${exhibition.year}</strong></td>
                <td>
                    <span style="display: inline-block; padding: 4px 8px; background: ${typeColors[exhibition.type] || '#999'}; color: white; border-radius: 3px; font-size: 11px; text-transform: capitalize;">
                        ${exhibition.type}
                    </span>
                </td>
                <td>${exhibition.title}</td>
                <td>${exhibition.location || '-'}</td>
                <td>
                    <div class="table-actions">
                        <button class="btn-icon" onclick="editExhibition('${exhibition.id}')">Edit</button>
                        <button class="btn-icon delete" onclick="deleteExhibition('${exhibition.id}')">Delete</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

/**
 * Open add exhibition modal
 */
function openAddExhibitionModal() {
    document.getElementById('modalTitle').textContent = 'Add New Exhibition';
    document.getElementById('exhibitionForm').reset();
    document.getElementById('exhibitionId').value = '';
    showModal('exhibitionModal');
}

/**
 * Edit exhibition
 */
function editExhibition(exhibitionId) {
    const exhibition = exhibitionsData.find(e => e.id === exhibitionId);
    if (!exhibition) return;
    
    document.getElementById('modalTitle').textContent = 'Edit Exhibition';
    document.getElementById('exhibitionId').value = exhibition.id;
    document.getElementById('exhibitionYear').value = exhibition.year;
    document.getElementById('exhibitionType').value = exhibition.type;
    document.getElementById('exhibitionTitle').value = exhibition.title;
    document.getElementById('exhibitionLocation').value = exhibition.location || '';
    document.getElementById('exhibitionDescription').value = exhibition.description || '';
    
    showModal('exhibitionModal');
}

/**
 * Delete exhibition
 */
function deleteExhibition(exhibitionId) {
    const exhibition = exhibitionsData.find(e => e.id === exhibitionId);
    if (!exhibition) return;
    
    if (!confirm(`Are you sure you want to delete this exhibition?`)) return;
    
    exhibitionsData = exhibitionsData.filter(e => e.id !== exhibitionId);
    saveJSON('data/exhibitions.json', exhibitionsData);
    renderExhibitionsTable();
    showNotification('Exhibition deleted successfully');
}

/**
 * Handle form submission
 */
document.getElementById('exhibitionForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const exhibitionId = formData.get('id');
    
    const exhibitionData = {
        id: exhibitionId || `exhibition-${Date.now()}`,
        year: parseInt(formData.get('year')),
        type: formData.get('type'),
        title: formData.get('title'),
        location: formData.get('location') || '',
        description: formData.get('description') || ''
    };
    
    if (exhibitionId) {
        // Update existing exhibition
        const index = exhibitionsData.findIndex(e => e.id === exhibitionId);
        if (index !== -1) {
            exhibitionsData[index] = { ...exhibitionsData[index], ...exhibitionData };
            showNotification('Exhibition updated successfully');
        }
    } else {
        // Add new exhibition
        exhibitionsData.push(exhibitionData);
        showNotification('Exhibition added successfully');
    }
    
    await saveJSON('data/exhibitions.json', exhibitionsData);
    renderExhibitionsTable();
    hideModal('exhibitionModal');
});
