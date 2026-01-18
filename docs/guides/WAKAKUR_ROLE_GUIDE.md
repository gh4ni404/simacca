# Panduan Role Wakakur (Wakil Kepala Sekolah Bidang Kurikulum)

## 🎯 Deskripsi

Role **Wakakur** (Wakil Kepala Sekolah Bidang Kurikulum) adalah role baru yang memiliki akses gabungan dari:
- ✅ **Guru Mata Pelajaran** - Kelola absensi, jurnal KBM, jadwal mengajar
- ✅ **Wali Kelas** - Kelola siswa, approve izin
- ✅ **Laporan Detail** - Akses laporan absensi detail seluruh sekolah (unique)

## 📋 Cara Membuat User Wakakur

### Melalui Admin Panel

1. **Login sebagai Admin**
   ```
   URL: /login
   Gunakan akun admin
   ```

2. **Buka Menu Data Guru**
   ```
   Admin Dashboard > Manajemen User > Data Guru > Tambah Guru
   ```

3. **Isi Form dengan Role Wakakur**
   ```
   NIP: [NIP Wakakur]
   Nama Lengkap: [Nama Lengkap]
   Jenis Kelamin: [L/P]
   Username: [username untuk login]
   Password: [password minimum 6 karakter]
   Email: [email@example.com]
   Role: wakakur ⭐ (Pilih "wakakur" dari dropdown)
   Mata Pelajaran: [Optional - jika mengajar]
   Kelas: [Optional - jika jadi wali kelas]
   Is Wali Kelas: [Centang jika ditugaskan sebagai wali kelas]
   ```

4. **Simpan**
   - User akan otomatis dibuat dengan role `wakakur`
   - Data guru akan tersimpan di tabel `guru`

### Melalui Import Excel

Edit file import guru, tambahkan kolom role dengan value `wakakur`:
```
NIP | NAMA | JENIS_KELAMIN | USERNAME | PASSWORD | EMAIL | ROLE | MAPEL_ID | KELAS_ID | IS_WALI_KELAS
1234567890 | Nama Wakakur | L | wakakur1 | pass123 | email@example.com | wakakur | 1 | 10 | 1
```

## 🔐 Login sebagai Wakakur

```
1. Buka: /login
2. Masukkan username & password wakakur
3. System akan redirect ke: /wakakur/dashboard
```

## 📊 Fitur yang Tersedia

### 1. Dashboard Wakakur
**URL**: `/wakakur/dashboard`

**Fitur**:
- 📈 **Overview Sekolah**: Total kelas, siswa, guru, mata pelajaran
- 📅 **Absensi Hari Ini**: Jumlah absensi yang sudah diinput hari ini
- 👨‍🏫 **Aktivitas Mengajar**: Jadwal, kelas diajar, absensi bulan ini
- 👥 **Info Wali Kelas**: Jika ditugaskan sebagai wali kelas
- ⚡ **Quick Actions**: Shortcut ke semua fitur

### 2. Fitur Mengajar (dari Guru Mapel)

#### a. Jadwal Mengajar
**URL**: `/wakakur/jadwal`
- Lihat jadwal mengajar per hari
- Filter by hari
- Detail kelas, mapel, jam

#### b. Absensi Siswa
**URL**: `/wakakur/absensi`
- Input absensi baru
- Edit absensi yang sudah ada
- View detail kehadiran siswa
- Print absensi per kelas
- Filter & pencarian

#### c. Jurnal KBM
**URL**: `/wakakur/jurnal`
- Buat jurnal KBM baru
- Edit jurnal existing
- Upload foto kegiatan
- Print jurnal
- View history jurnal

### 3. Fitur Wali Kelas

#### a. Data Siswa
**URL**: `/wakakur/siswa`
- Lihat daftar siswa di kelas (jika wali kelas)
- Filter & pencarian siswa
- View detail siswa

#### b. Persetujuan Izin
**URL**: `/wakakur/izin`
- Lihat daftar izin pending
- Approve/reject izin siswa
- View history izin
- Notifikasi izin baru

