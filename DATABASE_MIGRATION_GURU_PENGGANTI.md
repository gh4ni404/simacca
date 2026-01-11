# Database Migration - Guru Pengganti Feature

## Overview
File migration untuk menambahkan field `guru_pengganti_id` ke tabel `absensi` sudah tersedia di:
```
app/Database/Migrations/2026-01-11-183700_AddGuruPenggantiToAbsensi.php
```

## Migration Details

### Field yang Ditambahkan
- **Nama Field**: `guru_pengganti_id`
- **Tipe**: INT(11) UNSIGNED
- **Nullable**: YES (opsional)
- **Posisi**: Setelah field `created_by`
- **Foreign Key**: References `guru(id)`
  - ON DELETE SET NULL
  - ON UPDATE CASCADE

### SQL yang Dijalankan
```sql
-- Menambahkan kolom
ALTER TABLE `absensi` 
ADD `guru_pengganti_id` INT(11) UNSIGNED NULL 
AFTER `created_by`;

-- Menambahkan foreign key constraint
ALTER TABLE `absensi` 
ADD CONSTRAINT `fk_absensi_guru_pengganti` 
FOREIGN KEY (`guru_pengganti_id`) 
REFERENCES `guru`(`id`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;
```

## Cara Menjalankan Migration

### 1. Cek Status Migration
Untuk melihat migration mana yang sudah dan belum dijalankan:
```bash
php spark migrate:status
```

### 2. Jalankan Migration
Untuk menjalankan semua migration yang belum dijalankan:
```bash
php spark migrate
```

### 3. Jalankan Migration Spesifik (Opsional)
Jika hanya ingin menjalankan migration ini saja:
```bash
php spark migrate -n "App\Database\Migrations" -g default
```

### 4. Rollback Migration (Jika Diperlukan)
Untuk membatalkan migration terakhir:
```bash
php spark migrate:rollback
```

## Verifikasi Database

### Cek Struktur Tabel
Setelah migration dijalankan, verifikasi dengan query:
```sql
DESCRIBE absensi;
```

Output yang diharapkan harus menampilkan field `guru_pengganti_id`:
```
+----------------------+------------------+------+-----+---------+----------------+
| Field                | Type             | Null | Key | Default | Extra          |
+----------------------+------------------+------+-----+---------+----------------+
| id                   | int(11) unsigned | NO   | PRI | NULL    | auto_increment |
| jadwal_mengajar_id   | int(11) unsigned | NO   | MUL | NULL    |                |
| tanggal              | date             | NO   |     | NULL    |                |
| pertemuan_ke         | int(11)          | NO   |     | NULL    |                |
| materi_pembelajaran  | text             | YES  |     | NULL    |                |
| created_by           | int(11) unsigned | NO   | MUL | NULL    |                |
| guru_pengganti_id    | int(11) unsigned | YES  | MUL | NULL    |                |  <-- BARU
| created_at           | datetime         | YES  |     | NULL    |                |
+----------------------+------------------+------+-----+---------+----------------+
```

### Cek Foreign Key Constraint
Verifikasi foreign key sudah terbuat:
```sql
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM 
    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE 
    TABLE_NAME = 'absensi' 
    AND CONSTRAINT_NAME = 'fk_absensi_guru_pengganti';
```

Output yang diharapkan:
```
+-----------------------------+------------+-------------------+-----------------------+-----------------------+
| CONSTRAINT_NAME             | TABLE_NAME | COLUMN_NAME       | REFERENCED_TABLE_NAME | REFERENCED_COLUMN_NAME|
+-----------------------------+------------+-------------------+-----------------------+-----------------------+
| fk_absensi_guru_pengganti   | absensi    | guru_pengganti_id | guru                  | id                    |
+-----------------------------+------------+-------------------+-----------------------+-----------------------+
```

## Testing Migration

### 1. Test Insert dengan Guru Pengganti
```sql
-- Contoh: Insert absensi dengan guru pengganti
INSERT INTO absensi 
(jadwal_mengajar_id, tanggal, pertemuan_ke, materi_pembelajaran, created_by, guru_pengganti_id, created_at)
VALUES 
(1, '2026-01-15', 5, 'Matematika Dasar', 1, 2, NOW());
```

### 2. Test Insert tanpa Guru Pengganti
```sql
-- Contoh: Insert absensi tanpa guru pengganti (NULL)
INSERT INTO absensi 
(jadwal_mengajar_id, tanggal, pertemuan_ke, materi_pembelajaran, created_by, guru_pengganti_id, created_at)
VALUES 
(2, '2026-01-15', 3, 'Bahasa Indonesia', 1, NULL, NOW());
```

