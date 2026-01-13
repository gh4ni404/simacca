# 🚀 Deploy Now - Simple Checklist

## ✅ Files Ready to Upload

Semua masalah sudah diperbaiki! Berikut file yang perlu di-upload:

### 📁 Upload ke: `/home2/smknbone/simaccaProject/`

```
✅ app/Helpers/component_helper.php          (FIXED: Session error)
✅ app/Views/templates/auth_layout.php       (UPDATED: Use render_alerts())
✅ app/Views/templates/main_layout.php       (UPDATED: Use render_alerts())
✅ app/Config/Paths.php                      (DOCUMENTED: Production paths)
```

### 📁 Upload ke: `/home2/smknbone/simacca_public/`

```
✅ public/index.php                          (FIXED: Path to simaccaProject)
✅ public/connection-test.php                (FIXED: SQL syntax, paths)
✅ public/diagnostic.php                     (NEW: Troubleshooting tool)
```

---

## 🎯 Step-by-Step Deployment

### Step 1: Upload Files via cPanel File Manager

1. **Login to cPanel**
   - URL: https://smkn8bone.sch.id:2083
   
2. **Open File Manager**

3. **Upload to simaccaProject:**
   - Navigate: `/home2/smknbone/simaccaProject/app/Helpers/`
   - Upload: `component_helper.php` (overwrite existing)
   
   - Navigate: `/home2/smknbone/simaccaProject/app/Views/templates/`
   - Upload: `auth_layout.php` (overwrite)
   - Upload: `main_layout.php` (overwrite)
   
   - Navigate: `/home2/smknbone/simaccaProject/app/Config/`
   - Upload: `Paths.php` (overwrite)

4. **Upload to simacca_public:**
   - Navigate: `/home2/smknbone/simacca_public/`
   - Upload: `index.php` (overwrite)
   - Upload: `connection-test.php` (overwrite)
   - Upload: `diagnostic.php` (new file)

---

### Step 2: Run Diagnostic

1. **Visit:** `https://simacca.smkn8bone.sch.id/diagnostic.php`

2. **Check output for:**
   ```json
   {
       "index_php": {
           "status": "EXISTS",
           "line_50": "require FCPATH . '../simaccaProject/app/Config/Paths.php';"
       },
       "paths_php_check": {
           "../simaccaProject/app/Config/Paths.php": {
               "exists": true,
               "readable": true
           }
       },
       "vendor_check": {
           "../simaccaProject/vendor/autoload.php": {
               "exists": true
           }
       }
   }
   ```

3. **If any file shows "NOT_FOUND":** Upload that file!

---

### Step 3: Run Connection Test

1. **Visit:** `https://simacca.smkn8bone.sch.id/connection-test.php`

2. **Expected result:**
   ```json
   {
       "overall": "HEALTHY",
       "fail_count": 0
   }
   ```

3. **All tests should PASS:**
   - ✅ database_connect
   - ✅ database_query
   - ✅ database_tables
   - ✅ connection_stability
   - ✅ permissions (all)
   - ✅ php_config

---

### Step 4: Test Website

1. **Visit:** `https://simacca.smkn8bone.sch.id`

2. **Expected:** Login page loads (NO HTTP 500!)

3. **Test login:**
   - Login dengan user admin/guru/siswa
   - Check flash messages work
   - Navigate around
   - Test file upload (if applicable)

---

### Step 5: Cleanup (Security!)

**Via cPanel File Manager:**

1. Navigate: `/home2/smknbone/simacca_public/`
2. **DELETE:**
   - ❌ `diagnostic.php`
   - ❌ `connection-test.php`

**Why?** These files expose system information!

---

## 🆘 If Something Goes Wrong

### Scenario 1: Still HTTP 500

**Action:**
1. Check diagnostic.php output
2. Look for missing files
3. Check cPanel → Errors log
4. Refer to: `HTTP500_TROUBLESHOOTING_STEPS.md`

### Scenario 2: vendor/ not found

**Solution via SSH:**
```bash
cd /home2/smknbone/simaccaProject
composer install
```

**Or:** Upload entire `vendor/` folder via FTP (large, ~50MB)

### Scenario 3: .env missing

**Solution:**
1. Copy `.env` from local to server
2. Upload to: `/home2/smknbone/simaccaProject/.env`
3. Set permission: 600
4. Update database credentials

### Scenario 4: Permission errors return

**Solution:**
```bash
cd /home2/smknbone/simaccaProject
chmod -R 775 writable/
```

---

## 📊 Quick Verification Commands (SSH)

If you have SSH access:

```bash
# Check if files exist
ls -la /home2/smknbone/simacca_public/index.php
ls -la /home2/smknbone/simaccaProject/app/Helpers/component_helper.php

# Check line 50 of index.php
sed -n '50p' /home2/smknbone/simacca_public/index.php

# Test PHP syntax
php -l /home2/smknbone/simacca_public/index.php

# Check writable permissions
ls -la /home2/smknbone/simaccaProject/ | grep writable

# Test spark commands
cd /home2/smknbone/simaccaProject
php spark list
```

---

## ✅ Success Indicators

After deployment, you should see:

- ✅ diagnostic.php shows all files exist
- ✅ connection-test.php shows HEALTHY
- ✅ Website loads without HTTP 500
- ✅ Login page displays correctly
- ✅ Can login successfully
- ✅ Flash messages display properly
- ✅ No errors in cPanel error log
- ✅ `php spark` commands work (if SSH)

---

## 📞 Need Help?

**Check these docs:**
1. `SESSION_ERROR_FIX_SUMMARY.md` - What was fixed
2. `HTTP500_TROUBLESHOOTING_STEPS.md` - Detailed troubleshooting
3. `SPLIT_DIRECTORY_DEPLOYMENT_GUIDE.md` - Full deployment guide
4. `FIX_PERMISSIONS_GUIDE.md` - Permission issues

**Still stuck?**
- Check cPanel error logs
- Share diagnostic.php output
- Share connection-test.php output
- Check recent error_log entries

---

## 🎉 After Successful Deployment

1. ✅ Delete diagnostic.php
2. ✅ Delete connection-test.php
3. ✅ Test all major features
4. ✅ Monitor error logs for 24 hours
5. ✅ Celebrate! 🎊

---

**Pro Tip:** Keep a backup of working files before any future updates!

**Last Updated:** 2026-01-14
**Ready to Deploy:** YES ✅
