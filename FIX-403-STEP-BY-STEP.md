# 🚀 FIX 403 FORBIDDEN - COMPLETE GUIDE

## 🎯 ROOT CAUSE (100% CONFIRMED)

**Folder vật lý `artists/` đang chặn MVC routing!**

```
Server logic:
1. Request: GET /artists/
2. Apache finds physical folder: /httpdocs/artists/
3. Apache tries to serve it as directory
4. No index.php in artists/ → 403 Forbidden
5. NEVER routes to index.php → MVC controller 🚫
```

**Apache log:**
```
Auto index is disabled for [.../httpdocs/artists/], access denied
```

---

## ✅ SOLUTION: Di chuyển/Xóa folder `artists/`

### 🔥 **Cách 1: Di chuyển vào legacy (RECOMMENDED)**

**Via cPanel File Manager:**
```
1. Login cPanel → File Manager
2. Navigate to: public_html/
3. Find folder: artists/
4. Right-click → Move
5. Destination: public_html/legacy/artists/
6. Click: Move
```

**Via SSH:**
```bash
ssh user@gallerynguyenthanh.com
cd ~/public_html
mv artists legacy/artists
ls -la | grep artists    # Should return nothing
```

### 🔥 **Cách 2: Xóa folder (Nếu không cần backup)**

**Via cPanel:**
```
1. File Manager → public_html/
2. Select: artists/ folder
3. Delete
```

**Via SSH:**
```bash
cd ~/public_html
rm -rf artists
```

---

## 🧪 TEST LOCAL (Đã pass! ✅)

```bash
# Đã test xong:
curl http://localhost:8080/artists
→ 200 OK ✅ (có HTML content)

# Folder đã move:
ls -la /Users/nguyenhai/Documents/Web/test/legacy/
→ artists/ ✅ (đã trong legacy)
```

---

## 📤 DEPLOY TO SERVER

### Step 1: Move/Delete folder `artists/` trên server

**Chọn 1 trong 2 cách trên** (recommend: move vào legacy)

### Step 2: Test URL

**Open browser:**
```
https://gallerynguyenthanh.com/artists
```

**Expected:**
```
✅ Status: 200 OK
✅ Page: Artist list hiển thị
✅ No more 403!
```

### Step 3: Verify other pages

```
✓ https://gallerynguyenthanh.com/
✓ https://gallerynguyenthanh.com/artists
✓ https://gallerynguyenthanh.com/artists/nguyen-thanh
✓ https://gallerynguyenthanh.com/artists/nguyen-thanh/2020
✓ https://gallerynguyenthanh.com/contact
```

---

## 📂 Folder Structure (AFTER FIX)

### ✅ CORRECT (Working):
```
public_html/
├── .htaccess           ← Routes /artists → index.php
├── index.php           ← Front controller
├── app/
│   ├── Controllers/
│   │   └── ArtistsController.php  ← Handles /artists
│   └── ...
├── legacy/
│   └── artists/        ← OLD static files (moved here)
│       └── nguyen-thanh/
│           └── 2020.html
└── cms/
```

### ❌ WRONG (403 Error):
```
public_html/
├── artists/            ← ⚠️ BLOCKS MVC!
│   └── nguyen-thanh/   ← Apache serves this, gets 403
├── index.php
└── app/
```

---

## 🔍 Why This Happened

### Old Architecture (Static HTML):
```
artists/
└── nguyen-thanh/
    ├── 2002.html  → /artists/nguyen-thanh/2002.html
    ├── 2004.html
    └── ...
```

### New Architecture (MVC):
```
URL: /artists
↓
.htaccess → index.php → Router → ArtistsController
                                  ↓
                              artists.php view
```

**Conflict:**
```
Physical folder artists/ exists
→ Apache serves it directly
→ NEVER reaches MVC
→ No index in folder → 403
```

---

## 🆘 Troubleshooting

### If still 403 after moving folder:

**1. Clear browser cache:**
```
Ctrl+Shift+R (Windows)
Cmd+Shift+R (Mac)
```

**2. Check if folder really moved:**
```bash
# Via SSH:
ls ~/public_html/artists
→ Should show: "No such file or directory"
```

**3. Check .htaccess exists:**
```bash
# Via SSH:
ls -la ~/public_html/.htaccess
→ Should show the file
```

**4. Verify file permissions:**
```bash
# Via SSH:
chmod 644 ~/public_html/.htaccess
chmod 644 ~/public_html/index.php
chmod 755 ~/public_html/app
```

**5. Check Apache error log:**
```
cPanel → Metrics → Errors
Look for new errors after moving folder
```

---

## ✅ SUCCESS INDICATORS

**After fix, you should see:**

1. **Browser:** `/artists` loads with artist list
2. **Console:** No more `TypeError: Cannot read properties of null`
3. **Network tab:** `GET /artists` returns 200 OK
4. **Apache log:** No more 403 errors

---

## 🎉 NEXT STEPS

After 403 is fixed:

1. ✅ **Test CMS save** (đã fix `save.php` for series)
2. ✅ **Test series display** on artist pages
3. ✅ **Generate series pages** via CMS button
4. ✅ **Verify everything works**

---

**Upload check-server.php to double-check config!** 🔍

**Move/delete artists/ folder to fix 403!** 🚀
