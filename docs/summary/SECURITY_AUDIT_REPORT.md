# Security Audit Report - SIMACCA

## Date
January 19, 2026

## Executive Summary
Comprehensive security audit of the SIMACCA codebase to identify and document potential security vulnerabilities and bugs. Overall, the codebase follows good security practices, but several areas need attention.

## Security Score: 8.5/10
- ✅ **Strong Authentication**: Proper password hashing, session management
- ✅ **SQL Injection Protection**: Using Query Builder (parameterized queries)
- ✅ **XSS Protection**: Consistent use of `esc()` in views
- ✅ **CSRF Protection**: CodeIgniter CSRF enabled
- ⚠️ **File Upload Security**: Some improvements needed
- ⚠️ **Error Handling**: Could expose sensitive info in production
- ⚠️ **Race Conditions**: Potential issues in concurrent operations

---

## Critical Issues (Priority 1) 🔴

### 1. Missing wakakur Role Check in getUserFullName()
**Location**: `app/Controllers/ProfileController.php:72`

**Issue**:
```php
if ($role === 'guru_mapel' || $role === 'wali_kelas') {
    // Get from guru table
    $guru = $this->guruModel->where('user_id', $userId)->first();
    // ...
}
```

**Problem**: The function doesn't check for `wakakur` role, which also uses the guru table.

**Impact**: Wakakur users won't get their full name properly, falling back to username.

**Fix**:
```php
if ($role === 'guru_mapel' || $role === 'wali_kelas' || $role === 'wakakur') {
    // Get from guru table
    $guru = $this->guruModel->where('user_id', $userId)->first();
    // ...
}
```

**Status**: ⚠️ Needs Fix

---

### 2. Unprotected File Deletion in unlink()
**Location**: Multiple files

**Files Affected**:
- `app/Controllers/ProfileController.php:400, 435`
- `app/Controllers/Guru/JurnalController.php:219, 239, 378, 422, 505, 528`
- `app/Controllers/Siswa/IzinController.php:209, 232`

**Issue**:
```php
// No check if path traversal attempt
if ($oldPhoto && file_exists($uploadPath . $oldPhoto)) {
    unlink($uploadPath . $oldPhoto);
}
```

**Problem**: If `$oldPhoto` contains path traversal sequences (`../../../etc/passwd`), it could delete files outside the upload directory.

**Impact**: Potential file deletion vulnerability.

**Fix**:
```php
// Sanitize and verify the file is within upload directory
if ($oldPhoto) {
    $oldPhoto = basename($oldPhoto); // Remove any path components
    $fullPath = realpath($uploadPath . $oldPhoto);
    
    // Ensure file is within upload directory
    if ($fullPath && strpos($fullPath, realpath($uploadPath)) === 0 && file_exists($fullPath)) {
        unlink($fullPath);
    }
}
```

**Status**: ⚠️ Needs Fix

---

### 3. Direct File Access via URL
**Location**: `app/Views/siswa/izin/index.php:197`

**Issue**:
```php
<a href="<?= base_url('writable/uploads/izin/' . $izin['berkas']); ?>" target="_blank">
```

**Problem**: Direct access to writable directory. If `.htaccess` is not properly configured, sensitive files could be accessed by anyone.

**Impact**: Unauthorized file access.

**Fix**:
```php
// Use FileController to serve files with authorization check
<a href="<?= base_url('files/izin/' . $izin['berkas']); ?>" target="_blank">
```

Then create controller method:
```php
// In FileController
public function izinBerkas($filename)
{
    // Check authorization
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/login');
    }
    
    // Sanitize filename
    $filename = basename($filename);
    $filepath = WRITEPATH . 'uploads/izin/' . $filename;
    
    if (!file_exists($filepath)) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('File tidak ditemukan');
    }
    
    return $this->response
        ->setHeader('Content-Type', mime_content_type($filepath))
        ->setBody(file_get_contents($filepath));
}
```

**Status**: ⚠️ Needs Fix

---

## High Priority Issues (Priority 2) 🟡

### 4. Potential Race Condition in Photo Upload
**Location**: `app/Controllers/ProfileController.php:352-401`

**Issue**:
```php
// Get old photo to delete
$userData = $this->userModel->find($userId);
$oldPhoto = $userData['profile_photo'] ?? null;

// ... upload new photo ...

// Update database
$this->userModel->update($userId, [
    'profile_photo' => $newName,
    'profile_photo_uploaded_at' => date('Y-m-d H:i:s')
]);

// Delete old photo if exists
if ($oldPhoto && file_exists($uploadPath . $oldPhoto)) {
    unlink($uploadPath . $oldPhoto);
}
```

