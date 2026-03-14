/**
 * Nguyen Thanh Gallery CMS
 * Admin Script
 */

// Default admin credentials (should be changed in production)
const ADMIN_CREDENTIALS = {
    username: 'admin',
    password: 'admin123'
};

// Check authentication on page load
document.addEventListener('DOMContentLoaded', () => {
    const currentPage = window.location.pathname.split('/').pop();
    
    if (currentPage === 'index.html' || currentPage === '') {
        initLogin();
    } else {
        checkAuth();
        initDashboard();
    }
});

/**
 * Initialize login form
 */
function initLogin() {
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    
    // Check if already logged in
    if (isAuthenticated()) {
        window.location.href = 'dashboard.html';
        return;
    }
    
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        if (username === ADMIN_CREDENTIALS.username && password === ADMIN_CREDENTIALS.password) {
            // Set authentication
            sessionStorage.setItem('cms_authenticated', 'true');
            sessionStorage.setItem('cms_user', username);
            sessionStorage.setItem('cms_login_time', Date.now());
            
            window.location.href = 'dashboard.html';
        } else {
            loginError.textContent = 'Invalid username or password';
            loginError.style.display = 'block';
            
            setTimeout(() => {
                loginError.style.display = 'none';
            }, 3000);
        }
    });
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    const authenticated = sessionStorage.getItem('cms_authenticated');
    const loginTime = sessionStorage.getItem('cms_login_time');
    
    if (!authenticated || !loginTime) return false;
    
    // Session expires after 8 hours
    const eightHours = 8 * 60 * 60 * 1000;
    if (Date.now() - parseInt(loginTime) > eightHours) {
        sessionStorage.clear();
        return false;
    }
    
    return authenticated === 'true';
}

/**
 * Check authentication for protected pages
 */
function checkAuth() {
    if (!isAuthenticated()) {
        window.location.href = 'index.html';
    }
}

/**
 * Logout function
 */
function logout() {
    sessionStorage.clear();
    window.location.href = 'index.html';
}

/**
 * Initialize dashboard
 */
function initDashboard() {
    // Add logout button handler
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', logout);
    }
    
    // Load dashboard stats
    loadDashboardStats();
}

/**
 * Load dashboard statistics
 */
async function loadDashboardStats() {
    try {
        const artists = await loadJSON('data/artists.json');
        const artworks = await loadJSON('data/artworks.json');
        const exhibitions = await loadJSON('data/exhibitions.json');
        
        // Update stat cards if they exist
        const artistCount = document.getElementById('artistCount');
        const artworkCount = document.getElementById('artworkCount');
        const exhibitionCount = document.getElementById('exhibitionCount');
        
        if (artistCount) artistCount.textContent = artists.length;
        if (artworkCount) artworkCount.textContent = artworks.length;
        if (exhibitionCount) exhibitionCount.textContent = exhibitions.length;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

/**
 * Load JSON data
 */
async function loadJSON(path) {
    try {
        const response = await fetch(path);
        if (!response.ok) throw new Error('Failed to load data');
        return await response.json();
    } catch (error) {
        console.error('Error loading JSON:', error);
        return [];
    }
}

/**
 * Save JSON data
 */
async function saveJSON(path, data) {
    try {
        // Use PHP backend API
        const fileName = path.split('/').pop();
        const response = await fetch('api/save.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                file: fileName,
                content: data
            })
        });
        
        if (!response.ok) {
            throw new Error('Failed to save data');
        }
        
        const result = await response.json();
        console.log('Data saved:', result);
        
        // Also save to localStorage as backup
        const key = path.replace('data/', 'cms_');
        localStorage.setItem(key, JSON.stringify(data));
        
        return true;
    } catch (error) {
        console.error('Error saving data:', error);
        // Fallback to localStorage only
        const key = path.replace('data/', 'cms_');
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    }
}

/**
 * Show modal
 */
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Hide modal
 */
function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Show notification
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 24px;
        right: 24px;
        padding: 16px 24px;
        background: ${type === 'success' ? 'var(--color-success)' : 'var(--color-danger)'};
        color: white;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10000;
        font-size: 14px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

/**
 * Format date
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
