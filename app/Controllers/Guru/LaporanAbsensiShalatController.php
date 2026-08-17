<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\AbsensiShalatModel;
use App\Models\GuruModel;
use App\Models\GuruPiketModel;

class LaporanAbsensiShalatController extends BaseController
{
    protected $absensiShalatModel;
    protected $guruModel;
    protected $guruPiketModel;

    public function __construct()
    {
        $this->absensiShalatModel = new AbsensiShalatModel();
        $this->guruModel = new GuruModel();
        $this->guruPiketModel = new GuruPiketModel();
    }

    public function index()
    {
        $userId = $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            $this->session->setFlashdata('error', 'Data guru tidak ditemukan');
            return redirect()->to('/guru/dashboard');
        }

        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');

        $rekap = $this->absensiShalatModel->getRekapByGuru($guru['id'], $from, $to);

        // Group by date
        $grouped = [];
        foreach ($rekap as $row) {
            $date = date('Y-m-d', strtotime($row['waktu_sesi']));
            $grouped[$date][] = $row;
        }

        // Summary stats
        $totalSesi = $this->absensiShalatModel->getTotalSessions($from, $to);
        $totalKehadiran = count($rekap);
        $siswaUnik = count(array_unique(array_column($rekap, 'nis')));

        $data = [
            'title'          => 'Laporan Absensi Shalat',
            'guru'           => $guru,
            'from'           => $from,
            'to'             => $to,
            'rekap'          => $grouped,
            'totalSesi'      => $totalSesi,
            'totalKehadiran' => $totalKehadiran,
            'siswaUnik'      => $siswaUnik,
        ];

        return view('guru/laporan_absensi_shalat/index', $data);
    }
}
