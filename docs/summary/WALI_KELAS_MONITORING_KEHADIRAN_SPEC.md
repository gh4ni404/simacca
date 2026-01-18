# Spesifikasi Fitur: Monitoring Kehadiran Kelas (Wali Kelas)

## Overview
Fitur khusus untuk Wali Kelas yang memungkinkan monitoring kehadiran siswa di kelasnya **untuk semua mata pelajaran**, berbeda dengan guru mata pelajaran yang hanya melihat absensi untuk mapel yang diajarnya.

---

## 🎯 Use Cases

### UC-1: Melihat Daftar Kehadiran Per Mata Pelajaran
**Actor**: Wali Kelas  
**Goal**: Melihat statistik kehadiran siswa per mata pelajaran di kelasnya

**Flow**:
1. Wali kelas masuk ke menu "Monitoring Kehadiran Kelas"
2. Sistem menampilkan daftar semua mata pelajaran yang ada di kelas
3. Untuk setiap mata pelajaran, ditampilkan:
   - Nama mata pelajaran
   - Nama guru pengampu
   - Total pertemuan
   - Statistik: Jumlah Hadir, Sakit, Izin, Alpa
   - Persentase kehadiran rata-rata
4. Wali kelas dapat filter by periode (bulan/tanggal range)

### UC-2: Melihat Detail Kehadiran Per Mata Pelajaran
**Actor**: Wali Kelas  
**Goal**: Melihat detail kehadiran setiap siswa untuk satu mata pelajaran tertentu

**Flow**:
1. Dari daftar mata pelajaran, wali kelas klik salah satu mapel
2. Sistem menampilkan tabel detail dengan struktur:
   - Kolom: No, Nama Siswa, Pertemuan 1, Pertemuan 2, ..., Total H/S/I/A
   - Baris: Setiap siswa di kelas
   - Cell: Status kehadiran (H/S/I/A) dengan warna
3. Tampilkan statistik per siswa di kolom terakhir
4. Filter by tanggal range

### UC-3: Melihat Rekapan Bulanan Kehadiran
**Actor**: Wali Kelas  
**Goal**: Melihat rekap kehadiran semua siswa untuk semua mata pelajaran dalam periode tertentu

**Flow**:
1. Wali kelas pilih menu "Rekapan Bulanan"
2. Pilih bulan dan tahun
3. Sistem menampilkan tabel rekapitulasi:
   - Kolom: No, NIS, Nama, Hadir, Sakit, Izin, Alpa, Total, % Hadir
   - Baris: Setiap siswa
   - Footer: Rata-rata kelas
4. Dapat di-export ke Excel/PDF

---

## 📱 Wireframe/Mockup

### View 1: List Mata Pelajaran
```
┌─────────────────────────────────────────────────────────────┐
│ 📊 Monitoring Kehadiran Kelas                               │
│ Kelas: XII RPL 1 | Periode: Januari 2026                    │
├─────────────────────────────────────────────────────────────┤
│ Filter: [Bulan ▼] [Tahun ▼] [Terapkan]                     │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ┌────────────────────────────────────────────────────────┐ │
│ │ 📚 Matematika                                          │ │
│ │ Guru: Budi Santoso, S.Pd | Pertemuan: 12x             │ │
│ │ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │ │
│ │ ✅ Hadir: 324 | 🤒 Sakit: 12 | ✋ Izin: 8 | ❌ Alpa: 6│ │
│ │ 📊 Rata-rata kehadiran: 92.6%                          │ │
│ │ [Lihat Detail →]                                       │ │
│ └────────────────────────────────────────────────────────┘ │
│                                                              │
│ ┌────────────────────────────────────────────────────────┐ │
│ │ 💻 Pemrograman Web                                     │ │
│ │ Guru: Ani Wijaya, S.Kom | Pertemuan: 15x              │ │
│ │ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │ │
│ │ ✅ Hadir: 405 | 🤒 Sakit: 15 | ✋ Izin: 10 | ❌ Alpa:10│ │
│ │ 📊 Rata-rata kehadiran: 91.7%                          │ │
│ │ [Lihat Detail →]                                       │ │
│ └────────────────────────────────────────────────────────┘ │
│                                                              │
│ ... (list semua mapel)                                      │
│                                                              │
│ [📥 Export Semua] [📊 Lihat Rekapan Bulanan]               │
└─────────────────────────────────────────────────────────────┘
```

