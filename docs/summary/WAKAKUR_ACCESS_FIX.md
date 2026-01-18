# Wakakur Access Fix - Enable Teaching Features

## Problem
User dengan role `wakakur` tidak bisa mengakses fitur absensi, jurnal, dan jadwal mengajar meskipun mereka memiliki jadwal mengajar seperti `guru_mapel`. Wakakur diredirect ke halaman `access-denied` saat mencoba akses route `/guru/absensi`.

## Root Cause
Route group `/guru/*` hanya memiliki filter `role:guru_mapel`, sehingga user dengan role `wakakur` tidak bisa mengakses fitur-fitur guru meskipun mereka juga mengajar.

## Analysis

### Wakakur User Details
```
User ID: 930
Username: guru1
Role: wakakur
Guru ID: 120
Nama Lengkap: Marlina, S.Pd, Gr
Jumlah Jadwal Mengajar: 3

Jadwal Mengajar:
  Senin | 08:00:00-11:15:00 | X-AT | Bahasa Inggris
  Senin | 12:45:00-14:15:00 | XII-DKV | Bahasa Inggris
  Selasa | 08:46:00-12:00:00 | X-MPLB 2 | Bahasa Inggris
```

**Kesimpulan**: Wakakur adalah **dual role** - mereka memiliki tugas administratif Wakakur DAN tugas mengajar seperti guru biasa.

## Solution

### 1. Created Diagnostic Command
File: `app/Commands/CheckWakakurSchedule.php`

Command untuk mengecek apakah Wakakur memiliki jadwal mengajar:
```bash
php spark wakakur:check-schedule
```

Output menunjukkan:
- User ID dan detail guru
- Jumlah jadwal mengajar
- Detail jadwal per hari
- Rekomendasi apakah perlu akses route guru

### 2. Updated Routes Configuration
File: `app/Config/Routes.php`

**Changed:** Menambahkan `wakakur` ke filter role di semua route Guru

#### Before:
```php
// Guru Routes
$routes->group('guru', ['filter' => 'auth'], function ($routes) {
    $routes->get('absensi', 'Guru\AbsensiController::index', ['filter' => 'role:guru_mapel']);
    $routes->get('jadwal', 'Guru\JadwalController::index', ['filter' => 'role:guru_mapel']);
    $routes->get('jurnal', 'Guru\JurnalController::index', ['filter' => 'role:guru_mapel']);
    // ... semua route lainnya
});
```

#### After:
```php
// Guru Routes (accessible by guru_mapel and wakakur who teach)
$routes->group('guru', ['filter' => 'auth'], function ($routes) {
    $routes->get('absensi', 'Guru\AbsensiController::index', ['filter' => 'role:guru_mapel,wakakur']);
    $routes->get('jadwal', 'Guru\JadwalController::index', ['filter' => 'role:guru_mapel,wakakur']);
    $routes->get('jurnal', 'Guru\JurnalController::index', ['filter' => 'role:guru_mapel,wakakur']);
    // ... semua route dengan filter yang sama
});
```

### 3. Routes Updated

**Total routes yang diupdate**: 20 routes dalam group `/guru/*`

#### Dashboard Routes (2):
- ✅ `GET /guru/dashboard`
- ✅ `POST /guru/dashboard/quick-action`

#### Jadwal Routes (1):
- ✅ `GET /guru/jadwal`

#### Absensi Routes (9):
- ✅ `GET /guru/absensi`
- ✅ `GET /guru/absensi/kelas/(:num)`
- ✅ `GET /guru/absensi/tambah`
- ✅ `POST /guru/absensi/simpan`
- ✅ `GET /guru/absensi/show/(:num)`
- ✅ `GET /guru/absensi/edit/(:num)`
- ✅ `POST /guru/absensi/update/(:num)`
- ✅ `GET /guru/absensi/delete/(:num)`
- ✅ `GET /guru/absensi/print/(:num)`

#### AJAX Routes for Absensi (3):
- ✅ `GET /guru/absensi/getSiswaByKelas`
- ✅ `GET /guru/absensi/getJadwalByHari`
- ✅ `GET /guru/absensi/getNextPertemuanByJadwal`

#### Jurnal Routes (8):
- ✅ `GET /guru/jurnal`
- ✅ `GET /guru/jurnal/preview/(:num)/(:num)`
- ✅ `GET /guru/jurnal/tambah/(:num)`
- ✅ `POST /guru/jurnal/simpan`
- ✅ `GET /guru/jurnal/show/(:num)`
- ✅ `GET /guru/jurnal/print/(:num)`
- ✅ `GET /guru/jurnal/edit/(:num)`
- ✅ `PUT /guru/jurnal/update/(:num)`

#### Laporan Routes (2):
- ✅ `GET /guru/laporan`
- ✅ `GET /guru/laporan/print`

## Benefits

### For Wakakur Users:
1. **Full Teaching Access**: Bisa mengisi absensi untuk kelas yang mereka ajar
2. **Jurnal KBM**: Bisa membuat dan mengedit jurnal kegiatan belajar mengajar
3. **Jadwal**: Bisa melihat jadwal mengajar mereka
4. **Laporan**: Bisa mencetak laporan absensi untuk kelas yang mereka ajar
5. **Dashboard Guru**: Akses ke dashboard guru dengan quick actions

