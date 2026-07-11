<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AbsensiModel;
use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\MataPelajaranModel;

class AbsensiController extends BaseController
{
    protected $absensiModel;
    protected $kelasModel;
    protected $guruModel;
    protected $mataPelajaranModel;
    protected $session;

    public function __construct()
    {
        $this->absensiModel = new AbsensiModel();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->mataPelajaranModel = new MataPelajaranModel();
        $this->session = session();
        helper(['form', 'url']);
    }

    /**
     * Halaman utama kelola absensi (list semua absensi dengan opsi unlock)
     */
    public function index()
    {
        // Check if user is admin
        if (!$this->hasRole('admin')) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        // Get filters from request
        $tanggalDari = $this->request->getGet('tanggal_dari') ?: date('Y-m-01'); // First day of month
        $tanggalSampai = $this->request->getGet('tanggal_sampai') ?: date('Y-m-d'); // Today
        $kelasId = $this->request->getGet('kelas_id');
        $guruId = $this->request->getGet('guru_id');
        $mapelId = $this->request->getGet('mapel_id');
        $statusLock = $this->request->getGet('status_lock'); // 'locked', 'unlocked', 'editable'

        // Build query
        $builder = $this->absensiModel
            ->select('absensi.*,
                jadwal_mengajar.hari,
                jadwal_mengajar.jam_mulai,
                jadwal_mengajar.jam_selesai,
                guru.nama_lengkap as nama_guru,
                guru.nip,
                kelas.nama_kelas,
                mata_pelajaran.nama_mapel,
                guru_pengganti.nama_lengkap as nama_guru_pengganti')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('guru guru_pengganti', 'guru_pengganti.id = absensi.guru_pengganti_id', 'left')
            ->where('absensi.tanggal >=', $tanggalDari)
            ->where('absensi.tanggal <=', $tanggalSampai)
            ->orderBy('absensi.tanggal', 'DESC')
            ->orderBy('jadwal_mengajar.jam_mulai', 'ASC');

        if ($kelasId) {
            $builder->where('jadwal_mengajar.kelas_id', $kelasId);
        }

        if ($guruId) {
            $builder->where('jadwal_mengajar.guru_id', $guruId);
        }

        if ($mapelId) {
            $builder->where('jadwal_mengajar.mata_pelajaran_id', $mapelId);
        }

        $absensiList = $builder->findAll();

        // Add editable status to each absensi
        foreach ($absensiList as &$absensi) {
            $absensi['is_editable'] = $this->isAbsensiEditable($absensi);
            $absensi['is_locked'] = !$absensi['is_editable'];
            
            // Calculate hours since created/unlocked
            $referenceTime = !empty($absensi['unlocked_at']) 
                ? strtotime($absensi['unlocked_at']) 
                : strtotime($absensi['created_at']);
            $now = time();
            $absensi['hours_passed'] = round(($now - $referenceTime) / 3600, 1);
        }

        // Filter by lock status if specified
        if ($statusLock) {
            $absensiList = array_filter($absensiList, function($absensi) use ($statusLock) {
                if ($statusLock === 'locked') {
                    return $absensi['is_locked'];
                } elseif ($statusLock === 'editable') {
                    return $absensi['is_editable'];
                }
                return true;
            });
        }

        // Get dropdown data
        $data = [
            'title' => 'Kelola Absensi',
            'pageTitle' => 'Kelola Absensi',
            'pageDescription' => 'Kelola dan unlock absensi yang terkunci',
            'absensiList' => $absensiList,
            'kelasList' => $this->kelasModel->where('tahun_ajaran', get_active_tahun_ajaran())->orderBy('tingkat', 'ASC')->orderBy('nama_kelas', 'ASC')->findAll(),
            'guruList' => $this->guruModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'mapelList' => $this->mataPelajaranModel->orderBy('nama_mapel', 'ASC')->findAll(),
            'filters' => [
                'tanggal_dari' => $tanggalDari,
                'tanggal_sampai' => $tanggalSampai,
                'kelas_id' => $kelasId,
                'guru_id' => $guruId,
                'mapel_id' => $mapelId,
                'status_lock' => $statusLock
            ]
        ];

        return view('admin/absensi/index', $data);
    }

    /**
     * Unlock absensi (reset timer 24 jam)
     */
    public function unlock($absensiId)
    {
        // Check if user is admin
        if (!$this->hasRole('admin')) {
            return redirect()->to('/access-denied')->with('error', 'Akses ditolak');
        }

        $absensi = $this->absensiModel->find($absensiId);

        if (!$absensi) {
            $this->session->setFlashdata('error', 'Absensi tidak ditemukan');
            return redirect()->back();
        }

        // Update unlocked_at to current timestamp
        $updated = $this->absensiModel->update($absensiId, [
            'unlocked_at' => date('Y-m-d H:i:s')
        ]);

        if ($updated) {
            // Get absensi details for success message
            $absensiDetail = $this->absensiModel
                ->select('absensi.*, kelas.nama_kelas, guru.nama_lengkap as nama_guru, mata_pelajaran.nama_mapel')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->find($absensiId);

            $message = sprintf(
                'Absensi berhasil di-unlock! Guru "%s" sekarang bisa edit absensi %s - %s untuk kelas %s selama 24 jam ke depan.',
                $absensiDetail['nama_guru'],
                $absensiDetail['nama_mapel'],
                date('d M Y', strtotime($absensiDetail['tanggal'])),
                $absensiDetail['nama_kelas']
            );

            $this->session->setFlashdata('success', $message);
        } else {
            $this->session->setFlashdata('error', 'Gagal unlock absensi');
        }

        return redirect()->back();
    }

    /**
     * Bulk unlock multiple absensi
     */
    public function bulkUnlock()
    {
        // Check if user is admin
        if (!$this->hasRole('admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Akses ditolak'
            ]);
        }

        $absensiIds = $this->request->getPost('absensi_ids');

        if (empty($absensiIds) || !is_array($absensiIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal satu absensi untuk di-unlock'
            ]);
        }

        $successCount = 0;
        foreach ($absensiIds as $id) {
            $updated = $this->absensiModel->update($id, [
                'unlocked_at' => date('Y-m-d H:i:s')
            ]);
            if ($updated) {
                $successCount++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Berhasil unlock {$successCount} absensi"
        ]);
    }
}