### View 2: Detail Per Mata Pelajaran
```
┌─────────────────────────────────────────────────────────────┐
│ ← Kembali | Matematika - XII RPL 1                          │
│ Guru: Budi Santoso, S.Pd | Periode: Januari 2026            │
├─────────────────────────────────────────────────────────────┤
│ Filter: [📅 01/01/2026] - [📅 31/01/2026] [Terapkan]       │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ┌──────────────────────────────────────────────────────┐   │
│ │ No │ Nama Siswa    │ 2/1 │ 5/1 │ 9/1 │ ... │ H│S│I│A│   │
│ ├────┼───────────────┼─────┼─────┼─────┼─────┼─────────┤   │
│ │ 1  │ Ahmad         │ ✅  │ ✅  │ 🤒  │ ... │27│1│0│0│   │
│ │ 2  │ Budi          │ ✅  │ ❌  │ ✅  │ ... │25│0│1│2│   │
│ │ 3  │ Citra         │ ✅  │ ✅  │ ✅  │ ... │28│0│0│0│   │
│ │ ...│ ...           │ ... │ ... │ ... │ ... │..........│   │
│ └──────────────────────────────────────────────────────┘   │
│                                                              │
│ Legend: ✅ Hadir | 🤒 Sakit | ✋ Izin | ❌ Alpa             │
│                                                              │
│ [📥 Export Excel] [🖨️ Print]                               │
└─────────────────────────────────────────────────────────────┘
```

### View 3: Rekapan Bulanan
```
┌─────────────────────────────────────────────────────────────┐
│ 📊 Rekapan Kehadiran Bulanan - XII RPL 1                    │
│ Periode: Januari 2026 | Total Siswa: 30                     │
├─────────────────────────────────────────────────────────────┤
│ [Bulan: Januari ▼] [Tahun: 2026 ▼] [Tampilkan]             │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│ ┌─────────────────────────────────────────────────────┐    │
│ │No│NIS  │Nama      │Hadir│Sakit│Izin│Alpa│Total│%   │    │
│ ├──┼─────┼──────────┼─────┼─────┼────┼────┼─────┼────┤    │
│ │1 │12001│Ahmad     │ 45  │  2  │ 1  │ 0  │ 48  │93.8│    │
│ │2 │12002│Budi      │ 42  │  1  │ 2  │ 3  │ 48  │87.5│    │
│ │3 │12003│Citra     │ 47  │  1  │ 0  │ 0  │ 48  │97.9│    │
│ │..│.....│.......   │ ... │ ... │ .. │ .. │ ... │... │    │
│ ├──┴─────┴──────────┼─────┼─────┼────┼────┼─────┼────┤    │
│ │ RATA-RATA KELAS   │43.2 │ 1.5 │1.2 │1.1 │ 48  │90.0│    │
│ └───────────────────┴─────┴─────┴────┴────┴─────┴────┘    │
│                                                              │
│ 📈 Grafik Kehadiran:                                        │
│ ┌────────────────────────────────────┐                     │
│ │     ██ Hadir (90%)                 │                     │
│ │     ▓▓ Sakit (3.1%)                │                     │
│ │     ░░ Izin (2.5%)                 │                     │
│ │     ▒▒ Alpa (2.3%)                 │                     │
│ └────────────────────────────────────┘                     │
│                                                              │
│ [📥 Export Excel] [📄 Export PDF] [🖨️ Print]              │
└─────────────────────────────────────────────────────────────┘
```

---

## 💾 Database Queries

### Query 1: Get All Mata Pelajaran di Kelas dengan Statistik
```sql
SELECT 
    mp.id,
    mp.nama_mapel,
    mp.kode_mapel,
    g.nama_lengkap as nama_guru,
    COUNT(DISTINCT a.id) as total_pertemuan,
    COUNT(ad.id) as total_record,
    SUM(CASE WHEN ad.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
    SUM(CASE WHEN ad.status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
    SUM(CASE WHEN ad.status = 'izin' THEN 1 ELSE 0 END) as total_izin,
    SUM(CASE WHEN ad.status = 'alpa' THEN 1 ELSE 0 END) as total_alpa,
    ROUND((SUM(CASE WHEN ad.status = 'hadir' THEN 1 ELSE 0 END) / COUNT(ad.id)) * 100, 1) as persentase_hadir
FROM mata_pelajaran mp
JOIN jadwal_mengajar jm ON jm.mata_pelajaran_id = mp.id
JOIN guru g ON g.id = jm.guru_id
LEFT JOIN absensi a ON a.jadwal_mengajar_id = jm.id
LEFT JOIN absensi_detail ad ON ad.absensi_id = a.id
WHERE jm.kelas_id = ?
  AND a.tanggal BETWEEN ? AND ?
GROUP BY mp.id, g.id
ORDER BY mp.nama_mapel
```

