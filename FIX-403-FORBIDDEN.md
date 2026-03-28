# 🔥 FIX 403 FORBIDDEN ERROR

## 🐛 Error You're Seeing

```
URL: gallerynguyenthanh.com/artists/
Error: 403 Forbidden
Message: "You do not have permission to access this document."
```

**Console errors:**
```javascript
TypeError: Cannot read properties of null (reading 'data')
```

---

## 🎯 Root Causes (Most Common)

### 1. **File Permissions Wrong** ⭐ MOST LIKELY

**Problem:**
```
- PHP files set to 600 (owner only)
- Directories set to 700 (owner only)
- Web server can't read files → 403 Forbidden
```

**Fix via cPanel:**
```
1. File Manager → public_html/
2. Select ALL files and folders
3. Right-click → Change Permissions
4. Set:
   - Files: 644 (rw-r--r--)
   - Folders: 755 (rwxr-xr-x)
5. Check: "Recurse into subdirectories"
6. Click: Change Permissions
```

**Fix via SSH:**
```bash
# Navigate to your web root
cd ~/public_html

# Fix all directories
find . -type d -exec chmod 755 {} \;

# Fix all files
find . -type f -exec chmod 644 {} \;

# Verify critical files
chmod 644 .htaccess
chmod 644 index.php
chmod 755 app/
chmod 755 cms/
```

---

### 2. **Missing .htaccess or index.php** ⭐ VERY COMMON

**Problem:**
```
- .htaccess not uploaded → No URL rewriting
- index.php not uploaded → No front controller
- Server returns 403 instead of routing to PHP
```

**Check:**
```
1. cPanel → File Manager → public_html/
2. Look for these files:
   - .htaccess (in root)
   - index.php (in root)
3. If missing → Upload them!
```

**Upload .htaccess:**
```
Local: /Users/nguyenhai/Documents/Web/test/.htaccess
Remote: ~/public_html/.htaccess
Permission: 644
```

**Upload index.php:**
```
Local: /Users/nguyenhai/Documents/Web/test/index.php
Remote: ~/public_html/index.php
Permission: 644
```

---

### 3. **mod_rewrite Disabled**

**Problem:**
```
- Apache mod_rewrite module is disabled
- .htaccess rewrite rules don't work
- Server can't route /artists → index.php
```

**Check:**
```
1. Upload and visit: check-server.php
2. Look for "mod_rewrite" status
3. If DISABLED → Contact hosting provider
```

**Request to hosting:**
```
Hi,

Please enable Apache mod_rewrite for my account at:
gallerynguyenthanh.com

This is required for clean URLs in my application.

Thank you!
```

---

### 4. **.htaccess Not Allowed**

**Problem:**
```
- Server doesn't allow .htaccess overrides
- AllowOverride is set to None
```

**Fix:**
```
Contact hosting provider to set AllowOverride to All
```

---

## 🚀 STEP-BY-STEP FIX GUIDE

### Step 1: Upload Diagnostic Tool

**Upload this file:**
```
Local:  /Users/nguyenhai/Documents/Web/test/check-server.php
Remote: ~/public_html/check-server.php
```

**Via cPanel:**
```
1. Login → File Manager
2. Navigate to: public_html/
3. Click: Upload
4. Select: check-server.php
5. After upload, set permission: 644
```

**Via FTP:**
```
1. Connect to: gallerynguyenthanh.com
2. Navigate to: /public_html/
3. Upload: check-server.php
4. Set permission: 644
```

### Step 2: Run Diagnostic

**Visit:**
```
https://gallerynguyenthanh.com/check-server.php
```

**Look for:**
```
✅ PHP Version 7.4+ (green)
✅ mod_rewrite ENABLED (green)
✅ .htaccess EXISTS (green)
✅ index.php EXISTS (green)
✅ app/ directory EXISTS (green)
```

**If any show ❌ (red):**
- Follow the fix instructions on that page

### Step 3: Fix Permissions (Most Likely Issue)

**Via cPanel File Manager:**
```
1. Login → File Manager → public_html/
2. Select ALL (Ctrl+A or Cmd+A)
3. Right-click → Change Permissions
4. Files: 644
5. Folders: 755
6. ☑ Recurse into subdirectories
7. Click: Change Permissions
8. Wait for completion (may take 1-2 minutes)
```

**Via SSH (Faster):**
```bash
ssh user@gallerynguyenthanh.com
cd ~/public_html
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
echo "Done! All permissions fixed."
```

### Step 4: Verify Upload

**Check these files exist on server:**
```
public_html/
├── .htaccess              ← CRITICAL!
├── index.php              ← CRITICAL!
├── app/
│   ├── config.php
│   ├── Controllers/
│   │   ├── ArtistsController.php  ← For /artists page
│   │   ├── ArtistController.php
│   │   └── ...
│   ├── Models/
│   │   ├── Artist.php
│   │   ├── Artwork.php
│   │   └── Series.php
│   ├── Views/
│   │   ├── artists.php     ← Template for /artists
│   │   └── ...
│   └── Core/
│       └── Router.php
├── cms/
│   ├── data/
│   │   ├── artists.json
│   │   ├── artworks.json
│   │   └── series.json
│   └── api/
│       └── save.php        ← FIXED for series!
└── images/
```

