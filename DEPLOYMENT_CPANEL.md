# Panduan Deploy ke cPanel (Production)

Aplikasi ini Laravel 12 + Livewire. Karena cPanel biasanya document root-nya adalah folder akun (`public_html`), sedangkan Laravel butuh document root ke folder `public/`, ikuti salah satu skema di bawah.

## 1. Struktur folder di server

Rekomendasi (paling aman): taruh seluruh project DI LUAR `public_html`, lalu arahkan `public_html` (atau subdomain) ke folder `public/` project via **cPanel > Domains > Document Root**, atau jika tidak bisa ubah document root, gunakan skema symlink berikut:

```
/home/USER/
├── pharm_app/              <- seluruh isi repo (app, routes, vendor, dst) TIDAK boleh diakses publik
│   ├── app/
│   ├── public/
│   ├── .env
│   └── ...
└── public_html/            <- document root default cPanel
    ├── index.php           <- disalin & dimodifikasi dari pharm_app/public/index.php
    ├── .htaccess           <- disalin dari pharm_app/public/.htaccess
    └── build/, images/, favicon.ico, robots.txt  <- disalin/symlink dari pharm_app/public/
```

Jika bisa set document root langsung ke `pharm_app/public`, skip bagian symlink dan langsung pakai isi `public/` apa adanya — ini cara paling bersih.

### Jika TIDAK bisa ubah document root (shared hosting umum)

1. Upload seluruh project ke `/home/USER/pharm_app` (di luar `public_html`), via git clone atau zip upload lalu extract.
2. Copy isi `pharm_app/public/*` ke `public_html/`.
3. Edit `public_html/index.php`, ubah 2 baris require path:

```php
require __DIR__.'/../pharm_app/vendor/autoload.php';
$app = require_once __DIR__.'/../pharm_app/bootstrap/app.php';
```

4. Pastikan `public_html/.htaccess` sama dengan `public/.htaccess` bawaan Laravel (sudah benar di repo ini, lihat `public/.htaccess`).

## 2. Environment (.env)

**Jangan pernah** commit file `.env` — sudah benar diabaikan di `.gitignore`. Buat `.env` baru di server (`pharm_app/.env`) berdasarkan template berikut. Nilai wajib diubah ditandai `# WAJIB`:

```env
APP_NAME="Sistem Farmasi"                 # WAJIB - sesuaikan nama RS/instansi
APP_ENV=production                        # WAJIB - jangan "local"
APP_KEY=                                  # WAJIB - generate via: php artisan key:generate
APP_DEBUG=false                           # WAJIB - JANGAN true di production
APP_URL=https://domain-anda.com           # WAJIB - URL asli

APP_LOCALE=id
APP_FALLBACK_LOCALE=id

LOG_CHANNEL=stack
LOG_LEVEL=error                           # jangan "debug" di production

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cpanelusername_pharmdb        # WAJIB - buat via cPanel > MySQL Databases
DB_USERNAME=cpanelusername_dbuser         # WAJIB
DB_PASSWORD=                              # WAJIB - password kuat, jangan kosong

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true                      # aktifkan di production
SESSION_SECURE_COOKIE=true                # WAJIB jika pakai HTTPS (harus, untuk data medis/keuangan)

QUEUE_CONNECTION=database
CACHE_STORE=database

# Wajib dikonfigurasi agar fitur reset password & notifikasi email berfungsi
# (sebelumnya MAIL_MAILER=log — email tidak benar-benar terkirim)
MAIL_MAILER=smtp                          # WAJIB - ganti dari "log"
MAIL_HOST=mail.domain-anda.com            # WAJIB - host SMTP dari cPanel (Email Accounts)
MAIL_PORT=465
MAIL_USERNAME=noreply@domain-anda.com     # WAJIB
MAIL_PASSWORD=                            # WAJIB
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@domain-anda.com"
MAIL_FROM_NAME="${APP_NAME}"

# Fitur AI (pop-up ringkasan harian, rekomendasi restock, chat assistant).
# Dapatkan API key GRATIS di https://aistudio.google.com/apikey
GEMINI_API_KEY=                           # WAJIB jika ingin fitur AI aktif
GEMINI_MODEL=gemini-2.5-flash
```

