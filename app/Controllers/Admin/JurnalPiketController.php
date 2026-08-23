<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\JurnalPiketService;
use App\Models\GuruModel;

class JurnalPiketController extends BaseController
{
    protected $jurnalPiketService;
    protected $guruModel;

    public function __construct()
    {
        $this->jurnalPiketService = new JurnalPiketService();
        $this->guruModel          = new GuruModel();

        if (!is_logged_in() || !has_role(['admin', 'kepala_sekolah'])) {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Display all teacher duty journals for admin monitoring
     */
    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
        $guruId    = $this->request->getGet('guru_id') ? (int)$this->request->getGet('guru_id') : null;

        $result = $this->jurnalPiketService->getJurnalWithGuru($startDate, $endDate, $guruId);
        $guruList = $this->guruModel->select('id, nama_lengkap, nip')->orderBy('nama_lengkap', 'ASC')->findAll();

        $data = [
            'title'           => 'Monitoring Jurnal Piket Guru',
            'pageTitle'       => 'Rekap Jurnal Piket',
            'pageDescription' => 'Laporan dan dokumentasi pelaksanaan piket harian guru',
            'user'            => $this->getUserData(),
            'jurnalList'      => $result['data'] ?? [],
            'guruList'        => $guruList,
            'startDate'       => $startDate,
            'endDate'         => $endDate,
            'selectedGuruId'  => $guruId,
        ];

        return view('admin/jurnal_piket/index', $data);
    }

    /**
     * Show detail of specific jurnal piket
     */
    public function detail($id)
    {
        $result = $this->jurnalPiketService->getById((int) $id);

        if (!$result['success']) {
            return redirect()->to('/admin/jurnal-piket')->with('error', $result['message']);
        }

        $data = [
            'title'     => 'Detail Jurnal Piket Guru',
            'user'      => $this->getUserData(),
            'jurnal'    => $result['data'],
        ];

        return view('admin/jurnal_piket/detail', $data);
    }

    /**
     * Print report of jurnal piket
     */
    public function print()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-t');
        $guruId    = $this->request->getGet('guru_id') ? (int)$this->request->getGet('guru_id') : null;

        $result = $this->jurnalPiketService->getJurnalWithGuru($startDate, $endDate, $guruId);

        $selectedGuru = null;
        if ($guruId) {
            $selectedGuru = $this->guruModel->find($guruId);
        }

        $data = [
            'title'        => 'Laporan Jurnal Piket Guru',
            'jurnalList'   => $result['data'] ?? [],
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'selectedGuru' => $selectedGuru,
            'tahunAjaran'  => get_active_tahun_ajaran(),
        ];

        return view('admin/jurnal_piket/print', $data);
    }
}
