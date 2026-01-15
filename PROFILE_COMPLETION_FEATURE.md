# Profile Completion Feature

## Overview
Fitur ini memaksa pengguna untuk melengkapi profil mereka saat pertama kali login. Pengguna yang belum mengubah password, mengisi email, atau mengupload foto profil akan diarahkan ke halaman profil secara otomatis.

## Tujuan
- Meningkatkan keamanan dengan memaksa pengguna mengubah password default
- Memastikan semua pengguna memiliki email yang valid untuk komunikasi
- Mendorong pengguna untuk mengupload foto profil untuk personalisasi

## Komponen Yang Diubah

### 1. Database Migration
**File**: `app/Database/Migrations/2026-01-15-140600_AddProfileTrackingFields.php`

Menambahkan 3 field baru pada tabel `users`:
- `password_changed_at` (DATETIME, NULL) - Timestamp saat password diubah
- `email_changed_at` (DATETIME, NULL) - Timestamp saat email diisi/diubah
- `profile_photo_uploaded_at` (DATETIME, NULL) - Timestamp saat foto profil diupload

```bash
# Jalankan migration untuk menambahkan field
php spark migrate
```

### 2. UserModel
**File**: `app/Models/UserModel.php`

**Perubahan**:
- Menambahkan field baru ke `$allowedFields`
- Menambahkan method `needsProfileCompletion($userId)` untuk mengecek status profil

**Method Baru**:
```php
public function needsProfileCompletion($userId)
{
    // Return true jika salah satu field tracking masih null
    // Artinya user belum lengkapi profil
}
```

### 3. ProfileCompletionFilter
**File**: `app/Filters/ProfileCompletionFilter.php` (NEW)

**Fungsi**:
- Filter middleware yang mengecek apakah user sudah lengkapi profil
- Jika belum, redirect ke halaman `/profile` dengan pesan warning
- Skip pengecekan jika user sudah di halaman profile atau logout

**Logika**:
```php
- Cek apakah user login
- Cek apakah sudah di halaman profile (skip jika ya)
- Cek status profil menggunakan UserModel::needsProfileCompletion()
- Redirect ke /profile jika belum lengkap
```

### 4. ProfileController
**File**: `app/Controllers/ProfileController.php`

**Perubahan pada method `update()`**:
- Saat password diubah: Set `password_changed_at` = sekarang
- Saat email diubah/diisi: Set `email_changed_at` = sekarang
- Sudah ada tracking untuk `profile_photo_uploaded_at` (sudah implement sebelumnya)

**Perubahan pada method `uploadPhoto()`**:
- Update `profile_photo_uploaded_at` saat foto diupload

### 5. Filters Configuration
**File**: `app/Config/Filters.php`

**Perubahan**:
- Menambahkan alias `profile_completion` untuk ProfileCompletionFilter
- Menambahkan filter ke routes yang memerlukan profil lengkap:
  - `admin/*`
  - `guru/*`
  - `walikelas/*`
  - `siswa/*`
  - `dashboard/*`
  - `change-password/*`

**Catatan**: Route `/profile/*` tidak difilter agar user bisa akses halaman profil untuk melengkapinya.

## Flow Diagram

```
┌─────────────────┐
│  User Login     │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────┐
│ AuthFilter                  │
│ (Cek apakah user login)     │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│ ProfileCompletionFilter     │
│ (Cek status profil)         │
└────────┬────────────────────┘
         │
         ├─► Profil Lengkap? ──────► Lanjut ke halaman yang diminta
         │
         └─► Profil Belum Lengkap? ─► Redirect ke /profile
                                       dengan pesan warning
```

## Kriteria Profil Lengkap

User dianggap memiliki profil lengkap jika SEMUA kondisi berikut terpenuhi:

1. ✅ `password_changed_at` IS NOT NULL
   - User sudah pernah mengubah password dari default

2. ✅ `email_changed_at` IS NOT NULL
   - User sudah mengisi/mengubah email

3. ✅ `profile_photo_uploaded_at` IS NOT NULL
   - User sudah mengupload foto profil

## Pesan Warning

Saat user diarahkan ke halaman profil, akan muncul pesan:
> "Lengkapi profil kamu dulu ya! Ganti password, isi email, dan upload foto profil 📝✨"

## Testing

Untuk menguji logika tanpa database:
```bash
php tmp_rovodev_test_profile_completion.php
```

**Test Cases**:
1. ✅ User baru (tidak ada field yang terisi) → Needs Completion
2. ✅ User dengan hanya password diubah → Needs Completion
3. ✅ User dengan password dan email → Needs Completion
4. ✅ User dengan profil lengkap → Complete

## Cara Penggunaan

### Untuk User Baru
1. Login dengan username dan password default
2. Otomatis diarahkan ke halaman profil
3. Lengkapi:
   - Ubah password
   - Isi email
   - Upload foto profil
4. Setelah lengkap, bisa akses semua halaman dashboard

### Untuk User Lama (Existing Users)
User yang sudah ada sebelum fitur ini diimplementasikan:
- Semua field tracking akan NULL
- Akan diarahkan ke halaman profil saat login
- Harus melengkapi profil untuk bisa akses dashboard

**Update Manual untuk User Lama** (optional):
Jika ingin skip requirement untuk user tertentu yang sudah aktif:
```sql
UPDATE users 
SET password_changed_at = NOW(),
    email_changed_at = NOW(),
    profile_photo_uploaded_at = NOW()
WHERE id = [user_id];
```

## Keamanan

1. **Password Security**: Memaksa user mengubah password default meningkatkan keamanan
2. **Email Validation**: Memastikan setiap user punya email valid untuk reset password
3. **Session Management**: Filter berjalan setelah AuthFilter, memastikan hanya user login yang dicek
4. **Skip Profile Page**: User bisa akses halaman profil untuk melengkapi data (tidak infinite loop)

## Troubleshooting

### User terjebak di halaman profil (infinite redirect)?
- Pastikan route `/profile/*` tidak ada dalam filter `profile_completion`
- Cek di `app/Config/Filters.php`, pastikan `/profile/*` tidak masuk dalam array before

### Field tracking tidak terupdate?
- Pastikan migration sudah dijalankan: `php spark migrate`
- Cek `UserModel::$allowedFields` sudah include field baru
- Cek `ProfileController::update()` dan `uploadPhoto()` sudah set timestamp

### User tidak diredirect meskipun profil belum lengkap?
- Cek apakah route tersebut sudah masuk dalam filter `profile_completion` di `app/Config/Filters.php`
- Pastikan `ProfileCompletionFilter` sudah terdaftar di aliases

## Maintenance

### Menambah/Mengurangi Requirement
Edit method `needsProfileCompletion()` di `UserModel.php`:

```php
// Contoh: Hanya require password dan email (tanpa foto)
return empty($user['password_changed_at']) 
    || empty($user['email_changed_at']);
```

### Menonaktifkan Fitur
Comment out filter di `app/Config/Filters.php`:
```php
public array $filters = [
    // 'profile_completion' => [
    //     'before' => [...]
    // ]
];
```

## Version History

- **v1.0.0** (2026-01-15): Initial implementation
  - Added profile tracking fields
  - Created ProfileCompletionFilter
  - Updated ProfileController to track changes
  - Configured routes and filters
