# 🔍 Debugging Guide - Camera Upload Error

**Issue:** Edit jurnal dengan camera capture menampilkan error "Terjadi kesalahan saat menyimpan jurnal"

**Status:** 🔧 DEBUGGING IN PROGRESS

---

## 📊 Comprehensive Logging Added

### Backend Logs (Controller)
All logs prefixed with `[JURNAL UPDATE]`

**Location:** `writable/logs/log-YYYY-MM-DD.log`

**Log Points:**
1. ✅ Started - ID: {jurnalId}
2. ✅ POST data: {json}
3. ✅ Validation passed/failed: {errors}
4. ✅ Jurnal found: {jurnal data}
5. ✅ Prepared data: {data}
6. ✅ File check - isValid: yes/no
7. ✅ Processing file upload - Name, Size, Type
8. ✅ File validation passed/failed: {error}
9. ✅ Foto uploaded successfully: {filename}
10. ✅ Final data for update: {json}
11. ✅ Update result: success/failed
12. ✅ Model update failed: {model errors}
13. ✅ Exception occurred: {exception message + stack}

### Frontend Logs (JavaScript)
All logs prefixed with `[JURNAL EDIT]`

**Location:** Browser Console (F12 > Console tab)

**Log Points:**
1. ✅ Submitting form...
2. ✅ FormData entries: [{key, value/Blob info}]
3. ✅ Response status: {status code}
4. ✅ Response ok: {true/false}
5. ✅ Response redirected: {true/false}
6. ✅ JSON/HTML response: {content}
7. ✅ Exception: {error + stack}

---

## 🧪 Testing Instructions

### Step 1: Prepare Environment
```bash
# Clear old logs
# On Windows PowerShell:
Remove-Item writable/logs/*.log

# Or manually delete files in writable/logs/
```

### Step 2: Open Browser Console
1. Open browser (Chrome/Firefox recommended)
2. Press **F12** to open DevTools
3. Go to **Console** tab
4. Keep it open during test

### Step 3: Perform Test
1. Login sebagai Guru
2. Navigate to `/guru/jurnal`
3. Click **"Edit"** on any jurnal entry
4. Click **"Ganti dengan Foto Baru"** button
5. Allow camera access
6. Capture photo
7. Click **"Update Jurnal"** button
8. **DO NOT close** browser console yet

### Step 4: Collect Logs

#### A. Frontend Logs (Browser Console)
```
Copy ALL lines that start with:
- [JURNAL EDIT]
```

**Example:**
```javascript
[JURNAL EDIT] Submitting form...
[JURNAL EDIT] FormData entries: [["_method", "PUT"], ["kegiatan_pembelajaran", "..."], ["foto_dokumentasi", "Blob(45678 bytes, image/jpeg)"]]
[JURNAL EDIT] Response status: 500
[JURNAL EDIT] Response ok: false
[JURNAL EDIT] Response redirected: false
[JURNAL EDIT] HTML response: <!DOCTYPE html>...
```

#### B. Backend Logs (Server)
```bash
# Location: writable/logs/log-2026-01-11.log
# Look for lines with [JURNAL UPDATE]

# On Windows PowerShell:
Get-Content writable/logs/log-*.log | Select-String "JURNAL UPDATE"

# Or open file manually and search for "JURNAL UPDATE"
```

**Example:**
```
INFO - [JURNAL UPDATE] Started - ID: 123
INFO - [JURNAL UPDATE] POST data: {"_method":"PUT","kegiatan_pembelajaran":"..."}
INFO - [JURNAL UPDATE] Validation passed
INFO - [JURNAL UPDATE] Jurnal found: {"id":123,...}
INFO - [JURNAL UPDATE] File check - isValid: yes
INFO - [JURNAL UPDATE] Processing file upload - Name: captured_photo.jpg, Size: 45678, Type: image/jpeg
ERROR - [JURNAL UPDATE] File validation failed: ...
```

---

## 🔍 Common Issues & Solutions

### Issue 1: File Not Valid
**Log:**
```
[JURNAL UPDATE] File check - isValid: no
[JURNAL UPDATE] File not valid or already moved: ...
```

