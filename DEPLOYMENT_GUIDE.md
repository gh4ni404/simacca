# 🌐 Deployment Guide

Panduan lengkap untuk deploy aplikasi SIMACCA ke production server.

---

## 📋 Checklist Pre-Deployment

Sebelum deploy, pastikan:

- ✅ Aplikasi berjalan dengan baik di local
- ✅ Semua fitur sudah di-test
- ✅ Database backup tersedia
- ✅ Requirements server terpenuhi
- ✅ Domain sudah siap (jika menggunakan custom domain)

---

## 🖥️ Server Requirements

### Minimum Specifications

- **CPU**: 1 Core
- **RAM**: 512 MB (1 GB recommended)
- **Storage**: 500 MB
- **PHP**: 8.1 atau lebih tinggi
- **Database**: MySQL 5.7+ atau MariaDB 10.3+
- **Web Server**: Apache 2.4+ atau Nginx 1.18+

### Software Requirements

```bash
# Check requirements
php -v              # PHP 8.1+
mysql --version     # MySQL/MariaDB
apache2 -v          # Apache (atau nginx -v)
composer -v         # Composer 2.0+
```

---

## 📦 Deployment ke Shared Hosting

### Step 1: Upload Files

**Via FTP/SFTP:**
1. Compress project menjadi zip (kecuali folder `vendor`)
2. Upload ke server via FileZilla/FTP client
3. Extract di public_html atau folder root

**Via cPanel File Manager:**
1. Zip project folder
2. Upload via cPanel File Manager
3. Extract

### Step 2: Install Dependencies

```bash
# Via SSH (jika tersedia)
cd /path/to/simacca
composer install --no-dev --optimize-autoloader

# Jika tidak ada SSH, upload folder vendor yang sudah di-install local
```

### Step 3: Setup Environment

```bash
# Copy file environment
cp env .env

# Edit dengan File Manager atau FTP
```

Konfigurasi `.env` untuk production:
```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://yourdomain.com/'
app.forceGlobalSecureRequests = true

database.default.hostname = localhost
database.default.database = your_db_name
database.default.username = your_db_user
database.default.password = your_db_password
```

### Step 4: Generate Encryption Key

```bash
# Via SSH
php spark key:generate

# Atau manual: generate random 32 characters dan masukkan ke .env
```

### Step 5: Setup Database

**Via cPanel:**
1. Buka phpMyAdmin
2. Create database baru
3. Create user dan assign ke database
4. Import file `simacca_db.sql` (jika ada) atau run migrations

**Via SSH:**
```bash
php spark migrate
php spark db:seed AdminSeeder
```

### Step 6: Set Permissions

```bash
chmod -R 755 /path/to/simacca
chmod -R 777 writable/
```

### Step 7: Configure .htaccess

Pastikan file `.htaccess` ada di root folder dengan content:

```apache
# Disable directory browsing
Options -Indexes

# Rewrite rules
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect to https
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
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

---

## 🐧 Deployment ke VPS/Dedicated Server (Linux)

### Step 1: Persiapan Server

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y apache2 mysql-server php8.1 php8.1-cli php8.1-mysql \
    php8.1-mbstring php8.1-xml php8.1-curl php8.1-intl php8.1-zip php8.1-gd \
    composer git unzip
```

### Step 2: Configure MySQL

```bash
# Secure installation
sudo mysql_secure_installation

# Create database
sudo mysql -u root -p
```

```sql
CREATE DATABASE simacca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'simacca_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON simacca_db.* TO 'simacca_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Clone Repository

```bash
# Clone ke server
cd /var/www
sudo git clone https://github.com/username/simacca.git
sudo chown -R www-data:www-data simacca
cd simacca
```

### Step 4: Install Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Step 5: Configure Environment

```bash
cp env .env
nano .env  # Edit konfigurasi
```

```bash
# Generate key
php spark key:generate
```

### Step 6: Setup Database

```bash
php spark migrate
php spark db:seed AdminSeeder
```

### Step 7: Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/simacca
sudo chmod -R 755 /var/www/simacca
sudo chmod -R 777 /var/www/simacca/writable
```