### Query 2: Get Detail Kehadiran Per Mapel
```sql
SELECT 
    s.id as siswa_id,
    s.nis,
    s.nama_lengkap,
    a.id as absensi_id,
    a.tanggal,
    a.pertemuan_ke,
    ad.status,
    ad.keterangan
FROM siswa s
LEFT JOIN absensi_detail ad ON ad.siswa_id = s.id
LEFT JOIN absensi a ON a.id = ad.absensi_id
LEFT JOIN jadwal_mengajar jm ON jm.id = a.jadwal_mengajar_id
WHERE s.kelas_id = ?
  AND jm.mata_pelajaran_id = ?
  AND a.tanggal BETWEEN ? AND ?
ORDER BY s.nama_lengkap, a.tanggal
```

**PHP Processing**: Pivot data untuk tampilan tabel horizontal

### Query 3: Get Rekapan Bulanan
```sql
SELECT 
    s.id,
    s.nis,
    s.nama_lengkap,
    COUNT(ad.id) as total_absensi,
    SUM(CASE WHEN ad.status = 'hadir' THEN 1 ELSE 0 END) as total_hadir,
    SUM(CASE WHEN ad.status = 'sakit' THEN 1 ELSE 0 END) as total_sakit,
    SUM(CASE WHEN ad.status = 'izin' THEN 1 ELSE 0 END) as total_izin,
    SUM(CASE WHEN ad.status = 'alpa' THEN 1 ELSE 0 END) as total_alpa,
    ROUND((SUM(CASE WHEN ad.status = 'hadir' THEN 1 ELSE 0 END) / COUNT(ad.id)) * 100, 1) as persentase_hadir
FROM siswa s
LEFT JOIN absensi_detail ad ON ad.siswa_id = s.id
LEFT JOIN absensi a ON a.id = ad.absensi_id
WHERE s.kelas_id = ?
  AND MONTH(a.tanggal) = ?
  AND YEAR(a.tanggal) = ?
GROUP BY s.id
ORDER BY s.nama_lengkap
```

---

## 🏗️ Implementation Structure

### Controllers