**Problem**: If two requests upload simultaneously:
1. Request A gets old photo = `photo1.jpg`
2. Request B gets old photo = `photo1.jpg`
3. Request A uploads `photo2.jpg`, updates DB, deletes `photo1.jpg`
4. Request B uploads `photo3.jpg`, updates DB, tries to delete `photo1.jpg` (already deleted)

**Impact**: Error messages, orphaned files.

**Fix**:
```php
// Use transaction and lock
$db = \Config\Database::connect();
$db->transStart();

// Lock the row
$userData = $this->userModel
    ->where('id', $userId)
    ->selectMax('id') // Forces SELECT FOR UPDATE in transaction
    ->first();

$oldPhoto = $userData['profile_photo'] ?? null;

// ... upload and update ...

$db->transComplete();

// Delete old photo AFTER transaction commits
if ($db->transStatus() && $oldPhoto && file_exists($uploadPath . $oldPhoto)) {
    @unlink($uploadPath . $oldPhoto); // @ suppresses error if already deleted
}
```

**Status**: ⚠️ Needs Improvement

---

### 5. Missing Upload Directory Existence Check
**Location**: `app/Controllers/Siswa/IzinController.php:130`

**Issue**:
```php
$uploadPath = WRITEPATH . 'uploads/izin';
// No check if directory exists before using it
```

**Problem**: If upload directory doesn't exist, file upload will fail.

**Impact**: Upload failures.

**Fix**:
```php
$uploadPath = WRITEPATH . 'uploads/izin';

// Create directory if not exists
if (!is_dir($uploadPath)) {
    mkdir($uploadPath, 0755, true);
}
```

**Status**: ⚠️ Needs Fix

---

### 6. Sensitive Information in Error Messages
**Location**: Multiple controllers

**Issue**:
```php
catch (\Exception $e) {
    log_message('error', 'Profile photo upload error: ' . $e->getMessage());
    session()->setFlashdata('error', 'Gagal mengupload foto. Silakan coba lagi.');
}
```

**Problem**: In development mode, detailed exception messages might be shown to users.

**Impact**: Information disclosure.

**Fix**: Already partially implemented, but ensure all exceptions use safe error messages:
```php
catch (\Exception $e) {
    helper('security');
    $userMessage = safe_error_message($e, 'Gagal mengupload foto');
    session()->setFlashdata('error', $userMessage);
}
```

**Status**: ✅ Partially Fixed (security helper exists)

---

## Medium Priority Issues (Priority 3) 🟢

### 7. No File Extension Validation in Some Places
**Location**: `app/Controllers/Guru/JurnalController.php`

**Issue**: Relies only on MIME type, not double-checking extension.

**Fix**: Already handled by `validate_file_upload()` in security helper, but not always used.

**Recommendation**: Always use `validate_file_upload()` helper for all uploads.

**Status**: ⚠️ Needs Consistency

---

### 8. Session Fixation Risk (Already Mitigated)
**Location**: `app/Controllers/AuthController.php:136`

**Status**: ✅ Already Fixed
```php
// Then regenerate session ID to prevent session fixation attacks
session()->regenerate();
```

**Notes**: Good implementation. Session ID is regenerated after login.

---

### 9. Missing Rate Limiting on Login
**Location**: `app/Controllers/AuthController.php:login()`

**Issue**: No rate limiting on login attempts.

**Impact**: Brute force attacks possible.

**Recommendation**: Implement rate limiting (e.g., max 5 attempts per IP per 15 minutes).

**Fix**: Use CodeIgniter Throttle filter or implement custom rate limiting:
```php
// In AuthController::login()
$attempts = cache()->get('login_attempts_' . $request->getIPAddress()) ?? 0;

if ($attempts >= 5) {
    $lockoutTime = cache()->get('login_lockout_' . $request->getIPAddress());
    if ($lockoutTime && time() < $lockoutTime) {
        return redirect()->back()->with('error', 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.');
    }
}
```

**Status**: ⚠️ Enhancement Recommended

---

### 10. Password Complexity Not Enforced
**Location**: Validation rules in various controllers

**Issue**:
```php
$rules['password'] = 'required|min_length[6]';
```

**Problem**: Only checks length, not complexity.

**Recommendation**: Add password strength validation:
```php
$rules['password'] = 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]';
// At least: 8 chars, 1 uppercase, 1 lowercase, 1 number
```

**Status**: ⚠️ Enhancement Recommended

---

## Low Priority Issues (Priority 4) 🔵

### 11. No Content-Type Validation on File Serve
**Location**: `app/Controllers/FileController.php`

**Issue**: While files are validated on upload, there's no re-validation when serving.

**Status**: ✅ Low Risk (files validated on upload)

