# Nguyen Thanh Gallery CMS

Content Management System for managing artists, artworks, exhibitions, and media.

## Features

- **Artist Management**: Add, edit, and delete artist profiles
- **Artwork Catalog**: Manage artwork details, images, and metadata
- **Exhibition History**: Track exhibitions, awards, and art fairs
- **Media Library**: Upload and organize images
- **User Authentication**: Secure login system
- **Responsive Design**: Works on desktop, tablet, and mobile

## Quick Start

### 1. Setup

The CMS is located in the `/cms` directory. No installation required for basic use.

### 2. Login

Navigate to `/cms/index.html` in your browser.

**Default credentials:**
- Username: `admin`
- Password: `admin123`

**IMPORTANT:** Change these credentials in production!

### 3. Backend (PHP)

For full functionality (data persistence, image uploads), you need a PHP server:

```bash
# Development server
cd cms
php -S localhost:8000
```

Then access: `http://localhost:8000/index.html`

### 4. Data Structure

All data is stored in JSON format in `/cms/data/`:

- `artists.json` - Artist profiles
- `artworks.json` - Artwork catalog
- `exhibitions.json` - Exhibition history

## Usage

### Managing Artists

1. Go to **Artists** page
2. Click **+ Add New Artist**
3. Fill in artist details:
   - Name and display name
   - Artist code (e.g., NT for Nguyen Thanh)
   - Birth date and place
   - Biography
   - Image paths
4. Save

### Managing Artworks

1. Go to **Artworks** page
2. Click **+ Add New Artwork**
3. Fill in artwork details:
   - Select artist
   - Artwork code (e.g., NT1)
   - Medium (e.g., oil on canvas)
   - Size (e.g., 100x120cm)
   - Image path
4. Save

### Media Library

1. Go to **Media Library**
2. Drag & drop images or click to browse
3. Images are uploaded to `/images/uploads/`
4. Click any image to copy its path
5. Use the path in artist/artwork forms

### Generating Pages

The CMS includes a page generator that creates HTML pages from JSON data:

**Manual Generation:**
Navigate to: `/cms/api/generate-pages.php?action=generate-all`

This will generate/update all artist pages based on the JSON data.

## Data Format

### Artists JSON

```json
{
  "id": "nguyen-thanh",
  "name": "Nguyen Thanh",
  "nameDisplay": "NGUYEN THANH",
  "code": "NT",
  "slug": "artist-nguyen-thanh",
  "born": "1976-04-20",
  "birthPlace": "Quảng Bình, Vietnam",
  "bio": "Artist biography...",
  "featuredImage": "../images/artists/...",
  "thumbnailImage": "images/artists/...",
  "featured": true,
  "hasSeries": true
}
```

### Artworks JSON

```json
{
  "id": "nt-2002-1",
  "artistId": "nguyen-thanh",
  "seriesYear": "2002",
  "code": "NT1",
  "title": "My Hometown",
  "medium": "oil on canvas",
  "size": "100x120cm",
  "imagePath": "../../images/artists/...",
  "year": 2002,
  "available": true
}
```

## Security Notes

1. **Change default password** in `admin-script.js` before deployment
2. **Protect the /cms directory** with .htaccess or server configuration
3. **Use HTTPS** in production
4. **Backup data regularly** using the export function

## File Structure

```
cms/
├── index.html              # Login page
├── dashboard.html          # Main dashboard
├── artists.html            # Artist management
├── artworks.html           # Artwork management
├── exhibitions.html        # Exhibition management
├── media.html              # Media library
├── settings.html           # Settings
├── admin-style.css         # CMS styling
├── admin-script.js         # Core CMS functions
├── artists-manager.js      # Artist CRUD
├── artworks-manager.js     # Artwork CRUD
├── exhibitions-manager.js  # Exhibition CRUD
├── media-manager.js        # Media upload
├── data/                   # JSON data storage
│   ├── artists.json
│   ├── artworks.json
│   └── exhibitions.json
└── api/                    # PHP backend
    ├── save.php            # Save data endpoint
    ├── upload.php          # File upload endpoint
    └── generate-pages.php  # Page generator
```

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support (iOS 12+)
- Mobile: Responsive design, touch-optimized

## Support

For issues or questions, contact: nguyenthanhgallerie@gmail.com

## License

Proprietary - Nguyen Thanh Gallery © 2026
