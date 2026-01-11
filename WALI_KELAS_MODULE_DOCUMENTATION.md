# 📚 Dokumentasi Wali Kelas Module

**Status:** ✅ COMPLETE (100%)  
**Tanggal Selesai:** 2026-01-11  
**Total Files:** 10 files (5 controllers + 5 views)

---

## 📋 Daftar Isi
- [Overview](#overview)
- [Controllers](#controllers)
- [Views](#views)
- [Fitur Detail](#fitur-detail)
- [Testing Checklist](#testing-checklist)
- [Routes](#routes)

---

## 🎯 Overview

Wali Kelas Module adalah modul untuk guru yang ditugaskan sebagai wali kelas. Module ini memungkinkan wali kelas untuk:
- Monitoring kehadiran siswa di kelasnya
- Melihat data lengkap siswa
- Menyetujui/menolak izin siswa
- Membuat laporan kehadiran
- Mengidentifikasi siswa bermasalah

---

## 🎮 Controllers

### 1. DashboardController.php
**Path:** `app/Controllers/WaliKelas/DashboardController.php`

**Fungsi:**
- Validasi user adalah wali kelas
- Get kelas yang diampu
- Statistik siswa (total, aktif)
- Statistik kehadiran bulan ini (H/S/I/A)
- Identifikasi siswa dengan alpa ≥3
- List izin pending
- Recent absensi kelas

**Data yang dikirim ke view:**
```php
[
    'title' => 'Dashboard Wali Kelas',
    'guru' => [], // Data guru
    'kelas' => [], // Data kelas
    'stats' => [
        'total_siswa',
        'siswa_aktif',
        'total_absensi_bulan_ini',
        'izin_pending'
    ],
    'kehadiranStats' => [
        'total', 'hadir', 'sakit', 'izin', 'alpa'
    ],
    'siswaAlpa' => [], // Siswa dengan alpa ≥3
    'recentAbsensi' => [], // 5 absensi terakhir
    'izinPending' => [] // Izin pending
]
```

---

### 2. SiswaController.php
**Path:** `app/Controllers/WaliKelas/SiswaController.php`

**Fungsi:**
- List semua siswa di kelas
- Data lengkap siswa (NIS, nama, email, username)
- Statistik kehadiran bulan ini per siswa
- Persentase kehadiran per siswa

**Data yang dikirim ke view:**
```php
[
    'title' => 'Data Siswa',
    'guru' => [],
    'kelas' => [],
    'siswa' => [
        [
            'id', 'nis', 'nama_lengkap', 'jenis_kelamin',
            'username', 'email', 'is_active',
            'kehadiran' => ['total', 'hadir', 'sakit', 'izin', 'alpa'],
            'persentase_hadir'
        ]
    ]
]
```

---

### 3. AbsensiController.php
**Path:** `app/Controllers/WaliKelas/AbsensiController.php`

**Fungsi:**
- Monitoring absensi kelas
- Filter berdasarkan periode (start_date, end_date)
- Get detail statistik untuk setiap absensi
- Hitung persentase kehadiran per pertemuan

**Data yang dikirim ke view:**
```php
[
    'title' => 'Monitoring Absensi Kelas',
    'guru' => [],
    'kelas' => [],
    'absensiData' => [
        [
            'tanggal', 'nama_mapel', 'nama_guru', 'pertemuan_ke',
            'materi_pembelajaran',
            'detail' => ['total', 'hadir', 'sakit', 'izin', 'alpa'],
            'persentase_hadir'
        ]
    ],
    'startDate' => '',
    'endDate' => ''
]
```

---

### 4. IzinController.php
**Path:** `app/Controllers/WaliKelas/IzinController.php`

**Fungsi:**
- List izin siswa di kelas
- Filter by status (pending, disetujui, ditolak)
- Approve izin (AJAX endpoint)
- Reject izin (AJAX endpoint)
- Count by status

**Methods:**
- `index()` - Display list izin
- `approve($id)` - POST endpoint untuk approve
- `reject($id)` - POST endpoint untuk reject

**AJAX Response:**
```json
{
    "status": "success|error",
    "message": "Pesan"
}
```

---

### 5. LaporanController.php
**Path:** `app/Controllers/WaliKelas/LaporanController.php`

**Fungsi:**
- Laporan kehadiran 2 mode:
  1. Rekapitulasi semua siswa
  2. Detail per siswa
- Filter periode (start_date, end_date)
- Filter siswa (siswa_id)
- Summary statistik

**Data yang dikirim ke view:**
```php
[
    'title' => 'Laporan Kehadiran',
    'guru' => [],
    'kelas' => [],
    'siswaList' => [], // For dropdown
    'laporan' => [], // Data berbeda tergantung mode
    'summary' => [], // Hanya untuk mode per siswa
    'startDate' => '',
    'endDate' => '',
    'siswaId' => null
]
```

---

## 🎨 Views

### 1. dashboard.php
**Path:** `app/Views/walikelas/dashboard.php`

**Komponen:**
- Welcome banner dengan info kelas
- 4 stats cards (Total Siswa, Absensi Bulan Ini, Izin Pending, Tingkat Kehadiran)
- Statistik kehadiran dengan 4 boxes (H/S/I/A)
- Alert siswa bermasalah (alpa ≥3)
- Recent absensi list
- Quick actions menu
- Izin pending preview
- Info kelas card

**Features:**
- Responsive grid layout
- Color-coded indicators
- Icons Font Awesome
- Hover effects
- Quick links

---

### 2. siswa/index.php
**Path:** `app/Views/walikelas/siswa/index.php`

**Komponen:**
- Header dengan total siswa
- 4 stats cards (Total, Aktif, Laki-laki, Perempuan)
- Filter & search bar
- Table siswa dengan:
  - NIS, Nama, Jenis Kelamin
  - Username, Status (active/inactive)
  - Kehadiran bulan ini (progress bar)
  - Detail H/S/I/A
- Info footer

**Features:**
- Real-time search (JavaScript)
- Filter by status & gender
- Progress bar visualization
- Color coding (hijau ≥80%, kuning 60-79%, merah <60%)

**JavaScript:**
```javascript
- filterTable() - Filter siswa based on search & filters
- Event listeners untuk search & select inputs
```

---

### 3. absensi/index.php
**Path:** `app/Views/walikelas/absensi/index.php`

**Komponen:**
- Header dengan info kelas
- Filter form (start_date, end_date)
- 5 stats cards (Total Pertemuan, H, S, I, A)
- Progress bar persentase kehadiran keseluruhan
- Table absensi dengan detail:
  - Tanggal, Mata Pelajaran, Guru
  - Pertemuan ke-N, Materi
  - Kehadiran (badges H/S/I/A)
  - Persentase dengan progress bar
- Info footer

**Features:**
- Date range filter
- Summary statistics
- Visual indicators
- Responsive table

---

### 4. izin/index.php
**Path:** `app/Views/walikelas/izin/index.php`

**Komponen:**
- Header dengan info kelas
- 3 stats cards (Pending, Disetujui, Ditolak)
- Tab navigation (Semua/Pending/Disetujui/Ditolak)
- Card list izin dengan:
  - Foto & nama siswa
  - Status badge
  - Info lengkap (tanggal, jenis, alasan)
  - Dokumen pendukung link
  - Catatan wali kelas (jika sudah diproses)
  - Action buttons (Setujui/Tolak) untuk pending
- Modal approve (dengan catatan opsional)
- Modal reject (dengan catatan wajib)
- Info footer

**Features:**
- Tab filtering
- Card-based layout
- AJAX approval system
- Modal confirmations
- Document viewer link

**JavaScript:**
```javascript
- showApproveModal(izinId, namaSiswa)
- closeApproveModal()
- showRejectModal(izinId, namaSiswa)
- closeRejectModal()
- processApprove() - AJAX POST
- processReject() - AJAX POST with validation
```

**AJAX Endpoints:**
- POST `/walikelas/izin/setujui/{id}` - Parameters: catatan
- POST `/walikelas/izin/tolak/{id}` - Parameters: catatan (required)

---

### 5. laporan/index.php
**Path:** `app/Views/walikelas/laporan/index.php`

**Komponen:**
- Header dengan print button
- Filter form (start_date, end_date, siswa_id)
- **Mode 1: Rekapitulasi Semua Siswa**
  - Table dengan kolom: No, NIS, Nama, Total, H, S, I, A, Persentase
  - Visual progress bars
  - Summary stats (Total Siswa, Baik, Cukup, Kurang)
- **Mode 2: Detail Per Siswa**
  - 5 summary boxes (Total, H, S, I, A)
  - Progress bar persentase kehadiran
  - Table detail per mata pelajaran:
    - Tanggal, Mata Pelajaran, Status, Keterangan
- Info footer
- Print-ready CSS

**Features:**
- Dual mode reporting
- Print layout optimization
- Color indicators
- Summary statistics
- Responsive tables

**Print CSS:**
```css
@media print {
    .print\:hidden { display: none !important; }
    body { print-color-adjust: exact; }
}
```

---

## 🎯 Fitur Detail

### Color Coding System
Persentase kehadiran menggunakan 3 level warna:
- **Hijau (≥80%)** - Kehadiran Baik
- **Kuning (60-79%)** - Kehadiran Cukup, perlu perhatian
- **Merah (<60%)** - Kehadiran Kurang, perlu tindakan

### Alert System
Dashboard menampilkan alert untuk siswa dengan:
- Alpa ≥3 kali dalam bulan berjalan
- Ditampilkan di card khusus dengan highlight merah
- Menampilkan jumlah total alpa

### Filter & Search
- **Search:** Real-time search di client-side (JavaScript)
- **Filter Period:** Server-side filtering dengan GET parameters
- **Filter Status:** Client-side untuk quick filtering
- **Filter Gender:** Client-side untuk data siswa

### Approval Workflow
1. Siswa submit izin
2. Status: `pending`
3. Wali kelas approve/reject
4. Jika approve: status → `disetujui`
5. Jika reject: status → `ditolak` (catatan wajib)
6. Response AJAX dengan message

---

## ✅ Testing Checklist

### Dashboard
- [ ] Akses dashboard sebagai wali kelas
- [ ] Cek apakah statistik ditampilkan dengan benar
- [ ] Verifikasi siswa alpa ≥3 muncul di alert
- [ ] Cek izin pending count dan preview
- [ ] Test quick actions links
- [ ] Verifikasi recent absensi ditampilkan

### Data Siswa
- [ ] List semua siswa di kelas
- [ ] Search siswa by nama atau NIS
- [ ] Filter by status (aktif/tidak aktif)
- [ ] Filter by gender (L/P)
- [ ] Verifikasi kehadiran bulan ini akurat
- [ ] Cek progress bar visualization
- [ ] Test responsive layout

### Monitoring Absensi
- [ ] Filter by periode tanggal
- [ ] Verifikasi summary statistik
- [ ] Cek detail absensi per pertemuan
- [ ] Verifikasi persentase kehadiran
- [ ] Test reset filter
- [ ] Empty state ketika tidak ada data

### Persetujuan Izin
- [ ] Tab navigation (Semua/Pending/Disetujui/Ditolak)
- [ ] Count badge sesuai dengan jumlah
- [ ] View detail izin lengkap
- [ ] Test approve izin (dengan & tanpa catatan)
- [ ] Test reject izin (validasi catatan wajib)
- [ ] View dokumen pendukung
- [ ] Verifikasi AJAX response
- [ ] Modal close functionality

### Laporan
- [ ] Filter periode tanggal
- [ ] Mode rekapitulasi semua siswa
- [ ] Mode detail per siswa
- [ ] Verifikasi summary statistik
- [ ] Test print functionality
- [ ] Empty state handling
- [ ] Reset filter
- [ ] Responsive table layout

### General
- [ ] Non-wali kelas tidak bisa akses
- [ ] Session validation
- [ ] Error handling
- [ ] Responsive design (mobile/tablet)
- [ ] Loading states
- [ ] Back button functionality

---

## 🛣️ Routes

### Routes untuk Wali Kelas Module:

```php
// All routes require: filter => 'auth' and filter => 'role:wali_kelas'

GET  /walikelas/dashboard          - Dashboard
GET  /walikelas/siswa              - Data Siswa
GET  /walikelas/absensi            - Monitoring Absensi
     ?start_date=YYYY-MM-DD
     ?end_date=YYYY-MM-DD
GET  /walikelas/izin               - Persetujuan Izin
     ?status=pending|disetujui|ditolak
POST /walikelas/izin/setujui/{id}  - Approve Izin (AJAX)
     Body: catatan (optional)
POST /walikelas/izin/tolak/{id}    - Reject Izin (AJAX)
     Body: catatan (required)
GET  /walikelas/laporan            - Laporan Kehadiran
     ?start_date=YYYY-MM-DD
     ?end_date=YYYY-MM-DD
     ?siswa_id=ID (optional)
```

---

## 🔧 Dependencies

### Models Used:
- `GuruModel` - Get data guru & validasi wali kelas
- `KelasModel` - Get kelas by wali kelas
- `SiswaModel` - Get siswa by kelas
- `AbsensiModel` - Get absensi by kelas
- `AbsensiDetailModel` - Get detail kehadiran & statistik
- `IzinSiswaModel` - Get izin, approve, reject

### Helper Functions:
- `session()->get()` - Get session data
- `base_url()` - Generate URLs
- `esc()` - Escape HTML
- `date()` - Format tanggal

### Frontend Libraries:
- **Tailwind CSS** - Styling framework
- **Font Awesome** - Icons
- **Vanilla JavaScript** - Filter, search, AJAX

---

## 📊 Database Queries

### Query Patterns Used:

1. **Get Kelas by Wali Kelas:**
```php
$kelasModel->getByWaliKelas($guruId)
```

2. **Get Siswa by Kelas:**
```php
$siswaModel->getByKelas($kelasId)
```

3. **Get Absensi Detail with Stats:**
```php
$absensiDetailModel->select('
    COUNT(*) as total,
    SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
    ...
')->join()->where()->groupBy()->first()
```

4. **Get Siswa with Alpa Count:**
```php
$absensiDetailModel->select('siswa.nama_lengkap, COUNT(*) as total_alpa')
    ->join()
    ->where('status', 'alpa')
    ->groupBy('siswa.id')
    ->having('total_alpa >=', 3)
```

---

## 🎨 UI/UX Guidelines

### Color Palette:
- **Primary:** Blue (`bg-blue-600`, `text-blue-600`)
- **Success:** Green (`bg-green-600`, `text-green-600`)
- **Warning:** Yellow (`bg-yellow-600`, `text-yellow-600`)
- **Danger:** Red (`bg-red-600`, `text-red-600`)
- **Info:** Purple (`bg-purple-600`, `text-purple-600`)

### Typography:
- **Headings:** `text-2xl font-bold`
- **Subheadings:** `text-lg font-semibold`
- **Body:** `text-sm` or `text-base`
- **Muted:** `text-gray-600` or `text-gray-500`

### Spacing:
- **Section Gap:** `mb-6` or `space-y-6`
- **Card Padding:** `p-4` or `p-6`
- **Grid Gap:** `gap-4`

### Components:
- **Cards:** White background, rounded corners, shadow
- **Buttons:** Rounded, with icon, hover effect
- **Tables:** Striped rows, hover effect, responsive
- **Badges:** Rounded-full, small text, color-coded
- **Progress Bars:** Height 2 or 4, rounded-full

---

## 📝 Notes

### Performance Considerations:
- Query optimization dengan proper joins
- Limit data di dashboard (5 recent items)
- Client-side filtering untuk better UX
- Minimal AJAX calls

### Security:
- Session validation di setiap controller
- Role checking (wali_kelas only)
- CSRF protection via framework
- Input escaping dengan `esc()`
- Prepared statements via ORM

### Future Enhancements:
- [ ] Export laporan ke Excel
- [ ] Export laporan ke PDF
- [ ] Email notification untuk approval
- [ ] Real-time updates dengan WebSocket
- [ ] Pagination untuk data besar
- [ ] Advanced filters & sorting
- [ ] Grafik/chart visualization
- [ ] Bulk approval untuk izin

---

**Dokumentasi dibuat:** 2026-01-11  
**Version:** 1.0.0  
**Status:** ✅ Production Ready
