# CMS Deployment Guide

## Prerequisites

- Web server with PHP 7.4+ support
- Apache or Nginx
- File write permissions
- HTTPS certificate (recommended)

---

## Deploy trên Plesk (Hosting Plesk)

Hướng dẫn từng bước deploy CMS lên hosting dùng Plesk.

### Bước 1: Chuẩn bị domain/website trong Plesk

1. Đăng nhập **Plesk** (thường là `https://ip-server:8443` hoặc `https://tên-miền-của-bạn:8443`).
2. Nếu site chính đã có:
   - Dùng **Domains** → chọn domain → **Hosting & DNS**.
   - **Document root** thường là `httpdocs` (hoặc `public_html` tùy host). Ghi nhớ đường dẫn này.
3. Nếu muốn CMS chạy riêng subdomain (vd: `cms.tenwebsite.com`):
   - **Domains** → **Add Subdomain** → nhập subdomain.
   - Đặt document root ví dụ: `httpdocs/cms` hoặc `httpdocs` cho subdomain.

### Bước 2: Upload files CMS lên server

**Cách A – File Manager trong Plesk**

1. **Domains** → chọn domain → **File Manager**.
2. Vào thư mục document root (vd: `httpdocs`).
3. Tạo thư mục `cms` (nếu bạn muốn CMS tại `https://domain.com/cms/`).
4. Upload toàn bộ nội dung thư mục `cms` của bạn vào đây (config, api, data, các file .html, .js, .css, .htaccess…).
5. Đảm bảo cấu trúc giống local, ví dụ:
   ```
   httpdocs/
   ├── cms/
   │   ├── api/
   │   ├── data/
   │   ├── .htaccess
   │   ├── config.php
   │   ├── dashboard.html
   │   └── ...
   ├── images/
   │   └── uploads/
   └── index.html
   ```
6. Nếu website chính nằm ngay trong `httpdocs`, upload thêm `index.html` và thư mục `images` (có `uploads`) lên `httpdocs` như trên.

**Cách B – FTP/SFTP**

1. Trong Plesk: **Domains** → domain → **FTP Access** → tạo hoặc xem tài khoản FTP (user, mật khẩu, host).
2. Dùng FileZilla (hoặc client FTP khác): kết nối bằng host FTP, user và mật khẩu.
3. Upload toàn bộ project vào đúng document root (vd: `httpdocs`) giữ nguyên cấu trúc thư mục `cms/`, `images/`, `index.html` như trên.

**Cách C – Git (nếu host hỗ trợ)**

1. **Domains** → domain → **Git** (nếu có).
2. Thêm repository, chọn branch, set deployment path là document root hoặc `httpdocs/cms`.
3. Deploy; sau đó kiểm tra lại có đủ thư mục `cms`, `images`, `index.html`.

### Bước 3: Chọn phiên bản PHP trong Plesk

1. **Domains** → chọn domain → **PHP Settings** (hoặc **Hosting Settings**).
2. Chọn **PHP version**: 7.4 trở lên (khuyến nghị 8.0 hoặc 8.1).
3. Lưu. Kiểm tra lại bằng cách tạo file `httpdocs/cms/info.php` với nội dung `<?php phpinfo(); ?>`, mở `https://domain.com/cms/info.php` xem PHP version, sau đó **xóa file** `info.php` vì lý do bảo mật.

### Bước 4: Quyền ghi (permissions) cho CMS

CMS cần ghi file trong `cms/data/` và `images/uploads/`.

1. Mở **File Manager** (hoặc FTP), vào đúng document root.
2. Đặt quyền thư mục:
   - `cms/data/` → **755** (hoặc 775 nếu 755 không ghi được).
   - `images/uploads/` → **755** (hoặc 775).
3. Tạo thư mục backup (nếu chưa có): `cms/data/backups/` → **755**.
4. File JSON trong `cms/data/`: **644** (để PHP có thể ghi).
5. Trong Plesk, owner thường là user của domain (vd: `username`); nếu dùng 755 mà vẫn lỗi khi lưu, thử 775 cho các thư mục cần ghi.

### Bước 5: Cấu hình .htaccess (Apache)

- Plesk thường dùng Apache; `.htaccess` đã có trong `cms/`.
- Nếu CMS nằm tại `https://domain.com/cms/`, trong `.htaccess` đã có `RewriteBase /cms/` – giữ nguyên.
- Nếu bạn deploy CMS vào **subdomain** và document root là chính thư mục cms (vd: `httpdocs` của subdomain), sửa trong `cms/.htaccess`:
  - `RewriteBase /cms/` → `RewriteBase /`
