# 🔧 Fix CMS 404 Errors - Quick Solution

## ⚡ Problem

CMS pages show "Loading artists..." but data never loads. Console shows 404 errors for JSON files.

---

## ✅ Solution (3 Minutes)

### Step 1: Check Files (30 seconds)

Open these URLs directly in your browser:

1. https://gallerynguyenthanh.com/cms/check.php
   - This will show if files exist and are readable
   
2. https://gallerynguyenthanh.com/cms/data/artists.json
   - Should show JSON data, not 404

**If you get 404 on the JSON URL → Files are not uploaded. Go to Step 2.**

---

### Step 2: Upload Missing Files (2 minutes)

**Via FTP/SFTP (Recommended):**

1. Open your FTP client (FileZilla, Cyberduck, etc.)
2. Connect to: `gallerynguyenthanh.com`
3. Navigate to: `/public_html/cms/` (or wherever your site is)
4. Upload the entire `data` folder from your local computer:
   ```
   Local:  /Users/nguyenhai/Documents/Web/test/cms/data/
   Remote: /public_html/cms/data/
   ```
5. Make sure these 3 files are uploaded:
   - `artists.json` (~20 KB)
   - `artworks.json` (~60 KB)
   - `exhibitions.json` (~2 KB)

**Via cPanel File Manager:**

1. Login to cPanel
2. File Manager → public_html → cms
3. Create folder: `data` (if not exists)
4. Upload 3 JSON files into the `data` folder
5. Set permissions: Folder = 755, Files = 644

---

### Step 3: Set Permissions (30 seconds)

**Via SSH:**
```bash
cd /home/yourusername/public_html/cms
chmod 755 data
chmod 644 data/*.json
```

**Via FTP:**
- Right-click `data` folder → Permissions → Set to `755`
- Right-click each `.json` file → Permissions → Set to `644`

**Via cPanel:**
- Select `data` folder → Permissions → Change to `755`
- Select each JSON file → Permissions → Change to `644`

---

### Step 4: Verify (10 seconds)

1. Open: https://gallerynguyenthanh.com/cms/data/artists.json
   - Should see JSON data (starting with `[{"id":"nguyen-thanh"...`)

2. Open: https://gallerynguyenthanh.com/cms/artists.html
   - Should show table with 12 artists

3. If still not working: https://gallerynguyenthanh.com/cms/test-connection.html
   - Run automatic tests

---

## 🎯 What Files You Need on Server

```
gallerynguyenthanh.com/
└── cms/
    ├── index.html              ✓ (Already there)
    ├── dashboard.html          ✓ (Already there)
    ├── artists.html            ✓ (Already there)
    ├── artworks.html           ✓ (Already there)
    ├── exhibitions.html        ✓ (Already there)
    ├── media.html              ✓ (Already there)
    ├── settings.html           ✓ (Already there)
    ├── admin-script.js         ✓ (Already there)
    ├── admin-style.css         ✓ (Already there)
    ├── artists-manager.js      ⚠ (Need to re-upload with fixes)
    ├── artworks-manager.js     ⚠ (Need to re-upload with fixes)
    ├── exhibitions-manager.js  ⚠ (Need to re-upload with fixes)
    ├── .htaccess               ⚠ (Need to re-upload with fixes)
    ├── check.php               ⚠ (NEW - Upload this)
    ├── test-connection.html    ⚠ (NEW - Upload this)
    │
    ├── data/                   ❌ MISSING - UPLOAD THIS FOLDER!
    │   ├── artists.json        ← 12 artists, ~20 KB
    │   ├── artworks.json       ← 118 artworks, ~60 KB
    │   └── exhibitions.json    ← 8 exhibitions, ~2 KB
    │
    └── api/
        ├── save.php            ✓ (Already there)
        └── upload.php          ✓ (Already there)
```

---

## 🚀 Quick Fix Checklist

Do these in order:

- [ ] **1. Upload `data` folder** (with 3 JSON files)
- [ ] **2. Upload updated files** (check.php, test-connection.html, .htaccess, *-manager.js)
- [ ] **3. Set permissions** (755 for folder, 644 for files)
- [ ] **4. Test direct access** to https://gallerynguyenthanh.com/cms/data/artists.json
- [ ] **5. Clear browser cache** (Ctrl+Shift+R or Cmd+Shift+R)
- [ ] **6. Reload CMS page** https://gallerynguyenthanh.com/cms/artists.html

---

## 📦 Files to Re-Upload (with Fixes)

These files have been updated with better error handling:

1. `cms/artists-manager.js` - Shows error messages
2. `cms/artworks-manager.js` - Shows error messages
3. `cms/exhibitions-manager.js` - Shows error messages
4. `cms/admin-script.js` - Better logging
5. `cms/.htaccess` - Allows JSON access
6. `cms/check.php` - **NEW** Server-side health check
7. `cms/test-connection.html` - **NEW** Client-side test

**Re-upload all of these from your local `/Users/nguyenhai/Documents/Web/test/cms/` folder.**

---

## 💡 Why This Happened

The CMS HTML files were uploaded but the **data folder with JSON files** was not uploaded to the server.

- ✅ HTML pages: Uploaded
- ✅ JavaScript: Uploaded
- ✅ CSS: Uploaded
- ❌ **data/artists.json**: Missing
- ❌ **data/artworks.json**: Missing
- ❌ **data/exhibitions.json**: Missing

---

## 🎯 After Upload

Your CMS will show:

- **Artists page:** 12 artists in table
- **Artworks page:** 118 artworks with filters
- **Exhibitions page:** 8 exhibitions sorted by year

---

## 🆘 Still Not Working?

1. **Run health check:**
   - https://gallerynguyenthanh.com/cms/check.php
   
2. **Run connection test:**
   - https://gallerynguyenthanh.com/cms/test-connection.html

3. **Check browser console:**
   - Press F12
   - Look for red error messages
   - Take screenshot and send

4. **Contact:**
   - Email: nguyenthanhgallerie@gmail.com
   - Include: Screenshot of errors + check.php results

---

**⏱️ Estimated fix time: 3 minutes**

Just upload the `data` folder and you're done! 🎉
