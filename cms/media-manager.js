/**
 * Media Library Manager
 */

let mediaFiles = [];

document.addEventListener('DOMContentLoaded', () => {
    initMediaUpload();
    loadMediaFiles();
});

function initMediaUpload() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.getElementById('uploadBtn');

    if (uploadArea && fileInput) {
        uploadArea.addEventListener('click', (e) => {
            if (e.target.closest('button') || e.target.closest('select') || e.target.closest('input')) {
                return;
            }
            fileInput.click();
        });

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', async (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files || []);
            await handleFiles(files);
        });

        fileInput.addEventListener('change', async (e) => {
            const files = Array.from(e.target.files || []);
            await handleFiles(files);
            fileInput.value = '';
        });
    }

    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', () => fileInput.click());
    }
}

async function loadMediaFiles() {
    try {
        mediaFiles = await loadJSON('data/media.json');
        if (!Array.isArray(mediaFiles)) mediaFiles = [];
    } catch (error) {
        console.warn('media.json missing or invalid, using empty list', error);
        mediaFiles = [];
    }
    renderMediaGrid();
}

async function persistMedia() {
    await saveJSON('data/media.json', mediaFiles);
}

function getUploadFolder() {
    const folderSelect = document.getElementById('uploadFolder');
    const customSubfolder = document.getElementById('uploadSubfolder');
    const folder = folderSelect?.value || 'uploads';
    const subfolderRaw = (customSubfolder?.value || '').trim();
    const sanitizedSubfolder = subfolderRaw.replace(/[^a-zA-Z0-9/_-]/g, '').replace(/\/{2,}/g, '/').replace(/^\/|\/$/g, '');
    return sanitizedSubfolder ? `${folder}/${sanitizedSubfolder}` : folder;
}

async function handleFiles(files) {
    const imageFiles = files.filter((file) => file.type.startsWith('image/'));
    if (imageFiles.length === 0) {
        showNotification('Please select image files only', 'error');
        return;
    }

    const folder = getUploadFolder();
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) uploadArea.style.opacity = '0.7';

    let successCount = 0;
    for (const file of imageFiles) {
        try {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('folder', folder);

            const response = await fetch('api/upload.php', {
                method: 'POST',
                body: formData
            });
            if (response.status === 401) {
                await handleUnauthorized();
                return;
            }
            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.error || 'Upload failed');
            }

            const item = {
                id: `media-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
                name: result.file?.name || file.name,
                path: result.path || result.file?.path || '',
                folder: folder.split('/')[0],
                size: formatFileSize(Number(result.file?.size || file.size || 0)),
                type: result.file?.type || file.type,
                uploadedAt: new Date().toISOString()
            };
            mediaFiles.unshift(item);
            successCount += 1;
        } catch (error) {
            console.error('Upload error:', error);
            showNotification(`${file.name}: ${error.message || 'Upload failed'}`, 'error');
        }
    }

    await persistMedia();
    renderMediaGrid();

    if (uploadArea) uploadArea.style.opacity = '';
    if (successCount > 0) {
        showNotification(`Uploaded ${successCount} image(s) successfully`);
    }
}

function renderMediaGrid(data = mediaFiles) {
    const grid = document.getElementById('mediaGrid');
    if (!grid) return;

    if (!Array.isArray(data) || data.length === 0) {
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--color-text-light);">No images found</div>';
        return;
    }

    grid.innerHTML = data.map((file) => {
        const imageSrc = toCmsImagePath(file.path);
        return `
        <div class="media-item" onclick="showImageDetail('${file.id}')">
            <img src="${imageSrc}" alt="${escapeHtml(file.name || 'media')}" class="media-thumbnail" onerror="this.style.display='none'">
            <div class="media-info">
                <div class="media-name">${escapeHtml(file.name || '')}</div>
                <div class="media-size">${escapeHtml(file.size || '')}</div>
            </div>
        </div>
    `;
    }).join('');
}

function filterMedia() {
    const selectedFolder = document.getElementById('filterFolder')?.value || '';
    if (!selectedFolder) {
        renderMediaGrid(mediaFiles);
        return;
    }
    const filtered = mediaFiles.filter((f) => f.folder === selectedFolder || (f.path || '').includes(`images/${selectedFolder}/`));
    renderMediaGrid(filtered);
}

function showImageDetail(fileId) {
    const file = mediaFiles.find((f) => f.id === fileId);
    if (!file) return;

    document.getElementById('modalImage').src = toCmsImagePath(file.path);
    document.getElementById('modalImagePath').value = file.path || '';
    document.getElementById('modalImageName').textContent = `${file.name || ''} (${file.size || ''})`;
    window.currentImageId = fileId;
    showModal('imageModal');
}

async function copyPath() {
    const path = document.getElementById('modalImagePath').value;
    if (!path) return;
    try {
        await navigator.clipboard.writeText(path);
        showNotification('Path copied to clipboard');
    } catch (error) {
        console.error(error);
        showNotification('Could not copy path', 'error');
    }
}

async function deleteImage() {
    const item = mediaFiles.find((f) => f.id === window.currentImageId);
    if (!item) return;
    if (!confirm('Are you sure you want to delete this image?')) return;

    try {
        const response = await fetch('api/delete-media.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ path: item.path })
        });
        if (response.status === 401) {
            await handleUnauthorized();
            return;
        }
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.error || 'Delete failed');
        }
    } catch (error) {
        console.error(error);
        showNotification(error.message || 'Delete failed', 'error');
        return;
    }

    mediaFiles = mediaFiles.filter((f) => f.id !== window.currentImageId);
    await persistMedia();
    renderMediaGrid();
    hideModal('imageModal');
    showNotification('Image deleted successfully');
}

function toCmsImagePath(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('../')) {
        return path;
    }
    return `../${path.replace(/^\//, '')}`;
}

function formatFileSize(bytes) {
    if (!Number.isFinite(bytes) || bytes <= 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`;
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