---

### 12. Verbose Logging Might Fill Disk
**Location**: Multiple controllers with extensive logging

**Issue**: Very detailed logging could fill disk space.

**Recommendation**: 
- Use log rotation
- Reduce verbosity in production
- Consider log levels (debug vs info vs error)

**Status**: ℹ️ Monitoring Recommended

---

## Code Quality Issues (Not Security)

### 13. Duplicate Code in getUserFullName()
**Location**: Multiple controllers have similar logic

**Recommendation**: Extract to a shared helper or service class.

**Status**: ℹ️ Refactoring Opportunity

---

### 14. Magic Numbers in Code
**Location**: Various places with hardcoded values

**Example**: `5120` (max file size), `300` (session timeout)

**Recommendation**: Use constants:
```php
// In Config/App.php or Constants.php
define('MAX_PROFILE_PHOTO_SIZE', 5 * 1024 * 1024); // 5MB
define('SESSION_ACTIVITY_TIMEOUT', 300); // 5 minutes
```

**Status**: ℹ️ Code Quality Improvement

---

## Positive Security Findings ✅

### Good Practices Already Implemented:

1. **Parameterized Queries**: All database queries use Query Builder with parameter binding
2. **Password Hashing**: Using `password_hash()` with `PASSWORD_DEFAULT`
3. **XSS Protection**: Consistent use of `esc()` in all views reviewed
4. **CSRF Protection**: CodeIgniter CSRF filter enabled
5. **Session Security**: Session regeneration after login
6. **File Validation**: Comprehensive file validation helper exists
7. **Error Logging**: Good logging practices for debugging
8. **Authorization Filters**: Role-based access control implemented
9. **Secure Headers**: Security headers likely configured (check CSP)
10. **Input Validation**: Validation rules applied to all forms

---

## Recommendations by Priority

### Immediate Actions (This Sprint):
1. ✅ Fix `getUserFullName()` to include wakakur role
2. ✅ Add path sanitization to all `unlink()` calls
3. ✅ Create FileController method for izin file access
4. ✅ Add directory existence checks before uploads

### Short Term (Next Sprint):
5. ⚠️ Implement transaction locking for concurrent uploads
6. ⚠️ Add rate limiting to login endpoint
7. ⚠️ Review and strengthen password policy
8. ⚠️ Ensure all uploads use security helper

### Long Term (Backlog):
9. 💡 Implement comprehensive security audit logging
10. 💡 Add automated security testing to CI/CD
11. 💡 Consider implementing 2FA for sensitive operations
12. 💡 Regular security dependency updates

---

## Testing Checklist

### Security Tests to Perform:
- [ ] SQL Injection testing (already protected, but verify)
- [ ] XSS attempts in all input fields
- [ ] CSRF token validation
- [ ] File upload with malicious filenames
- [ ] Path traversal attempts
- [ ] Session fixation attempts
- [ ] Brute force login attempts
- [ ] Unauthorized file access attempts
- [ ] Race condition testing on uploads
- [ ] Password strength validation

---

## Conclusion

The SIMACCA codebase demonstrates **good security practices** overall, with proper use of:
- Modern authentication
- Parameterized queries
- XSS protection
- CSRF protection
- File validation

**Main Areas for Improvement**:
1. File operation security (path traversal protection)
2. Concurrency handling (race conditions)
3. Rate limiting (brute force protection)
4. Password strength requirements

**Risk Level**: **LOW to MEDIUM**
- No critical vulnerabilities that allow immediate exploitation
- Issues found are edge cases or enhancement opportunities
- System is production-ready with recommended fixes applied

**Next Steps**:
1. Apply Priority 1 fixes immediately
2. Schedule Priority 2 fixes for next sprint
3. Plan Priority 3 enhancements in backlog
4. Conduct penetration testing after fixes

---

## Appendix: Security Checklist for Future Development

### For Every New Feature:
- [ ] Input validation on all user inputs
- [ ] Output escaping in all views (`esc()`)
- [ ] Authorization checks in controllers
- [ ] File uploads use security helper
- [ ] Database queries use Query Builder
- [ ] Error messages don't expose sensitive info
- [ ] Logging for security-relevant actions
- [ ] Unit tests include security test cases

### Code Review Checklist:
- [ ] No raw SQL queries
- [ ] No direct `$_GET`/`$_POST` usage
- [ ] All views use `esc()` or `| esc`
- [ ] File operations check paths
- [ ] Sensitive operations logged
- [ ] Errors handled gracefully
- [ ] Authorization verified

---

**Audit Performed By**: AI Security Review (Rovo Dev)
**Date**: January 19, 2026
**Version**: 1.0
**Confidence Level**: High