### 4. Laporan Detail (UNIQUE - Khusus Wakakur)

#### a. Daftar Laporan Absensi
**URL**: `/wakakur/laporan`

**Fitur**:
- 🔍 **Filter Advanced**:
  - By Kelas (semua kelas sekolah)
  - By Mata Pelajaran
  - By Tanggal (range)
- 📊 **Statistics Overview**:
  - Total Hadir
  - Total Sakit
  - Total Izin
  - Total Alpa
- 📋 **Daftar Absensi**:
  - Semua absensi dari semua guru
  - Info lengkap: tanggal, kelas, mapel, guru
  - Action: Detail & Print

#### b. Detail Absensi
**URL**: `/wakakur/laporan/detail/{id}`

**Fitur**:
- 📝 Informasi lengkap absensi
- 👥 Daftar kehadiran semua siswa
- 📊 Statistik per status
- 🖨️ Button print

#### c. Print Laporan
**URL**: `/wakakur/laporan/print/{id}`

**Fitur**:
- 📄 Layout print profesional
- ✍️ Tanda tangan Wakakur & Guru
- 📊 Rekapitulasi kehadiran
- 🖨️ Print-ready format

## 🎨 Tampilan UI

### Desktop View
- Sidebar navigation dengan dropdown menu
- Dashboard dengan card statistics
- Table view untuk data list
- Form layout yang luas

### Mobile View
- Bottom navigation bar (4 items)
- Card-based layout
- Touch-friendly buttons
- Optimized untuk layar kecil

## 🔄 Upgrade User Existing

Jika ingin mengubah guru yang sudah ada menjadi wakakur:

```
1. Admin Panel > Data Guru
2. Klik Edit pada guru yang ingin diupgrade
3. Ubah Role menjadi: wakakur
4. Simpan
5. User tersebut sekarang memiliki akses wakakur
```

## ⚙️ Technical Details

### Role Hierarchy
```
admin > wakakur > guru_mapel/wali_kelas > siswa
```

### Access Matrix
| Fitur | Admin | Wakakur | Guru Mapel | Wali Kelas | Siswa |
|-------|-------|---------|------------|------------|-------|
| Dashboard Sekolah | ✅ | ✅ | ❌ | ❌ | ❌ |
| Kelola Guru/Siswa | ✅ | ❌ | ❌ | ❌ | ❌ |
| Input Absensi | ✅ | ✅ | ✅ | ❌ | ❌ |
| Jurnal KBM | ✅ | ✅ | ✅ | ❌ | ❌ |
| View Siswa Kelas | ✅ | ✅ | ❌ | ✅ | ❌ |
| Approve Izin | ✅ | ✅ | ❌ | ✅ | ❌ |
| Laporan Detail All | ✅ | ✅ | ❌ | ❌ | ❌ |
| Ajukan Izin | ❌ | ❌ | ❌ | ❌ | ✅ |

### Database Schema
```sql
-- users table
role ENUM('admin', 'guru_mapel', 'wali_kelas', 'wakakur', 'siswa')

-- Wakakur user linked ke tabel guru
-- Sama seperti guru_mapel dan wali_kelas
```

## 🚨 Troubleshooting

### Role wakakur tidak muncul di dropdown
**Solusi**: Pastikan migration sudah dijalankan
```bash
php spark migrate
```

### Redirect error setelah login
**Solusi**: Clear browser cache dan cookies

### Menu tidak muncul
**Solusi**: Logout dan login kembali

### Laporan detail tidak muncul data
**Solusi**: 
1. Pastikan ada data absensi di database
2. Cek filter tanggal (default: bulan ini)
3. Reset filter ke "Semua Kelas" dan "Semua Mapel"

## 📞 Support

Jika mengalami masalah:
1. Cek dokumentasi ini
2. Cek log error di `writable/logs/`
3. Contact system administrator

## 🔄 Update History

| Versi | Tanggal | Perubahan |
|-------|---------|-----------|
| 1.0.0 | 2026-01-18 | Initial implementation |

---

**Dibuat oleh**: SIMACCA Development Team
**Terakhir Update**: 2026-01-18