### Step 8: Configure Apache

```bash
# Create virtual host
sudo nano /etc/apache2/sites-available/simacca.conf
```

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/simacca
    
    <Directory /var/www/simacca>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/simacca-error.log
    CustomLog ${APACHE_LOG_DIR}/simacca-access.log combined
</VirtualHost>
```

```bash
# Enable site and rewrite module
sudo a2enmod rewrite
sudo a2ensite simacca.conf
sudo systemctl restart apache2
```

### Step 9: Setup SSL (HTTPS)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Get SSL certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

---

## 🔒 Security Best Practices

### 1. File Permissions

```bash
# Production permissions
find /var/www/simacca -type f -exec chmod 644 {} \;
find /var/www/simacca -type d -exec chmod 755 {} \;
chmod -R 777 /var/www/simacca/writable
```

### 2. Hide Sensitive Files

```apache
# Add to .htaccess
<FilesMatch "^\.env$">
    Require all denied
</FilesMatch>
```

### 3. Disable Debug Mode

```ini
# .env
CI_ENVIRONMENT = production
database.default.DBDebug = false
```

### 4. Regular Updates

```bash
# Update aplikasi
git pull origin main
composer update
php spark migrate

# Update system
sudo apt update && sudo apt upgrade
```

---

## 🔄 Backup Strategy

### Automated Backup Script

```bash
#!/bin/bash
# backup.sh

BACKUP_DIR="/home/backups/simacca"
DATE=$(date +%Y%m%d_%H%M%S)

# Backup database
mysqldump -u simacca_user -p'password' simacca_db > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/simacca/writable/uploads

# Delete old backups (older than 30 days)
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

### Setup Cron Job

```bash
# Edit crontab
crontab -e

# Add backup schedule (daily at 2 AM)
0 2 * * * /home/scripts/backup.sh
```

---

## 📊 Monitoring

### Check Logs

```bash
# Application logs
tail -f /var/www/simacca/writable/logs/log-*.log

# Apache logs
tail -f /var/log/apache2/simacca-error.log
```

### Database Maintenance

```bash
# Check database size
sudo mysql -u root -p
```

```sql
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'simacca_db'
GROUP BY table_schema;
```

---

## 🚀 Performance Optimization

### Enable OPcache

```bash
sudo nano /etc/php/8.1/apache2/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=2
```

### Optimize Composer Autoloader

```bash
composer dump-autoload --optimize --no-dev
```

### Enable Gzip Compression

```apache
# .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

---

## ✅ Post-Deployment Checklist

- [ ] Aplikasi dapat diakses via domain
- [ ] HTTPS berfungsi dengan baik
- [ ] Login berhasil dengan akun admin
- [ ] Database terkoneksi dengan benar
- [ ] Email notification berfungsi (jika dikonfigurasi)
- [ ] File upload berfungsi
- [ ] Semua menu dapat diakses
- [ ] Permissions folder writable/ sudah benar
- [ ] Backup otomatis sudah dijadwalkan
- [ ] Monitoring logs sudah aktif
- [ ] Password default sudah diganti

---

## 🆘 Troubleshooting Production

### 500 Internal Server Error

```bash
# Check Apache error log
sudo tail -f /var/log/apache2/error.log

# Check application log
tail -f writable/logs/log-*.log

# Check permissions
sudo chown -R www-data:www-data /var/www/simacca
sudo chmod -R 777 writable/
```

### White Screen / Blank Page

```ini
# Enable debug temporarily
# .env
CI_ENVIRONMENT = development

# Check error, then change back to production
```

### Database Connection Error

```bash
# Test database connection
mysql -u simacca_user -p simacca_db

# Check .env configuration
cat .env | grep database
```

---

*Panduan deployment untuk SIMACCA*  
*Terakhir diupdate: 2026-01-15*
