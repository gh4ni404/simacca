<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;

class LaporanController extends BaseController
{
    protected $absensiModel;
    protected $absensiDetailModel;
    protected $guruModel;
    protected $jadwalModel;
    protected $kelasModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get filter dari request
        $kelasId = $this->request->getGet('kelas_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Get jadwal mengajar guru (untuk filter kelas)
        $jadwalGuru = $this->jadwalModel->getByGuru($guru['id']);
        
        // Extract unique kelas from jadwal
        $kelasIds = array_unique(array_column($jadwalGuru, 'kelas_id'));
        $kelasList = [];
        foreach ($kelasIds as $id) {
            $kelas = $this->kelasModel->find($id);
            if ($kelas) {
                $kelasList[$id] = $kelas['nama_kelas'];
            }
        }

        $laporan = null;
        $rekap = null;

        // Generate laporan jika ada filter
        if ($kelasId && $startDate && $endDate) {
            // Get absensi data
            $absensiData = $this->absensiModel->select('absensi.*, jadwal_mengajar.kelas_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->where('jadwal_mengajar.guru_id', $guru['id'])
                ->where('jadwal_mengajar.kelas_id', $kelasId)
                ->where('absensi.tanggal >=', $startDate)
                ->where('absensi.tanggal <=', $endDate)
                ->orderBy('absensi.tanggal', 'ASC')
                ->findAll();

            // Get siswa in kelas
            $siswaList = $this->siswaModel->where('kelas_id', $kelasId)
                ->orderBy('nama_lengkap', 'ASC')
                ->findAll();

            // Build laporan matrix
            $laporan = [];
            foreach ($siswaList as $siswa) {
                $laporanSiswa = [
                    'siswa' => $siswa,
                    'detail' => [],
                    'hadir' => 0,
                    'sakit' => 0,
                    'izin' => 0,
                    'alpa' => 0,
                    'total' => 0
                ];

                foreach ($absensiData as $absensi) {
                    $detail = $this->absensiDetailModel->where('absensi_id', $absensi['id'])
                        ->where('siswa_id', $siswa['id'])
                        ->first();

                    if ($detail) {
                        $laporanSiswa['detail'][] = [
                            'tanggal' => $absensi['tanggal'],
                            'status' => $detail['status'],
                            'keterangan' => $detail['keterangan']
                        ];

                        // Count status
                        switch ($detail['status']) {
                            case 'hadir':
                                $laporanSiswa['hadir']++;
                                break;
                            case 'sakit':
                                $laporanSiswa['sakit']++;
                                break;
                            case 'izin':
                                $laporanSiswa['izin']++;
                                break;
                            case 'alpa':
                                $laporanSiswa['alpa']++;
                                break;
                        }
                        $laporanSiswa['total']++;
                    }
                }

                $laporan[] = $laporanSiswa;
            }

            // Calculate rekap keseluruhan
            $rekap = [
                'total_siswa' => count($siswaList),
                'total_pertemuan' => count($absensiData),
                'total_hadir' => array_sum(array_column($laporan, 'hadir')),
                'total_sakit' => array_sum(array_column($laporan, 'sakit')),
                'total_izin' => array_sum(array_column($laporan, 'izin')),
                'total_alpa' => array_sum(array_column($laporan, 'alpa'))
            ];

            // Calculate percentage
            $totalKehadiran = $rekap['total_siswa'] * $rekap['total_pertemuan'];
            if ($totalKehadiran > 0) {
                $rekap['persentase_hadir'] = round(($rekap['total_hadir'] / $totalKehadiran) * 100, 2);
                $rekap['persentase_sakit'] = round(($rekap['total_sakit'] / $totalKehadiran) * 100, 2);
                $rekap['persentase_izin'] = round(($rekap['total_izin'] / $totalKehadiran) * 100, 2);
                $rekap['persentase_alpa'] = round(($rekap['total_alpa'] / $totalKehadiran) * 100, 2);
            }
        }

        $data = [
            'title' => 'Laporan Absensi',
            'guru' => $guru,
            'kelasList' => $kelasList,
            'kelasId' => $kelasId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'laporan' => $laporan,
            'rekap' => $rekap
        ];

        return view('guru/laporan/index_enhanced', $data);
    }

    public function print()
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', '❌ Data guru tidak ditemukan');
        }

        // Get filter dari request
        $kelasId = $this->request->getGet('kelas_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Validate parameters
        if (!$kelasId || !$startDate || !$endDate) {
            return redirect()->to('/guru/laporan')->with('error', '❌ Parameter tidak lengkap. Silakan pilih filter terlebih dahulu.');
        }

        // Get kelas name
        $kelas = $this->kelasModel->find($kelasId);
        if (!$kelas) {
            return redirect()->to('/guru/laporan')->with('error', '❌ Data kelas tidak ditemukan');
        }

        // Get absensi data
        $absensiData = $this->absensiModel->select('absensi.*, jadwal_mengajar.kelas_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.guru_id', $guru['id'])
            ->where('jadwal_mengajar.kelas_id', $kelasId)
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->orderBy('absensi.tanggal', 'ASC')
            ->findAll();

        // Get siswa in kelas
        $siswaList = $this->siswaModel->where('kelas_id', $kelasId)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        // Build laporan matrix
        $laporan = [];
        foreach ($siswaList as $siswa) {
            $laporanSiswa = [
                'siswa' => $siswa,
                'detail' => [],
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'total' => 0
            ];

            foreach ($absensiData as $absensi) {
                $detail = $this->absensiDetailModel->where('absensi_id', $absensi['id'])
                    ->where('siswa_id', $siswa['id'])
                    ->first();

                if ($detail) {
                    $laporanSiswa['detail'][] = [
                        'tanggal' => $absensi['tanggal'],
                        'status' => $detail['status'],
                        'keterangan' => $detail['keterangan']
                    ];

                    // Count status
                    switch ($detail['status']) {
                        case 'hadir':
                            $laporanSiswa['hadir']++;
                            break;
                        case 'sakit':
                            $laporanSiswa['sakit']++;
                            break;
                        case 'izin':
                            $laporanSiswa['izin']++;
                            break;
                        case 'alpa':
                            $laporanSiswa['alpa']++;
                            break;
                    }
                    $laporanSiswa['total']++;
                }
            }

            $laporan[] = $laporanSiswa;
        }

        // Calculate rekap keseluruhan
        $rekap = [
            'total_siswa' => count($siswaList),
            'total_pertemuan' => count($absensiData),
            'total_hadir' => array_sum(array_column($laporan, 'hadir')),
            'total_sakit' => array_sum(array_column($laporan, 'sakit')),
            'total_izin' => array_sum(array_column($laporan, 'izin')),
            'total_alpa' => array_sum(array_column($laporan, 'alpa'))
        ];

        // Calculate percentage
        $totalKehadiran = $rekap['total_siswa'] * $rekap['total_pertemuan'];
        if ($totalKehadiran > 0) {
            $rekap['persentase_hadir'] = round(($rekap['total_hadir'] / $totalKehadiran) * 100, 2);
            $rekap['persentase_sakit'] = round(($rekap['total_sakit'] / $totalKehadiran) * 100, 2);
            $rekap['persentase_izin'] = round(($rekap['total_izin'] / $totalKehadiran) * 100, 2);
            $rekap['persentase_alpa'] = round(($rekap['total_alpa'] / $totalKehadiran) * 100, 2);
        }

        $data = [
            'guru' => $guru,
            'namaKelas' => $kelas['nama_kelas'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'laporan' => $laporan,
            'rekap' => $rekap
        ];

        return view('guru/laporan/print', $data);
    }
}