cPanel biasanya menyediakan SMTP lokal (`mail.domain-anda.com`, port 465/587) begitu Anda membuat email account di **cPanel > Email Accounts** — pakai itu, bukan Gmail SMTP untuk email transaksional aplikasi.

## 3. Langkah instalasi di server (via SSH atau Terminal cPanel)

```bash
cd /home/USER/pharm_app

composer install --no-dev --optimize-autoloader

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force        # hanya sekali, saat instalasi awal (seeder aman, tidak isi data dummy transaksi)

php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Jika hosting tidak punya akses SSH, gunakan fitur **Terminal** di cPanel (jika tersedia), atau jalankan composer install secara lokal lalu upload folder `vendor/` via zip (jangan upload `vendor/` satu-per-satu file, akan sangat lambat).

## 4. PHP version & extension di cPanel

Di **cPanel > MultiPHP Manager**, set PHP version untuk domain ini ke **PHP 8.2** (sesuai `composer.json`: `"php": "^8.2"`). Aktifkan extension berikut di **MultiPHP INI Editor / PHP Selector**:
`bcmath, ctype, curl, dom, fileinfo, gd, json, mbstring, openssl, pcre, pdo, pdo_mysql, tokenizer, xml, zip`

## 5. Cron job (WAJIB untuk queue & scheduler)

Aplikasi pakai `QUEUE_CONNECTION=database` (import Excel, dsb kemungkinan queued job) dan Laravel scheduler — termasuk generate ringkasan AI harian tiap jam 01:00 (`ai:generate-daily-briefing`). Tambahkan di **cPanel > Cron Jobs**:

```
* * * * * cd /home/USER/pharm_app && php artisan schedule:run >> /dev/null 2>&1
```

Untuk queue worker, karena shared hosting biasanya tidak boleh proses long-running, jalankan queue via scheduler tiap menit sebagai alternatif `queue:work`:

```
* * * * * cd /home/USER/pharm_app && php artisan queue:work --stop-when-empty >> /dev/null 2>&1
```
(Jika hosting mendukung proses persisten/Node/Supervisor, `queue:work --daemon` via Supervisor lebih baik — tanyakan ke provider hosting.)

## 6. Permission folder

```bash
chmod -R 755 storage bootstrap/cache
chown -R USER:USER storage bootstrap/cache
```
`storage/` dan `bootstrap/cache/` harus writable oleh PHP-FPM user cPanel.

## 7. SSL/HTTPS

Aktifkan **AutoSSL / Let's Encrypt** di cPanel untuk domain ini (wajib — aplikasi menangani data resep, stok obat, dan data akuntansi). Setelah aktif, paksa redirect HTTP→HTTPS via `.htaccess` tambahan di document root:

```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```
Taruh di atas blok Laravel yang sudah ada di `.htaccess`.

## 8. Checklist final sebelum go-live

- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] `.env` tidak ter-upload ke public_html / tidak bisa diakses browser (test: buka `https://domain/.env` harus 403/404)
- [ ] SSL aktif, `SESSION_SECURE_COOKIE=true`
- [ ] `MAIL_MAILER=smtp` terisi kredensial asli (fitur reset password baru berfungsi nyata jika ini benar)
- [ ] `php artisan migrate --force` sudah jalan, tabel `settings` sudah ada isi (cek halaman Settings di app, isi identitas RS/instansi yang benar)
- [ ] Role & permission sudah di-assign ke masing-masing user sesuai kebutuhan (lihat hasil kerja otorisasi terbaru)
- [ ] Cron job schedule:run aktif
- [ ] Backup database dijadwalkan (cPanel > Backup Wizard, atau `mysqldump` via cron harian)
- [ ] Test login, reset password, generate PDF (RKO/PO/laporan), dan salah satu alur approval end-to-end di environment production sebelum dibuka ke user asli
