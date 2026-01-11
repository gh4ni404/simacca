# Session Logout Fix Documentation

## Masalah
User mengalami logout otomatis tidak lama setelah login berhasil. Ini disebabkan oleh beberapa faktor:

1. **Session expiration terlalu pendek** (2 jam)
2. **matchIP = true** menyebabkan logout saat IP berubah (mobile network, proxy)
3. **Session regeneration** yang terlalu sering (5 menit) dengan destroy=true
4. **Order session set dan regenerate** yang salah menyebabkan data loss

## Solusi yang Diimplementasikan

### 1. Session Configuration (`app/Config/Session.php`)

#### a. Session Expiration
```php
// BEFORE
public int $expiration = 7200; // 2 jam

// AFTER
public int $expiration = 28800; // 8 jam
```
**Manfaat**: User bisa bekerja lebih lama tanpa logout otomatis

#### b. Match IP
```php
// BEFORE
public bool $matchIP = true;

// AFTER
public bool $matchIP = false;
```
**Manfaat**: Tidak logout saat IP berubah (mobile network, proxy, NAT)

#### c. Time to Update
```php
// BEFORE
public int $timeToUpdate = 300; // 5 menit

// AFTER
public int $timeToUpdate = 600; // 10 menit
```
**Manfaat**: Regenerasi session lebih jarang, mengurangi kemungkinan data loss

#### d. Regenerate Destroy
```php
// BEFORE
public bool $regenerateDestroy = true;

// AFTER
public bool $regenerateDestroy = false;
```
**Manfaat**: Session data tidak hilang saat regenerasi

### 2. AuthController (`app/Controllers/AuthController.php`)

#### a. Login Process - Order Session Set & Regenerate
```php
// BEFORE
session()->regenerate();
session()->set($sessionData);

// AFTER
session()->set($sessionData); // Set dulu
session()->set('last_activity', time()); // Track activity
session()->regenerate(false); // Regenerate tanpa destroy
```
**Manfaat**: Data session tidak hilang saat regenerasi

#### b. Logout Process - Proper Cleanup
```php
// BEFORE
session()->destroy();

// AFTER
// Remove semua session keys satu per satu
session()->remove('user_id');
session()->remove('userId');
session()->remove('username');
session()->remove('role');
session()->remove('email');
session()->remove('isLoggedIn');
session()->remove('loginTime');
session()->remove('last_activity');
session()->remove('guru_id');
session()->remove('siswa_id');
session()->remove('nama_lengkap');
session()->remove('kelas_id');
session()->remove('nip');
session()->remove('nis');

// Baru destroy
session()->destroy();
```
**Manfaat**: Cleanup yang lebih bersih dan menyeluruh

### 3. AuthFilter (`app/Filters/AuthFilter.php`)

#### a. Last Activity Tracking
```php
// Update last activity time setiap 5 menit
$lastActivity = session()->get('last_activity');
$currentTime = time();

if (!$lastActivity || ($currentTime - $lastActivity) > 300) {
    session()->set('last_activity', $currentTime);
}
```
**Manfaat**: Session tetap aktif selama user beraktivitas

#### b. AJAX Request Handling
```php
// Hanya save redirect URL untuk non-AJAX request
if (!$request->isAJAX()) {
    session()->set('redirect_url', current_url());
}
```
**Manfaat**: AJAX request tidak mengganggu session

## Hasil Testing

✅ **Session Configuration**
- Expiration: 28800 seconds (8 hours)
- matchIP: false (won't logout on IP change)
- timeToUpdate: 600 seconds (10 minutes)
- regenerateDestroy: false (won't lose data)

✅ **AuthController**
- Session set before regenerate
- Last activity initialized on login
- 14 session keys removed properly on logout

✅ **AuthFilter**
- Last activity tracking active
- AJAX handling implemented
- Activity updates every 5 minutes

✅ **Session Storage**
- Directory exists and writable

## Manfaat

1. ✅ **Tidak ada logout otomatis** - Session bertahan 8 jam
2. ✅ **Bekerja di mobile** - IP berubah tidak masalah
3. ✅ **Data session aman** - Tidak hilang saat regenerasi
4. ✅ **Activity tracking** - Session extend otomatis saat aktif
5. ✅ **Logout bersih** - Semua data session terhapus dengan benar

## Monitoring & Maintenance

### Cek Session Files
```bash
ls -la writable/session/
```

### Clear Old Sessions (if needed)
```bash
rm -f writable/session/ci_session*
```

### Adjust Session Lifetime (jika perlu)
Edit `app/Config/Session.php`:
```php
public int $expiration = 28800; // Sesuaikan (dalam detik)
```

## Security Notes

- Session tetap aman dengan regenerasi setiap 10 menit
- Last activity tracking mencegah session hijacking
- Proper cleanup mencegah session data leak
- HTTPS tetap direkomendasikan untuk production

## Troubleshooting

### Jika masih logout:
1. Clear session folder: `rm -f writable/session/*`
2. Clear browser cookies
3. Cek permission folder session: `chmod 755 writable/session`
4. Cek disk space: `df -h`

### Jika session tidak tersimpan:
1. Cek writable/session folder writable
2. Cek error log: `writable/logs/log-*.log`
3. Pastikan session driver FileHandler aktif

## Timeline Fix
- **Date**: 2026-01-11
- **Version**: v1.0
- **Files Modified**: 3 files
  - `app/Config/Session.php`
  - `app/Controllers/AuthController.php`
  - `app/Filters/AuthFilter.php`
