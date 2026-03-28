# 🚨 URGENT: Fix 404 Error on Server

## Problem:
```
URL: gallerynguyenthanh.com/artists
Error: 404 Page Not Found
```

## Root Cause:
**Server thiếu `.htaccess` file để route requests đến MVC!**

---

## ✅ Solution (2 minutes)

### Step 1: Upload .htaccess

**File location on local:** `/Users/nguyenhai/Documents/Web/test/.htaccess`

**Upload to server:** `/public_html/.htaccess`

**Via cPanel File Manager:**
```
1. Login cPanel: https://gallerynguyenthanh.com/cpanel
2. Click "File Manager"
3. Navigate to public_html/
4. Click "Upload"
5. Select .htaccess from local folder
6. Wait for upload complete
7. Set permissions: 644 (rw-r--r--)
```

**Via FTP (FileZilla):**
```
1. Connect to: ftp.gallerynguyenthanh.com
2. Navigate to: /public_html/
3. Drag .htaccess from local to server
4. Right-click → Permissions → 644
```

### Step 2: Test

**Open browser:**
```
https://gallerynguyenthanh.com/artists
```

**Should work now!** ✅

---

## 🔍 What .htaccess Does

```apache
RewriteEngine On

# Allow static files (images, css, js)
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Route everything else to index.php
RewriteRule ^ index.php [L]
```

**This tells Apache:**
```
1. If file exists (like style.css) → serve it directly
2. If file doesn't exist → send request to index.php
3. index.php runs MVC router
4. Router finds matching controller
5. Controller renders view
6. Done!
```

---

## 📊 Request Flow

### Without .htaccess (Current - 404):
```
Browser → /artists → Server → File not found → 404 ❌
```

### With .htaccess (After upload):
```
Browser → /artists → .htaccess → index.php → Router 
→ ArtistsController → View → HTML → Browser ✅
```

---

## ⚠️ Important: Upload ALL These Files

**Critical files for server:**

```
ROOT DIRECTORY:
├── .htaccess                   ← MUST UPLOAD (fixes 404)
├── index.php                   ← MVC entry point
├── router.php                  ← For dev server
├── 404.html                    ← Error page
├── style.css
├── script.js
│
├── app/
│   ├── Controllers/            ← Upload entire folder
│   ├── Models/                 ← Upload entire folder
│   ├── Views/                  ← Upload entire folder
│   ├── Core/                   ← Upload entire folder
│   └── config.php
│
├── cms/
│   ├── .htaccess              ← For CMS JSON access
│   ├── [all CMS files]
│   └── data/
│       ├── artists.json
│       ├── artworks.json
│       └── exhibitions.json
│
├── images/                     ← All images
└── artists/                    ← Artist folders
```

---

## 🧪 Test After Upload

### Test 1: Check .htaccess Works

**Via browser:**
```
1. https://gallerynguyenthanh.com/
   → Should show homepage ✓

2. https://gallerynguyenthanh.com/artists
   → Should show artists page ✓

3. https://gallerynguyenthanh.com/exhibitions
   → Should show exhibitions ✓
```

### Test 2: Check Server Logs

**Via SSH:**
```bash
# Check Apache error log
tail -f /var/log/apache2/error.log

# Should show no errors
```

### Test 3: Check File Exists

**Via SSH:**
```bash
ls -la /home/user/public_html/.htaccess
```

**Should show:**
```
-rw-r--r-- 1 user user 1234 Mar 28 12:24 .htaccess
```

---

## 🚀 Quick Upload Command (SSH)

**From your local computer:**

```bash
# Upload .htaccess
scp /Users/nguyenhai/Documents/Web/test/.htaccess \
    user@gallerynguyenthanh.com:~/public_html/

# Upload entire MVC structure
scp -r /Users/nguyenhai/Documents/Web/test/app \
    user@gallerynguyenthanh.com:~/public_html/

# Set permissions
ssh user@gallerynguyenthanh.com "chmod 644 ~/public_html/.htaccess"
```

---

## 🔧 Alternative: Update Existing .htaccess

**If .htaccess already exists on server:**

1. **Login cPanel → File Manager**
2. **Navigate to:** `/public_html/.htaccess`
3. **Right-click → Edit**
4. **Replace entire content with:**

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Allow static files
    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]
    
    # Route to index.php
    RewriteRule ^ index.php [L]
</IfModule>

<IfModule !mod_rewrite.c>
    FallbackResource /index.php
</IfModule>

DirectoryIndex index.php
Options -Indexes
```

5. **Save**
6. **Test:** `https://gallerynguyenthanh.com/artists`

---

## ⚡ Fastest Fix

**3 lines to add to .htaccess:**

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```

**Upload this → 404 fixed!**

---

## ✨ Expected Result

**After uploading .htaccess:**

```
✓ gallerynguyenthanh.com/           → Homepage
✓ gallerynguyenthanh.com/artists    → Artists page
✓ gallerynguyenthanh.com/exhibitions → Exhibitions
✓ gallerynguyenthanh.com/contact    → Contact
```

**All clean URLs work!** 🎉

---

## 📞 Still 404?

### Check 1: .htaccess Uploaded?
```
Test: https://gallerynguyenthanh.com/.htaccess
Should: 403 Forbidden or show content
Should NOT: 404 Not Found
```

### Check 2: mod_rewrite Enabled?
```bash
# SSH to server
apache2ctl -M | grep rewrite

# Should show: rewrite_module (shared)
```

### Check 3: index.php Exists?
```
Test: https://gallerynguyenthanh.com/index.php
Should: Show homepage
```

### Check 4: File Permissions?
```bash
ls -la /home/user/public_html/.htaccess
# Should be: -rw-r--r-- (644)
```

---

## 🎯 ACTION NOW

**Upload 1 file to fix:**
```
.htaccess → /public_html/.htaccess
```

**Time: 2 minutes**  
**Result: 404 fixed!** ✅

---

**Ready to upload?** Follow steps above! 🚀
