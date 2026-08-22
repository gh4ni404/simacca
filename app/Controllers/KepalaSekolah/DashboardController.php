<?php

namespace App\Controllers\KepalaSekolah;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiGuruModel;

class DashboardController extends BaseController
{
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $absensiModel;
    protected $absensiGuruModel;

    public function __construct()
    {
        $this->guruModel        = new GuruModel();
        $this->siswaModel       = new SiswaModel();
        $this->kelasModel       = new KelasModel();
        $this->absensiModel     = new AbsensiModel();
        $this->absensiGuruModel = new AbsensiGuruModel();
    }

    public function index()
    {
        $activeTA = get_active_tahun_ajaran();
        $today    = date('Y-m-d');
        $monthStart = date('Y-m-01');

        // Total stats
        $totalSiswa = $this->siswaModel->where('tahun_ajaran', $activeTA)->where('deleted_at', null)->countAllResults();
        $totalGuru  = $this->guruModel->countAllResults();
        $totalKelas = $this->kelasModel->where('tahun_ajaran', $activeTA)->countAllResults();

        // Absensi Siswa hari ini
        $absensiSiswaToday = $this->absensiModel
            ->select('COUNT(*) as total, SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir')
            ->where('tahun_ajaran', $activeTA)
            ->where('tanggal', $today)
            ->first();

        // Absensi Guru hari ini
        $absensiGuruToday = $this->absensiGuruModel
            ->select('COUNT(*) as total, SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir')
            ->where('tanggal', $today)
            ->first();

        $data = [
            'title'           => 'Executive Dashboard Kepala Sekolah',
            'pageTitle'       => 'Executive Dashboard Kepala Sekolah',
            'pageDescription' => 'Monitoring dan Rekapitulasi Eksekutif Sekolah - SIMACCA',
            'user'            => $this->getUserData(),
            'activeTA'        => $activeTA,
            'stats'           => [
                'total_siswa'   => $totalSiswa,
                'total_guru'    => $totalGuru,
                'total_kelas'   => $totalKelas,
                'siswa_hadir_today' => $absensiSiswaToday['hadir'] ?? 0,
                'guru_hadir_today'  => $absensiGuruToday['hadir'] ?? 0,
            ],
            'kepalaSekolahNama' => get_kepala_sekolah_nama(),
            'kepalaSekolahNip'  => get_kepala_sekolah_nip(),
        ];

        return view('kepala_sekolah/dashboard', $data);
    }
}
