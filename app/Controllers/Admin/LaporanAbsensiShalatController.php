<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiShalatModel;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;

class LaporanAbsensiShalatController extends BaseController
{
    protected $absensiShalatModel;
    protected $guruModel;
    protected $kelasModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->absensiShalatModel = new AbsensiShalatModel();
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');
        $rawKelasId = $this->request->getGet('kelas_id');
        $kelasId = !empty($rawKelasId) ? (int) $rawKelasId : null;
        $tab = $this->request->getGet('tab') ?: 'siswa';

        $rekapKelas = $this->absensiShalatModel->getRekapPerKelas($from, $to);
        $rekapSiswa = $this->absensiShalatModel->getRekapPerSiswa($from, $to, $kelasId);
        $rekapGuru  = $this->absensiShalatModel->getRekapPerGuru($from, $to);
        $rekapHarian = $this->absensiShalatModel->getRekapHarian($from, $to);
        $totalSessions = $this->absensiShalatModel->getTotalSessions($from, $to);
        $totalSiswaHadir = count($rekapSiswa);
        $totalGuruHadir  = count($rekapGuru);
        $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());
        $guruList  = $this->guruModel->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title'           => 'Laporan Absensi Shalat',
            'from'            => $from,
            'to'              => $to,
            'kelasId'         => $kelasId,
            'tab'             => $tab,
            'kelasList'       => $kelasList,
            'guruList'        => $guruList,
            'rekapKelas'      => $rekapKelas,
            'rekapSiswa'      => $rekapSiswa,
            'rekapGuru'       => $rekapGuru,
            'rekapHarian'     => $rekapHarian,
            'totalSessions'   => $totalSessions,
            'totalSiswaHadir' => $totalSiswaHadir,
            'totalGuruHadir'  => $totalGuruHadir,
        ];

        return view('admin/laporan_absensi_shalat/index', $data);
    }

    public function print()
    {
        $from       = $this->request->getGet('from') ?: date('Y-m-01');
        $to         = $this->request->getGet('to') ?: date('Y-m-t');
        $rawKelasId = $this->request->getGet('kelas_id');
        $kelasId    = !empty($rawKelasId) ? (int) $rawKelasId : null;
        $type       = $this->request->getGet('type') ?: 'semua'; // 'siswa', 'guru', 'harian', 'semua'

        $rekapKelas = $this->absensiShalatModel->getRekapPerKelas($from, $to);
        $rekapSiswa = $this->absensiShalatModel->getRekapPerSiswa($from, $to, $kelasId);
        $rekapGuru  = $this->absensiShalatModel->getRekapPerGuru($from, $to);
        $rekapHarian = $this->absensiShalatModel->getRekapHarian($from, $to);
        $totalSessions = $this->absensiShalatModel->getTotalSessions($from, $to);

        $namaKelasSelected = null;
        if ($kelasId) {
            $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());
            $namaKelasSelected = $kelasList[$kelasId] ?? null;
        }

        $data = [
            'title'             => 'Cetak Laporan Absensi Shalat Admin',
            'from'              => $from,
            'to'                => $to,
            'kelasId'           => $kelasId,
            'namaKelasSelected' => $namaKelasSelected,
            'type'              => $type,
            'rekapKelas'        => $rekapKelas,
            'rekapSiswa'        => $rekapSiswa,
            'rekapGuru'         => $rekapGuru,
            'rekapHarian'       => $rekapHarian,
            'totalSessions'     => $totalSessions,
            'auto_print'        => true,
        ];

        return view('admin/laporan_absensi_shalat/print', $data);
    }

    /**
     * Quick Test: Simulasikan Tampilan Portal Guru tanpa perlu switch akun
     */
    public function previewGuru()
    {
        $guruId     = $this->request->getGet('guru_id');
        $from       = $this->request->getGet('from') ?: date('Y-m-01');
        $to         = $this->request->getGet('to') ?: date('Y-m-t');
        $rawKelasId = $this->request->getGet('kelas_id');
        $kelasId    = !empty($rawKelasId) ? (int) $rawKelasId : null;
        $tab        = $this->request->getGet('tab') ?: 'personal';

        if ($guruId) {
            $guru = $this->guruModel->find((int)$guruId);
        } else {
            $guru = $this->guruModel->first();
        }

        if (!$guru) {
            return redirect()->to('/admin/laporan/absensi-shalat')->with('error', 'Data guru tidak ditemukan');
        }

        // 1. Rekapan Presensi Shalat Pribadi
        $rekapPersonalRaw = $this->absensiShalatModel->getRekapByGuruPersonal((int)$guru['id'], $from, $to);
        $rekapPersonal = [];
        foreach ($rekapPersonalRaw as $row) {
            $date = date('Y-m-d', strtotime($row['waktu_sesi']));
            $rekapPersonal[$date][] = $row;
        }

        // 2. Rekapan Hasil Piket Shalat
        $rekapPiketRaw = $this->absensiShalatModel->getRekapByGuru((int)$guru['id'], $from, $to, $kelasId);
        
        $rekapPiketSiswa = [];
        $rekapPiketGuru  = [];

        foreach ($rekapPiketRaw as $row) {
            $date = date('Y-m-d', strtotime($row['waktu_sesi']));
            if (($row['user_type'] ?? 'siswa') === 'guru') {
                $rekapPiketGuru[$date][] = $row;
            } else {
                $rekapPiketSiswa[$date][] = $row;
            }
        }

        $totalSessions        = $this->absensiShalatModel->getTotalSessions($from, $to);
        $totalPersonalHadir   = count($rekapPersonalRaw);
        $totalPiketSiswaHadir = 0;
        foreach ($rekapPiketSiswa as $rows) {
            $totalPiketSiswaHadir += count($rows);
        }
        $totalPiketGuruHadir  = 0;
        foreach ($rekapPiketGuru as $rows) {
            $totalPiketGuruHadir += count($rows);
        }

        $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());

        $data = [
            'title'                => '[Quick Test Admin] Pratinjau Portal Guru: ' . $guru['nama_lengkap'],
            'guru'                 => $guru,
            'from'                 => $from,
            'to'                   => $to,
            'kelasId'              => $kelasId,
            'tab'                  => $tab,
            'kelasList'            => $kelasList,
            'rekapPersonal'        => $rekapPersonal,
            'rekapPiketSiswa'      => $rekapPiketSiswa,
            'rekapPiketGuru'       => $rekapPiketGuru,
            'totalSessions'        => $totalSessions,
            'totalPersonalHadir'   => $totalPersonalHadir,
            'totalPiketSiswaHadir' => $totalPiketSiswaHadir,
            'totalPiketGuruHadir'  => $totalPiketGuruHadir,
            'isQuickTest'          => true,
        ];

        return view('guru/laporan_absensi_shalat/index', $data);
    }

    /**
     * Quick Test: Simulasikan Cetak Portal Guru untuk Guru tertentu
     */
    public function previewGuruPrint()
    {
        $guruId     = $this->request->getGet('guru_id');
        $from       = $this->request->getGet('from') ?: date('Y-m-01');
        $to         = $this->request->getGet('to') ?: date('Y-m-t');
        $rawKelasId = $this->request->getGet('kelas_id');
        $kelasId    = !empty($rawKelasId) ? (int) $rawKelasId : null;
        $type       = $this->request->getGet('type') ?: 'personal';

        if ($guruId) {
            $guru = $this->guruModel->find((int)$guruId);
        } else {
            $guru = $this->guruModel->first();
        }

        if (!$guru) {
            return redirect()->to('/admin/laporan/absensi-shalat')->with('error', 'Data guru tidak ditemukan');
        }

        $rekapPersonalRaw = $this->absensiShalatModel->getRekapByGuruPersonal((int)$guru['id'], $from, $to);
        $rekapPiketRaw    = $this->absensiShalatModel->getRekapByGuru((int)$guru['id'], $from, $to, $kelasId);

        $rekapPiketSiswa = array_filter($rekapPiketRaw, fn($r) => ($r['user_type'] ?? 'siswa') === 'siswa');
        $rekapPiketGuru  = array_filter($rekapPiketRaw, fn($r) => ($r['user_type'] ?? 'siswa') === 'guru');

        $namaKelasSelected = null;
        if ($kelasId) {
            $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());
            $namaKelasSelected = $kelasList[$kelasId] ?? null;
        }

        $data = [
            'title'             => '[Quick Test] Cetak Laporan Guru: ' . $guru['nama_lengkap'],
            'guru'              => $guru,
            'from'              => $from,
            'to'                => $to,
            'kelasId'           => $kelasId,
            'namaKelasSelected' => $namaKelasSelected,
            'type'              => $type,
            'rekapPersonal'     => $rekapPersonalRaw,
            'rekapPiketSiswa'   => array_values($rekapPiketSiswa),
            'rekapPiketGuru'    => array_values($rekapPiketGuru),
            'auto_print'        => true,
        ];

        return view('guru/laporan_absensi_shalat/print', $data);
    }

    /**
     * Quick Test: Generator Data Uji Cepat untuk simulasi absensi shalat hari ini
     */
    public function generateTestData()
    {
        $db = \Config\Database::connect();
        
        $guru = $this->guruModel->first();
        if (!$guru) {
            return redirect()->back()->with('error', 'Tidak ada data guru untuk simulasi');
        }

        $guruPiket = $db->table('guru_piket')->where('guru_id', $guru['id'])->get()->getRowArray();
        if (!$guruPiket) {
            $db->table('guru_piket')->insert([
                'guru_id' => $guru['id'],
                'created_at' => date('Y-m-01 H:i:s'),
            ]);
            $guruPiketId = $db->insertID();
        } else {
            $guruPiketId = $guruPiket['id'];
        }

        $now = date('Y-m-d H:i:s');
        $token = bin2hex(random_bytes(16));
        $db->table('prayer_sessions')->insert([
            'token' => $token,
            'guru_piket_id' => $guruPiketId,
            'is_active' => 1,
            'created_at' => $now,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+15 seconds')),
            'session_expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
        ]);
        $sessionId = $db->insertID();

        // Absenkan 5-10 siswa
        $siswaList = $this->siswaModel->limit(10)->findAll();
        foreach ($siswaList as $s) {
            $db->query('INSERT IGNORE INTO absensi_shalat (prayer_session_id, siswa_id, user_type, waktu_absen, created_at) VALUES (?, ?, ?, ?, ?)',
                [$sessionId, $s['id'], 'siswa', $now, $now]);
        }

        // Absenkan 3-5 guru
        $guruList = $this->guruModel->limit(5)->findAll();
        foreach ($guruList as $g) {
            $db->query('INSERT IGNORE INTO absensi_shalat (prayer_session_id, guru_id, user_type, waktu_absen, created_at) VALUES (?, ?, ?, ?, ?)',
                [$sessionId, $g['id'], 'guru', $now, $now]);
        }

        return redirect()->to('/admin/laporan/absensi-shalat')->with('success', '⚡ Data simulasi absensi shalat berhasil dibuat untuk pengujian!');
    }
}
