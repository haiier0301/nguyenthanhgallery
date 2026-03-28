# Legacy Static Pages Archive

This folder marks the migration from static HTML pages to MVC-rendered routes.

## Migrated to MVC routes

- `/index.html` -> `/`
- `/about.html` -> `/about`
- `/artists.html` -> `/artists`
- `/exhibitions.html` -> `/exhibitions`
- `/art-fairs.html` -> `/art-fairs`
- `/contact.html` -> `/contact`
- `/artists/artist-{slug}.html` -> `/artists/{slug}`
- `/artists/{slug}/{year}.html` -> `/artists/{slug}/{year}`

The site now uses `index.php` + controllers/views as the single rendering source.
Legacy URL compatibility is handled in:

- `/.htaccess` (Apache/Plesk)
- `/router.php` (PHP built-in server)

