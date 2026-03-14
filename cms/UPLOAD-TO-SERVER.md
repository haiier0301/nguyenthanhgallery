# 📤 Upload CMS to Server Guide

## ⚡ Quick Upload (5 Minutes)

### What You Need to Upload

From your local folder: `/Users/nguyenhai/Documents/Web/test/cms/`

Upload **everything** to your server at: `/public_html/cms/`

---

## 📋 Upload Checklist

### ✅ Critical Files (MUST UPLOAD):

```
cms/
├── data/                          ← MOST IMPORTANT!
│   ├── artists.json               (19 KB - 12 artists)
│   ├── artworks.json              (33 KB - 118 artworks)
│   └── exhibitions.json           (1.8 KB - 8 exhibitions)
│
├── api/
│   ├── save.php
│   ├── upload.php
│   ├── generate-pages.php
│   └── config.php
│
├── HTML Pages (8 files)
│   ├── index.html
│   ├── dashboard.html
│   ├── artists.html
│   ├── artworks.html
│   ├── exhibitions.html
│   ├── media.html
│   ├── art-fairs.html
│   └── settings.html
│
├── JavaScript (6 files)
│   ├── admin-script.js
│   ├── artists-manager.js
│   ├── artworks-manager.js
│   ├── exhibitions-manager.js
│   ├── media-manager.js
│   └── integration.js
│
├── Styling & Config
│   ├── admin-style.css
│   ├── .htaccess
│   └── config.php
│
└── Diagnostic Tools (NEW)
    ├── check.php                  ← Server-side test
    ├── test-connection.html       ← Client-side test
    ├── diagnostic-tools.html      ← Tool hub
    ├── FIX-404-NOW.md            ← Quick fix guide
    └── TROUBLESHOOTING-404.md    ← Full guide
```

---

## 🚀 Upload Methods

### Method 1: FTP/SFTP (Recommended)

**Using FileZilla:**

1. Open FileZilla
2. Connect to your server:
   - Host: `gallerynguyenthanh.com` or your FTP server
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21 (FTP) or 22 (SFTP)

3. Navigate:
   - **Local site** (left): `/Users/nguyenhai/Documents/Web/test/`
   - **Remote site** (right): `/public_html/`

4. Upload:
   - Drag the entire `cms` folder from left to right
   - Wait for upload to complete (should take 2-3 minutes)

5. Verify:
   - Check if `cms/data/` folder exists on server
   - Check if 3 JSON files are inside

**Using Cyberduck (Mac):**

1. Open Cyberduck
2. Click **Open Connection**
3. Select FTP or SFTP
4. Enter server details
5. Navigate to `/public_html/`
6. Drag `cms` folder from Finder
7. Wait for upload

---

### Method 2: cPanel File Manager

1. **Login to cPanel:**
   - Go to your hosting control panel
   - Find **File Manager**

2. **Navigate:**
   - Go to `public_html`
   - If `cms` folder exists, go inside it
   - If not, create it first

3. **Upload:**
   - Click **Upload** button
   - Select multiple files or zip the `cms` folder first
   - Upload the zip, then **Extract**

4. **Set Permissions:**
   - Select `data` folder → Permissions → `755`
   - Select each `.json` file → Permissions → `644`

---

### Method 3: SSH/SCP (Advanced)

**From your Mac terminal:**

```bash
# Navigate to your project
cd /Users/nguyenhai/Documents/Web/test

# Upload entire cms folder via SCP
scp -r cms/ username@gallerynguyenthanh.com:/path/to/public_html/

# Or use rsync (better, shows progress)
rsync -avz --progress cms/ username@gallerynguyenthanh.com:/path/to/public_html/cms/
```

**Then set permissions:**

```bash
# SSH into server
ssh username@gallerynguyenthanh.com

# Navigate and set permissions
cd /path/to/public_html/cms
chmod 755 data
chmod 644 data/*.json
```

---

## 🔍 After Upload - Verification

### 1. Check Files via URL

Open these in your browser:

- ✅ https://gallerynguyenthanh.com/cms/check.php
- ✅ https://gallerynguyenthanh.com/cms/data/artists.json
- ✅ https://gallerynguyenthanh.com/cms/data/artworks.json
- ✅ https://gallerynguyenthanh.com/cms/data/exhibitions.json

**Expected:**
- `check.php` - Shows "All checks passed!"
- JSON URLs - Display JSON data (not 404)

### 2. Test CMS Pages

- ✅ https://gallerynguyenthanh.com/cms/artists.html → Shows 12 artists
- ✅ https://gallerynguyenthanh.com/cms/artworks.html → Shows 118 artworks
- ✅ https://gallerynguyenthanh.com/cms/exhibitions.html → Shows 8 exhibitions

---

## 📁 Folder Structure on Server

After upload, your server should have:

