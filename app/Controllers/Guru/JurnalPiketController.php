<?php

namespace App\Controllers\Guru;

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
    }

    /**
     * Get authenticated guru data or redirect
     */
    private function getGuruDataOrRedirect()
    {
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return null;
        }

        return $guru;
    }

    /**
     * List journals created by this guru
     */
    public function index()
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $result = $this->jurnalPiketService->getJurnalByGuru($guru['id'], $startDate, $endDate);

        $data = [
            'title'     => 'Jurnal Piket Guru',
            'guru'      => $guru,
            'jurnalList' => $result['data'] ?? [],
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ];

        return view('guru/jurnal_piket/index', $data);
    }

    /**
     * Show form to create new jurnal piket entry
     */
    public function create()
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');
        $rincianTugas = $this->jurnalPiketService->getRincianTugasForGuruAndDate($guru['id'], $tanggal);

        $data = [
            'title'        => 'Tambah Jurnal Piket',
            'guru'         => $guru,
            'tanggal'      => $tanggal,
            'rincianTugas' => $rincianTugas,
            'tahunAjaran'  => get_active_tahun_ajaran(),
            'semester'     => (int) date('m', strtotime($tanggal)) >= 7 && (int) date('m', strtotime($tanggal)) <= 12 ? 'ganjil' : 'genap',
        ];

        return view('guru/jurnal_piket/create', $data);
    }

    /**
     * Store new jurnal piket entry
     */
    public function store()
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $tanggal = $this->request->getPost('tanggal') ?: date('Y-m-d');
        $month = (int) date('m', strtotime($tanggal));
        $semester = ($month >= 7 && $month <= 12) ? 'ganjil' : 'genap';

        $data = [
            'guru_id'       => $guru['id'],
            'tanggal'       => $tanggal,
            'tahun_ajaran'  => get_active_tahun_ajaran(),
            'semester'      => $semester,
            'rincian_tugas' => $this->request->getPost('rincian_tugas'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'catatan'       => $this->request->getPost('catatan'),
        ];

        $file = $this->request->getFile('foto_dokumentasi');

        $result = $this->jurnalPiketService->create($data, $file);

        if ($this->request->isAJAX() || str_contains($this->request->getHeaderLine('Accept'), 'application/json')) {
            if (!$result['success']) {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => $result['message'],
                    'errors'     => $result['errors'] ?? [],
                    'csrf_token' => csrf_token(),
                    'csrf_hash'  => csrf_hash(),
                ]);
            }

            session()->setFlashdata('success', 'Jurnal piket berhasil disimpan');
            return $this->response->setJSON([
                'success'      => true,
                'message'      => 'Jurnal piket berhasil disimpan',
                'redirect_url' => base_url('guru/jurnal-piket'),
                'csrf_token'   => csrf_token(),
                'csrf_hash'    => csrf_hash(),
            ]);
        }

        if (!$result['success']) {
            $redirect = redirect()->back()->withInput()->with('error', $result['message']);
            if (!empty($result['errors'])) {
                $redirect->with('errors', $result['errors']);
            }
            return $redirect;
        }

        return redirect()->to('/guru/jurnal-piket')->with('success', 'Jurnal piket berhasil disimpan');
    }

    /**
     * Show journal detail
     */
    public function show($id)
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $result = $this->jurnalPiketService->getById((int) $id);

        if (!$result['success']) {
            return redirect()->to('/guru/jurnal-piket')->with('error', $result['message']);
        }

        $jurnal = $result['data'];
        if ($jurnal['guru_id'] != $guru['id']) {
            return redirect()->to('/guru/jurnal-piket')->with('error', 'Akses ditolak');
        }

        $data = [
            'title'  => 'Detail Jurnal Piket',
            'guru'   => $guru,
            'jurnal' => $jurnal,
        ];

        return view('guru/jurnal_piket/show', $data);
    }

    /**
     * Show form to edit jurnal piket
     */
    public function edit($id)
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $result = $this->jurnalPiketService->getById((int) $id);

        if (!$result['success']) {
            return redirect()->to('/guru/jurnal-piket')->with('error', $result['message']);
        }

        $jurnal = $result['data'];
        if ($jurnal['guru_id'] != $guru['id']) {
            return redirect()->to('/guru/jurnal-piket')->with('error', 'Akses ditolak');
        }

        $data = [
            'title'  => 'Edit Jurnal Piket',
            'guru'   => $guru,
            'jurnal' => $jurnal,
        ];

        return view('guru/jurnal_piket/edit', $data);
    }

    /**
     * Update jurnal piket entry
     */
    public function update($id)
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data guru tidak ditemukan']);
            }
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $resultCheck = $this->jurnalPiketService->getById((int) $id);
        if (!$resultCheck['success'] || $resultCheck['data']['guru_id'] != $guru['id']) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak / data tidak ditemukan']);
            }
            return redirect()->to('/guru/jurnal-piket')->with('error', 'Akses ditolak / data tidak ditemukan');
        }

        $tanggal = $this->request->getPost('tanggal');
        $data = [
            'guru_id'       => $guru['id'],
            'tanggal'       => $tanggal,
            'rincian_tugas' => $this->request->getPost('rincian_tugas'),
            'deskripsi'     => $this->request->getPost('deskripsi'),
            'catatan'       => $this->request->getPost('catatan'),
        ];

        $file = $this->request->getFile('foto_dokumentasi');

        $result = $this->jurnalPiketService->update((int) $id, $data, $file);

        if ($this->request->isAJAX() || str_contains($this->request->getHeaderLine('Accept'), 'application/json')) {
            if (!$result['success']) {
                return $this->response->setJSON([
                    'success'    => false,
                    'message'    => $result['message'],
                    'errors'     => $result['errors'] ?? [],
                    'csrf_token' => csrf_token(),
                    'csrf_hash'  => csrf_hash(),
                ]);
            }

            session()->setFlashdata('success', 'Jurnal piket berhasil diperbarui');
            return $this->response->setJSON([
                'success'      => true,
                'message'      => 'Jurnal piket berhasil diperbarui',
                'redirect_url' => base_url('guru/jurnal-piket'),
                'csrf_token'   => csrf_token(),
                'csrf_hash'    => csrf_hash(),
            ]);
        }

        if (!$result['success']) {
            $redirect = redirect()->back()->withInput()->with('error', $result['message']);
            if (!empty($result['errors'])) {
                $redirect->with('errors', $result['errors']);
            }
            return $redirect;
        }

        return redirect()->to('/guru/jurnal-piket')->with('success', 'Jurnal piket berhasil diperbarui');
    }

    /**
     * Delete jurnal piket entry
     */
    public function delete($id)
    {
        $guru = $this->getGuruDataOrRedirect();
        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru tidak ditemukan');
        }

        $resultCheck = $this->jurnalPiketService->getById((int) $id);
        if (!$resultCheck['success'] || $resultCheck['data']['guru_id'] != $guru['id']) {
            return redirect()->to('/guru/jurnal-piket')->with('error', 'Akses ditolak / data tidak ditemukan');
        }

        $result = $this->jurnalPiketService->delete((int) $id);

        if (!$result['success']) {
            return redirect()->to('/guru/jurnal-piket')->with('error', $result['message']);
        }

        return redirect()->to('/guru/jurnal-piket')->with('success', 'Jurnal piket berhasil dihapus');
    }
}
