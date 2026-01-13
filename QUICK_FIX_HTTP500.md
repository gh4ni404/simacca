# Quick Fix: HTTP 500 Error - SIMACCA

## ⚡ Langkah Cepat (5 Menit)

### 1️⃣ Upload File yang Sudah Diperbaiki

Upload 3 file ini ke server production Anda:

#### File 1: `simacca_public/index.php`
```php
// Line 50 harus:
require FCPATH . '../simaccaProject/app/Config/Paths.php';
```

#### File 2: `simaccaProject/app/Config/Paths.php`
Sudah benar (no changes needed, just verify)

#### File 3: `simacca_public/connection-test.php`
Path updated untuk check writable di simaccaProject

---

### 2️⃣ Set Permissions via cPanel

1. **Login cPanel** → File Manager
2. Navigate ke: `/home2/smknbone/simaccaProject/writable`
3. **Right-click** folder `writable`
4. **Change Permissions**
5. Set to: `755` or `775`
6. ✅ **CHECK: "Recurse into subdirectories"**
7. **Click "Change Permissions"**

---

### 3️⃣ Verify Document Root

1. **cPanel** → **Domains**
2. Find: `simacca.smkn8bone.sch.id`
3. Verify **Document Root** = `simacca_public`
4. If not, click Edit and change it

---

### 4️⃣ Test Connection

Visit: `https://simacca.smkn8bone.sch.id/connection-test.php`

**Expected Result:**
```json
{
    "overall": "HEALTHY",
    "fail_count": 0
}
```

---

### 5️⃣ Test Website

Visit: `https://simacca.smkn8bone.sch.id`

Should show login page (no HTTP 500)

---

## 🔍 Jika Masih Error 500

### Check Error Log

**cPanel → Errors** (di sidebar kiri, bagian Metrics)

Look for recent errors. Common issues:

#### Error: "failed to open stream: No such file or directory"
**Cause:** Path configuration salah
**Fix:** Verify `index.php` line 50 mengarah ke `../simaccaProject/`

#### Error: "Unable to write to session directory"
**Cause:** Permission issue
**Fix:** `chmod -R 775 writable/`

#### Error: "Class 'CodeIgniter\Boot' not found"
**Cause:** Vendor directory missing
**Fix:** Upload folder `vendor/` atau run `composer install`

#### Error: "Database connection failed"
**Cause:** .env configuration salah
**Fix:** Check credentials di `.env`

---

## 📋 Verification Checklist

Centang semua sebelum proceed:

### File Structure
- [ ] `/home2/smknbone/simacca_public/index.php` exists
- [ ] `/home2/smknbone/simaccaProject/app/` exists
- [ ] `/home2/smknbone/simaccaProject/vendor/` exists
- [ ] `/home2/smknbone/simaccaProject/writable/` exists
- [ ] `/home2/smknbone/simaccaProject/.env` exists

### Paths Configuration
- [ ] `index.php` line 50: `require FCPATH . '../simaccaProject/app/Config/Paths.php';`
- [ ] `Paths.php` has correct relative paths
- [ ] Document Root in cPanel = `simacca_public`

### Permissions
- [ ] `writable/` = 755 or 775
- [ ] `writable/session/` = 755 or 775
- [ ] `writable/uploads/` = 755 or 775
- [ ] `writable/logs/` = 755 or 775
- [ ] `.env` = 600

### Testing
- [ ] `connection-test.php` shows "HEALTHY"
- [ ] All permission tests PASS
- [ ] Website loads (no HTTP 500)
- [ ] Can see login page

---

## 🆘 Emergency: Revert to Working State

Jika semua cara gagal, restore backup:

1. **cPanel → File Manager → Backup**
2. Restore ke versi terakhir yang working
3. Contact hosting support

---

## 📞 Need Help?

1. **Check logs first**: cPanel → Errors
2. **Run diagnostic**: `connection-test.php`
3. **Review guide**: `SPLIT_DIRECTORY_DEPLOYMENT_GUIDE.md`
4. **Contact hosting** if permission issues

---

**Quick Reference:**
- Production Public: `/home2/smknbone/simacca_public/`
- Production Project: `/home2/smknbone/simaccaProject/`
- Document Root: `simacca_public`
- PHP Version: 8.3.16
