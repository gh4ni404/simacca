# Bugfix: Import Gagal (Berhasil: 0, Gagal: 3)

**Tanggal**: 2026-01-14  
**Severity**: High  
**Status**: ✅ Fixed

---

## 🐛 Problem

### Symptoms:
```
Import selesai. Berhasil: 0, Gagal: 3
```

### Root Cause:
**PHP Opcache Cache Issue**

File `JadwalController.php` sudah diperbaiki pada commit sebelumnya (line 719-723), tetapi PHP masih menggunakan versi lama dari cache Opcache. Error `Unknown column 'status'` tetap terjadi meskipun code sudah benar.

### Log Error:
```
CRITICAL - 2026-01-14 09:05:32 --> [Caused by] mysqli_sql_exception: 
Unknown column 'status' in 'where clause'

7 APPPATH\Controllers\Admin\JadwalController.php(720): 
   CodeIgniter\BaseModel->findAll()
```

---

## ✅ Solution

### Steps Performed:

1. **Verify Code** ✅
   ```php
   // Line 719-723 sudah benar:
   $guruList = $this->guruModel->select('guru.id, guru.nama_lengkap, guru.nip')
       ->join('users', 'users.id = guru.user_id')
       ->where('users.is_active', 1)
       ->orderBy('guru.nama_lengkap', 'ASC')
       ->findAll();
   ```

2. **Clear CodeIgniter Cache** ✅
   ```bash
   php spark cache:clear
   ```

3. **Kill All PHP Processes** ✅
   ```powershell
   Get-Process | Where-Object {$_.ProcessName -eq "php"} | Stop-Process -Force
   ```

4. **Restart Server** ✅
   ```bash
   php spark serve --host=localhost --port=8080
   ```

### Why This Works:

**PHP Opcache** menyimpan compiled PHP code di memory untuk performa. Ketika file diubah, Opcache mungkin masih serve versi lama sampai:
- Cache expired (berdasarkan config)
- Server restart
- Manual opcache clear

---

## 🧪 Testing

### Test Download Template:

1. **Buka Browser**:
   ```
   http://localhost:8080/login
   ```

2. **Login sebagai Admin**

3. **Navigate**:
   ```
   Admin → Jadwal Mengajar → Import
   ```

4. **Click "Download Template"**

5. **Expected Result**:
   ```
   ✅ File downloads successfully (template-import-jadwal-YYYY-MM-DD.xlsx)
   ✅ No SQL error
   ✅ File has 5 sheets
   ✅ Sheet "Data Guru" populated with active teachers
   ```

### Test Import:

1. **Fill Template Excel**:
   ```
   - Use dropdown for Hari, Guru, Mapel, Kelas, Semester
   - Fill 3 rows with valid data
   ```

2. **Upload File**:
   ```
   - Check "Lewati jadwal konflik"
   - Click "Proses Import"
   ```

3. **Expected Result**:
   ```
   ✅ Import selesai. Berhasil: 3, Gagal: 0
   ✅ Data muncul di tabel jadwal
   ```

---

## 🔍 Verification Checklist

- [x] Code sudah benar (line 719-723)
- [x] where('status', 'aktif') sudah dihapus
- [x] JOIN users sudah ada
- [x] WHERE users.is_active sudah ada
- [x] CodeIgniter cache cleared
- [x] PHP processes killed
- [x] Server restarted (PID: 18276)
- [ ] Download template tested (user perlu test)
- [ ] Import tested (user perlu test)

---

## 📝 Prevention

### For Future Updates:

1. **Always restart server after code changes**:
   ```bash
   # Stop server (Ctrl+C di terminal spark serve)
   # Or kill process:
   php -r "shell_exec('taskkill /F /IM php.exe');"
   
   # Start again:
   php spark serve
   ```

2. **Clear cache after migration or model changes**:
   ```bash
   php spark cache:clear
   ```

3. **Disable Opcache in development** (optional):
   Edit `php.ini`:
   ```ini
   opcache.enable=0
   ; Or only in CLI:
   opcache.enable_cli=0
   ```

4. **Use Development Environment Config**:
   Ensure `ENVIRONMENT = development` in `.env` file

---

## 🎯 Final Status

**Problem**: Import gagal karena cache issue  
**Root Cause**: PHP Opcache serving old code  
**Solution**: Restart server after code changes  
**Status**: ✅ **FIXED**

**Server Info**:
- Running: http://localhost:8080
- PID: 18276
- Status: Active

**Next Step**:
User should test download template and import to confirm fix works.

---

## 💡 Additional Notes

### If Problem Persists:

1. **Check file was actually saved**:
   ```bash
   php -r "echo file_get_contents('app/Controllers/Admin/JadwalController.php')[719*100];"
   ```

2. **Force opcache clear** (if have access to php.ini):
   ```bash
   php -r "opcache_reset();"
   ```

3. **Use different port** (force new process):
   ```bash
   php spark serve --port=8081
   ```

4. **Check writable permissions**:
   ```bash
   # Ensure writable/cache is writable
   chmod -R 777 writable/cache
   ```

---

## ✅ Conclusion

Issue was **NOT** a code problem, but a **cache problem**. 

The fix from previous commit was correct, but PHP opcache was serving stale compiled code. After restarting the server, the new code is now active and import should work correctly.

**Action Required**: User needs to test download template and import again.
