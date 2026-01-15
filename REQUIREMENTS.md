# 📋 System Requirements - SIMACCA

Daftar lengkap persyaratan sistem untuk menjalankan aplikasi SIMACCA.

---

## 🖥️ Server Requirements

### Minimum Specifications

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| **CPU** | 1 Core | 2+ Cores |
| **RAM** | 512 MB | 1 GB+ |
| **Storage** | 500 MB | 1 GB+ |
| **Bandwidth** | 1 GB/month | 10 GB/month |

### Operating System

✅ **Supported OS:**
- Windows 10/11
- Linux (Ubuntu 20.04+, Debian 10+, CentOS 7+)
- macOS 10.15+
- Windows Server 2016+

---

## 🔧 Software Requirements

### PHP

**Version:** PHP 8.1 atau lebih tinggi

**Cara cek:**
```bash
php -v
```

**Download:** https://www.php.net/downloads

### Required PHP Extensions

| Extension | Status | Keterangan |
|-----------|--------|------------|
| `intl` | ✅ Required | Internationalization |
| `mbstring` | ✅ Required | Multibyte string |
| `json` | ✅ Required | JSON handling (enabled by default) |
| `mysqlnd` | ✅ Required | MySQL native driver |
| `xml` | ✅ Required | XML parser |
| `curl` | ✅ Required | HTTP requests |
| `fileinfo` | ✅ Required | File type detection |
| `gd` | ✅ Required | Image manipulation |
| `zip` | ✅ Required | Zip archive handling |
| `openssl` | ⚠️ Recommended | SSL/TLS support |
| `opcache` | ⚠️ Recommended | Performance boost |

**Cara cek extensions:**
```bash
php -m
```

**Cara enable extension (php.ini):**
```ini
# Remove semicolon (;) to enable
extension=intl
extension=mbstring
extension=curl
extension=gd
```

---

## 🗄️ Database Requirements

### MySQL

**Version:** MySQL 5.7 atau lebih tinggi

**Cara cek:**
```bash
mysql --version
```

**Download:** https://dev.mysql.com/downloads/

### MariaDB

**Version:** MariaDB 10.3 atau lebih tinggi

**Cara cek:**
```bash
mariadb --version
```

**Download:** https://mariadb.org/download/

### Database Configuration

| Setting | Value |
|---------|-------|
| **Character Set** | `utf8mb4` |
| **Collation** | `utf8mb4_unicode_ci` |
| **Storage Engine** | InnoDB |

---

## 🌐 Web Server Requirements

### Apache

**Version:** Apache 2.4 atau lebih tinggi

**Required Modules:**
- `mod_rewrite` (untuk URL rewriting)
- `mod_headers` (untuk security headers)
- `mod_deflate` (untuk compression)

**Cara enable module:**
```bash
# Linux
sudo a2enmod rewrite
sudo a2enmod headers
sudo systemctl restart apache2
```

### Nginx

**Version:** Nginx 1.18 atau lebih tinggi

**Required Configuration:** FastCGI support untuk PHP

### PHP Development Server

Untuk development/testing, Anda bisa menggunakan built-in PHP server:
```bash
php spark serve
```

---

## 📦 Composer

**Version:** Composer 2.0 atau lebih tinggi

**Cara cek:**
```bash
composer -v
```

**Download:** https://getcomposer.org/download/

**Cara install (Linux):**
```bash
# Download installer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

# Install globally
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify
composer -v
```

---

## 🔐 SSL/HTTPS (Production)

