# Split Directory Deployment Guide

## Struktur Direktori Production

Aplikasi SIMACCA menggunakan struktur direktori terpisah untuk keamanan yang lebih baik:

```
/home2/smknbone/
├── simacca_public/          # ← Public web root (document root)
│   ├── index.php            # Entry point
│   ├── connection-test.php  # Testing script
│   ├── .htaccess
│   └── assets/              # CSS, JS, images
│
└── simaccaProject/          # ← Application files (NOT web accessible)
    ├── app/                 # Application logic
    ├── vendor/              # Dependencies
    ├── writable/            # Logs, cache, sessions, uploads
    │   ├── cache/
    │   ├── logs/
    │   ├── session/
    │   └── uploads/
    ├── tests/
    ├── composer.json
    └── .env
```

### Mengapa Struktur Terpisah?

✅ **Keamanan Lebih Baik**: File aplikasi, konfigurasi, dan .env tidak bisa diakses dari web
✅ **Isolasi**: Public files terpisah dari application logic
✅ **Best Practice**: Sesuai dengan standar CodeIgniter 4 dan framework modern

---

## Konfigurasi yang Dibutuhkan

### 1. File: `simacca_public/index.php`

**Line 50** harus mengarah ke simaccaProject:

```php
// LOAD OUR PATHS CONFIG FILE
require FCPATH . '../simaccaProject/app/Config/Paths.php';
```

### 2. File: `simaccaProject/app/Config/Paths.php`

Paths sudah dikonfigurasi dengan benar (relative paths):

```php
public string $systemDirectory = __DIR__ . '/../../vendor/codeigniter4/framework/system';
public string $appDirectory = __DIR__ . '/..';
public string $writableDirectory = __DIR__ . '/../../writable';
public string $testsDirectory = __DIR__ . '/../../tests';
```

### 3. File: `simacca_public/.htaccess`

Pastikan file ini ada dan configured:

```apache
# Disable directory browsing
Options -Indexes

# Rewrite engine
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect to index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>

# Security headers
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

### 4. cPanel Configuration

**Document Root** harus di-set ke: `simacca_public`

Cara setting di cPanel:
1. Login ke cPanel
2. **Domains** → **Domains** (atau **Addon Domains**)
3. Pilih domain `simacca.smkn8bone.sch.id`
4. Set **Document Root** = `simacca_public`
5. Save

---

## Permissions Setup

### Struktur Permission yang Benar

```bash
/home2/smknbone/
├── simacca_public/          # 755
│   ├── index.php            # 644
│   ├── .htaccess            # 644
│   └── assets/              # 755
│
└── simaccaProject/          # 755
    ├── app/                 # 755
    ├── vendor/              # 755
    ├── writable/            # 755 atau 775 ← HARUS WRITABLE!
    │   ├── cache/           # 755 atau 775
    │   ├── logs/            # 755 atau 775
    │   ├── session/         # 755 atau 775
    │   └── uploads/         # 755 atau 775
    ├── .env                 # 600 (SECURITY!)
    └── composer.json        # 644
```

### Cara Set Permissions

#### Option 1: Via cPanel File Manager

1. Login to cPanel
2. File Manager → Navigate to `/home2/smknbone/simaccaProject`
3. Right-click `writable` folder
4. **Change Permissions**
5. Set to `755` (or `775` if needed)
6. ✅ **Check "Recurse into subdirectories"**
7. Click **Change Permissions**

#### Option 2: Via SSH

```bash
# Login via SSH
ssh smknbone@smkn8bone.sch.id

# Navigate to project
cd /home2/smknbone/simaccaProject

# Set writable permissions
chmod -R 755 writable/

# If 755 doesn't work, try 775
chmod -R 775 writable/