```
/public_html/
├── index.html              (Your website)
├── artists.html
├── exhibitions.html
├── images/                 (Website images)
│
└── cms/                    ← CMS folder
    ├── index.html
    ├── dashboard.html
    ├── artists.html        (Different from main site)
    ├── artworks.html
    ├── exhibitions.html    (Different from main site)
    ├── media.html
    ├── settings.html
    ├── art-fairs.html
    │
    ├── admin-script.js
    ├── admin-style.css
    ├── artists-manager.js
    ├── artworks-manager.js
    ├── exhibitions-manager.js
    ├── media-manager.js
    ├── integration.js
    │
    ├── .htaccess
    ├── config.php
    ├── check.php           ← NEW
    ├── test-connection.html ← NEW
    ├── diagnostic-tools.html ← NEW
    │
    ├── data/               ← CRITICAL FOLDER
    │   ├── artists.json
    │   ├── artworks.json
    │   └── exhibitions.json
    │
    └── api/
        ├── save.php
        ├── upload.php
        └── generate-pages.php
```

---

## ⚠️ Common Upload Mistakes

### ❌ Mistake #1: Forgot `data` folder
```
cms/
├── index.html ✓
├── artists.html ✓
└── (missing data folder) ✗
```

**Fix:** Upload the `data` folder!

### ❌ Mistake #2: Files in wrong location
```
cms/
└── artists.json ✗ (should be in data/ subfolder)
```

**Fix:** Move to `cms/data/artists.json`

### ❌ Mistake #3: Wrong permissions
```
data/ → 777 ✗ (too permissive)
*.json → 444 ✗ (too restrictive)
```

**Fix:** 
- Folder: `755`
- Files: `644`

---

## 🎯 Upload Priority

If you have slow internet, upload in this order:

**Priority 1 (Critical):**
1. `data/artists.json`
2. `data/artworks.json`
3. `data/exhibitions.json`
4. `admin-script.js`
5. `artists-manager.js`
6. `artworks-manager.js`
7. `exhibitions-manager.js`

**Priority 2 (Important):**
8. All HTML files
9. `admin-style.css`
10. `.htaccess`

**Priority 3 (Optional):**
11. PHP API files
12. Documentation files
13. Diagnostic tools

---

## 📊 File Sizes (for reference)

- `artists.json`: ~19 KB
- `artworks.json`: ~33 KB
- `exhibitions.json`: ~1.8 KB
- Total JSON: ~54 KB

**Total CMS folder:** ~300 KB (with all files)

---

## 🔄 Re-Upload Updated Files

If you previously uploaded but now have updated files with fixes:

**Files to re-upload:**
1. `admin-script.js` (updated error handling)
2. `artists-manager.js` (updated error messages)
3. `artworks-manager.js` (updated error messages)
4. `exhibitions-manager.js` (updated error messages)
5. `.htaccess` (updated JSON access rules)
6. `check.php` (NEW - diagnostic tool)
7. `test-connection.html` (NEW - diagnostic tool)
8. `diagnostic-tools.html` (NEW - tool hub)

**Keep as-is (don't re-upload if working):**
- All HTML pages
- `admin-style.css`
- `data/*.json` (unless you updated the data)

---

## ✅ Success Indicators

After successful upload:

1. **check.php shows:**
   ```
   ✓ All files exist
   ✓ Correct permissions
   ✓ Valid JSON syntax
   ✓ All checks passed
   ```

2. **Artists page shows:**
   - Table with 12 artists
   - Thumbnails visible
   - Edit/Delete buttons work

3. **Artworks page shows:**
   - 118 artworks in table
   - Filters work (by artist/year)
   - Images preview correctly

4. **No console errors:**
   - Open browser console (F12)
   - No red errors
   - Only green success messages

---

## 🆘 Upload Failed?

### Issue: Connection timeout
- **Solution:** Upload in batches (HTML first, then JS, then data)

### Issue: Permission denied
- **Solution:** Contact hosting provider to enable write access

### Issue: Quota exceeded
- **Solution:** Clear old files or upgrade hosting plan

### Issue: Files corrupt after upload
- **Solution:** Use Binary mode in FTP (not ASCII mode)

---

## 📞 Need Help?

1. **Run diagnostics first:**
   - https://gallerynguyenthanh.com/cms/diagnostic-tools.html

2. **Check guides:**
   - `FIX-404-NOW.md` - Quick 3-minute fix
   - `TROUBLESHOOTING-404.md` - Detailed troubleshooting
   - `DEPLOYMENT.md` - Full deployment guide

3. **Contact support:**
   - Email: nguyenthanhgallerie@gmail.com
   - Include: Screenshots of errors + check.php results

---

**⏱️ Total upload time: 3-5 minutes (depending on internet speed)**

Upload ngay để CMS hoạt động! 🚀