- Đảm bảo **AllowOverride** cho thư mục site được bật (mặc định Plesk thường bật). Nếu không, liên hệ nhà cung cấp hosting.

### Bước 6: Bật HTTPS và chuyển hướng HTTP → HTTPS

1. **Domains** → domain → **SSL/TLS Certificates**.
2. Cài đặt certificate (Let’s Encrypt hoặc certificate riêng).
3. Bật **Permanent SEO-safe 301 redirect from HTTP to HTTPS** (hoặc tương đương) để ép dùng HTTPS.
4. (Tùy chọn) Trong `cms/.htaccess`, bỏ comment hai dòng force HTTPS:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### Bước 7: Bảo vệ thư mục CMS (khuyến nghị)

Để chỉ người quản trị mới mở được trang CMS:

1. **Domains** → domain → **Password-Protected Directories** (hoặc **Directory Protection**).
2. Thêm thư mục bảo vệ:
   - Path: `cms` (hoặc đường dẫn tương đối tới thư mục cms trong document root).
   - Tạo user/mật khẩu và áp dụng. Khi truy cập `https://domain.com/cms/` trình duyệt sẽ hỏi user/pass trước khi vào CMS.

Hoặc dùng `.htpasswd` thủ công trong `cms/.htaccess` như đã ghi trong phần Security Configuration bên dưới.

### Bước 8: Cấu hình PHP (upload, thời gian chạy)

1. **Domains** → domain → **PHP Settings**.
2. Chỉnh nếu cần:
   - `upload_max_filesize` = 10M (hoặc lớn hơn nếu cần).
   - `post_max_size` = 10M (≥ upload_max_filesize).
   - `max_execution_time` = 300 (nếu upload file lớn).
3. Lưu.

### Bước 9: Đổi mật khẩu admin CMS

1. Mở file `cms/admin-script.js` trên server (File Manager hoặc FTP).
2. Tìm và sửa:
   ```javascript
   const ADMIN_CREDENTIALS = {
       username: 'tên-đăng-nhập-của-bạn',
       password: 'mật-khẩu-mạnh'
   };
   ```
3. Lưu file.

### Bước 10: Tắt hiển thị lỗi PHP trên production

1. Trong `cms/config.php` sửa:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```
2. Lưu.

### Bước 11: Kiểm tra sau khi deploy

- Truy cập: `https://domain.com/cms/` (hoặc `https://cms.domain.com/` nếu dùng subdomain).
- Đăng nhập bằng user/pass vừa đặt.
- Thử: thêm/sửa artist, artwork, upload ảnh, xem dữ liệu có lưu và hiển thị lại đúng.

**Lỗi thường gặp trên Plesk**

| Triệu chứng | Cách xử lý |
|-------------|------------|
| 403 Forbidden khi vào `/cms/` | Kiểm tra quyền thư mục (755), và Password-Protected Directories có chặn nhầm không. |
| 500 Internal Server Error | Xem log: **Domains** → **Logs** (error_log). Thường do `.htaccess` (syntax) hoặc PHP version. |
| Không lưu được dữ liệu | Kiểm tra quyền 755/775 cho `cms/data/`, 644 cho file .json; owner đúng user domain. |
| Upload ảnh lỗi | Tăng `upload_max_filesize` và `post_max_size` trong PHP Settings; kiểm tra quyền `images/uploads/`. |
| Trang trắng | Bật log trong config tạm thời hoặc xem error_log; kiểm tra PHP version và extension (json, fileinfo…). |

Sau khi chạy ổn định, nhớ xóa `info.php` và tắt `display_errors` trong production.

---

## Local Development

### Option 1: PHP Built-in Server

```bash
cd cms
php -S localhost:8000
```

Access at: `http://localhost:8000/index.html`

### Option 2: XAMPP/MAMP

1. Copy project to `htdocs` folder
2. Start Apache server
3. Access: `http://localhost/test/cms/`

### Option 3: Node.js with php-server

```bash
npm install -g php-server
cd cms
php-server
```

## Production Deployment

### 1. Upload Files

Upload the entire project to your web server via FTP/SFTP:

```
/public_html/
├── cms/              # CMS files
├── images/           # Images
├── index.html        # Website files
└── ...
```

### 2. Set File Permissions

