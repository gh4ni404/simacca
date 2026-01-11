<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JurnalKbmModel;
use App\Models\AbsensiModel;
use App\Models\GuruModel;
use App\Models\JadwalMengajarModel;

class JurnalController extends BaseController
{
    protected $jurnalModel;
    protected $absensiModel;
    protected $guruModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->jurnalModel = new JurnalKbmModel();
        $this->absensiModel = new AbsensiModel();
        $this->guruModel = new GuruModel();
        $this->jadwalModel = new JadwalMengajarModel();
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
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Get jurnal by guru
        $jurnal = $this->jurnalModel->getByGuru($guru['id'], $startDate, $endDate);

        $data = [
            'title' => 'Jurnal KBM',
            'guru' => $guru,
            'jurnal' => $jurnal,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('guru/jurnal/index', $data);
    }

    public function create($absensiId)
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get absensi detail
        $absensi = $this->absensiModel->getAbsensiWithDetail($absensiId);

        if (!$absensi) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data absensi tidak ditemukan');
        }

        // Cek apakah sudah ada jurnal untuk absensi ini
        if ($this->jurnalModel->isJurnalExist($absensiId)) {
            return redirect()->to('/guru/jurnal')->with('error', 'Jurnal untuk absensi ini sudah dibuat');
        }

        // Cek apakah absensi milik guru yang login
        if ($absensi['nama_guru'] !== $guru['nama_lengkap']) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke absensi ini');
        }

        $data = [
            'title' => 'Tambah Jurnal KBM',
            'guru' => $guru,
            'absensi' => $absensi
        ];

        return view('guru/jurnal/create', $data);
    }

    public function store()
    {
        // Validasi input
        $rules = [
            'absensi_id' => 'required|numeric',
            'tujuan_pembelajaran' => 'required',
            'kegiatan_pembelajaran' => 'required',
            'media_ajar' => 'permit_empty|string',
            'penilaian' => 'permit_empty|string',
            'catatan_khusus' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Cek apakah sudah ada jurnal untuk absensi ini
        $absensiId = $this->request->getPost('absensi_id');
        if ($this->jurnalModel->isJurnalExist($absensiId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Jurnal untuk absensi ini sudah dibuat'
            ]);
        }

        // Insert jurnal
        $data = [
            'absensi_id' => $absensiId,
            'tujuan_pembelajaran' => $this->request->getPost('tujuan_pembelajaran'),
            'kegiatan_pembelajaran' => $this->request->getPost('kegiatan_pembelajaran'),
            'media_ajar' => $this->request->getPost('media_ajar'),
            'penilaian' => $this->request->getPost('penilaian'),
            'catatan_khusus' => $this->request->getPost('catatan_khusus'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->jurnalModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Jurnal KBM berhasil disimpan'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan jurnal KBM',
                'errors' => $this->jurnalModel->errors()
            ]);
        }
    }

    public function edit($jurnalId)
    {
        // Get guru data from session
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        // Get jurnal with detail
        $jurnal = $this->jurnalModel->getJurnalWithDetail($jurnalId);

        if (!$jurnal) {
            return redirect()->to('/guru/jurnal')->with('error', 'Data jurnal tidak ditemukan');
        }

        // Cek apakah jurnal milik guru yang login
        if ($jurnal['nama_guru'] !== $guru['nama_lengkap']) {
            return redirect()->to('/guru/jurnal')->with('error', 'Anda tidak memiliki akses ke jurnal ini');
        }

        $data = [
            'title' => 'Edit Jurnal KBM',
            'guru' => $guru,
            'jurnal' => $jurnal
        ];

        return view('guru/jurnal/edit', $data);
    }

    public function update($jurnalId)
    {
        // Validasi input
        $rules = [
            'tujuan_pembelajaran' => 'required',
            'kegiatan_pembelajaran' => 'required',
            'media_ajar' => 'permit_empty|string',
            'penilaian' => 'permit_empty|string',
            'catatan_khusus' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Cek apakah jurnal ada
        $jurnal = $this->jurnalModel->find($jurnalId);
        if (!$jurnal) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data jurnal tidak ditemukan'
            ]);
        }

        // Update jurnal (tanpa update absensi_id)
        $data = [
            'tujuan_pembelajaran' => $this->request->getPost('tujuan_pembelajaran'),
            'kegiatan_pembelajaran' => $this->request->getPost('kegiatan_pembelajaran'),
            'media_ajar' => $this->request->getPost('media_ajar'),
            'penilaian' => $this->request->getPost('penilaian'),
            'catatan_khusus' => $this->request->getPost('catatan_khusus')
        ];

        if ($this->jurnalModel->update($jurnalId, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Jurnal KBM berhasil diperbarui'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui jurnal KBM',
                'errors' => $this->jurnalModel->errors()
            ]);
        }
    }
}
