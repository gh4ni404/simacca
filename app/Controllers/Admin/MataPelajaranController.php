<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataPelajaranModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class MataPelajaranController extends BaseController
{
    protected $mataPelajaranModel;
    protected $session;

    public function __construct()
    {
        $this->mataPelajaranModel = new MataPelajaranModel();
        $this->session = session();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $perPage = $this->request->getGet('per_page') ?? 50;
        $search = $this->request->getGet('search');

        $data = [
            'title' => 'Manajemen Mata Pelajaran',
            'pageTitle' => 'Mata Pelajaran',
            'pageDescription' => 'Kelola data mata pelajaran',
            'mapel' => $this->mataPelajaranModel->getAllMapel($perPage, $search),
            'pager' => $this->mataPelajaranModel->pager,
            'search' => $search,
            'perPage' => $perPage,
            'stats' => $this->mataPelajaranModel->countByKategori(),
        ];

        return view('admin/mata_pelajaran/index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'Tambah Mata Pelajaran',
            'pageTitle' => 'Tambah Mata Pelajaran',
            'pageDescription' => 'Isi form untuk menambahkan mata pelajaran baru',
            'validation' => \Config\Services::validation()
        ];

        return view('admin/mata_pelajaran/create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Validate input
        if (!$this->validate($this->mataPelajaranModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel'),
            'kategori' => $this->request->getPost('kategori')
        ];

        // Save to database
        if ($this->mataPelajaranModel->save($data)) {
            $this->session->setFlashdata('success', 'Sip! Mapel baru sudah masuk.');
            return redirect()->to('/admin/mata-pelajaran');
        } else {
            $this->session->setFlashdata('error', 'Oops, mapel gagal ditambahkan ??');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        $mapel = $this->mataPelajaranModel->find($id);

        if (!$mapel) {
            throw new PageNotFoundException('Mata pelajaran tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Mata Pelajaran',
            'pageTitle' => 'Edit Mata Pelajaran',
            'pageDescription' => 'Edit data mata pelajaran',
            'mapel' => $mapel,
            'validation' => \Config\Services::validation()
        ];

        return view('admin/mata_pelajaran/edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Check if exists
        $mapel = $this->mataPelajaranModel->find($id);
        if (!$mapel) {
            throw new PageNotFoundException('Mata pelajaran tidak ditemukan');
        }

        // Custom validation rule for unique kode_mapel except current id
        $validationRules = $this->mataPelajaranModel->getValidationRules();
        $validationRules['kode_mapel'] = 'required|min_length[3]|max_length[10]|is_unique[mata_pelajaran.kode_mapel,id,' . $id . ']';

        // Validate input
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare data
        $data = [
            'id' => $id,
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel'),
            'kategori' => $this->request->getPost('kategori')
        ];

        // Update database
        if ($this->mataPelajaranModel->save($data)) {
            $this->session->setFlashdata('success', 'Done! Mapel sudah diperbarui ??');
            return redirect()->to('/admin/mata-pelajaran');
        } else {
            $this->session->setFlashdata('error', 'Waduh, update mapel gagal nih ??');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        // Check if user is logged in and has admin role
        if (!$this->session->get('isLoggedIn') || $this->session->get('role') != 'admin') {
            return redirect()->to('/login');
        }

        // Check if exists
        $mapel = $this->mataPelajaranModel->find($id);
        if (!$mapel) {
            throw new PageNotFoundException('Mata pelajaran tidak ditemukan');
        }

        // Check if mata pelajaran is used in jadwal_mengajar
        $db = \Config\Database::connect();
        $checkUsage = $db->table('jadwal_mengajar')
            ->where('mata_pelajaran_id', $id)
            ->countAllResults();

        if ($checkUsage > 0) {
            $this->session->setFlashdata('error', 'Mapel ini masih dipake di jadwal, belum bisa dihapus ya ??');
            return redirect()->back();
        }

        // Check if mata pelajaran is used in guru table
        $checkGuruUsage = $db->table('guru')
            ->where('mata_pelajaran_id', $id)
            ->countAllResults();

        if ($checkGuruUsage > 0) {
            $this->session->setFlashdata('error', 'Ada guru yang ngajar mapel ini, belum bisa dihapus.');
            return redirect()->back();
        }

        // Delete from database
        if ($this->mataPelajaranModel->delete($id)) {
            $this->session->setFlashdata('success', 'Mapel sudah dihapus ?');
        } else {
            $this->session->setFlashdata('error', 'Hmm, gagal hapus mapel ??');
        }

        return redirect()->to('/admin/mata-pelajaran');
    }
}
