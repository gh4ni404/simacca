<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\AbsensiShalatModel;
use App\Models\GuruModel;
use App\Models\GuruPiketModel;
use App\Models\KelasModel;

class LaporanAbsensiShalatController extends BaseController
{
    protected $absensiShalatModel;
    protected $guruModel;
    protected $guruPiketModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->session = session();
        $this->absensiShalatModel = new AbsensiShalatModel();
        $this->guruModel = new GuruModel();
        $this->guruPiketModel = new GuruPiketModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan');
            return redirect()->to('/guru/dashboard');
        }

        $from       = $this->request->getGet('from') ?: date('Y-m-01');
        $to         = $this->request->getGet('to') ?: date('Y-m-t');
        $rawKelasId = $this->request->getGet('kelas_id');
        $kelasId    = !empty($rawKelasId) ? (int) $rawKelasId : null;
        $tab        = $this->request->getGet('tab') ?: 'personal';

        // 1. Rekapan Presensi Shalat Pribadi (Guru sebagai Peserta)
        $rekapPersonalRaw = $this->absensiShalatModel->getRekapByGuruPersonal((int)$guru['id'], $from, $to);
        $rekapPersonal = [];
        foreach ($rekapPersonalRaw as $row) {
            $date = date('Y-m-d', strtotime($row['waktu_sesi']));
            $rekapPersonal[$date][] = $row;
        }

        // 2. Rekapan Hasil Piket Shalat (Guru sebagai Guru Piket)
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

        // Summary stats
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
            'title'                => 'Laporan Absensi Shalat',
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
        ];

        return view('guru/laporan_absensi_shalat/index', $data);
    }

    public function print()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan');
            return redirect()->to('/guru/dashboard');
        }

        $from       = $this->request->getGet('from') ?: date('Y-m-01');
        $to         = $this->request->getGet('to') ?: date('Y-m-t');
        $rawKelasId = $this->request->getGet('kelas_id');
        $kelasId    = !empty($rawKelasId) ? (int) $rawKelasId : null;
        $type       = $this->request->getGet('type') ?: 'personal'; // 'personal', 'piket_siswa', 'piket_guru', 'piket_semua'

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
            'title'             => 'Cetak Laporan Absensi Shalat',
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
}