#### File: `app/Controllers/WaliKelas/MonitoringController.php` (NEW)
```php
<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;
use App\Models\JadwalMengajarModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\SiswaModel;

class MonitoringController extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $mapelModel;
    protected $jadwalModel;
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->mapelModel = new MataPelajaranModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->siswaModel = new SiswaModel();
    }

    /**
     * View 1: List Mata Pelajaran dengan Statistik
     */
    public function index()
    {
        // Verify wali kelas
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || !$guru['is_wali_kelas']) {
            return redirect()->to('/access-denied');
        }

        $kelas = $this->kelasModel->getByWaliKelas($guru['id']);
        if (!$kelas) {
            return redirect()->to('/access-denied');
        }

        // Get filter params
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $startDate = "$tahun-$bulan-01";
        $endDate = date("Y-m-t", strtotime($startDate));

        // Get all mapel di kelas dengan statistik
        $mapelStats = $this->getMapelStatistics($kelas['id'], $startDate, $endDate);

        $data = [
            'title' => 'Monitoring Kehadiran Kelas',
            'guru' => $guru,
            'kelas' => $kelas,
            'mapelStats' => $mapelStats,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_bulan' => date('F', strtotime($startDate))
        ];

        return view('walikelas/monitoring/index', $data);
    }

    /**
     * View 2: Detail Kehadiran Per Mata Pelajaran
     */
    public function detail($mapelId)
    {
        // Verify wali kelas
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || !$guru['is_wali_kelas']) {
            return redirect()->to('/access-denied');
        }

        $kelas = $this->kelasModel->getByWaliKelas($guru['id']);
        if (!$kelas) {
            return redirect()->to('/access-denied');
        }

        // Get mata pelajaran
        $mapel = $this->mapelModel->find($mapelId);
        if (!$mapel) {
            return redirect()->back()->with('error', 'Mata pelajaran tidak ditemukan');
        }

        // Get filter params
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        // Get detail kehadiran
        $detailKehadiran = $this->getDetailKehadiranMapel($kelas['id'], $mapelId, $startDate, $endDate);

        $data = [
            'title' => 'Detail Kehadiran - ' . $mapel['nama_mapel'],
            'guru' => $guru,
            'kelas' => $kelas,
            'mapel' => $mapel,
            'detailKehadiran' => $detailKehadiran,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('walikelas/monitoring/detail', $data);
    }

    /**
     * View 3: Rekapan Bulanan
     */
    public function rekapan()
    {
        // Verify wali kelas
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || !$guru['is_wali_kelas']) {
            return redirect()->to('/access-denied');
        }

        $kelas = $this->kelasModel->getByWaliKelas($guru['id']);
        if (!$kelas) {
            return redirect()->to('/access-denied');
        }

        // Get filter params
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Get rekapan data
        $rekapan = $this->getRekapanBulanan($kelas['id'], $bulan, $tahun);

        // Calculate average
        $average = $this->calculateAverageAttendance($rekapan);

        $data = [
            'title' => 'Rekapan Kehadiran Bulanan',
            'guru' => $guru,
            'kelas' => $kelas,
            'rekapan' => $rekapan,
            'average' => $average,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_bulan' => date('F', strtotime("$tahun-$bulan-01"))
        ];

        return view('walikelas/monitoring/rekapan', $data);
    }

    /**
     * Export Rekapan to Excel
     */
    public function exportExcel()
    {
        // Implementation using PhpSpreadsheet
        // TODO: Implement export functionality
    }

    /**
     * Export Rekapan to PDF
     */
    public function exportPdf()
    {
        // Implementation using mPDF/TCPDF
        // TODO: Implement export functionality
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Get statistik semua mata pelajaran di kelas
     */
    private function getMapelStatistics($kelasId, $startDate, $endDate)
    {
        return $this->mapelModel
            ->select('
                mata_pelajaran.id,
                mata_pelajaran.nama_mapel,
                mata_pelajaran.kode_mapel,
                guru.nama_lengkap as nama_guru,
                COUNT(DISTINCT absensi.id) as total_pertemuan,
                COUNT(absensi_detail.id) as total_record,
                SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi_detail.status = "sakit" THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN absensi_detail.status = "izin" THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN absensi_detail.status = "alpa" THEN 1 ELSE 0 END) as total_alpa
            ')
            ->join('jadwal_mengajar', 'jadwal_mengajar.mata_pelajaran_id = mata_pelajaran.id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('absensi', 'absensi.jadwal_mengajar_id = jadwal_mengajar.id', 'left')
            ->join('absensi_detail', 'absensi_detail.absensi_id = absensi.id', 'left')
            ->where('jadwal_mengajar.kelas_id', $kelasId)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->groupBy('mata_pelajaran.id, guru.id')
            ->orderBy('mata_pelajaran.nama_mapel')
            ->findAll();
    }

    /**
     * Get detail kehadiran per mata pelajaran
     */
    private function getDetailKehadiranMapel($kelasId, $mapelId, $startDate, $endDate)
    {
        $siswa = $this->siswaModel->getByKelas($kelasId);
        $absensi = $this->absensiModel
            ->select('absensi.*, jadwal_mengajar.mata_pelajaran_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.kelas_id', $kelasId)
            ->where('jadwal_mengajar.mata_pelajaran_id', $mapelId)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->orderBy('absensi.tanggal')
            ->findAll();

        // Build matrix siswa x pertemuan
        $matrix = [];
        foreach ($siswa as $s) {
            $matrix[$s['id']] = [
                'siswa' => $s,
                'kehadiran' => [],
                'stats' => ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0]
            ];

            foreach ($absensi as $a) {
                $detail = $this->absensiDetailModel
                    ->where('absensi_id', $a['id'])
                    ->where('siswa_id', $s['id'])
                    ->first();

                $status = $detail ? $detail['status'] : '-';
                $matrix[$s['id']]['kehadiran'][$a['id']] = $status;

                if ($detail && isset($matrix[$s['id']]['stats'][$status])) {
                    $matrix[$s['id']]['stats'][$status]++;
                }
            }
        }

        return [
            'matrix' => $matrix,
            'absensi' => $absensi
        ];
    }

    /**
     * Get rekapan bulanan
     */
    private function getRekapanBulanan($kelasId, $bulan, $tahun)
    {
        return $this->siswaModel
            ->select('
                siswa.id,
                siswa.nis,
                siswa.nama_lengkap,
                COUNT(absensi_detail.id) as total_absensi,
                SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN absensi_detail.status = "sakit" THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN absensi_detail.status = "izin" THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN absensi_detail.status = "alpa" THEN 1 ELSE 0 END) as total_alpa
            ')
            ->join('absensi_detail', 'absensi_detail.siswa_id = siswa.id', 'left')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id', 'left')
            ->where('siswa.kelas_id', $kelasId)
            ->where('MONTH(absensi.tanggal)', $bulan)
            ->where('YEAR(absensi.tanggal)', $tahun)
            ->groupBy('siswa.id')
            ->orderBy('siswa.nama_lengkap')
            ->findAll();
    }

    /**
     * Calculate average attendance
     */
    private function calculateAverageAttendance($rekapan)
    {
        if (empty($rekapan)) {
            return [
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'total' => 0,
                'persentase' => 0
            ];
        }

        $totalSiswa = count($rekapan);
        $sumHadir = array_sum(array_column($rekapan, 'total_hadir'));
        $sumSakit = array_sum(array_column($rekapan, 'total_sakit'));
        $sumIzin = array_sum(array_column($rekapan, 'total_izin'));
        $sumAlpa = array_sum(array_column($rekapan, 'total_alpa'));
        $sumTotal = array_sum(array_column($rekapan, 'total_absensi'));

        return [
            'hadir' => round($sumHadir / $totalSiswa, 1),
            'sakit' => round($sumSakit / $totalSiswa, 1),
            'izin' => round($sumIzin / $totalSiswa, 1),
            'alpa' => round($sumAlpa / $totalSiswa, 1),
            'total' => round($sumTotal / $totalSiswa, 1),
            'persentase' => $sumTotal > 0 ? round(($sumHadir / $sumTotal) * 100, 1) : 0
        ];
    }
}
```

