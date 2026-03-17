# 🚀 Laravel MVC Setup Plan for Hosting

## 📋 Hosting Requirements Check

### Minimum Requirements:
- ✅ PHP 8.1 or higher
- ✅ Composer
- ✅ MySQL 5.7+ or PostgreSQL 9.6+
- ✅ Apache/Nginx with mod_rewrite
- ✅ SSL Certificate (for https)
- ✅ SSH access (recommended)

### Server Extensions Required:
```
php-mbstring
php-xml
php-bcmath
php-json
php-openssl
php-pdo
php-tokenizer
php-ctype
php-fileinfo
php-gd (for image processing)
```

---

## 🎯 Setup Strategy

### Option A: Direct Laravel on Hosting (Recommended) ⭐

**If hosting supports:**
- SSH access
- Composer
- PHP 8.1+

**Steps:**
```bash
1. SSH into hosting
2. Install Laravel via Composer
3. Setup database
4. Configure .env
5. Migrate & seed data
6. Point domain to public/
```

### Option B: Upload Pre-built Laravel

**If hosting has limited access:**
- Upload via FTP/SFTP
- Pre-built Laravel folder
- Database setup via cPanel
- Manual configuration

---

## 📦 What We'll Create

```
gallery-website-laravel/
├── app/
│   ├── Models/
│   │   ├── Artist.php
│   │   ├── Artwork.php
│   │   └── Exhibition.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Web/
│   │   │   │   ├── HomeController.php
│   │   │   │   ├── ArtistController.php
│   │   │   │   ├── ExhibitionController.php
│   │   │   │   └── ContactController.php
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── ArtistController.php
│   │   │       ├── ArtworkController.php
│   │   │       └── ExhibitionController.php
│   │   └── Middleware/
│   │       └── AdminAuth.php
│   │
│   └── Services/
│       └── ImageUploadService.php
│
├── database/
│   ├── migrations/
│   │   ├── 2026_03_14_000001_create_artists_table.php
│   │   ├── 2026_03_14_000002_create_artworks_table.php
│   │   └── 2026_03_14_000003_create_exhibitions_table.php
│   └── seeders/
│       └── ImportCurrentDataSeeder.php
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php
│   │   ├── pages/
│   │   │   ├── home.blade.php
│   │   │   ├── about.blade.php
│   │   │   ├── artists/
│   │   │   │   ├── index.blade.php
│   │   │   │   └── show.blade.php
│   │   │   ├── exhibitions.blade.php
│   │   │   ├── contact.blade.php
│   │   │   └── art-fairs.blade.php
│   │   └── admin/
│   │       └── [Filament auto-generates]
│   │
│   └── css/
│       └── app.css (current style.css)
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── public/
│   ├── index.php (Laravel entry point)
│   ├── images/
│   ├── css/
│   └── js/
│
├── storage/
│   └── app/public/uploads/
│
├── .env (configuration)
└── composer.json
```

---

## 🔄 Migration from Current to Laravel

### Step 1: Database Schema
Convert JSON files to MySQL tables:

**artists.json → artists table**
```sql
CREATE TABLE artists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_display VARCHAR(255) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    born DATE NULL,
    birth_place VARCHAR(255) NULL,
    bio TEXT NULL,
    thumbnail_image VARCHAR(255) NULL,
    featured_image VARCHAR(255) NULL,
    featured BOOLEAN DEFAULT 0,
    has_series BOOLEAN DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_slug (slug),
    INDEX idx_featured (featured)
);
```

**artworks.json → artworks table**
```sql
CREATE TABLE artworks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    artist_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(20) NOT NULL,
    title VARCHAR(255) NULL,
    series_year VARCHAR(4) NULL,
    medium VARCHAR(255) NOT NULL,
    size VARCHAR(100) NULL,
    image_path VARCHAR(255) NOT NULL,
    year INT NULL,
    available BOOLEAN DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
    INDEX idx_artist_id (artist_id),
    INDEX idx_series_year (series_year)
);
```

**exhibitions.json → exhibitions table**
```sql
CREATE TABLE exhibitions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('solo', 'group', 'award', 'art-fair') NOT NULL,
    year VARCHAR(4) NOT NULL,
    title VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_type (type),
    INDEX idx_year (year)
);
```

### Step 2: Import Current Data

**From:**
```json
cms/data/artists.json (12 artists)
cms/data/artworks.json (118 artworks)
cms/data/exhibitions.json (8 exhibitions)
```

**To:**
```sql
MySQL database tables
```

**Method:**
```php
// database/seeders/ImportCurrentDataSeeder.php
use App\Models\{Artist, Artwork, Exhibition};

public function run()
{
    // Import artists
    $artistsJson = json_decode(
        file_get_contents(base_path('import/artists.json')),
        true
    );
    
    foreach ($artistsJson as $data) {
        Artist::create([
            'name' => $data['name'],
            'name_display' => $data['nameDisplay'],
            'code' => $data['code'],
            'slug' => $data['slug'],
            'born' => $data['born'] ?? null,
            'birth_place' => $data['birthPlace'] ?? null,
            'bio' => $data['bio'] ?? null,
            'thumbnail_image' => $data['thumbnailImage'] ?? null,
            'featured' => $data['featured'] ?? false,
            'has_series' => $data['hasSeries'] ?? false,
        ]);
    }
    
    // Import artworks...
    // Import exhibitions...
}
```

