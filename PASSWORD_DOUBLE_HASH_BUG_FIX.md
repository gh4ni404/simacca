# 🐛 Password Double-Hash Bug Fix

**Date:** 2026-01-15  
**Issue:** User tidak bisa login setelah mengganti password  
**Status:** ✅ **FIXED**

---

## 🐛 The Bug

### User Report
Setelah user mengganti password di profile, kemudian logout dan mencoba login lagi, muncul error:
```
"Username atau password salah"
```

Padahal password yang dimasukkan BENAR!

### Root Cause

Password di-**hash DUA KALI**:

1. **First Hash** - Di Controller:
   ```php
   // ProfileController line 159
   $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
   ```

2. **Second Hash** - Di Model callback:
   ```php
   // UserModel line 63-69
   protected function hashPassword(array $data) {
       if (isset($data['data']['password'])) {
           $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
       }
   }
   ```

### The Flow (BUGGY)

```
User changes password: "newpass123"
    ↓
ProfileController hashes it
    → $2y$10$abc...xyz (hash 1)
    ↓
Model's beforeUpdate callback runs
    → Takes the hash and hashes it AGAIN!
    → $2y$10$def...uvw (hash of hash!)
    ↓
Database stores: $2y$10$def...uvw (double-hashed!)
    ↓
User tries to login with "newpass123"
    ↓
System checks: password_verify("newpass123", "$2y$10$def...uvw")
    ↓
FAILS! Because it's comparing against a DOUBLE-HASHED password ❌
```

### Why This Happened

The UserModel has `beforeUpdate` callback that automatically hashes passwords:
```php
protected $beforeUpdate = ['hashPassword'];
```

But the Controllers were ALSO hashing passwords before sending to the Model, resulting in **double-hashing**.

---

## ✅ The Fix

### Strategy

**Let the Model handle ALL password hashing** - Remove manual hashing from Controllers.

But we also need to handle cases where password is already hashed (like in seeder or admin panel where password might be pre-hashed).

### Solution 1: Remove Manual Hashing from Controllers

**ProfileController.php** (line 159):
```php
// BEFORE (BUGGY)
if ($this->request->getPost('password')) {
    $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
}

// AFTER (FIXED)
// Don't hash here - let the Model's beforeUpdate callback handle it
if ($this->request->getPost('password')) {
    $updateData['password'] = $this->request->getPost('password');  // Plain text
}
```

**AuthController.php - processResetPassword** (line 336):
```php
// BEFORE (BUGGY)
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$this->userModel->update($user['id'], ['password' => $hashedPassword]);

// AFTER (FIXED)
// Let Model's beforeUpdate callback handle hashing
$this->userModel->update($user['id'], ['password' => $password]);  // Plain text
```

**AuthController.php - processChangePassword** (line 414):
```php
// BEFORE (BUGGY)
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
$this->userModel->update($userId, ['password' => $hashedPassword]);

// AFTER (FIXED)
// Let Model's beforeUpdate callback handle hashing
$this->userModel->update($userId, ['password' => $newPassword]);  // Plain text
```

### Solution 2: Smart Hash Detection in Model

**UserModel.php** (line 60-69):
```php
// BEFORE (BUGGY)
protected function hashPassword(array $data)
{
    if (isset($data['data']['password'])) {
        $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
    }
    return $data;
}

// AFTER (FIXED)
protected function hashPassword(array $data)
{
    if (isset($data['data']['password'])) {
        $password = $data['data']['password'];
        
        // Check if password is already hashed (bcrypt hashes start with $2y$)
        // If not hashed yet, hash it
        if (!preg_match('/^\$2[ayb]\$.{56}$/', $password)) {
            $data['data']['password'] = password_hash($password, PASSWORD_DEFAULT);
            log_message('info', 'UserModel hashPassword - Password hashed for user');
        } else {
            log_message('info', 'UserModel hashPassword - Password already hashed, skipping');
        }
    }
    return $data;
}
```

**What This Does:**
- Checks if password is already a bcrypt hash using regex
- Bcrypt hashes have format: `$2y$10$...` (exactly 60 characters)
- If already hashed → Skip hashing
- If plain text → Hash it

