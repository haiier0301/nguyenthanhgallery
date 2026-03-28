/**
 * Nguyen Thanh Gallery CMS
 * Admin Script
 */

const AUTH_TTL_MS = 8 * 60 * 60 * 1000;

document.addEventListener('DOMContentLoaded', () => {
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage === 'index.html' || currentPage === '') {
        initLogin();
    } else {
        checkAuth();
        initDashboard();
    }
});

async function initLogin() {
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    if (!loginForm || !loginError) return;

    if (await verifyServerSession()) {
        window.location.href = 'dashboard.html';
        return;
    }

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('api/auth.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.error || 'Invalid username or password');
            }

            sessionStorage.setItem('cms_authenticated', 'true');
            sessionStorage.setItem('cms_user', result.user || username);
            sessionStorage.setItem('cms_login_time', Date.now().toString());
            window.location.href = 'dashboard.html';
        } catch (error) {
            loginError.textContent = error.message || 'Login failed';
            loginError.style.display = 'block';
            setTimeout(() => {
                loginError.style.display = 'none';
            }, 3000);
        }
    });
}

function isAuthenticated() {
    const authenticated = sessionStorage.getItem('cms_authenticated');
    const loginTime = Number(sessionStorage.getItem('cms_login_time') || 0);
    if (authenticated !== 'true' || loginTime <= 0) return false;
    if ((Date.now() - loginTime) > AUTH_TTL_MS) {
        sessionStorage.clear();
        return false;
    }
    return true;
}

async function verifyServerSession() {
    try {
        const response = await fetch('api/auth.php?action=status');
        if (!response.ok) return false;
        const result = await response.json();
        return Boolean(result.authenticated);
    } catch (error) {
        console.error('Unable to verify server session:', error);
        return false;
    }
}

async function checkAuth() {
    if (!isAuthenticated()) {
        window.location.href = 'index.html';
        return;
    }
    const serverOk = await verifyServerSession();
    if (!serverOk) {
        sessionStorage.clear();
        window.location.href = 'index.html';
    }
}

async function logout() {
    try {
        await fetch('api/auth.php?action=logout', { method: 'POST' });
    } catch (error) {
        console.warn('Server logout failed:', error);
    } finally {
        sessionStorage.clear();
        window.location.href = 'index.html';
    }
}

function initDashboard() {
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) logoutBtn.addEventListener('click', logout);
    loadDashboardStats();
}

async function loadDashboardStats() {
    try {
        const artists = await loadJSONSafe('data/artists.json');
        const artworks = await loadJSONSafe('data/artworks.json');
        const exhibitions = await loadJSONSafe('data/exhibitions.json');
        const artFairs = await loadJSONSafe('data/art-fairs.json');

        const artistCount = document.getElementById('artistCount');
        const artworkCount = document.getElementById('artworkCount');
        const exhibitionCount = document.getElementById('exhibitionCount');
        const artFairCount = document.getElementById('artFairCount');

        if (artistCount) artistCount.textContent = Array.isArray(artists) ? artists.length : 0;
        if (artworkCount) artworkCount.textContent = Array.isArray(artworks) ? artworks.length : 0;
        if (exhibitionCount) exhibitionCount.textContent = Array.isArray(exhibitions) ? exhibitions.length : 0;
        if (artFairCount) artFairCount.textContent = Array.isArray(artFairs) ? artFairs.length : 0;
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

async function loadJSON(path) {
    const response = await fetch(path);
    if (response.status === 401) {
        await handleUnauthorized();
        throw new Error('Unauthorized');
    }
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText} - Failed to load ${path}`);
    }
    return response.json();
}

async function loadJSONSafe(path) {
    try {
        return await loadJSON(path);
    } catch (error) {
        console.warn(`Failed to load ${path}:`, error);
        return [];
    }
}

async function saveJSON(path, data) {
    const fileName = path.split('/').pop();
    const response = await fetch('api/save.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ file: fileName, content: data })
    });
    if (response.status === 401) {
        await handleUnauthorized();
        throw new Error('Session expired');
    }
    const result = await response.json();
    if (!response.ok || !result.success) {
        throw new Error(result.error || 'Failed to save data');
    }

    const key = path.replace('data/', 'cms_');
    localStorage.setItem(key, JSON.stringify(data));
    return true;
}

async function regenerateLegacyPages() {
    try {
        const response = await fetch('api/generate-pages.php?action=generate-all');
        if (response.status === 401) {
            await handleUnauthorized();
            return;
        }
        const result = await response.json();
        if (!response.ok || !result.success) {
            throw new Error(result.error || 'Failed to generate pages');
        }
        showNotification(`Generated ${result.files?.length || 0} page(s) successfully`);
    } catch (error) {
        console.error(error);
        showNotification(error.message || 'Generate pages failed', 'error');
    }
}

async function handleUnauthorized() {
    sessionStorage.clear();
    showNotification('Session expired. Please login again.', 'error');
    setTimeout(() => {
        window.location.href = 'index.html';
    }, 800);
}

function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('active');
    document.body.style.overflow = '';
}

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

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}
