<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Services\JurnalPklService;
use App\Models\SiswaModel;
use App\Models\SiswaPklModel;
use App\Models\TempatPklModel;
use App\Models\PembimbingPklModel;

helper('setting');

class JurnalPklController extends BaseController
{
    protected $siswaModel;
    protected $jurnalService;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->jurnalService = new JurnalPklService();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $status = $this->request->getGet('status');
        $jurnalPklStartDate = get_jurnal_pkl_start_date();

        $weeklyResult = $this->jurnalService->getWeeklyGrouped($siswa['id'], $jurnalPklStartDate);
        $weeklyData = $weeklyResult['success'] ? $weeklyResult['data'] : [];

        $statsResult = $this->jurnalService->getStatistics($siswa['id']);
        $stats = $statsResult['success'] ? $statsResult['data'] : [
            'total' => 0, 'pending' => 0, 'disetujui' => 0, 'revisi' => 0, 'ditolak' => 0
        ];

        $data = [
            'title' => 'Jurnal PKL',
            'siswa' => $siswa,
            'weeklyData' => $weeklyData,
            'stats' => $stats,
            'status' => $status,
            'jurnalPklStartDate' => $jurnalPklStartDate,
        ];

        return view('siswa/jurnal_pkl/index', $data);
    }

    public function create()
    {
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $data = [
            'title' => 'Tambah Jurnal PKL',
            'siswa' => $siswa,
        ];

        return view('siswa/jurnal_pkl/create', $data);
    }

    public function store()
    {
        helper('security');

        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->back();
        }

        $rules = [
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'required|min_length[10]',
            'tanggal' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorList = '<ul class="list-disc ml-4">';
            foreach ($errors as $field => $error) {
                $errorList .= '<li>' . $error . '</li>';
            }
            $errorList .= '</ul>';
            session()->setFlashdata('error', 'Lengkapi datanya ya 😊' . $errorList);
            return redirect()->back()->withInput();
        }

        $uploadPath = WRITEPATH . 'uploads/jurnal_pkl';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $foto = $this->request->getFile('foto');
        $fotoName = null;

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/webp',
            ];

            $validation = validate_file_upload($foto, $allowedTypes, 5242880);

            if (!$validation['valid']) {
                session()->setFlashdata('error', '📁 ' . $validation['error']);
                return redirect()->back()->withInput();
            }

            try {
                $fotoName = 'jurnal_pkl_' . time() . '_' . uniqid() . '.' . $foto->getExtension();
                $foto->move($uploadPath, $fotoName);

                helper('image');
                $filePath = $uploadPath . '/' . $fotoName;
                $optimized = optimize_jurnal_pkl_photo($filePath, $filePath);

                if ($optimized) {
                    log_message('info', "[JURNAL PKL] Image optimized: {$fotoName}");
                }
            } catch (\Exception $e) {
                log_message('error', '[JURNAL PKL] File upload failed: ' . $e->getMessage());
                session()->setFlashdata('error', 'Upload foto gagal');
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'siswa_id' => $siswa['id'],
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'foto' => $fotoName,
            'status' => 'pending',
        ];

        $result = $this->jurnalService->create($data);

        if ($result['success']) {
            session()->setFlashdata('success', 'Jurnal PKL berhasil ditambahkan 📝✨');
            return redirect()->to('/siswa/jurnal-pkl');
        } else {
            if ($fotoName && file_exists($uploadPath . '/' . $fotoName)) {
                unlink($uploadPath . '/' . $fotoName);
            }

            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $result = $this->jurnalService->getById($id);
        if (!$result['success']) {
            session()->setFlashdata('error', 'Jurnal tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $jurnal = $result['data'];

        if ($jurnal['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Akses ditolak');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        if ($jurnal['status'] === 'disetujui') {
            session()->setFlashdata('error', 'Jurnal yang sudah disetujui tidak dapat diedit');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $data = [
            'title' => 'Edit Jurnal PKL',
            'siswa' => $siswa,
            'jurnal' => $jurnal,
        ];

        return view('siswa/jurnal_pkl/edit', $data);
    }

    public function update($id)
    {
        helper('security');

        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            session()->setFlashdata('error', 'Data siswa tidak ditemukan');
            return redirect()->back();
        }

        $jurnalResult = $this->jurnalService->getById($id);
        if (!$jurnalResult['success']) {
            session()->setFlashdata('error', 'Jurnal tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $jurnal = $jurnalResult['data'];

        if ($jurnal['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Akses ditolak');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        if ($jurnal['status'] === 'disetujui') {
            session()->setFlashdata('error', 'Jurnal yang sudah disetujui tidak dapat diedit');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $rules = [
            'nama_kegiatan' => 'required|min_length[3]|max_length[255]',
            'deskripsi' => 'required|min_length[10]',
            'tanggal' => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorList = '<ul class="list-disc ml-4">';
            foreach ($errors as $field => $error) {
                $errorList .= '<li>' . $error . '</li>';
            }
            $errorList .= '</ul>';
            session()->setFlashdata('error', 'Lengkapi datanya ya 😊' . $errorList);
            return redirect()->back()->withInput();
        }

        $uploadPath = WRITEPATH . 'uploads/jurnal_pkl';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $foto = $this->request->getFile('foto');
        $fotoName = $jurnal['foto'];

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = [
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/webp',
            ];

            $validation = validate_file_upload($foto, $allowedTypes, 5242880);

            if (!$validation['valid']) {
                session()->setFlashdata('error', '📁 ' . $validation['error']);
                return redirect()->back()->withInput();
            }

            try {
                $newFotoName = 'jurnal_pkl_' . time() . '_' . uniqid() . '.' . $foto->getExtension();
                $foto->move($uploadPath, $newFotoName);

                helper('image');
                $filePath = $uploadPath . '/' . $newFotoName;
                $optimized = optimize_jurnal_pkl_photo($filePath, $filePath);

                if ($optimized) {
                    log_message('info', "[JURNAL PKL] Image optimized: {$newFotoName}");
                }

                if ($jurnal['foto'] && file_exists($uploadPath . '/' . $jurnal['foto'])) {
                    @unlink($uploadPath . '/' . $jurnal['foto']);
                }

                $fotoName = $newFotoName;
            } catch (\Exception $e) {
                log_message('error', '[JURNAL PKL] File upload failed: ' . $e->getMessage());
                session()->setFlashdata('error', 'Upload foto gagal');
                return redirect()->back()->withInput();
            }
        }

        $removeFoto = $this->request->getPost('remove_foto');
        if ($removeFoto === '1' && $jurnal['foto']) {
            if (file_exists($uploadPath . '/' . $jurnal['foto'])) {
                @unlink($uploadPath . '/' . $jurnal['foto']);
            }
            $fotoName = null;
        }

        $data = [
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tanggal' => $this->request->getPost('tanggal'),
            'foto' => $fotoName,
        ];

        if ($jurnal['status'] === 'revisi') {
            $data['status'] = 'pending';
            $data['verified_by'] = null;
            $data['verified_at'] = null;
            $data['catatan_pembimbing'] = null;
        }

        $result = $this->jurnalService->update($id, $data);

        if ($result['success']) {
            session()->setFlashdata('success', 'Jurnal PKL berhasil diperbarui 📝✨');
            return redirect()->to('/siswa/jurnal-pkl');
        } else {
            session()->setFlashdata('error', $result['message']);
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $jurnalResult = $this->jurnalService->getById($id);
        if (!$jurnalResult['success']) {
            session()->setFlashdata('error', 'Jurnal tidak ditemukan');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $jurnal = $jurnalResult['data'];

        if ($jurnal['siswa_id'] != $siswa['id']) {
            session()->setFlashdata('error', 'Akses ditolak');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $result = $this->jurnalService->delete($id);

        if ($result['success']) {
            session()->setFlashdata('success', 'Jurnal berhasil dihapus');
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/siswa/jurnal-pkl');
    }

    public function detail($tahun, $minggu)
    {
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $jurnalPklStartDate = get_jurnal_pkl_start_date();
        $result = $this->jurnalService->getByWeek($siswa['id'], $tahun, $minggu, $jurnalPklStartDate);
        $entries = $result['success'] ? $result['data'] : [];

        $allDisetujui = !empty($entries);
        foreach ($entries as $entry) {
            if ($entry['status'] !== 'disetujui') {
                $allDisetujui = false;
                break;
            }
        }

        $data = [
            'title' => 'Detail Jurnal PKL',
            'siswa' => $siswa,
            'entries' => $entries,
            'tahun' => $tahun,
            'minggu' => $minggu,
            'allDisetujui' => $allDisetujui,
            'jurnalPklStartDate' => $jurnalPklStartDate,
        ];

        return view('siswa/jurnal_pkl/detail', $data);
    }

    public function print($tahun, $minggu)
    {
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa tidak ditemukan');
        }

        $jurnalPklStartDate = get_jurnal_pkl_start_date();
        $result = $this->jurnalService->getByWeek($siswa['id'], $tahun, $minggu, $jurnalPklStartDate);
        $entries = $result['success'] ? $result['data'] : [];

        $allDisetujui = !empty($entries);
        foreach ($entries as $entry) {
            if ($entry['status'] !== 'disetujui') {
                $allDisetujui = false;
                break;
            }
        }

        if (!$allDisetujui) {
            session()->setFlashdata('error', 'Semua jurnal harus disetujui sebelum dicetak');
            return redirect()->to('/siswa/jurnal-pkl');
        }

        $siswaPklModel = new SiswaPklModel();
        $tempatPklModel = new TempatPklModel();
        $pembimbingPklModel = new PembimbingPklModel();

        $siswaPkl = $siswaPklModel->getBySiswaAndTahun($siswa['id'], $siswa['tahun_ajaran']);
        $tempatPkl = null;
        $pembimbing = null;
        if ($siswaPkl && !empty($siswaPkl['tempat_pkl_id'])) {
            $tempatPkl = $tempatPklModel->find($siswaPkl['tempat_pkl_id']);
            $pembimbing = $pembimbingPklModel->getByTempatPklAndTahun($siswaPkl['tempat_pkl_id'], $siswaPkl['tahun_ajaran']);
        }

        $data = [
            'title' => 'Cetak Jurnal PKL',
            'siswa' => $siswa,
            'entries' => $entries,
            'tahun' => $tahun,
            'minggu' => $minggu,
            'tempatPkl' => $tempatPkl,
            'siswaPkl' => $siswaPkl,
            'pembimbing' => $pembimbing,
            'jurnalPklStartDate' => $jurnalPklStartDate,
        ];

        return view('siswa/jurnal_pkl/print', $data);
    }
}
