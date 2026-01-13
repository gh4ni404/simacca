# HTTP 500 Troubleshooting Steps - SIMACCA

## ✅ Good News!

**Connection Test Result:** `HEALTHY` ✓
- Database: ✅ PASS
- Permissions: ✅ PASS (all writable directories)
- PHP Config: ✅ PASS

**This means:**
- Database connection is working
- File permissions are correct
- PHP version is compatible

## ❌ Problem

Website still shows **HTTP ERROR 500**

This indicates a **configuration or missing file issue**, NOT database or permissions.

---

## 🔍 Step 1: Run Diagnostic Script

I've created a diagnostic script to identify the exact problem.

### Upload & Run:

1. **Upload file:** `public/diagnostic.php` 
   - To: `/home2/smknbone/simacca_public/diagnostic.php`

2. **Visit URL:** `https://simacca.smkn8bone.sch.id/diagnostic.php`

3. **Copy the entire JSON output** and send it back

### What it checks:

- ✓ Does `index.php` exist and is readable?
- ✓ What does line 50 of `index.php` say?
- ✓ Does `Paths.php` exist at expected location?
- ✓ Does `vendor/autoload.php` exist?
- ✓ Does `.env` file exist?
- ✓ What files are in the public and parent directories?
- ✓ Can PHP include the required files?
- ✓ What are the recent error log entries?

---

## 🎯 Common Causes of HTTP 500 (After Permissions are Fixed)

### 1. **index.php Not Updated** ❌
**Problem:** Old `index.php` still looking for `../app/` instead of `../simaccaProject/app/`

**Check:** Line 50 should be:
```php
require FCPATH . '../simaccaProject/app/Config/Paths.php';
```

**Fix:** Upload the corrected `index.php` from your workspace

---

### 2. **vendor/ Directory Missing** ❌
**Problem:** Dependencies not uploaded to server

**Check via SSH:**
```bash
ls -la /home2/smknbone/simaccaProject/vendor/
```

**Fix:** 
```bash
cd /home2/smknbone/simaccaProject
composer install
```

Or upload the entire `vendor/` folder via FTP (might be large ~50MB)

---

### 3. **.env File Missing or Wrong** ❌
**Problem:** Environment configuration missing

**Check via SSH:**
```bash
ls -la /home2/smknbone/simaccaProject/.env
```

**Fix:** Create `.env` with correct configuration:
```bash
cd /home2/smknbone/simaccaProject
nano .env
```

Paste this:
```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.baseURL = 'https://simacca.smkn8bone.sch.id/'
app.indexPage = ''

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = smknbone_simacca_database
database.default.username = smknbone_simacca_user
database.default.password = gi2Bw~,_bU+8
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------

encryption.key = hex2bin:YOUR_HEX_KEY_HERE

#--------------------------------------------------------------------
# SESSION
#--------------------------------------------------------------------

session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
session.cookieName = 'ci_session'
session.expiration = 7200
session.savePath = NULL
session.matchIP = false
session.timeToUpdate = 300
session.regenerateDestroy = false

#--------------------------------------------------------------------
# SECURITY
#--------------------------------------------------------------------

security.csrfProtection = 'session'
security.tokenRandomize = false
security.tokenName = 'csrf_token_name'
security.headerName = 'X-CSRF-TOKEN'
security.cookieName = 'csrf_cookie_name'
security.expires = 7200
security.regenerate = true
security.redirect = true
security.samesite = 'Lax'

#--------------------------------------------------------------------
# COOKIE
#--------------------------------------------------------------------

cookie.prefix = ''
cookie.expires = 0
cookie.path = '/'
cookie.domain = ''
cookie.secure = false
cookie.httponly = true
cookie.samesite = 'Lax'
```

Save (Ctrl+O, Enter, Ctrl+X) and set permissions:
```bash
chmod 600 .env
```

---

### 4. **Paths.php Not Found** ❌
**Problem:** `app/Config/Paths.php` doesn't exist at expected location

**Check via SSH:**
```bash
ls -la /home2/smknbone/simaccaProject/app/Config/Paths.php
```

**Fix:** Upload `app/Config/Paths.php` from workspace

---

### 5. **PHP Syntax Error** ❌
**Problem:** Syntax error in PHP files