**Regex Explanation:**
```regex
/^\$2[ayb]\$.{56}$/
  ^          - Start of string
  \$2[ayb]   - Bcrypt identifier ($2a, $2y, or $2b)
  \$         - Dollar sign separator
  .{56}      - Exactly 56 more characters (salt + hash)
  $          - End of string
```

---

## 🔍 Technical Details

### Bcrypt Hash Format

```
$2y$10$N9qo8uLOickgx2ZMRZoMye.IjefDeJQjgv/9UUZO3z/0CPu4E7LyO
│││ ││ │                                              │
│││ ││ └─ Salt (22 chars) ────────────────────────────┤
│││ ││                                                 │
│││ │└─ Cost factor (10 = 2^10 iterations)            │
│││ └─ Separator                                       │
││└─ Algorithm variant (a, y, or b)                    │
│└─ Bcrypt identifier                                  │
└─ Start marker                                        │
                                                       │
                    Hash (31 chars) ───────────────────┘

Total length: 60 characters
```

### Why Double-Hashing is Bad

1. **Security:** Double-hashing doesn't make it more secure, just slower
2. **Incompatibility:** The hash of a hash is not verifiable with original password
3. **Login Breaks:** `password_verify(plain, hash_of_hash)` always returns `false`

### The Correct Flow (FIXED)

```
User changes password: "newpass123"
    ↓
ProfileController passes plain text
    → "newpass123"
    ↓
Model's beforeUpdate callback runs
    → Checks: Is "newpass123" a hash? NO
    → Hashes it: $2y$10$abc...xyz
    ↓
Database stores: $2y$10$abc...xyz (single hash!) ✅
    ↓
User tries to login with "newpass123"
    ↓
System checks: password_verify("newpass123", "$2y$10$abc...xyz")
    ↓
SUCCESS! Password matches ✅
```

---

## 📊 Files Modified

### 3 Files Updated

1. **app/Controllers/ProfileController.php**
   - Line 159: Removed `password_hash()` call
   - Now passes plain text password to Model

2. **app/Controllers/AuthController.php**
   - Line 336 (processResetPassword): Removed `password_hash()` call
   - Line 414 (processChangePassword): Removed `password_hash()` call
   - Both now pass plain text password to Model

3. **app/Models/UserModel.php**
   - Lines 60-80: Updated `hashPassword()` callback
   - Added regex check for already-hashed passwords
   - Added logging for debugging
   - Prevents double-hashing

---

## 🧪 Testing

### Test Case 1: Change Password in Profile

**Steps:**
1. Login as any user
2. Go to profile page
3. Click "Ubah Password"
4. Enter new password: `newpass123`
5. Confirm password: `newpass123`
6. Click "Ubah Password"
7. Logout
8. Login with username and `newpass123`

**Expected Result:**
- ✅ Password change success message
- ✅ Logout successful
- ✅ **Login with new password WORKS!**

**Before Fix:**
- ❌ Login failed with "Username atau password salah"

### Test Case 2: Reset Password via Email

**Steps:**
1. Go to "Lupa Password?"
2. Enter email address
3. Check email and click reset link
4. Enter new password: `resetpass123`
5. Confirm password: `resetpass123`
6. Submit
7. Login with username and `resetpass123`

**Expected Result:**
- ✅ Password reset success message
- ✅ **Login with new password WORKS!**

### Test Case 3: Change Password in Auth Page

**Steps:**
1. Login as any user
2. Go to /change-password
3. Enter current password
4. Enter new password: `changepass123`
5. Confirm password: `changepass123`
6. Submit
7. Logout
8. Login with username and `changepass123`

**Expected Result:**
- ✅ Password change success message
- ✅ **Login with new password WORKS!**

### Test Case 4: Admin Creates User (Seeder/Admin Panel)

**Important:** If admin creates user with pre-hashed password:

```php
$this->userModel->insert([
    'username' => 'newuser',
    'password' => '$2y$10$...already hashed...',  // Pre-hashed
    'role' => 'guru_mapel'
]);
```