# Verify
ls -la writable/
```

#### Option 3: Via FTP

Using FileZilla or similar:
1. Connect to server
2. Navigate to `simaccaProject/writable`
3. Right-click → File Permissions
4. Set Numeric value: `755` or `775`
5. Check "Recurse into subdirectories"
6. Apply

---

## Deployment Checklist

### Pre-Deployment (Development)

- [ ] Test locally dengan struktur yang sama
- [ ] Update `public/index.php` line 50
- [ ] Verify `app/Config/Paths.php` paths
- [ ] Update `public/connection-test.php` paths
- [ ] Check `.env` configuration
- [ ] Test database connection locally

### Upload Files

- [ ] Upload `simaccaProject/` ke `/home2/smknbone/simaccaProject/`
  - `app/`
  - `vendor/`
  - `writable/` (struktur folder, tapi kosongkan isinya)
  - `.env`
  - `composer.json`
  
- [ ] Upload `public/` ke `/home2/smknbone/simacca_public/`
  - `index.php`
  - `.htaccess`
  - `assets/`
  - `connection-test.php` (temporary)

### Post-Upload Configuration

- [ ] Set Document Root di cPanel → `simacca_public`
- [ ] Fix permissions: `writable/` → 755 or 775
- [ ] Update `.env` dengan database production credentials
- [ ] Set `.env` permission to 600
- [ ] Clear cache: delete `writable/cache/*`

### Testing

- [ ] Run `connection-test.php`:
  ```
  https://simacca.smkn8bone.sch.id/connection-test.php
  ```
- [ ] Verify all tests PASS:
  - ✅ database_connect
  - ✅ database_query
  - ✅ database_tables
  - ✅ connection_stability
  - ✅ permissions (writable, session, uploads, logs)
  - ✅ php_config

- [ ] Test aplikasi:
  - ✅ Login page loads
  - ✅ Can login
  - ✅ Session works
  - ✅ Upload file works
  - ✅ No errors in logs

### Security & Cleanup

- [ ] **DELETE** `connection-test.php` setelah testing!
- [ ] Verify `.env` is NOT web-accessible
- [ ] Check error logs untuk issues
- [ ] Test all major features
- [ ] Monitor `writable/logs/` untuk errors

---

## Troubleshooting HTTP 500 Error

### Kemungkinan Penyebab & Solusi

#### 1. Path Configuration Salah

**Gejala:** HTTP 500 Error saat akses website

**Solusi:**
```php
// public/index.php line 50 HARUS:
require FCPATH . '../simaccaProject/app/Config/Paths.php';

// BUKAN:
require FCPATH . '../app/Config/Paths.php';
```

#### 2. Writable Directory Tidak Writable

**Gejala:** HTTP 500, error di log: "Unable to write to session/logs"

**Solusi:**
```bash
chmod -R 775 simaccaProject/writable/
```

#### 3. Vendor Directory Missing

**Gejala:** HTTP 500, error: "Class not found"

**Solusi:**
```bash
cd simaccaProject
composer install
```

#### 4. .env File Missing atau Salah

**Gejala:** HTTP 500, database connection error

**Solusi:**
```bash
# Check .env exists
ls -la simaccaProject/.env

# Verify database credentials
cat simaccaProject/.env | grep database
```

#### 5. PHP Version Tidak Compatible

**Gejala:** HTTP 500, "Parse error" or "Syntax error"

**Solusi:**
- Pastikan PHP 8.1+ di cPanel
- Software → Select PHP Version → 8.1 atau 8.3

#### 6. Document Root Salah

**Gejala:** 404 atau HTTP 500

**Solusi:**
- cPanel → Domains → Pastikan Document Root = `simacca_public`
- BUKAN `public_html` atau `simaccaProject`

---

## Checking Logs for Errors

### Via cPanel

1. cPanel → **Errors** (di bagian Metrics)
2. View last 300 errors
3. Look for PHP errors related to your domain

### Via SSH

```bash
# Check PHP error log
tail -f /home2/smknbone/public_html/error_log

# Check CodeIgniter logs
tail -f /home2/smknbone/simaccaProject/writable/logs/*.log
```

### Via File Manager

1. File Manager
2. Navigate to `simaccaProject/writable/logs/`
3. Download latest log file
4. Open dengan text editor

---

## Environment-Specific Configuration

### Development (.env)

```ini
CI_ENVIRONMENT = development

database.default.hostname = localhost
database.default.database = simacca_local
database.default.username = root
database.default.password = 
```

### Production (.env)

```ini
CI_ENVIRONMENT = production

database.default.hostname = localhost
database.default.database = smknbone_simacca_database
database.default.username = smknbone_simacca_user
database.default.password = gi2Bw~,_bU+8
```

---

## Quick Command Reference

```bash
# Check directory structure
ls -la /home2/smknbone/

# Check public directory
ls -la /home2/smknbone/simacca_public/

# Check project directory
ls -la /home2/smknbone/simaccaProject/

# Check writable permissions
ls -la /home2/smknbone/simaccaProject/writable/

# Fix permissions
chmod -R 755 /home2/smknbone/simaccaProject/writable/

# Check PHP version
php -v

# Test PHP syntax
php -l /home2/smknbone/simacca_public/index.php

# Clear cache
rm -rf /home2/smknbone/simaccaProject/writable/cache/*

# View recent errors
tail -20 /home2/smknbone/public_html/error_log
```

---

## Security Best Practices

### ✅ DO:
- Keep `simaccaProject/` outside web root
- Set `.env` permission to 600
- Delete `connection-test.php` after testing
- Use strong database passwords
- Keep writable at 755 (or 775 max)
- Monitor error logs regularly

### ❌ DON'T:
- Never use 777 permissions
- Don't put `.env` in web-accessible directory
- Don't leave test files in production
- Don't commit database credentials to git
- Don't expose `writable/` to web access

---

## Contact & Support

**Jika masih ada masalah:**

1. Check error logs first
2. Run `connection-test.php` untuk diagnostic
3. Verify semua checklist sudah complete
4. Contact hosting support jika permission issues

**Hosting Details:**
- Provider: cPanel-based hosting
- Server: smkn8bone.sch.id
- PHP Version: 8.3.16
- Database: MariaDB

---

**Last Updated:** 2026-01-14
**Application:** SIMACCA v1.0
**Framework:** CodeIgniter 4