**Cause:** File upload failed before reaching controller
**Solution:** Check CSRF token, check max upload size in php.ini

---

### Issue 2: MIME Type Validation Failed
**Log:**
```
[JURNAL UPDATE] File validation failed: Tipe file tidak diizinkan
```

**Cause:** Captured image has unexpected MIME type
**Solution:** Add more MIME types or check blob creation

---

### Issue 3: File Move Failed
**Log:**
```
[JURNAL UPDATE] Failed to upload foto: ... (exception)
```

**Cause:** Permission issue or directory doesn't exist
**Solution:** 
```bash
# Check directory exists
Test-Path writable/uploads/jurnal

# Check permissions (should be writable)
```

---

### Issue 4: Model Update Failed
**Log:**
```
[JURNAL UPDATE] Update result: failed
[JURNAL UPDATE] Model update failed: {errors}
```

**Cause:** Database constraint violation or validation rule failure
**Solution:** Check model validation rules, check database constraints

---

### Issue 5: Database Exception
**Log:**
```
[JURNAL UPDATE] Exception occurred: SQLSTATE[...]
```

**Cause:** Database error (duplicate, foreign key, etc)
**Solution:** Check database structure, check foreign key constraints

---

## 📋 Checklist Before Reporting

Before sharing logs, verify:

- [ ] Browser console is open (F12)
- [ ] Captured photo successfully (preview shows)
- [ ] Clicked "Update Jurnal" button
- [ ] Error message appeared
- [ ] Copied **ALL** `[JURNAL EDIT]` logs from console
- [ ] Found and copied **ALL** `[JURNAL UPDATE]` logs from server
- [ ] Checked if foto file was created in `writable/uploads/jurnal/`
- [ ] Checked file permissions on `writable/uploads/jurnal/`

---

## 📤 How to Share Logs

### Format:
```markdown
## Frontend Logs (Browser Console)
```
[JURNAL EDIT] Submitting form...
[JURNAL EDIT] FormData entries: ...
[JURNAL EDIT] Response status: ...
...
```

## Backend Logs (Server)
```
INFO - [JURNAL UPDATE] Started - ID: ...
INFO - [JURNAL UPDATE] POST data: ...
...
```

## Additional Info
- Browser: Chrome 120.0.6099.130
- PHP Version: 8.1.10
- OS: Windows 10
- File exists in writable/uploads/jurnal/: Yes/No
```

---

## 🔧 Quick Fixes to Try

### Fix 1: Increase Upload Limits
**File:** `php.ini`
```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 60
```

### Fix 2: Check Directory Permissions
```bash
# Windows PowerShell
icacls writable\uploads\jurnal

# Should show: BUILTIN\IIS_IUSRS:(OI)(CI)(M)
```

### Fix 3: Clear Cache
```bash
php spark cache:clear
```

### Fix 4: Test with Regular Upload First
Instead of camera capture, try uploading a file first to isolate the issue:
1. Use "Ganti dengan Upload" button
2. Select a small image file (< 1MB)
3. Click "Update Jurnal"
4. Check if it works

If regular upload works but camera doesn't → Issue is with blob handling
If both fail → Issue is with update logic or permissions

---

## 💡 Next Steps

1. ✅ **Run the test** with instructions above
2. ✅ **Collect both logs** (frontend + backend)
3. ✅ **Share logs** here in the format above
4. 🔍 **I will analyze** and provide targeted fix
5. ✅ **Apply fix** and verify

---

## 📞 Need Help?

If you're stuck or logs are not appearing:

1. **Logs not in console?**
   - Hard refresh (Ctrl+Shift+R)
   - Clear cache
   - Try incognito mode

2. **Server logs empty?**
   - Check if writable/logs/ directory is writable
   - Check ENVIRONMENT is set to 'development' in .env
   - Check if error logging is enabled

3. **Error but no logs?**
   - PHP might be crashing before logging
   - Check PHP error log (not CI4 log)
   - Check web server error log (Apache/Nginx)

---

**Prepared by:** Rovo Dev  
**Date:** 2026-01-11  
**Version:** 1.0