### Step 3: Convert Templates

**From:**
```html
<!-- artists.html -->
<a href="artists/artist-nguyen-thanh.html">
    NGUYEN THANH
</a>
```

**To:**
```blade
<!-- resources/views/pages/artists/index.blade.php -->
<a href="{{ route('artists.show', $artist->slug) }}">
    {{ $artist->name_display }}
</a>
```

### Step 4: Setup Routes

**From:**
```
Static URLs:
/artists.html
/artists/artist-nguyen-thanh.html
/exhibitions.html
```

**To:**
```php
// routes/web.php
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
Route::get('/artists/{slug}', [ArtistController::class, 'show'])->name('artists.show');
Route::get('/exhibitions', [ExhibitionController::class, 'index'])->name('exhibitions');

// Clean URLs:
/
/artists
/artists/nguyen-thanh
/exhibitions
```

---

## 🎨 Keep Current Design

**Your current CSS/JS stays 100% the same!**

```
resources/
├── css/
│   └── app.css (your current style.css)
├── js/
│   └── app.js (your current script.js)
└── views/
    └── layouts/
        └── app.blade.php (wraps your HTML)
```

**Example layout:**
```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/assets/favicon.png') }}">
    <title>@yield('title', 'Nguyen Thanh Gallery')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @include('partials.navigation')
    
    @yield('content')
    
    @include('partials.footer')
    
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
```

---

## 🔐 Admin Panel with Filament

**Instead of custom CMS, use Filament (auto-generates admin UI):**

```bash
composer require filament/filament:"^3.0"
php artisan filament:install --panels
```

**Create admin resources:**
```bash
php artisan make:filament-resource Artist --generate
php artisan make:filament-resource Artwork --generate
php artisan make:filament-resource Exhibition --generate
```

**Result:**
- `/admin` - Beautiful admin panel
- CRUD for Artists, Artworks, Exhibitions
- File uploads built-in
- Search, filters, bulk actions
- Mobile responsive
- Dark mode

**No need to code CMS from scratch!**

---

## 📤 Deployment to Hosting

### Method 1: Via SSH (Best)

```bash
# 1. SSH to hosting
ssh user@yourdomain.com

# 2. Navigate to web root
cd public_html

# 3. Clone or upload Laravel project
# Option A: Git
git clone your-repo-url gallery
cd gallery

# Option B: Upload via SFTP
# Then navigate to folder

# 4. Install dependencies
composer install --optimize-autoloader --no-dev

# 5. Setup environment
cp .env.example .env
php artisan key:generate

# 6. Configure database in .env
nano .env
# Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 7. Run migrations
php artisan migrate --seed

# 8. Create storage link
php artisan storage:link

# 9. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 10. Point domain to public/
# In cPanel or hosting panel, set document root to:
# /home/user/public_html/gallery/public
```

### Method 2: Via cPanel/FTP

```
1. Build locally or download pre-built Laravel
2. Upload entire folder via FTP/FileZilla
3. Create MySQL database in cPanel
4. Import database (can export from local)
5. Edit .env file via File Manager
6. Set document root to public/
7. Done!
```

---

## 🔧 Hosting Configuration

### Apache (.htaccess in public/)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

---

## 🎯 Timeline

**Total setup time: 1-2 days**

### Day 1: Laravel Setup
- ✅ Install Laravel
- ✅ Create migrations
- ✅ Import current data
- ✅ Test database

### Day 2: Frontend + Admin
- ✅ Convert templates to Blade
- ✅ Setup routes & controllers
- ✅ Install Filament admin
- ✅ Test everything

### Day 3: Deploy (if needed)
- ✅ Upload to hosting
- ✅ Configure server
- ✅ Test live site
- ✅ Setup SSL

---

## ✅ Checklist Before Starting

**Information Needed:**

- [ ] Hosting provider name (e.g., cPanel, Plesk, VPS)
- [ ] PHP version on hosting (run: `php -v`)
- [ ] SSH access available? (Yes/No)
- [ ] Composer available on hosting? (Yes/No)
- [ ] MySQL credentials (host, database, user, password)
- [ ] Domain name
- [ ] Document root path (e.g., /public_html)

**Current Project:**

- [x] 12 artists in JSON
- [x] 118 artworks in JSON
- [x] 8 exhibitions in JSON
- [x] All images in images/ folder
- [x] Current HTML/CSS working

---

## 🚀 Ready to Start?

**Next steps:**
1. Provide hosting details (see checklist above)
2. I'll create Laravel project structure
3. We'll deploy to hosting
4. Test & verify everything works

**Questions?**
- Do you have SSH access to hosting?
- Is Composer installed on hosting?
- What's your hosting provider?