### 3. Test Foreign Key Constraint
```sql
-- Test 1: Coba insert dengan guru_pengganti_id yang tidak ada (harus gagal)
INSERT INTO absensi 
(jadwal_mengajar_id, tanggal, pertemuan_ke, created_by, guru_pengganti_id, created_at)
VALUES 
(1, '2026-01-16', 6, 1, 99999, NOW());
-- Expected: ERROR - Cannot add or update a child row: a foreign key constraint fails

-- Test 2: Coba delete guru yang menjadi pengganti (guru_pengganti_id harus menjadi NULL)
DELETE FROM guru WHERE id = 2;
SELECT guru_pengganti_id FROM absensi WHERE id = 1;
-- Expected: guru_pengganti_id = NULL (bukan error)
```

## Troubleshooting

### Error: "Table 'absensi' doesn't exist"
**Solusi**: Jalankan semua migration dari awal
```bash
php spark migrate
```

### Error: "Duplicate column name 'guru_pengganti_id'"
**Solusi**: Migration sudah pernah dijalankan. Cek dengan:
```bash
php spark migrate:status
```

### Error: "Cannot add foreign key constraint"
**Kemungkinan Penyebab**:
1. Tabel `guru` belum ada
2. Field `id` di tabel `guru` bukan primary key atau tipe data tidak sesuai

**Solusi**: Pastikan tabel `guru` sudah ada dan memiliki struktur yang benar:
```sql
DESCRIBE guru;
```

### Rollback Tidak Berfungsi
Jika rollback error, bisa manual dengan SQL:
```sql
-- Drop foreign key terlebih dahulu
ALTER TABLE absensi DROP FOREIGN KEY fk_absensi_guru_pengganti;

-- Drop column
ALTER TABLE absensi DROP COLUMN guru_pengganti_id;
```

## Migration untuk Environment Berbeda

### Development
```bash
php spark migrate
```

### Production
```bash
# 1. Backup database terlebih dahulu
mysqldump -u username -p database_name > backup_before_migration.sql

# 2. Jalankan migration
php spark migrate

# 3. Verifikasi
php spark migrate:status
```

### Testing/Staging
```bash
# Jalankan di environment test
php spark migrate --env testing
```

## Checklist Deployment

- [ ] Backup database production
- [ ] Cek status migration di production: `php spark migrate:status`
- [ ] Jalankan migration: `php spark migrate`
- [ ] Verifikasi struktur tabel dengan `DESCRIBE absensi`
- [ ] Verifikasi foreign key constraint
- [ ] Test insert data dengan dan tanpa guru_pengganti_id
- [ ] Test aplikasi melalui UI (create/edit absensi)
- [ ] Monitoring error log setelah deployment

## Rollback Plan

Jika terjadi masalah setelah migration:

### Option 1: Rollback via CodeIgniter
```bash
php spark migrate:rollback
```

### Option 2: Manual SQL Rollback
```sql
-- 1. Drop foreign key
ALTER TABLE absensi DROP FOREIGN KEY fk_absensi_guru_pengganti;

-- 2. Drop column
ALTER TABLE absensi DROP COLUMN guru_pengganti_id;
```

### Option 3: Restore dari Backup
```bash
mysql -u username -p database_name < backup_before_migration.sql
```

## Informasi Tambahan

### Dependencies
Migration ini memerlukan:
- Tabel `absensi` sudah ada
- Tabel `guru` sudah ada
- Field `guru.id` sebagai primary key

### Order Migration
Pastikan migration-migration berikut sudah dijalankan sebelum migration ini:
1. `2026-01-06-163119_CreateGuruTable.php`
2. `2026-01-06-163202_CreateAbsensiTable.php`

### Catatan Penting
- Field `guru_pengganti_id` bersifat **NULLABLE** (opsional)
- Foreign key menggunakan **ON DELETE SET NULL** untuk menghindari error jika guru dihapus
- **ON UPDATE CASCADE** memastikan jika ID guru berubah, referensi juga ikut berubah

## Support

Jika mengalami masalah:
1. Cek error log CodeIgniter: `writable/logs/`
2. Cek MySQL error log
3. Verifikasi struktur database dengan `DESCRIBE` dan `SHOW CREATE TABLE`
4. Pastikan user database memiliki privilege untuk ALTER TABLE

---

**Created**: 2026-01-12  
**Migration File**: `app/Database/Migrations/2026-01-11-183700_AddGuruPenggantiToAbsensi.php`  
**Feature**: Guru Pengganti (Piket Pengganti)