### For System:
1. **Konsistensi**: User yang mengajar dapat mengakses fitur mengajar
2. **No Duplication**: Tidak perlu duplikasi controller Wakakur untuk fitur guru
3. **Maintainability**: Satu controller untuk semua guru (termasuk wakakur)
4. **Role Flexibility**: Support dual-role users

## Role Access Matrix

| Feature | admin | guru_mapel | wali_kelas | wakakur | siswa |
|---------|-------|------------|------------|---------|-------|
| Admin Dashboard | ✅ | ❌ | ❌ | ❌ | ❌ |
| Guru Dashboard | ❌ | ✅ | ❌ | ✅ | ❌ |
| Isi Absensi | ❌ | ✅ | ❌ | ✅ | ❌ |
| Jurnal KBM | ❌ | ✅ | ❌ | ✅ | ❌ |
| Jadwal Mengajar | ❌ | ✅ | ❌ | ✅ | ❌ |
| Wakakur Dashboard | ❌ | ❌ | ❌ | ✅ | ❌ |
| Laporan Detail | ✅ | ❌ | ❌ | ✅ | ❌ |
| Kelola Izin (Wali) | ❌ | ❌ | ✅ | ✅ | ❌ |

## Technical Details

### RoleFilter Behavior
File: `app/Filters/RoleFilter.php`

Filter menerima multiple roles dipisahkan dengan koma:
```php
['filter' => 'role:guru_mapel,wakakur']
```

Implementasi:
```php
public function before(RequestInterface $request, $arguments = null)
{
    $userRole = session()->get('role');
    
    // Check if user role is in allowed roles
    if (!in_array($userRole, $arguments)) {
        return redirect()->to('/access-denied');
    }
    return $request;
}
```

### Route Group Structure
```
/guru
├── dashboard (guru_mapel, wakakur)
├── jadwal (guru_mapel, wakakur)
├── absensi/* (guru_mapel, wakakur)
├── jurnal/* (guru_mapel, wakakur)
└── laporan/* (guru_mapel, wakakur)

/wakakur
├── dashboard (wakakur only)
├── laporan (wakakur only - school-wide)
├── siswa (wakakur only)
└── izin (wakakur only)
```

## Testing Checklist

### Manual Testing:
- ✅ Login as Wakakur user
- ✅ Access `/guru/dashboard` - Should work
- ✅ Access `/guru/absensi` - Should work
- ✅ Access `/guru/absensi/tambah` - Should work
- ✅ Fill absensi for their class - Should work
- ✅ Access `/guru/jurnal` - Should work
- ✅ Create jurnal entry - Should work
- ✅ Access `/wakakur/dashboard` - Should still work
- ✅ Access `/wakakur/laporan` - Should still work

### Negative Testing:
- ✅ guru_mapel cannot access `/wakakur/*` routes
- ✅ wakakur without teaching schedule can still access routes (safe)
- ✅ siswa cannot access `/guru/*` routes
- ✅ siswa cannot access `/wakakur/*` routes

## Files Modified

1. ✅ `app/Config/Routes.php` - Added wakakur to guru routes filter
2. ✅ `app/Commands/CheckWakakurSchedule.php` - Created diagnostic tool
3. ✅ `docs/summary/WAKAKUR_ACCESS_FIX.md` - This documentation

## Future Considerations

### 1. Dynamic Role Assignment
Consider implementing dynamic role checking based on actual teaching schedule:
```php
if ($user->role === 'wakakur' && $user->hasTeachingSchedule()) {
    // Grant access to guru features
}
```

### 2. Role Hierarchy
Implement role hierarchy system:
```php
'wakakur' => ['admin_privileges' => true, 'can_teach' => true]
'guru_mapel' => ['can_teach' => true]
'wali_kelas' => ['can_manage_class' => true]
```

### 3. Permission System
Move from role-based to permission-based access control:
```php
$user->can('fill.attendance')
$user->can('view.school_reports')
```

## Migration Notes

### No Breaking Changes:
- ✅ Existing `guru_mapel` users unaffected
- ✅ Wakakur routes still work as before
- ✅ Additional access granted to Wakakur users only

### Database Changes:
- ❌ No database changes required
- ❌ No migration needed

### Backwards Compatibility:
- ✅ Fully backwards compatible
- ✅ No changes to existing functionality
- ✅ Only adds new access permissions

## Conclusion

✅ **Problem Solved**: Wakakur users can now access teaching features
✅ **Dual Role Support**: Wakakur can function as both administrator and teacher
✅ **No Duplication**: Uses existing Guru controllers
✅ **Maintainable**: Simple role filter update
✅ **Tested**: Verified with actual Wakakur user data

Wakakur users with teaching schedules now have full access to:
- Dashboard Guru
- Input Absensi
- Jurnal KBM
- Jadwal Mengajar
- Laporan Guru

Plus their existing Wakakur features:
- Dashboard Wakakur
- Laporan Detail Sekolah
- Kelola Siswa
- Kelola Izin

## Date Fixed
January 19, 2026
