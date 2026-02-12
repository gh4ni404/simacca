<?php

namespace App\Controllers\Wakakur;

use App\Controllers\BaseController;
use App\Models\IzinGuruModel;
use App\Models\GuruModel;
use App\Models\AbsensiGuruModel;

/**
 * IzinGuruController (Wakakur Role)
 * 
 * Handles approval/rejection of teacher leave requests.
 * Features:
 * - View all izin guru requests
 * - Approve/reject requests
 * - Auto-create absensi_guru records on approval
 * 
 * @package App\Controllers\Wakakur
 * @author SIMACCA Team
 * @version 2.0.0
 */
class IzinGuruController extends BaseController
{
    protected $izinGuruModel;
    protected $guruModel;
    protected $absensiGuruModel;
    protected $session;

    public function __construct()
    {
        $this->izinGuruModel = new IzinGuruModel();
        $this->guruModel = new GuruModel();
        $this->absensiGuruModel = new AbsensiGuruModel();
        $this->session = session();
    }

    /**
     * Display list of all izin guru requests
     */
    public function index()
    {
        // Get filter parameters
        $status = $this->request->getGet('status') ?? 'all';
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Build query
        $builder = $this->izinGuruModel
            ->select('izin_guru.*, guru.nama_lengkap as guru_nama, guru.nip, 
                     users.nama_lengkap as approver_name')
            ->join('guru', 'guru.id = izin_guru.guru_id')
            ->join('users', 'users.id = izin_guru.disetujui_oleh', 'left');

        // Apply filters
        if ($status !== 'all') {
            $builder->where('izin_guru.status', $status);
        }

        if ($bulan && $tahun) {
            $builder->where('MONTH(izin_guru.tanggal_mulai)', $bulan);
            $builder->where('YEAR(izin_guru.tanggal_mulai)', $tahun);
        }

        $izinList = $builder->orderBy('izin_guru.created_at', 'DESC')->findAll();

        // Get statistics
        $stats = [
            'total' => $this->izinGuruModel->countAll(),
            'pending' => $this->izinGuruModel->where('status', 'pending')->countAllResults(false),
            'disetujui' => $this->izinGuruModel->where('status', 'disetujui')->countAllResults(false),
            'ditolak' => $this->izinGuruModel->where('status', 'ditolak')->countAllResults(false),
        ];

        $data = [
            'title' => 'Kelola Izin Guru',
            'pageTitle' => 'Kelola Izin Guru',
            'pageDescription' => 'Approve atau tolak pengajuan izin guru',
            'izinList' => $izinList,
            'stats' => $stats,
            'currentStatus' => $status,
            'currentBulan' => $bulan,
            'currentTahun' => $tahun,
        ];

        return view('wakakur/izin_guru/index', $data);
    }

    /**
     * Show detail of izin request
     */
    public function show($id)
    {
        $izin = $this->izinGuruModel
            ->select('izin_guru.*, guru.nama_lengkap as guru_nama, guru.nip, guru.email,
                     users.nama_lengkap as approver_name')
            ->join('guru', 'guru.id = izin_guru.guru_id')
            ->join('users', 'users.id = izin_guru.disetujui_oleh', 'left')
            ->where('izin_guru.id', $id)
            ->first();

        if (!$izin) {
            return redirect()->to('/wakakur/izin-guru')->with('error', 'Data izin tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Izin Guru',
            'pageTitle' => 'Detail Pengajuan Izin',
            'pageDescription' => 'Informasi detail pengajuan izin guru',
            'izin' => $izin,
        ];

        return view('wakakur/izin_guru/show', $data);
    }

    /**
     * Approve izin request
     */
    public function approve($id)
    {
        $izin = $this->izinGuruModel->find($id);

        if (!$izin) {
            return redirect()->to('/wakakur/izin-guru')->with('error', 'Data izin tidak ditemukan');
        }

        if ($izin['status'] !== 'pending') {
            return redirect()->to('/wakakur/izin-guru')->with('error', 'Izin sudah diproses sebelumnya');
        }

        $catatan = $this->request->getPost('catatan_persetujuan') ?? '';
        $userId = $this->session->get('user_id') ?? $this->session->get('userId');

        // Update izin status
        $updateData = [
            'status' => 'disetujui',
            'disetujui_oleh' => $userId,
            'tanggal_disetujui' => date('Y-m-d H:i:s'),
            'catatan_persetujuan' => $catatan,
        ];

        if (!$this->izinGuruModel->update($id, $updateData)) {
            return redirect()->to('/wakakur/izin-guru')->with('error', 'Gagal menyetujui izin');
        }

        // Auto-create absensi_guru records for the date range
        $this->createAbsensiGuruRecords($izin);

        return redirect()->to('/wakakur/izin-guru')->with('success', 'Izin berhasil disetujui dan absensi guru otomatis dibuat');
    }

    /**
     * Reject izin request
     */
    public function reject($id)
    {
        $izin = $this->izinGuruModel->find($id);

        if (!$izin) {
            return redirect()->to('/wakakur/izin-guru')->with('error', 'Data izin tidak ditemukan');
        }

        if ($izin['status'] !== 'pending') {
            return redirect()->to('/wakakur/izin-guru')->with('error', 'Izin sudah diproses sebelumnya');
        }

        $catatan = $this->request->getPost('catatan_persetujuan');
        
        if (empty($catatan)) {
            return redirect()->back()->with('error', 'Catatan penolakan harus diisi');
        }

        $userId = $this->session->get('user_id') ?? $this->session->get('userId');

        // Update izin status
        $updateData = [
            'status' => 'ditolak',
            'disetujui_oleh' => $userId,
            'tanggal_disetujui' => date('Y-m-d H:i:s'),
            'catatan_persetujuan' => $catatan,
        ];

        if ($this->izinGuruModel->update($id, $updateData)) {
            return redirect()->to('/wakakur/izin-guru')->with('success', 'Izin berhasil ditolak');
        } else {
            return redirect()->to('/wakakur/izin-guru')->with('error', 'Gagal menolak izin');
        }
    }

    /**
     * Create absensi_guru records for approved izin
     */
    private function createAbsensiGuruRecords($izin)
    {
        $startDate = strtotime($izin['tanggal_mulai']);
        $endDate = strtotime($izin['tanggal_selesai']);
        
        // Map jenis_izin to status_kehadiran
        $statusMapping = [
            'sakit' => 'sakit',
            'izin' => 'izin',
            'cuti' => 'cuti',
            'dinas_luar' => 'dinas_luar',
            'lainnya' => 'izin',
        ];
        
        $status = $statusMapping[$izin['jenis_izin']] ?? 'izin';
        
        // Create record for each day in the range
        for ($date = $startDate; $date <= $endDate; $date = strtotime('+1 day', $date)) {
            $tanggal = date('Y-m-d', $date);
            
            // Check if record already exists
            $existing = $this->absensiGuruModel
                ->where('guru_id', $izin['guru_id'])
                ->where('tanggal', $tanggal)
                ->first();
            
            if (!$existing) {
                $this->absensiGuruModel->insert([
                    'guru_id' => $izin['guru_id'],
                    'tanggal' => $tanggal,
                    'status_kehadiran' => $status,
                    'keterangan' => 'Auto-created from approved izin: ' . $izin['alasan'],
                    'izin_guru_id' => $izin['id'],
                ]);
            }
        }
    }
}
