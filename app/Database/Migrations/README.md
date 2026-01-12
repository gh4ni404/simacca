# Database Migrations - SIMACCA

## 📋 Overview
File-file migration ini digunakan untuk membuat dan mengupdate struktur database secara otomatis.

## 🚀 Quick Start

### 1. Konfigurasi Database
Pastikan konfigurasi database sudah benar di file `.env`:
```env
database.default.hostname = localhost
database.default.database = simacca_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

### 2. Jalankan Migrations
```bash
# Jalankan semua migrations
php spark migrate

# Cek status migrations
php spark migrate:status

# Rollback migration terakhir
php spark migrate:rollback

# Rollback semua migrations
php spark migrate:rollback -all
```

### 3. Jalankan Seeders (Optional)
```bash
# Seed admin user
php spark db:seed AdminSeeder

# Seed dummy data untuk testing
php spark db:seed DummyDataSeeder
```

## 📁 Daftar Migrations

### Core Tables (Initial Setup)
Migrations ini membuat struktur tabel utama. **Harus dijalankan berurutan.**

| File | Description | Dependencies |
|------|-------------|--------------|
| `2026-01-06-163017_CreateUsersTable.php` | Tabel users untuk authentication | - |
| `2026-01-06-163050_CreateKelasTable.php` | Tabel kelas | - |
| `2026-01-06-163105_CreateMataPelajaranTable.php` | Tabel mata pelajaran | - |
| `2026-01-06-163119_CreateGuruTable.php` | Tabel guru | users |
| `2026-01-06-163132_CreateSiswaTable.php` | Tabel siswa | users, kelas |
| `2026-01-06-163148_CreateJadwalMengajarTable.php` | Tabel jadwal mengajar | guru, kelas, mata_pelajaran |
| `2026-01-06-163202_CreateAbsensiTable.php` | Tabel absensi (header) | jadwal_mengajar, users |
| `2026-01-06-163214_CreateAbsensiDetailTable.php` | Tabel absensi detail | absensi, siswa |
| `2026-01-06-163229_CreateJurnalKbmTable.php` | Tabel jurnal KBM | absensi |
| `2026-01-06-163357_CreateIzinSiswaTable.php` | Tabel izin siswa | siswa, kelas |

### Updates & Enhancements
Migrations ini menambahkan field baru atau mengupdate struktur tabel existing.

| File | Description | Date Added |
|------|-------------|------------|
| `2026-01-07-051505_UpdateKelasForeignKey.php` | Update foreign key di tabel kelas | 2026-01-07 |
| `2026-01-10-014749_AddNewStatus.php` | Tambah status baru di enum | 2026-01-10 |
| `2026-01-11-142000_AddFotoToJurnalKbm.php` | Tambah field foto dokumentasi | 2026-01-11 |
| `2026-01-11-183700_AddGuruPenggantiToAbsensi.php` | **Guru Pengganti Feature** | 2026-01-11 |

## 📊 Database Schema Overview

### Diagram Hubungan Antar Tabel
```
users (id, username, password, role)
  ↓
  ├─→ guru (id, user_id, nip, nama_lengkap)
  │     ↓
  │     └─→ jadwal_mengajar (id, guru_id, kelas_id, mata_pelajaran_id)
  │           ↓
  │           └─→ absensi (id, jadwal_mengajar_id, tanggal, guru_pengganti_id*)
  │                 ↓
  │                 ├─→ absensi_detail (id, absensi_id, siswa_id, status)
  │                 └─→ jurnal_kbm (id, absensi_id, catatan, foto_dokumentasi*)
  │
  └─→ siswa (id, user_id, nis, nisn, kelas_id)
        ↓
        ├─→ absensi_detail (siswa_id)
        └─→ izin_siswa (id, siswa_id, kelas_id, status_approval)

kelas (id, nama_kelas, wali_kelas_id)
  ↓
  ├─→ siswa (kelas_id)
  ├─→ jadwal_mengajar (kelas_id)
  └─→ izin_siswa (kelas_id)

mata_pelajaran (id, nama_mapel, kkm)
  ↓
  └─→ jadwal_mengajar (mata_pelajaran_id)
```

*Field dengan tanda * adalah field yang ditambahkan via update migrations

## 🔑 Field Penting

### Status Enum Values
- **Absensi Detail**: `hadir`, `sakit`, `izin`, `alpa`
- **Izin Siswa**: `pending`, `approved`, `rejected`

### Foreign Key Constraints
Semua foreign key menggunakan:
- **ON DELETE CASCADE** untuk data dependensi (detail records)
- **ON DELETE SET NULL** untuk data opsional (guru_pengganti_id)
- **ON UPDATE CASCADE** untuk semua foreign keys

### Indexes
- Primary key di semua tabel (auto_increment)
- Foreign key indexes otomatis terbuat
- Unique constraint di field unik (username, nip, nis, nisn)

## 🛠️ Troubleshooting

### Error: "Table already exists"
```bash
# Rollback dan jalankan ulang
php spark migrate:rollback -all
php spark migrate
```

### Error: "Cannot add foreign key constraint"
**Penyebab:** Urutan migration salah atau tabel parent belum ada.

**Solusi:** Pastikan migrations dijalankan berurutan. File migrations sudah diberi timestamp yang benar untuk urutan eksekusi.

### Error: "Unknown column in foreign key"
**Penyebab:** Tabel parent belum memiliki primary key atau column yang direferensikan.

**Solusi:** Jalankan migrations dari awal:
```bash
php spark migrate:rollback -all
php spark migrate
```

### Database Out of Sync
```bash
# Reset database ke kondisi awal
php spark migrate:rollback -all
php spark migrate
php spark db:seed AdminSeeder
php spark db:seed DummyDataSeeder
```

## 📝 Best Practices

### Jangan Edit Migration yang Sudah Dijalankan
Jika perlu mengubah struktur tabel:
1. Buat migration baru untuk perubahan
2. Jangan edit file migration lama
3. Gunakan `php spark make:migration NamaPerubahan`

### Testing Migrations
```bash
# Test di environment development
php spark migrate

# Test rollback
php spark migrate:rollback

# Jalankan ulang
php spark migrate
```

### Backup Database
Sebelum menjalankan migrations di production:
```bash
mysqldump -u username -p database_name > backup_YYYYMMDD.sql
```

## 🔄 Migration Flow

### Development
```bash
1. Edit .env (database config)
2. php spark migrate
3. php spark db:seed AdminSeeder
4. php spark db:seed DummyDataSeeder
5. Test aplikasi
```

### Production
```bash
1. Backup database
2. Pull/Upload kode terbaru
3. php spark migrate:status (cek status)
4. php spark migrate (jalankan migrations baru)
5. Test aplikasi
6. Monitor error logs
```

## 📞 Support

Jika mengalami error saat menjalankan migrations:
1. Cek error message di console
2. Cek log file di `writable/logs/`
3. Pastikan MySQL service berjalan
4. Pastikan user database memiliki privilege yang cukup

## ✅ Checklist Setup

- [ ] Konfigurasi database di `.env`
- [ ] MySQL service running
- [ ] Database `simacca_db` sudah dibuat (atau sesuai config)
- [ ] User database memiliki privilege CREATE, ALTER, DROP
- [ ] Jalankan `php spark migrate`
- [ ] Cek dengan `php spark migrate:status`
- [ ] Jalankan seeder: `php spark db:seed AdminSeeder`
- [ ] Test login dengan user admin
- [ ] (Optional) Jalankan `php spark db:seed DummyDataSeeder`

---

**Version:** 1.1.0  
**Last Updated:** 2026-01-12  
**Total Migrations:** 15 files