**Check via SSH:**
```bash
php -l /home2/smknbone/simacca_public/index.php
```

Should output: `No syntax errors detected`

---

## 📋 Quick Checklist

Run through this checklist:

### Files Exist?
```bash
# Via SSH
ls -la /home2/smknbone/simacca_public/index.php
ls -la /home2/smknbone/simaccaProject/app/Config/Paths.php
ls -la /home2/smknbone/simaccaProject/vendor/autoload.php
ls -la /home2/smknbone/simaccaProject/.env
ls -la /home2/smknbone/simaccaProject/writable/
```

### Permissions Correct?
```bash
# writable should be 755 or 775
ls -la /home2/smknbone/simaccaProject/ | grep writable

# .env should be 600
ls -la /home2/smknbone/simaccaProject/.env
```

### PHP Syntax OK?
```bash
php -l /home2/smknbone/simacca_public/index.php
php -l /home2/smknbone/simaccaProject/app/Config/Paths.php
```

---

## 🔧 Step-by-Step Fix Process

### Option A: Using Diagnostic Script (Recommended)

1. ✅ Upload `diagnostic.php`
2. ✅ Run it: `https://simacca.smkn8bone.sch.id/diagnostic.php`
3. ✅ Share the JSON output
4. ✅ I'll tell you exactly what's missing

### Option B: Manual Check via cPanel

1. **File Manager** → `/home2/smknbone/simacca_public/`
2. Check if `index.php` exists
3. Right-click → **Edit**
4. Check line 50: Should have `../simaccaProject/app/Config/Paths.php`
5. If wrong, fix it
6. **File Manager** → `/home2/smknbone/simaccaProject/`
7. Check if these exist:
   - `app/Config/Paths.php` ✓
   - `vendor/autoload.php` ✓
   - `.env` ✓

### Option C: Check Error Logs

1. **cPanel → Errors** (in Metrics section)
2. Look for recent errors
3. Common errors:
   - "failed to open stream" = file not found
   - "Class not found" = vendor missing
   - "Cannot modify header" = output before header (usually .env issue)

---

## 📊 Expected File Structure on Server

```
/home2/smknbone/
├── simacca_public/              ← Document Root
│   ├── index.php                ← Must have correct path on line 50
│   ├── .htaccess
│   ├── connection-test.php      ← This works (HEALTHY)
│   ├── diagnostic.php           ← NEW: Upload this
│   └── assets/
│
└── simaccaProject/
    ├── app/
    │   └── Config/
    │       └── Paths.php        ← Must exist
    ├── vendor/                  ← Must exist (composer dependencies)
    │   ├── autoload.php
    │   └── codeigniter4/
    ├── writable/                ← 755 or 775 (FIXED ✓)
    │   ├── cache/
    │   ├── logs/
    │   ├── session/
    │   └── uploads/
    └── .env                     ← Must exist (600 permission)
```

---

## 🆘 If Still Stuck

### Get More Info:

**Via SSH:**
```bash
# See last 30 lines of error log
tail -30 /home2/smknbone/public_html/error_log

# Check if PHP can find the files
cd /home2/smknbone/simacca_public
php -r "echo file_exists('../simaccaProject/app/Config/Paths.php') ? 'FOUND' : 'NOT FOUND';"
```

**Via cPanel:**
1. **File Manager** → Navigate to parent of `public_html`
2. Find `error_log` file
3. Right-click → **View**
4. Look for recent PHP errors

---

## ✅ Success Indicators

Once fixed, you should see:

1. ✅ `https://simacca.smkn8bone.sch.id` → Login page (no HTTP 500)
2. ✅ No PHP errors in cPanel → Errors log
3. ✅ `connection-test.php` shows HEALTHY
4. ✅ Can login and use the application

---

## 🎯 Next Steps

**RIGHT NOW:**

1. **Upload** `diagnostic.php` to your server
2. **Run** `https://simacca.smkn8bone.sch.id/diagnostic.php`
3. **Share** the output with me
4. **I'll identify** the exact missing file/configuration

**After we identify the issue:**

1. Fix the specific problem
2. Test the website
3. Delete diagnostic files (`diagnostic.php`, `connection-test.php`)
4. Celebrate! 🎉

---

**Let me know the diagnostic.php output and I'll tell you exactly what to fix!**
