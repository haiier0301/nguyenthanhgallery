# CMS Quick Start Guide

## 🚀 Getting Started in 3 Steps

### Step 1: Login to CMS

1. Open your website
2. Scroll to footer and click the small "CMS" link
   - Or directly go to: `yourwebsite.com/cms/`

3. Login with default credentials:
   - **Username:** `admin`
   - **Password:** `admin123`

### Step 2: Add Your First Artist

1. Click **"Artists"** in the sidebar
2. Click **"+ Add New Artist"** button
3. Fill in the form:
   ```
   Artist Name: Nguyen Van A
   Display Name: NGUYEN VAN A
   Artist Code: NVA
   Birth Date: 1980-01-01
   Birth Place: Hanoi, Vietnam
   Biography: (Paste artist bio here)
   Featured Image: ../images/artists/Nguyen-Van-A/portrait.jpg
   Thumbnail: images/artists/Nguyen-Van-A/thumb.jpg
   ✓ Featured Artist
   ✓ Has Series Pages
   ```
4. Click **"Save Artist"**

### Step 3: Add Artworks

1. Click **"Artworks"** in the sidebar
2. Click **"+ Add New Artwork"** button
3. Fill in the form:
   ```
   Artist: [Select from dropdown]
   Artwork Code: NVA1
   Title: Landscape Scene
   Medium: oil on canvas
   Size: 100x120cm
   Year: 2024
   Image Path: ../../images/artists/Nguyen-Van-A/artwork1.jpg
   ✓ Available
   ```
4. Click **"Save Artwork"**

---

## 📸 Uploading Images

### Method 1: Via Media Library (Recommended)

1. Go to **"Media Library"**
2. Drag & drop images into the upload area
3. Click the uploaded image to copy its path
4. Paste the path into artist/artwork forms

### Method 2: Manual Upload (via FTP)

1. Connect to your server via FTP
2. Upload images to: `/images/artists/[Artist-Name]/`
3. Use the path in CMS forms:
   - For thumbnails: `images/artists/Artist-Name/image.jpg`
   - For featured: `../images/artists/Artist-Name/image.jpg`

---

## 🎨 Managing Content

### Artists Page

**Dashboard → Artists**

- **View:** See all artists with thumbnails
- **Edit:** Click "Edit" button to modify details
- **Delete:** Click "Delete" (with confirmation)
- **View Page:** Click "View" to see public artist page

### Artworks Page

**Dashboard → Artworks**

- **Filter:** By artist or year using dropdowns
- **Add:** Individual artworks with all metadata
- **Edit:** Update details, change images
- **Delete:** Remove unwanted artworks

### Exhibitions Page

**Dashboard → Exhibitions**

- **Add:** Exhibition history entries
- **Types:** Solo, Group, Award, Art Fair
- **Edit/Delete:** Manage existing entries

---

## 🔄 Publishing Changes

### Current Workflow (Static)

1. Edit content in CMS
2. Content saved to JSON files
3. Manually refresh pages to see changes

### Future Dynamic Integration

To make pages load CMS data automatically:

1. Add to your HTML page:
   ```html
   <script src="cms/integration.js"></script>
   ```

2. Call integration function:
   ```html
   <script>
     loadArtistsFromCMS();     // For artists.html
     loadArtworksByCMS('id');  // For artist pages
   </script>
   ```

3. Or use page generator:
   - Navigate to: `cms/api/generate-pages.php?action=generate-all`
   - This regenerates all HTML pages from JSON

---

## ⚙️ Settings

### Change Password

**Dashboard → Settings → Change Password**

1. Enter current password
2. Enter new password
3. Confirm new password
4. Click "Update Password"

### Export Data (Backup)

**Dashboard → Settings → Data Management**

- Click **"Export All Data"**
- Download JSON file
- Store safely

### Import Data (Restore)

1. Click **"Import Data"**
2. Select exported JSON file
3. Confirm import

---

## 📱 Mobile Access

The CMS is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones

Login and manage content anywhere!

---

## ❓ Common Questions

### Q: Can I add custom fields?

Yes, edit the JSON structure in `cms/data/` files and update the forms in CMS HTML files.

### Q: How do I add more users?

Currently supports single admin. For multiple users, you'll need to extend the authentication system.

### Q: Is the data secure?

Data is stored in JSON files. For production:
1. Change default password
2. Enable HTTPS
3. Use .htaccess protection
4. Regular backups

### Q: Can I bulk upload?

Yes, use the Media Library drag & drop for multiple images at once.

### Q: Where are backups stored?

Automatic: `cms/data/backups/`
Manual exports: Downloaded to your device

---

## 🆘 Need Help?

1. Check **README.md** for detailed documentation
2. Check **DEPLOYMENT.md** for server setup
3. Contact: nguyenthanhgallerie@gmail.com

---

## 🎯 Pro Tips

1. **Always backup** before major changes
2. **Use consistent naming** for images (e.g., Artist-Name_1.jpg)
3. **Organize folders** by artist name
4. **Test on mobile** after adding content
5. **Use artist codes** consistently (NT for Nguyen Thanh)
6. **Fill all required fields** for best results
7. **Preview pages** after updates using "View" button

---

## Next Steps

✅ Login to CMS
✅ Add/edit an artist
✅ Add/edit artworks
✅ Upload images
✅ View published pages

**Ready to go!** 🎉
