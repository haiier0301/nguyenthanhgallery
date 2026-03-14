/**
 * Media Library Manager
 */

let mediaFiles = [];

document.addEventListener('DOMContentLoaded', () => {
    initMediaUpload();
    loadMediaFiles();
});

/**
 * Initialize media upload
 */
function initMediaUpload() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    
    // Click to upload
    uploadArea.addEventListener('click', () => fileInput.click());
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        handleFiles(files);
    });
}

/**
 * Handle uploaded files
 */
function handleFiles(files) {
    const imageFiles = files.filter(file => file.type.startsWith('image/'));
    
    if (imageFiles.length === 0) {
        showNotification('Please select image files only', 'error');
        return;
    }
    
    imageFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const newFile = {
                id: `img-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`,
                name: file.name,
                path: `../images/uploads/${file.name}`,
                dataUrl: e.target.result,
                size: formatFileSize(file.size),
                type: file.type,
                uploadedAt: new Date().toISOString()
            };
            
            mediaFiles.push(newFile);
            renderMediaGrid();
            showNotification(`${file.name} uploaded successfully (demo)`);
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Load existing media files
 */
function loadMediaFiles() {
    // In production, this would fetch from backend
    // For demo, show existing images from the project
    mediaFiles = [
        {
            id: 'demo-1',
            name: 'Nguyen Thanh - 2002_1.png',
            path: '../images/artists/Nguyen Thanh/2002/2002_1.png',
            folder: 'artists',
            size: '2.5 MB'
        },
        {
            id: 'demo-2',
            name: 'Ngo Dang Hiep_1.jpg',
            path: '../images/artists/Ngo Dang Hiep/Ngo Dang Hiep_1.jpg',
            folder: 'artists',
            size: '261 KB'
        }
    ];
    
    renderMediaGrid();
}

/**
 * Render media grid
 */
function renderMediaGrid(data = mediaFiles) {
    const grid = document.getElementById('mediaGrid');
    
    if (!data || data.length === 0) {
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--color-text-light);">No images found</div>';
        return;
    }
    
    grid.innerHTML = data.map(file => `
        <div class="media-item" onclick="showImageDetail('${file.id}')">
            <img src="${file.dataUrl || file.path}" alt="${file.name}" class="media-thumbnail">
            <div class="media-info">
                <div class="media-name">${file.name}</div>
                <div class="media-size">${file.size}</div>
            </div>
        </div>
    `).join('');
}

/**
 * Filter media
 */
function filterMedia() {
    const selectedFolder = document.getElementById('filterFolder').value;
    
    if (!selectedFolder) {
        renderMediaGrid(mediaFiles);
        return;
    }
    
    const filtered = mediaFiles.filter(f => f.folder === selectedFolder);
    renderMediaGrid(filtered);
}

/**
 * Show image detail modal
 */
function showImageDetail(fileId) {
    const file = mediaFiles.find(f => f.id === fileId);
    if (!file) return;
    
    document.getElementById('modalImage').src = file.dataUrl || file.path;
    document.getElementById('modalImagePath').value = file.path;
    document.getElementById('modalImageName').textContent = file.name;
    
    // Store current file id for actions
    window.currentImageId = fileId;
    
    showModal('imageModal');
}

/**
 * Copy image path
 */
function copyPath() {
    const pathInput = document.getElementById('modalImagePath');
    pathInput.select();
    document.execCommand('copy');
    showNotification('Path copied to clipboard');
}

/**
 * Delete image
 */
function deleteImage() {
    if (!confirm('Are you sure you want to delete this image?')) return;
    
    mediaFiles = mediaFiles.filter(f => f.id !== window.currentImageId);
    renderMediaGrid();
    hideModal('imageModal');
    showNotification('Image deleted successfully');
}

/**
 * Format file size
 */
function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