---

## 📂 Views Structure

### View Files to Create:
1. `app/Views/walikelas/monitoring/index.php` - List mata pelajaran
2. `app/Views/walikelas/monitoring/detail.php` - Detail per mapel
3. `app/Views/walikelas/monitoring/rekapan.php` - Rekapan bulanan
4. `app/Views/walikelas/monitoring/index_mobile.php` (optional)
5. `app/Views/walikelas/monitoring/index_desktop.php` (optional)

---

## 🛣️ Routes to Add

```php
// In app/Config/Routes.php
$routes->group('walikelas', ['filter' => 'role:wali_kelas'], function ($routes) {
    
    // ... existing routes ...
    
    // Monitoring Kehadiran Kelas (NEW)
    $routes->get('monitoring', 'WaliKelas\MonitoringController::index');
    $routes->get('monitoring/detail/(:num)', 'WaliKelas\MonitoringController::detail/$1');
    $routes->get('monitoring/rekapan', 'WaliKelas\MonitoringController::rekapan');
    $routes->get('monitoring/export-excel', 'WaliKelas\MonitoringController::exportExcel');
    $routes->get('monitoring/export-pdf', 'WaliKelas\MonitoringController::exportPdf');
});
```

---

## 🎨 UI Components Needed

### Status Badges
```php
// Helper function for status badge
function getStatusBadge($status) {
    $badges = [
        'hadir' => '<span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">✅ H</span>',
        'sakit' => '<span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">🤒 S</span>',
        'izin' => '<span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">✋ I</span>',
        'alpa' => '<span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">❌ A</span>',
    ];
    return $badges[$status] ?? '-';
}
```

### Chart Component (Using Chart.js)
```javascript
// Pie chart for attendance distribution
const ctx = document.getElementById('attendanceChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Hadir', 'Sakit', 'Izin', 'Alpa'],
        datasets: [{
            data: [<?= $average['hadir'] ?>, <?= $average['sakit'] ?>, <?= $average['izin'] ?>, <?= $average['alpa'] ?>],
            backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
```

---

## ✅ Acceptance Criteria

