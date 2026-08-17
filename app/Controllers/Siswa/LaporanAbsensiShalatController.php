<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\AbsensiShalatModel;
use App\Models\SiswaModel;

class LaporanAbsensiShalatController extends BaseController
{
    protected $absensiShalatModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->absensiShalatModel = new AbsensiShalatModel();
        $this->siswaModel = new SiswaModel();
    }

    public function index()
    {
        $userId = $this->session->get('userId');
        $siswa = $this->siswaModel->where('user_id', $userId)->first();

        if (!$siswa) {
            $this->session->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->to('/siswa/dashboard');
        }

        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');

        $rekap = $this->absensiShalatModel->getRekapBySiswa($siswa['id'], $from, $to);

        // Group by date
        $grouped = [];
        foreach ($rekap as $row) {
            $date = date('Y-m-d', strtotime($row['waktu_sesi']));
            $grouped[$date][] = $row;
        }

        $totalHadir = count($rekap);
        $hariBerlalu = (int) (strtotime($to) - strtotime($from)) / 86400 + 1;

        $data = [
            'title'      => 'Riwayat Absensi Shalat',
            'siswa'      => $siswa,
            'from'       => $from,
            'to'         => $to,
            'rekap'      => $grouped,
            'totalHadir' => $totalHadir,
            'hariBerlalu'=> $hariBerlalu,
        ];

        return view('siswa/laporan_absensi_shalat/index', $data);
    }
}
