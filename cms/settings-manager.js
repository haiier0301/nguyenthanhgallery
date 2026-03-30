/**
 * Settings Manager - CMS Settings Page
 * Handles loading, saving, and managing site settings
 */

let currentSettings = null;

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    console.log('[Settings] Initializing...');
    loadSettings();
    attachEventHandlers();
});

/**
 * Load settings from JSON
 */
async function loadSettings() {
    console.log('[Settings] Loading settings...');
    
    try {
        const response = await fetch('../cms/data/settings.json?' + Date.now());
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        currentSettings = await response.json();
        console.log('[Settings] Loaded:', currentSettings);
        
        // Populate form
        populateForm(currentSettings);
        
    } catch (error) {
        console.error('[Settings] Error loading:', error);
        showSettingsNotification('Failed to load settings: ' + error.message, 'error');
        
        // Use default values
        currentSettings = getDefaultSettings();
        populateForm(currentSettings);
    }
}

/**
 * Populate form with settings data
 */
function populateForm(settings) {
    document.getElementById('siteName').value = settings.siteName || '';
    document.getElementById('contactEmail').value = settings.contactEmail || '';
    document.getElementById('contactEmail2').value = settings.contactEmail2 || '';
    document.getElementById('contactPhone1').value = settings.contactPhone1 || '';
    document.getElementById('contactPhone2').value = settings.contactPhone2 || '';
    document.getElementById('contactAddress').value = settings.contactAddress || '';
}

/**
 * Get default settings
 */
function getDefaultSettings() {
    return {
        siteName: 'NGUYEN THANH GALLERIE',
        contactEmail: 'nguyenthanhgallerie@gmail.com',
        contactEmail2: 'tnguyentrangartist78@gmail.com',
        contactPhone1: '+84 (028) 3823 8754',
        contactPhone2: '+84 (0) 919 268 83',
        contactAddress: '139 Dong Khoi Street, Sai Gon Ward, Ho Chi Minh City, Vietnam',
        openingHours: 'Monday – Sunday\n9:00 AM – 7:00 PM',
        socialLinks: {
            email: 'thanhart2000@yahoo.com',
            mapUrl: 'https://www.google.com/maps/search/139+Dong+Khoi+Street+Sai+Gon+Ward+Ho+Chi+Minh+City+Vietnam'
        }
    };
}

/**
 * Attach event handlers
 */
function attachEventHandlers() {
    const settingsForm = document.getElementById('settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', handleSettingsSave);
    }
}

/**
 * Handle settings form save
 */
async function handleSettingsSave(e) {
    e.preventDefault();
    console.log('[Settings] Saving...');
    
    try {
        // Validate inputs
        const siteName = document.getElementById('siteName').value.trim();
        const contactEmail = document.getElementById('contactEmail').value.trim();
        const contactEmail2 = document.getElementById('contactEmail2').value.trim();
        const contactPhone1 = document.getElementById('contactPhone1').value.trim();
        const contactPhone2 = document.getElementById('contactPhone2').value.trim();
        const contactAddress = document.getElementById('contactAddress').value.trim();
        
        if (!siteName) {
            alert('Please enter gallery name');
            return;
        }
        
        if (!contactEmail) {
            alert('Please enter contact email 1');
            return;
        }
        
        if (!contactPhone1) {
            alert('Please enter at least one phone number');
            return;
        }
        
        if (!contactAddress) {
            alert('Please enter address');
            return;
        }
        
        // Build settings object
        const updatedSettings = {
            ...currentSettings,
            siteName,
            contactEmail,
            contactEmail2,
            contactPhone1,
            contactPhone2,
            contactAddress,
            updatedAt: new Date().toISOString()
        };
        
        console.log('[Settings] Saving data:', updatedSettings);
        
        // Save to server
        const response = await fetch('../cms/api/save.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                file: 'settings.json',
                data: updatedSettings
            })
        });
        
        const result = await response.json();
        
        if (!response.ok || result.error) {
            throw new Error(result.error || `HTTP ${response.status}`);
        }
        
        console.log('[Settings] Saved successfully:', result);
        currentSettings = updatedSettings;
        
        showSettingsNotification('Settings saved successfully! ✅', 'success');
        
        // Show additional message about frontend update
        setTimeout(() => {
            alert('Settings saved!\n\n✅ Footer and Contact page will update automatically.\n✅ Refresh your website to see changes.');
        }, 500);
        
    } catch (error) {
        console.error('[Settings] Save error:', error);
        showSettingsNotification('Error saving settings: ' + error.message, 'error');
        alert('Failed to save settings:\n' + error.message);
    }
}

/**
 * Show notification
 * Uses global showNotification from admin-script.js
 */
function showSettingsNotification(message, type = 'success') {
    // admin-script.js already defines window.showNotification
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        console.log(`[Settings] Notification (${type}):`, message);
    }
}