```bash
# CMS data directory (writable)
chmod 755 cms/data/
chmod 644 cms/data/*.json

# Uploads directory (writable)
chmod 755 images/uploads/

# Backup directory (writable)
mkdir cms/data/backups
chmod 755 cms/data/backups/
```

### 3. Configure .htaccess

The `.htaccess` file is already included in `/cms/`. Make sure:

- `mod_rewrite` is enabled
- `AllowOverride All` is set in Apache config

### 4. Security Configuration

**A. Change Admin Password**

Edit `cms/admin-script.js`:

```javascript
const ADMIN_CREDENTIALS = {
    username: 'your-username',
    password: 'your-secure-password'
};
```

**B. Protect CMS Directory**

Add password protection via cPanel or create additional `.htaccess`:

```apache
AuthType Basic
AuthName "CMS Access"
AuthUserFile /path/to/.htpasswd
Require valid-user
```

**C. Enable HTTPS**

Update `.htaccess` to force HTTPS (uncomment lines):

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 5. Update File Paths

If your site is in a subdirectory, update paths in:

- `cms/integration.js` - Update `CMS_DATA_PATH`
- `cms/admin-script.js` - Update relative paths
- PHP files - Update `__DIR__` references if needed

### 6. Test Everything

- [ ] Login works
- [ ] Can add/edit/delete artists
- [ ] Can add/edit/delete artworks
- [ ] Can upload images
- [ ] Data persists after page refresh
- [ ] Mobile responsive
- [ ] HTTPS certificate valid

## Server Requirements

### Minimum Requirements

- PHP 7.4+
- Apache 2.4+ or Nginx
- 100MB disk space
- SSL certificate

### Recommended

- PHP 8.0+
- 500MB disk space
- CDN for images (Cloudflare)
- Daily automated backups

## Backup Strategy

### Automatic Backups

The CMS creates automatic backups when saving data:
- Location: `cms/data/backups/`
- Format: `artists.json.YYYY-MM-DD_HH-mm-ss.bak`
- Retention: Manual cleanup required

### Manual Backup

1. Go to **Settings** in CMS
2. Click **Export All Data**
3. Download JSON file
4. Store securely

### Server Backup

Set up daily cron job:

```bash
#!/bin/bash
# Backup script
DATE=$(date +%Y-%m-%d)
tar -czf ~/backups/gallery-cms-$DATE.tar.gz /path/to/cms/data/
```

## Troubleshooting

### Issue: Cannot save data

**Solution:**
```bash
chmod 755 cms/data/
chmod 644 cms/data/*.json
```

### Issue: Image upload fails

**Solution:**
1. Check PHP upload settings in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```
2. Check directory permissions:
   ```bash
   chmod 755 images/uploads/
   ```

### Issue: Session expires too quickly

**Solution:**
Edit `admin-script.js`:
```javascript
// Change from 8 hours to 24 hours
const twentyFourHours = 24 * 60 * 60 * 1000;
```

### Issue: CORS errors

**Solution:**
Add to PHP files:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
```

## Performance Optimization

### 1. Enable Caching

Uncomment caching rules in `.htaccess`

### 2. Optimize Images

Before uploading, compress images:
- Use tools like TinyPNG, ImageOptim
- Target: < 500KB per image

### 3. Use CDN

Configure Cloudflare or similar CDN for:
- Images
- CSS/JS assets
- Better global performance

### 4. Database Migration (Future)

For large catalogs (1000+ artworks), consider migrating to MySQL:

1. Create database tables
2. Update PHP API to use PDO
3. Import JSON data to database
4. Update queries

## Monitoring

### Check Logs

Activity log: `cms/data/activity.log`

```bash
tail -f cms/data/activity.log
```

### Backup Status

Check backup folder size:

```bash
du -sh cms/data/backups/
```

Clean old backups (keep last 30 days):

```bash
find cms/data/backups/ -mtime +30 -delete
```

## Support

For technical support:
- Email: nguyenthanhgallerie@gmail.com
- Documentation: `/cms/README.md`

## Security Checklist

- [ ] Changed default admin password
- [ ] HTTPS enabled and enforced
- [ ] Directory browsing disabled
- [ ] File permissions set correctly
- [ ] Backup system active
- [ ] `.htaccess` protection enabled
- [ ] PHP error reporting disabled in production
- [ ] Regular security updates applied

## Maintenance Schedule

- **Daily**: Check activity logs
- **Weekly**: Test backup/restore
- **Monthly**: Review and clean old backups
- **Quarterly**: Update PHP and dependencies
