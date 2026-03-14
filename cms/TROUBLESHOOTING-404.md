# Troubleshooting 404 Errors - CMS

## 🔴 Problem: "Loading artists..." - Data Not Loading

If you see "Loading artists..." that never completes on the CMS pages, this means the JSON data files are not being loaded correctly.

---

## 🔍 Step 1: Run Connection Test

1. Open: `https://gallerynguyenthanh.com/cms/test-connection.html`
2. Wait for automatic tests to run
3. Check which files failed to load
4. Read the error messages and suggested solutions

---

## 🛠️ Step 2: Check If Files Exist on Server

### Via FTP/SFTP:

Navigate to your server and check if these folders/files exist:

```
/public_html/cms/
  └── data/
      ├── artists.json      ← Must exist (12 artists)
      ├── artworks.json     ← Must exist (118 artworks)
      └── exhibitions.json  ← Must exist (8 exhibitions)
```

### Via SSH:

```bash
cd /path/to/your/site/cms
ls -la data/

# Should show:
# artists.json (around 20KB)
# artworks.json (around 60KB)
# exhibitions.json (around 2KB)
```

**If files are missing:** Upload the entire `cms/data/` folder from your local computer to the server.

---

## 🔐 Step 3: Check File Permissions

### Via SSH:

```bash
# Set folder permissions
chmod 755 cms/data/

# Set file permissions
chmod 644 cms/data/*.json

# Verify
ls -la cms/data/
```

**Expected permissions:**
- Folder `data/`: `drwxr-xr-x` (755)
- Files `*.json`: `-rw-r--r--` (644)

---

## 🌐 Step 4: Test Direct JSON Access

Open these URLs directly in your browser:

1. `https://gallerynguyenthanh.com/cms/data/artists.json`
2. `https://gallerynguyenthanh.com/cms/data/artworks.json`
3. `https://gallerynguyenthanh.com/cms/data/exhibitions.json`

### Expected Result:
You should see JSON data displayed in the browser.

### If you get 404:
- Files are not uploaded
- Files are in wrong location
- Check folder names (case-sensitive on Linux servers)

### If you get 403:
- Permission issue
- Run: `chmod 644 cms/data/*.json`

### If browser downloads instead of displays:
- MIME type issue
- Add to `.htaccess`:
  ```apache
  AddType application/json .json
  ```

---

## 🚫 Step 5: Fix CORS Issues (if applicable)

If tests show CORS errors, add to `/cms/.htaccess`:

```apache
# Allow JSON access
<FilesMatch "\.(json)$">
    Header set Access-Control-Allow-Origin "*"
    Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
    Header set Access-Control-Allow-Headers "Content-Type"
</FilesMatch>
```

Then restart Apache or reload configuration.

---

## 📝 Step 6: Validate JSON Files

If files exist but still fail to load, check for syntax errors:

### Online:
1. Copy content of `artists.json`
2. Go to: https://jsonlint.com
3. Paste and validate
4. Fix any syntax errors

### Via Command Line:
```bash
# Test JSON validity
php -r 'json_decode(file_get_contents("cms/data/artists.json"));'
# No output = valid
# Error message = invalid JSON
```

---

## 🔧 Quick Fix Checklist

Run through this checklist:

- [ ] Files uploaded to `/cms/data/` folder?
- [ ] Folder permissions set to 755?
- [ ] File permissions set to 644?
- [ ] Can access JSON directly via URL?
- [ ] Browser console shows specific error?
- [ ] JSON syntax is valid?
- [ ] .htaccess allows JSON access?
- [ ] Server has PHP enabled?

---

## 🎯 Most Common Issues & Solutions

### Issue #1: Files Not Uploaded

**Solution:**
```bash
# Via FTP: Upload the entire cms/data/ folder
# Via SSH:
cd /path/to/website
# Then copy from local or re-upload
```

### Issue #2: Wrong Path

If your site is in a subdirectory, paths might be wrong.

**Example:** Site is at `example.com/gallery/`

Update `artists-manager.js`:
```javascript
// Change from:
artistsData = await loadJSON('data/artists.json');

// To:
artistsData = await loadJSON('/gallery/cms/data/artists.json');
```

### Issue #3: Server Doesn't Serve JSON

Some servers block JSON files by default.

**Solution - Add to .htaccess:**
```apache
<IfModule mod_mime.c>
    AddType application/json .json
</IfModule>

<FilesMatch "\.(json)$">
    Require all granted
</FilesMatch>
```

### Issue #4: Browser Cache

Your browser might be caching old/empty files.

**Solution:**
1. Hard refresh: `Ctrl+Shift+R` (Windows) or `Cmd+Shift+R` (Mac)
2. Clear browser cache
3. Open in incognito/private mode

---

## 🖥️ Testing Locally vs Production

### Local Testing (Working):
```bash
cd cms
php -S localhost:8000
# Open: http://localhost:8000/artists.html
```

### Production (Not Working):
- Upload all files via FTP/SFTP
- Check file permissions
- Test direct JSON URL access
- Check server logs for errors

---

## 📞 Server-Specific Instructions

### cPanel:

1. Login to cPanel
2. Go to **File Manager**
3. Navigate to `public_html/cms/data/`
4. Check if files exist
5. Right-click each file → **Permissions** → Set to `644`
6. Right-click `data` folder → **Permissions** → Set to `755`

### Plesk:

1. Login to Plesk
2. Go to **Files** → **File Manager**
3. Navigate to `httpdocs/cms/data/`
4. Check permissions
5. Upload files if missing

### Direct Admin:

1. Login to Direct Admin
2. Go to **File Manager**
3. Navigate to `public_html/cms/data/`
4. Verify files and permissions

---

## 🧪 Debug Mode

To see detailed error messages, open browser console:

**Chrome/Edge:**
- Press `F12` or `Ctrl+Shift+I`
- Go to **Console** tab

**Firefox:**
- Press `F12`
- Go to **Console** tab

**Safari:**
- Enable Developer Menu: Preferences → Advanced → Show Develop menu
- Press `Cmd+Option+C`

Look for red error messages about:
- 404 errors (file not found)
- CORS errors (access denied)
- JSON parse errors (invalid syntax)

---

## ✅ Verification After Fix

Once you've fixed the issues:

1. Open `cms/test-connection.html` → All tests should pass
2. Open `cms/artists.html` → Should show 12 artists in table
3. Open `cms/artworks.html` → Should show 118 artworks
4. Open `cms/exhibitions.html` → Should show 8 exhibitions

---

## 📧 Still Having Issues?

If none of the above solutions work:

1. **Check server logs:**
   - cPanel: Errors section
   - SSH: `tail -f /var/log/apache2/error.log`

2. **Contact your hosting provider:**
   - Ask if JSON files are blocked
   - Ask if mod_mime is enabled
   - Ask about file access restrictions

3. **Send debug info:**
   - Screenshot of browser console errors
   - Result from test-connection.html
   - Output of: `ls -la cms/data/`

Contact: nguyenthanhgallerie@gmail.com

---

## 🎯 Prevention

To avoid future issues:

1. **Always test locally first:**
   ```bash
   cd cms && php -S localhost:8000
   ```

2. **Use FTP client with permission settings:**
   - FileZilla (free)
   - Cyberduck (free)
   - Transmit (Mac)

3. **Keep backups:**
   - Export data regularly from CMS
   - Keep local copy of all files

4. **Document your setup:**
   - Note your server structure
   - Save SSH/FTP credentials securely
   - Document any special configurations

---

*Last Updated: Feb 10, 2026*
