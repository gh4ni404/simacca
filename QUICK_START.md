# ⚡ Quick Start Guide

Panduan cepat untuk menjalankan SIMACCA dalam 5 menit!

---

## 🚀 Instalasi Super Cepat

### Step 1: Clone Repository
```bash
git clone https://github.com/username/simacca.git
cd simacca
```

### Step 2: Install Dependencies
```bash
composer install
```

### Step 3: Setup Environment
```bash
# Windows
copy env .env

# Linux/Mac
cp env .env
```

### Step 4: Generate Key
```bash
php spark key:generate
```

### Step 5: Konfigurasi Database

Edit file `.env`:
```ini
database.default.database = simacca_db
database.default.username = root
database.default.password = 
```

Buat database:
```sql
CREATE DATABASE simacca_db;
```

### Step 6: Setup Database
```bash
# Tanpa data dummy
php spark setup

# Dengan data dummy (recommended untuk testing)
php spark setup --with-dummy
```

### Step 7: Jalankan Server
```bash
php spark serve
```

### Step 8: Buka Browser
```
http://localhost:8080
```

### Step 9: Login
```
Username: admin
Password: admin123
```

---

## ✅ Done!

**Total waktu: ~5 menit**

🔐 **Jangan lupa ganti password setelah login!**

📖 **Untuk panduan lengkap, baca:** [PANDUAN_INSTALASI.md](PANDUAN_INSTALASI.md)

---

## 🆘 Troubleshooting Cepat

**Database connection error?**
```bash
# Cek MySQL service berjalan
# Windows: net start mysql
# Linux: sudo systemctl start mysql
```

**Permission error?**
```bash
# Linux/Mac
chmod -R 777 writable/

# Windows (run as admin)
icacls writable /grant Everyone:F /T
```

**Encryption key error?**
```bash
php spark key:generate
```

---

*Happy coding! 🚀*
