/**
 * Art Fairs Management Script
 */

let artFairsData = [];

document.addEventListener('DOMContentLoaded', () => {
    loadArtFairs();
});

async function loadArtFairs() {
    try {
        artFairsData = await loadJSON('data/art-fairs.json');
        renderArtFairsTable();
    } catch (error) {
        console.error('Error loading art fairs:', error);
        document.getElementById('artFairsTableBody').innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #f44336;">
                    <strong>Error loading art fairs</strong><br>
                    <small>${error.message}</small>
                </td>
            </tr>
        `;
    }
}

function renderArtFairsTable() {
    const tbody = document.getElementById('artFairsTableBody');
    if (!tbody) return;

    if (!Array.isArray(artFairsData) || artFairsData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 40px;">No art fairs found</td></tr>';
        return;
    }

    const sorted = [...artFairsData].sort((a, b) => Number(b.year || 0) - Number(a.year || 0));
    tbody.innerHTML = sorted.map((item) => {
        const imgSrc = toCmsImagePath(item.imagePath);
        return `
            <tr>
                <td><strong>${item.year || '-'}</strong></td>
                <td>${escapeHtml(item.title || '')}</td>
                <td>${escapeHtml(item.location || '-')}</td>
                <td>
                    ${imgSrc ? `<img src="${imgSrc}" alt="${escapeHtml(item.title || 'Art Fair')}" class="image-preview" onerror="this.style.display='none'">` : '-'}
                </td>
                <td>
                    <div class="table-actions">
                        <button class="btn-icon" onclick="editArtFair('${item.id}')">Edit</button>
                        <button class="btn-icon" onclick="openArtFairLink('${item.id}')">Open Link</button>
                        <button class="btn-icon delete" onclick="deleteArtFair('${item.id}')">Delete</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function openAddArtFairModal() {
    ensureArtFairModal();
    document.getElementById('artFairModalTitle').textContent = 'Add New Art Fair';
    document.getElementById('artFairForm').reset();
    document.getElementById('artFairId').value = '';
    showModal('artFairModal');
}

function editArtFair(id) {
    const item = artFairsData.find((x) => x.id === id);
    if (!item) return;
    ensureArtFairModal();
    document.getElementById('artFairModalTitle').textContent = 'Edit Art Fair';
    document.getElementById('artFairId').value = item.id || '';
    document.getElementById('artFairYear').value = item.year || '';
    document.getElementById('artFairTitle').value = item.title || '';
    document.getElementById('artFairLocation').value = item.location || '';
    document.getElementById('artFairLink').value = item.link || '';
    document.getElementById('artFairImagePath').value = item.imagePath || '';
    document.getElementById('artFairDescription').value = item.description || '';
    showModal('artFairModal');
}

function deleteArtFair(id) {
    const item = artFairsData.find((x) => x.id === id);
    if (!item) return;
    if (!confirm(`Delete art fair "${item.title}"?`)) return;
    artFairsData = artFairsData.filter((x) => x.id !== id);
    saveJSON('data/art-fairs.json', artFairsData);
    renderArtFairsTable();
    showNotification('Art fair deleted successfully');
}

function openArtFairLink(id) {
    const item = artFairsData.find((x) => x.id === id);
    if (!item || !item.link) {
        showNotification('No link available for this record', 'error');
        return;
    }
    window.open(item.link, '_blank');
}

function ensureArtFairModal() {
    if (document.getElementById('artFairModal')) return;

    const modalHtml = `
    <div class="modal" id="artFairModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="artFairModalTitle">Add New Art Fair</h2>
                <button class="modal-close" onclick="hideModal('artFairModal')">&times;</button>
            </div>
            <form id="artFairForm">
                <div class="modal-body">
                    <input type="hidden" id="artFairId" name="id">

                    <div class="form-group">
                        <label for="artFairYear">Year *</label>
                        <input type="number" id="artFairYear" name="year" min="1900" max="2100" required>
                    </div>

                    <div class="form-group">
                        <label for="artFairTitle">Title *</label>
                        <input type="text" id="artFairTitle" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="artFairLocation">Location</label>
                        <input type="text" id="artFairLocation" name="location" placeholder="City, Country">
                    </div>

                    <div class="form-group">
                        <label for="artFairLink">External Link</label>
                        <input type="url" id="artFairLink" name="link" placeholder="https://...">
                    </div>

                    <div class="form-group">
                        <label for="artFairImagePath">Image Path</label>
                        <div style="display:flex; gap:8px;">
                            <input type="text" id="artFairImagePath" name="imagePath" placeholder="images/art-fair/your-image.jpg">
                            <button type="button" class="btn-secondary" id="artFairImageBrowse">Upload</button>
                        </div>
                        <input type="file" id="artFairImageFile" accept="image/*" style="display:none;">
                    </div>

                    <div class="form-group">
                        <label for="artFairDescription">Description</label>
                        <textarea id="artFairDescription" name="description" rows="4"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="hideModal('artFairModal')">Cancel</button>
                    <button type="submit" class="btn-primary">Save Art Fair</button>
                </div>
            </form>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
    bindArtFairForm();
    bindArtFairUpload();
}

function bindArtFairForm() {
    const form = document.getElementById('artFairForm');
    if (!form || form.dataset.bound === 'true') return;
    form.dataset.bound = 'true';

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const id = formData.get('id');
        const payload = {
            id: id || `art-fair-${Date.now()}`,
            year: Number(formData.get('year')) || '',
            title: String(formData.get('title') || '').trim(),
            location: String(formData.get('location') || '').trim(),
            link: String(formData.get('link') || '').trim(),
            imagePath: String(formData.get('imagePath') || '').trim(),
            description: String(formData.get('description') || '').trim()
        };

        if (id) {
            const idx = artFairsData.findIndex((x) => x.id === id);
            if (idx >= 0) artFairsData[idx] = { ...artFairsData[idx], ...payload };
            showNotification('Art fair updated successfully');
        } else {
            artFairsData.push(payload);
            showNotification('Art fair added successfully');
        }

        await saveJSON('data/art-fairs.json', artFairsData);
        renderArtFairsTable();
        hideModal('artFairModal');
    });
}

function bindArtFairUpload() {
    const fileInput = document.getElementById('artFairImageFile');
    const browseBtn = document.getElementById('artFairImageBrowse');
    const targetInput = document.getElementById('artFairImagePath');
    if (!fileInput || !browseBtn || !targetInput) return;

    browseBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', async () => {
        const file = fileInput.files?.[0];
        if (!file) return;
        browseBtn.disabled = true;
        browseBtn.textContent = 'Uploading...';

        try {
            const body = new FormData();
            body.append('file', file);
            body.append('folder', 'art-fair');
            const response = await fetch('api/upload.php', { method: 'POST', body });
            if (response.status === 401) {
                await handleUnauthorized();
                return;
            }
            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.error || 'Failed to upload image');
            }
            targetInput.value = result.path || result.file?.path || '';
            showNotification('Image uploaded successfully');
        } catch (error) {
            console.error(error);
            showNotification(error.message || 'Image upload failed', 'error');
        } finally {
            browseBtn.disabled = false;
            browseBtn.textContent = 'Upload';
            fileInput.value = '';
        }
    });
}

function toCmsImagePath(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('../')) {
        return path;
    }
    return `../${path.replace(/^\//, '')}`;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