### Feature Completion Checklist:
- [ ] View 1: List mata pelajaran dengan statistik berfungsi
- [ ] View 2: Detail kehadiran per mapel dengan tabel siswa x pertemuan
- [ ] View 3: Rekapan bulanan dengan rata-rata kelas
- [ ] Filter by bulan/tahun berfungsi
- [ ] Filter by date range berfungsi
- [ ] Statistik dihitung dengan benar (H/S/I/A)
- [ ] Persentase kehadiran akurat
- [ ] Export to Excel berfungsi
- [ ] Export to PDF berfungsi
- [ ] Print functionality berfungsi
- [ ] Responsive untuk mobile dan desktop
- [ ] Chart/grafik ditampilkan dengan benar
- [ ] Performance: Load time < 2 detik

### Data Accuracy:
- [ ] Hanya menampilkan data kelas yang diampu wali kelas
- [ ] Semua mata pelajaran di kelas ditampilkan
- [ ] Data absensi sesuai dengan yang di-input guru
- [ ] Perhitungan statistik akurat
- [ ] Tidak ada data leak (hanya kelas sendiri)

---

## 📊 Performance Considerations

### Optimization Strategies:
1. **Database Indexing**:
   - Index pada `jadwal_mengajar.kelas_id`
   - Index pada `absensi.tanggal`
   - Composite index pada `absensi_detail (absensi_id, siswa_id, status)`

2. **Query Optimization**:
   - Use appropriate JOINs (LEFT JOIN vs INNER JOIN)
   - Limit date range to prevent large datasets
   - Use pagination for large class sizes

3. **Caching**:
   - Cache mata pelajaran list (rarely changes)
   - Cache siswa list per kelas
   - Cache monthly statistics (invalidate on new absensi)

4. **Lazy Loading**:
   - Load detail only when mapel is clicked
   - Paginate rekapan if > 50 students

---

## 🔒 Security Considerations

### Access Control:
- ✅ Verify `is_wali_kelas` flag
- ✅ Verify user owns the kelas (via `wali_kelas_id`)
- ✅ Prevent access to other kelas data
- ✅ Sanitize all inputs (dates, IDs)
- ✅ CSRF protection on all forms

### Data Privacy:
- ✅ Only show data for wali kelas's own class
- ✅ No export of sensitive student data without logging
- ✅ Audit log for exports (who, when, what)

---

## 🧪 Testing Scenarios

### Unit Tests:
1. `testGetMapelStatistics()` - Verify statistics calculation
2. `testGetDetailKehadiranMapel()` - Verify matrix generation
3. `testGetRekapanBulanan()` - Verify monthly recap
4. `testCalculateAverageAttendance()` - Verify average calculation

### Integration Tests:
1. Test full flow: index → detail → rekapan
2. Test filters work correctly
3. Test export functionality
4. Test with different date ranges
5. Test with empty data (no absensi yet)
6. Test with incomplete data

### User Acceptance Tests:
1. Wali kelas can view all mapel in their class
2. Wali kelas can drill down to detail
3. Wali kelas can see monthly recap
4. Statistics are accurate and understandable
5. Export works and produces correct files
6. UI is intuitive and responsive

---

## 📅 Implementation Timeline

### Week 1:
- [ ] Day 1-2: Create MonitoringController with basic methods
- [ ] Day 3-4: Implement View 1 (List mata pelajaran)
- [ ] Day 5: Testing and refinement

### Week 2:
- [ ] Day 1-2: Implement View 2 (Detail per mapel)
- [ ] Day 3-4: Implement View 3 (Rekapan bulanan)
- [ ] Day 5: Testing and refinement

### Week 3:
- [ ] Day 1-2: Implement export to Excel
- [ ] Day 2-3: Implement export to PDF
- [ ] Day 4-5: Final testing, bug fixes, documentation

---

## 📝 Notes

### Future Enhancements:
1. **Trend Analysis**: Grafik kehadiran per minggu/bulan
2. **Alerts**: Notifikasi otomatis untuk siswa dengan alpa > threshold
3. **Comparison**: Bandingkan kehadiran antar mapel
4. **Predictions**: ML untuk prediksi siswa yang mungkin alpa
5. **Mobile App**: Native mobile app untuk monitoring real-time
6. **Parent Portal**: Portal untuk orang tua lihat kehadiran anak

### Known Limitations:
1. Performance may degrade with very large date ranges (> 1 year)
2. Export limited to 1000 records per file
3. Charts limited to 12 categories (months)

---

**Document Version**: 1.0  
**Last Updated**: 2026-01-19  
**Status**: Ready for Implementation
