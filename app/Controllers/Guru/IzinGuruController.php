<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\IzinGuruModel;

/**
 * IzinGuruController (Guru Role)
 * 
 * Handles teacher leave requests from guru perspective.
 * Features:
 * - Submit new izin request
 * - View submitted requests and their status
 * - Upload supporting documents
 * 
 * @package App\Controllers\Guru
 * @author SIMACCA Team
 * @version 2.0.0
 */
class IzinGuruController extends BaseController
{
    protected $guruModel;
    protected $izinGuruModel;
    protected $session;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->izinGuruModel = new IzinGuruModel();
        $this->session = session();
    }

    /**
     * Display list of izin requests
     */
    public function index()
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get all izin requests for this guru with approver info
        $izinList = $this->izinGuruModel
            ->select('izin_guru.*, users.nama_lengkap as approver_name')
            ->join('users', 'users.id = izin_guru.disetujui_oleh', 'left')
            ->where('izin_guru.guru_id', $guru['id'])
            ->orderBy('izin_guru.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Izin Guru',
            'pageTitle' => 'Pengajuan Izin Guru',
            'pageDescription' => 'Kelola pengajuan izin dan cuti',
            'guru' => $guru,
            'izinList' => $izinList,
        ];

        return view('guru/izin_guru/index', $data);
    }

    /**
     * Show form to create new izin request
     */
    public function create()
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login')->with('error', 'Data guru nggak ketemu 🤔');
        }

        $data = [
            'title' => 'Ajukan Izin',
            'pageTitle' => 'Ajukan Izin Baru',
            'pageDescription' => 'Form pengajuan izin/cuti guru',
            'guru' => $guru,
        ];

        return view('guru/izin_guru/create', $data);
    }

    /**
     * Store new izin request
     */
    public function store()
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Validation rules
        $rules = [
            'tanggal_mulai' => 'required|valid_date',
            'tanggal_selesai' => 'required|valid_date',
            'jenis_izin' => 'required|in_list[izin,sakit,cuti,dinas_luar,lainnya]',
            'alasan' => 'required|min_length[10]',
            'berkas' => 'permit_empty|uploaded[berkas]|max_size[berkas,2048]|ext_in[berkas,pdf,jpg,jpeg,png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle file upload
        $berkas = null;
        $file = $this->request->getFile('berkas');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads/izin_guru', $newName);

            // Optimize image server-side (safety net after client-side compression)
            $filePath = WRITEPATH . 'uploads/izin_guru/' . $newName;
            if (file_exists($filePath) && $file->isImage()) {
                helper('image');
                optimize_izin_photo($filePath, $filePath);
            }

            $berkas = $newName;
        }

        // Prepare data
        $data = [
            'guru_id' => $guru['id'],
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'jenis_izin' => $this->request->getPost('jenis_izin'),
            'alasan' => $this->request->getPost('alasan'),
            'berkas' => $berkas,
            'status' => 'pending',
        ];

        if ($this->izinGuruModel->insert($data)) {
            return redirect()->to('/guru/izin-guru')->with('success', 'Izin udah dikirim nih! Tunggu persetujuan ya 📨✨');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal ajukan izin nih 😅');
        }
    }

    /**
     * Show detail of izin request
     */
    public function show($id)
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get izin with approver info
        $izin = $this->izinGuruModel
            ->select('izin_guru.*, users.nama_lengkap as approver_name')
            ->join('users', 'users.id = izin_guru.disetujui_oleh', 'left')
            ->where('izin_guru.id', $id)
            ->where('izin_guru.guru_id', $guru['id'])
            ->first();

        if (!$izin) {
            return redirect()->to('/guru/izin-guru')->with('error', 'Data izin nggak ketemu 🤔');
        }

        $data = [
            'title' => 'Detail Izin',
            'pageTitle' => 'Detail Pengajuan Izin',
            'pageDescription' => 'Informasi detail pengajuan izin',
            'guru' => $guru,
            'izin' => $izin,
        ];

        return view('guru/izin_guru/show', $data);
    }

    /**
     * Delete pending izin request
     */
    public function delete($id)
    {
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/login')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get izin
        $izin = $this->izinGuruModel
            ->where('id', $id)
            ->where('guru_id', $guru['id'])
            ->first();

        if (!$izin) {
            return redirect()->to('/guru/izin-guru')->with('error', 'Data izin nggak ketemu 🤔');
        }

        // Only pending requests can be deleted
        if ($izin['status'] !== 'pending') {
            return redirect()->to('/guru/izin-guru')->with('error', 'Cuma izin yang masih pending yang bisa dihapus ya 😊');
        }

        // Delete file if exists
        if ($izin['berkas'] && file_exists(WRITEPATH . 'uploads/izin_guru/' . $izin['berkas'])) {
            unlink(WRITEPATH . 'uploads/izin_guru/' . $izin['berkas']);
        }

        if ($this->izinGuruModel->delete($id)) {
            return redirect()->to('/guru/izin-guru')->with('success', 'Izin udah dihapus ✓');
        } else {
            return redirect()->to('/guru/izin-guru')->with('error', 'Gagal hapus izin nih 😅');
        }
    }
}