### Step 5: Test URLs

**Try these URLs in browser:**
```
1. https://gallerynguyenthanh.com/
   → Should show homepage

2. https://gallerynguyenthanh.com/artists
   → Should show artists list (was 403 before)

3. https://gallerynguyenthanh.com/artists/nguyen-thanh
   → Should show artist page

4. https://gallerynguyenthanh.com/contact
   → Should show contact page
```

**If still 403:**
- Check Apache error log in cPanel
- Read diagnostic output from check-server.php

---

## 📤 Files to Upload (Complete List)

### CRITICAL (Fix 403):
```bash
.htaccess                          # URL routing rules
index.php                          # Front controller
app/config.php                     # Configuration
app/Core/Router.php                # Router
app/Core/Controller.php            # Base controller
```

### CONTROLLERS (All pages):
```bash
app/Controllers/HomeController.php
app/Controllers/AboutController.php
app/Controllers/ArtistsController.php      # ← For /artists list
app/Controllers/ArtistController.php       # ← For individual artist
app/Controllers/ExhibitionsController.php
app/Controllers/ContactController.php
```

### MODELS (Data access):
```bash
app/Models/Artist.php
app/Models/Artwork.php
app/Models/Series.php              # NEW!
app/Models/Exhibition.php
```

### VIEWS (Templates):
```bash
app/Views/layout.php               # Main layout
app/Views/artists.php              # ← Template for /artists list
app/Views/artist.php               # Individual artist overview
app/Views/artist-series.php        # Series detail page
app/Views/home.php
app/Views/about.php
app/Views/exhibitions.php
app/Views/contact.php
```

### CMS (Already uploaded, but update save.php):
```bash
cms/api/save.php                   # UPDATED - now supports series.json
cms/data/series.json               # NEW!
cms/series.html                    # NEW!
cms/series-manager.js              # NEW!
```

---

## 🔍 Diagnostic Commands

### Check if .htaccess is working:
```
Visit: https://gallerynguyenthanh.com/check-server.php
```

### Check PHP errors:
```
cPanel → Metrics → Errors → Last 24 hours
Look for errors at the time you got 403
```

### Test if mod_rewrite works:
```
1. Upload check-server.php
2. Visit it
3. Check "mod_rewrite" status
4. If disabled → contact hosting
```

---

## 🎯 Most Common Solution

**99% of time, it's file permissions!**

```bash
# Via SSH (fastest):
ssh user@gallerynguyenthanh.com
cd ~/public_html
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
```

**Or via cPanel:**
```
File Manager → Select All → Change Permissions
Files: 644, Folders: 755, Recurse: YES
```

---

## ⚠️ Important Notes

### About .htaccess visibility:
```
- .htaccess starts with a dot (.)
- It's a HIDDEN file
- In cPanel File Manager: Click "Settings" → Check "Show Hidden Files"
- In FTP: Enable "Show hidden files" in settings
```

### About index.php:
```
- Must be in the ROOT of public_html/
- NOT in a subdirectory
- File structure:
  public_html/
  ├── .htaccess
  ├── index.php        ← Here!
  ├── app/
  └── cms/
```

### About app/ directory:
```
- Upload ENTIRE app/ folder with ALL subfolders
- Structure must be preserved:
  app/
  ├── Controllers/
  ├── Models/
  ├── Views/
  └── Core/
```

---

## 🆘 Still Getting 403?

### Check these:

**1. Apache Error Log:**
```
cPanel → Metrics → Errors
Look for the actual error message
```

**2. PHP Error Log:**
```
cPanel → File Manager → public_html/
Look for: error_log file
Read it for PHP errors
```

**3. Contact Hosting:**
```
"I'm getting 403 Forbidden errors when accessing my site.
Please check:
1. Is mod_rewrite enabled?
2. Is AllowOverride set to All?
3. Are there any security rules blocking my app/?
4. Can you check error logs for details?
My domain: gallerynguyenthanh.com"
```

---

## ✅ After Fix

**Test these URLs should all work:**
```
✓ https://gallerynguyenthanh.com/
✓ https://gallerynguyenthanh.com/artists
✓ https://gallerynguyenthanh.com/artists/nguyen-thanh
✓ https://gallerynguyenthanh.com/contact
✓ https://gallerynguyenthanh.com/exhibitions
```

**NO MORE 403! 🎉**

---

## 📱 Next Steps

After fixing 403:
1. ✅ Test all pages work
2. ✅ Test CMS (save series should work after save.php fix)
3. ✅ Generate series pages
4. ✅ Verify series display on front-end

---

**Upload check-server.php now to diagnose the issue!** 🚀
