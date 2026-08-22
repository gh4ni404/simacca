<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiShalatModel;
use App\Models\KelasModel;

class LaporanAbsensiShalatController extends BaseController
{
    protected $absensiShalatModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->absensiShalatModel = new AbsensiShalatModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $from = $this->request->getGet('from') ?: date('Y-m-01');
        $to = $this->request->getGet('to') ?: date('Y-m-t');
        $kelasId = $this->request->getGet('kelas_id');

        $rekapKelas = $this->absensiShalatModel->getRekapPerKelas($from, $to);
        $rekapSiswa = $this->absensiShalatModel->getRekapPerSiswa($from, $to, $kelasId);
        $rekapGuru  = $this->absensiShalatModel->getRekapPerGuru($from, $to);
        $rekapHarian = $this->absensiShalatModel->getRekapHarian($from, $to);
        $totalSessions = $this->absensiShalatModel->getTotalSessions($from, $to);
        $totalSiswaHadir = count($rekapSiswa);
        $totalGuruHadir  = count($rekapGuru);
        $kelasList = $this->kelasModel->getListKelas(get_active_tahun_ajaran());

        $data = [
            'title'           => 'Laporan Absensi Shalat',
            'from'            => $from,
            'to'              => $to,
            'kelasId'         => $kelasId,
            'kelasList'       => $kelasList,
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
}
