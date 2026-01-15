<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\JadwalMengajarModel;
use App\Models\AbsensiDetailModel;
use App\Models\IzinSiswaModel;

class DashboardController extends BaseController
{
    protected $siswaModel;
    protected $jadwalModel;
    protected $absensiDetailModel;
    protected $izinSiswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->izinSiswaModel = new IzinSiswaModel();
    }

    public function index()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        // Get jadwal hari ini
        $hariIni = date('l');
        $hariIndonesia = $this->convertDayToIndonesian($hariIni);
        
        $jadwalHariIni = $this->jadwalModel
            ->select('jadwal_mengajar.*, mata_pelajaran.nama_mapel, guru.nama_lengkap as nama_guru')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->where('jadwal_mengajar.kelas_id', $siswa['kelas_id'])
            ->where('jadwal_mengajar.hari', $hariIndonesia)
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC')
            ->findAll();

        // Get statistik kehadiran bulan ini
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        $kehadiran = $this->absensiDetailModel
            ->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
            ')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('absensi_detail.siswa_id', $siswa['id'])
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->first();

        // Calculate percentage
        $persentaseKehadiran = 0;
        if ($kehadiran['total'] > 0) {
            $persentaseKehadiran = round(($kehadiran['hadir'] / $kehadiran['total']) * 100, 1);
        }

        // Get izin status
        $izinPending = $this->izinSiswaModel
            ->where('siswa_id', $siswa['id'])
            ->where('status', 'pending')
            ->countAllResults();

        $izinDisetujui = $this->izinSiswaModel
            ->where('siswa_id', $siswa['id'])
            ->where('status', 'disetujui')
            ->countAllResults();

        // Get recent absensi (5 terakhir)
        $recentAbsensi = $this->absensiDetailModel
            ->select('absensi_detail.*, absensi.tanggal, mata_pelajaran.nama_mapel, absensi.pertemuan_ke')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->where('absensi_detail.siswa_id', $siswa['id'])
            ->orderBy('absensi.tanggal', 'DESC')
            ->limit(5)
            ->findAll();

        $data = [
            'title' => 'Dashboard Siswa',
            'siswa' => $siswa,
            'jadwalHariIni' => $jadwalHariIni,
            'kehadiran' => $kehadiran,
            'persentaseKehadiran' => $persentaseKehadiran,
            'izinPending' => $izinPending,
            'izinDisetujui' => $izinDisetujui,
            'recentAbsensi' => $recentAbsensi
        ];

        return view('siswa/dashboard', $data);
    }

    private function convertDayToIndonesian($day)
    {
        $days = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
        ];
        return $days[$day] ?? $day;
    }
}
