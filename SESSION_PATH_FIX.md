# Session Path Error Fix

## 🐛 Error Description

**Error:**
```
ErrorException: touch(): Unable to create file
null/simacca_session485f24a1400baf8d89491ac5948669cc
because No such file or directory
```

**Cause:** `session.savePath = null` in `.env` file

---

## ❌ Wrong Configuration

**In `.env` or `.env.production`:**
```ini
session.savePath = null  # ❌ WRONG! "null" is not a valid path
```

**What happens:**
1. CodeIgniter tries to create session file at path: `null/session_file`
2. Directory `null` doesn't exist
3. PHP throws "No such file or directory" error

---

## ✅ Correct Configuration

### Option 1: Use Default (RECOMMENDED)

**Comment out or remove the line:**
```ini
# session.savePath = null
```

**Result:** CodeIgniter will use default: `WRITEPATH . 'session'`
- Local: `/path/to/project/writable/session`
- Production: `/home2/smknbone/simaccaProject/writable/session`

### Option 2: Specify Absolute Path (Advanced)

**Only if you need custom location:**
```ini
session.savePath = '/home2/smknbone/simaccaProject/writable/session'
```

**Requirements:**
- ✅ Must be absolute path (starting with `/`)
- ✅ Directory must exist
- ✅ Must be writable (755 or 775 permissions)

---

## 🔧 Fixed `.env.production`

**Updated configuration:**
```ini
#--------------------------------------------------------------------
# SESSION CONFIGURATION
#--------------------------------------------------------------------
# Session driver
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'

# Session save path (leave commented to use default WRITEPATH . 'session')
# For production with split directory: /home2/smknbone/simaccaProject/writable/session
# session.savePath = null

# Session cookie name
session.cookieName = 'simacca_session'

# Session expiration (8 hours)
session.expiration = 28800

# Cookie domain for production
session.cookieDomain = '.smkn8bone.sch.id'

# Secure cookies (HTTPS only)
session.cookieSecure = true

# HTTP only cookies (prevent XSS)
session.cookieHTTPOnly = true

# SameSite attribute
session.cookieSameSite = 'Lax'
```

**Key changes:**
- ✅ `session.savePath = null` is now **commented out**
- ✅ Added explanation comments
- ✅ Will use default path automatically

---

## 📋 Deployment Checklist

### For Production Server:

1. **Upload updated `.env` file:**
   ```
   Upload to: /home2/smknbone/simaccaProject/.env
   ```

2. **Set correct permissions:**
   ```bash
   chmod 600 /home2/smknbone/simaccaProject/.env
   ```

3. **Verify session directory exists and is writable:**
   ```bash
   ls -la /home2/smknbone/simaccaProject/writable/session
   chmod 775 /home2/smknbone/simaccaProject/writable/session
   ```

4. **Test the fix:**
   ```
   Visit: https://simacca.smkn8bone.sch.id/login
   ```

---

## 🧪 Verification Steps

### 1. Check Session Directory

**Via SSH:**
```bash
cd /home2/smknbone/simaccaProject
ls -la writable/session/
```

**Expected output:**
```
drwxrwxr-x 2 smknbone smknbone 4096 Jan 14 00:00 .
drwxrwxr-x 5 smknbone smknbone 4096 Jan 14 00:00 ..
```

### 2. Test Session Creation

**Via SSH:**
```bash
cd /home2/smknbone/simaccaProject
php -r "touch('writable/session/test.txt'); echo 'Success';"
```

**Expected output:**
```
Success
```

### 3. Test Website

**Visit login page:**
```
https://simacca.smkn8bone.sch.id/login
```

**Expected:**
- ✅ No error message
- ✅ Login form displays
- ✅ Can login successfully
- ✅ Session persists across pages

---

## 🎯 Understanding Session Paths

### How CodeIgniter Handles Session Paths

**Priority order:**

1. **`.env` value** (if set and not null)
   ```ini
   session.savePath = '/custom/path'
   ```

2. **Config/Session.php value** (if .env not set)
   ```php
   public string $savePath = WRITEPATH . 'session';
   ```

3. **Default** (if both above are not set)
   ```
   WRITEPATH . 'session'
   ```

### Current Configuration

**Config/Session.php (line 61):**
```php
public string $savePath = WRITEPATH . 'session';
```

**WRITEPATH constant (defined in Paths.php):**
```php
public string $writableDirectory = __DIR__ . '/../../writable';
```

**Final resolved path:**
```
Development: /path/to/project/writable/session
Production:  /home2/smknbone/simaccaProject/writable/session
```

---

## 🚨 Common Mistakes

### ❌ Setting savePath to "null" string
```ini
session.savePath = null  # This is literal string "null", not PHP null!
```

### ❌ Using relative path
```ini
session.savePath = '../writable/session'  # FileHandler requires absolute path
```

### ❌ Path doesn't exist
```ini
session.savePath = '/nonexistent/path'  # Directory must exist
```

### ❌ No write permissions
```ini
session.savePath = '/read-only/path'  # Must be writable
```

### ✅ Correct approaches
```ini
# Option 1: Comment out (use default)
# session.savePath = null

# Option 2: Use absolute path
session.savePath = '/home2/smknbone/simaccaProject/writable/session'

# Option 3: Don't set it at all (use Config/Session.php default)
```

---

## 🔍 Debugging Session Issues

### Check Current Session Configuration

**Create temporary debug file: `public/debug-session.php`**
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/Paths.php';

$paths = new Config\Paths();

echo "WRITEPATH: " . $paths->writableDirectory . "\n";
echo "Session Path: " . $paths->writableDirectory . '/session' . "\n";
echo "Exists: " . (is_dir($paths->writableDirectory . '/session') ? 'YES' : 'NO') . "\n";
echo "Writable: " . (is_writable($paths->writableDirectory . '/session') ? 'YES' : 'NO') . "\n";

// Check .env session config
$env = file_get_contents(__DIR__ . '/../.env');
preg_match('/session\.savePath\s*=\s*(.+)/', $env, $matches);
if ($matches) {
    echo ".env session.savePath: " . trim($matches[1]) . "\n";
} else {
    echo ".env session.savePath: NOT SET (using default)\n";
}
```

**Run it:**
```
https://simacca.smkn8bone.sch.id/debug-session.php
```

**Expected output:**
```
WRITEPATH: /home2/smknbone/simaccaProject/writable
Session Path: /home2/smknbone/simaccaProject/writable/session
Exists: YES
Writable: YES
.env session.savePath: NOT SET (using default)
```

**Don't forget to delete this file after debugging!**

---

## 📊 Summary

### Problem:
- `.env` had `session.savePath = null`
- CodeIgniter interpreted "null" as literal string
- Tried to create sessions in non-existent "null" directory

### Solution:
- Comment out `session.savePath` line in `.env`
- Let CodeIgniter use default: `WRITEPATH . 'session'`
- Ensure `writable/session` directory exists and is writable

### Files Updated:
- ✅ `.env.production` - Commented out `session.savePath = null`

---

## ✅ Verification Commands

**Quick test after fix:**
```bash
# Check session directory
ls -la /home2/smknbone/simaccaProject/writable/session

# Test write permission
touch /home2/smknbone/simaccaProject/writable/session/test.txt

# Clean up test file
rm /home2/smknbone/simaccaProject/writable/session/test.txt

# Test website
curl -I https://simacca.smkn8bone.sch.id/login
```

**Expected result:**
```
HTTP/2 200 OK
Set-Cookie: simacca_session=...
```

---

**Status:** ✅ FIXED  
**Ready for deployment:** YES  
**Files to upload:** `.env.production` → rename to `.env` on server
