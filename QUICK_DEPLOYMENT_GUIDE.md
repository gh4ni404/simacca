# Quick Deployment Guide - Fitur Guru Pengganti

## 🚀 Langkah Deploy (5 Menit)

### 1. Backup Database
```bash
# Backup database
mysqldump -u root -p nama_database > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Pull/Upload Kode
```bash
# Jika menggunakan Git
git pull origin main

# Atau upload file-file berikut:
# - app/Controllers/Guru/AbsensiController.php
# - app/Models/AbsensiModel.php
# - app/Views/guru/absensi/create.php
# - app/Views/guru/absensi/edit.php
# - app/Views/guru/absensi/show.php
# - app/Database/Migrations/2026-01-11-183700_AddGuruPenggantiToAbsensi.php
```

### 3. Jalankan Migration
```bash
cd /path/to/project
php spark migrate
```

**Expected Output:**
```
Running: 2026-01-11-183700_AddGuruPenggantiToAbsensi
Migrated: 2026-01-11-183700_AddGuruPenggantiToAbsensi
```

### 4. Verifikasi
```bash
# Cek status migration
php spark migrate:status

# Cek struktur tabel
mysql -u root -p
```

```sql
USE nama_database;
DESCRIBE absensi;
-- Pastikan field 'guru_pengganti_id' ada
```

### 5. Test di Browser
1. Login sebagai guru
2. Buka menu Absensi → Tambah Absensi
3. Pastikan dropdown "Guru Pengganti" muncul
4. Isi absensi dengan memilih guru pengganti
5. Lihat detail absensi, pastikan guru pengganti muncul

## ✅ Checklist Deployment

- [ ] Database sudah dibackup
- [ ] Semua file sudah diupload/pull
- [ ] Migration berhasil dijalankan (`php spark migrate`)
- [ ] Field `guru_pengganti_id` ada di tabel `absensi`
- [ ] Foreign key `fk_absensi_guru_pengganti` terbuat
- [ ] Dropdown guru pengganti muncul di form input absensi
- [ ] Dropdown guru pengganti muncul di form edit absensi
- [ ] Informasi guru pengganti muncul di detail absensi
- [ ] Test create absensi dengan guru pengganti → OK
- [ ] Test create absensi tanpa guru pengganti → OK
- [ ] Test edit guru pengganti → OK
- [ ] Laporan admin menampilkan guru pengganti → OK

## 🔧 Troubleshooting Cepat

### Migration Gagal
```bash
# Cek apakah tabel absensi dan guru ada
mysql -u root -p
```
```sql
SHOW TABLES;
DESCRIBE absensi;
DESCRIBE guru;
```

### Dropdown Tidak Muncul
- Clear browser cache (Ctrl+F5)
- Pastikan file view sudah terupdate
- Cek error di browser console (F12)

### Data Guru Pengganti Tidak Muncul
```sql
-- Cek apakah field ada
DESCRIBE absensi;

-- Cek data
SELECT id, tanggal, guru_pengganti_id FROM absensi LIMIT 10;
```

## 🔄 Rollback (Jika Ada Masalah)

### Via CodeIgniter
```bash
php spark migrate:rollback
```

### Manual SQL
```sql
ALTER TABLE absensi DROP FOREIGN KEY fk_absensi_guru_pengganti;
ALTER TABLE absensi DROP COLUMN guru_pengganti_id;
```

### Restore Backup
```bash
mysql -u root -p nama_database < backup_YYYYMMDD_HHMMSS.sql
```

## 📞 Support

Jika ada error:
1. Screenshot error message
2. Cek log: `writable/logs/log-YYYY-MM-DD.log`
3. Cek MySQL error log

---
**Estimasi Waktu**: 5-10 menit  
**Downtime**: Tidak ada (migration non-blocking)  
**Kompatibilitas**: Backward compatible (tidak break existing features)