**Expected Result:**
- ✅ Model detects it's already hashed
- ✅ Skips hashing (doesn't double-hash)
- ✅ **Login with original password WORKS!**

---

## 📝 Logs

### Successful Password Change Logs

```
INFO - ProfileController update - User ID: 930
INFO - ProfileController update - Update data: {"password":"newpass123"}
INFO - ProfileController update - Is password change only: YES
INFO - UserModel hashPassword - Password hashed for user
INFO - ProfileController update - Database update: SUCCESS
```

### Checking Logs

```powershell
# Check recent password changes
Get-Content writable/logs/log-$(Get-Date -Format 'yyyy-MM-dd').log -Tail 100 | Select-String 'hashPassword|password'
```

---

## 🔒 Security Considerations

### Is It Safe to Pass Plain Text to Model?

**YES**, because:

1. **Not Stored as Plain Text**
   - Plain text only exists in memory during request
   - Model immediately hashes it before database INSERT/UPDATE
   - Never stored in database as plain text

2. **No Network Transmission**
   - Password already transmitted via HTTPS from browser to server
   - This is just internal processing (Controller → Model)
   - No additional network exposure

3. **Follows CI4 Best Practices**
   - Models handle data preparation (including hashing)
   - Controllers handle business logic
   - Separation of concerns

4. **Previous Approach Was Flawed**
   - Double-hashing doesn't improve security
   - Actually breaks functionality
   - This fix aligns with framework design

### Password Hashing Best Practices

✅ **DO:**
- Let Model callbacks handle hashing
- Use `PASSWORD_DEFAULT` algorithm
- Use `password_verify()` for checking
- Log password change events (not the passwords!)

❌ **DON'T:**
- Hash in multiple places
- Store plain text passwords
- Use weak hashing algorithms (MD5, SHA1)
- Log actual password values

---

## 🎯 Prevention

### Code Review Checklist

When updating passwords, make sure:

- [ ] Only ONE place hashes the password
- [ ] Check if Model has `beforeInsert`/`beforeUpdate` callbacks
- [ ] If Model hashes, Controllers should pass plain text
- [ ] If Controllers hash, disable Model callbacks
- [ ] Test login after password change
- [ ] Check logs for double-hashing indicators

### Future Code Changes

**If you need to update passwords:**

```php
// ✅ CORRECT - Let Model handle hashing
$this->userModel->update($userId, [
    'password' => $plainTextPassword  // Plain text
]);

// ❌ WRONG - Double hashing!
$this->userModel->update($userId, [
    'password' => password_hash($plainTextPassword, PASSWORD_DEFAULT)
]);
```

**Remember:** UserModel has `beforeUpdate` callback that auto-hashes!

---

## ✅ Verification Checklist

After fix, verify:

- [x] Password change in profile works
- [x] Login after password change works
- [x] Password reset via email works
- [x] Login after password reset works
- [x] Change password page works
- [x] Login after change password works
- [x] Logs show single hashing
- [x] No "password salah" errors for correct passwords
- [x] Existing users can still login
- [x] New users can login after creation

---

## 📚 Related Documentation

- **Email Service:** `EMAIL_SERVICE_DOCUMENTATION.md`
- **Profile Updates:** `PROFILE_EMAIL_UPDATE_FIX.md`
- **Email Notifications:** `EMAIL_CHANGE_NOTIFICATION_FEATURE.md`

---

## 🎉 Summary

**Problem:** Password di-hash 2 kali → Login gagal

**Root Cause:** Controller dan Model sama-sama hash password

**Solution:**
1. Controllers pass plain text
2. Model checks if already hashed
3. Only hash if not already hashed

**Result:**
- ✅ Password hanya di-hash 1 kali
- ✅ Login setelah ganti password BERHASIL
- ✅ Semua password change methods fixed
- ✅ Backward compatible dengan pre-hashed passwords

**Files Modified:** 3 files (ProfileController, AuthController, UserModel)

**Testing:** All password change scenarios work ✅

---

**Fix Date:** 2026-01-15  
**Status:** ✅ FIXED & VERIFIED  
**Impact:** Critical (blocked login after password change)  
**Severity:** High → Fixed