**Required untuk production:**
- ✅ SSL Certificate (Let's Encrypt recommended - gratis)
- ✅ Domain name atau subdomain
- ✅ HTTPS enabled

**Optional untuk development:**
- ❌ Tidak diperlukan untuk testing di localhost

---

## 💻 Development Tools (Optional)

### Git

**Required jika:** Clone dari GitHub

**Cara cek:**
```bash
git --version
```

**Download:** https://git-scm.com/downloads

### Code Editor/IDE

**Recommended:**
- Visual Studio Code (Free)
- PhpStorm (Paid)
- Sublime Text (Paid/Trial)
- Notepad++ (Free - Windows)

---

## 📧 Email Service (Optional)

**Required jika:** Ingin menggunakan fitur email notification & password reset

### SMTP Requirements

| Provider | SMTP Host | Port | Encryption |
|----------|-----------|------|------------|
| **Gmail** | smtp.gmail.com | 587 | TLS |
| **Outlook** | smtp.office365.com | 587 | TLS |
| **Yahoo** | smtp.mail.yahoo.com | 587 | TLS |

**Untuk Gmail:**
- ✅ 2-Step Verification enabled
- ✅ App Password generated

**Note:** Fitur email bersifat optional. Aplikasi tetap bisa berjalan tanpa konfigurasi email.

---

## 🔍 Cara Cek Requirements

### Quick Check Script

Jalankan command ini untuk cek requirements:

```bash
# Check PHP version
php -v

# Check PHP extensions
php -m | grep -E "intl|mbstring|json|mysqlnd|xml|curl|fileinfo|gd|zip"

# Check Composer
composer -v

# Check MySQL
mysql --version

# Check Apache (Linux)
apache2 -v

# Check Nginx (Linux)
nginx -v
```

### Output yang Diharapkan

```
PHP 8.1.x atau lebih tinggi ✅
intl ✅
mbstring ✅
json ✅
mysqlnd ✅
xml ✅
curl ✅
fileinfo ✅
gd ✅
zip ✅
Composer version 2.x ✅
MySQL version 5.7.x atau MariaDB 10.3.x ✅
```

---

## 🐛 Troubleshooting Requirements

### PHP Version Too Low

**Problem:** PHP version < 8.1

**Solution:**

**Windows (XAMPP/WAMPP):**
- Download XAMPP versi terbaru (PHP 8.1+)
- https://www.apachefriends.org/

**Linux:**
```bash
# Ubuntu/Debian
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.1

# CentOS/RHEL
sudo yum install epel-release
sudo yum install php81
```

### Missing PHP Extensions

**Problem:** Extension not found

**Solution:**

**Windows (XAMPP):**
1. Open `php.ini`
2. Find `;extension=intl` (example)
3. Remove semicolon: `extension=intl`
4. Restart Apache

**Linux:**
```bash
# Ubuntu/Debian
sudo apt install php8.1-intl php8.1-mbstring php8.1-mysql php8.1-xml php8.1-curl php8.1-gd php8.1-zip

# CentOS/RHEL
sudo yum install php81-intl php81-mbstring php81-mysql php81-xml
```

### Composer Not Found

**Problem:** `composer: command not found`

**Solution:**

**Install Composer globally:**
```bash
# Download
curl -sS https://getcomposer.org/installer | php

# Move to global
sudo mv composer.phar /usr/local/bin/composer

# Verify
composer -v
```

### MySQL Connection Failed

**Problem:** Cannot connect to MySQL

**Solution:**

1. **Check MySQL service:**
   ```bash
   # Windows
   net start mysql
   
   # Linux
   sudo systemctl start mysql
   sudo systemctl status mysql
   ```

2. **Check credentials:**
   - Default username: `root`
   - Default password: (empty or set during installation)

3. **Reset root password (jika lupa):**
   ```bash
   sudo mysql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'new_password';
   FLUSH PRIVILEGES;
   EXIT;
   ```

---

## ✅ Pre-Installation Checklist

Sebelum mulai instalasi, pastikan:

- [ ] PHP 8.1+ terinstall
- [ ] Semua required PHP extensions aktif
- [ ] MySQL/MariaDB terinstall dan berjalan
- [ ] Composer terinstall
- [ ] Web server (Apache/Nginx) terinstall (untuk production)
- [ ] Git terinstall (jika clone dari GitHub)
- [ ] Database baru sudah dibuat
- [ ] Permissions folder writable/ bisa diakses

---

## 🌍 Hosting Compatibility

### Shared Hosting

✅ **Compatible jika memenuhi:**
- PHP 8.1+
- MySQL access
- SSH access (recommended untuk composer)
- File manager access
- Minimum 256 MB RAM

**Recommended providers:**
- Hostinger
- Niagahoster
- Rumahweb
- IDCloudHost

### VPS/Cloud Server

✅ **Fully compatible dengan:**
- DigitalOcean
- Linode
- Vultr
- AWS EC2
- Google Cloud
- Azure

### Local Development

✅ **Compatible dengan:**
- XAMPP (Windows/Linux/macOS)
- WAMPP (Windows)
- MAMP (macOS)
- Laragon (Windows)
- Docker

---

## 📊 Performance Recommendations

### Untuk Development

- RAM: 512 MB - 1 GB
- Storage: 500 MB
- PHP OPcache: Optional

### Untuk Production (< 100 users)

- RAM: 1 GB - 2 GB
- Storage: 1 GB - 2 GB
- PHP OPcache: Enabled
- MySQL Query Cache: Enabled

### Untuk Production (100+ users)

- RAM: 2 GB+
- Storage: 2 GB+
- PHP OPcache: Enabled
- MySQL Query Cache: Enabled
- CDN: Recommended
- Caching: Redis/Memcached

---

## 🔗 Useful Links

- **PHP**: https://www.php.net/
- **Composer**: https://getcomposer.org/
- **MySQL**: https://www.mysql.com/
- **MariaDB**: https://mariadb.org/
- **CodeIgniter 4**: https://codeigniter.com/
- **XAMPP**: https://www.apachefriends.org/

---

## 📞 Need Help?

Jika Anda mengalami masalah dengan requirements:

1. Check [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md#-troubleshooting)
2. Check dokumentasi official (PHP, MySQL, dll)
3. Create issue di GitHub repository
4. Contact support team

---

*System Requirements untuk SIMACCA*  
*Terakhir diupdate: 2026-01-15*  
*Minimum: PHP 8.1 | MySQL 5.7 | Composer 2.0*
