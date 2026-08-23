<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MasterJobdeskPiketModel;

class MasterJobdeskPiketController extends BaseController
{
    protected $masterJobdeskModel;

    public function __construct()
    {
        $this->masterJobdeskModel = new MasterJobdeskPiketModel();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    /**
     * Display list of master jobdesks
     */
    public function index()
    {
        $tahunAjaran = get_active_tahun_ajaran();
        $semester = $this->request->getGet('semester') ?: 'ganjil';

        $jobdeskList = $this->masterJobdeskModel->orderBy('is_active', 'DESC')
            ->orderBy('kode_jobdesk', 'ASC')
            ->findAll();

        $guruModel = new \App\Models\GuruModel();
        $guruList = $guruModel->select('guru.id, guru.nama_lengkap, guru.nip')
            ->join('users', 'users.id = guru.user_id')
            ->where('users.is_active', 1)
            ->orderBy('guru.nama_lengkap', 'ASC')
            ->findAll();

        $guruPiketModel = new \App\Models\GuruPiketModel();
        
        // Find which teachers are mapped to which jobdesk_id
        $assignments = $guruPiketModel->select('guru_id, jobdesk_id')
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('jobdesk_id IS NOT NULL')
            ->where('is_active', 1)
            ->groupBy('jobdesk_id, guru_id')
            ->findAll();

        $jobdeskGuruIdsMap = [];
        $guruJobdeskMap = []; // guru_id => jobdesk_id

        foreach ($assignments as $a) {
            $jId = (int) $a['jobdesk_id'];
            $gId = (int) $a['guru_id'];

            if (!isset($jobdeskGuruIdsMap[$jId])) {
                $jobdeskGuruIdsMap[$jId] = [];
            }
            if (!in_array($gId, $jobdeskGuruIdsMap[$jId])) {
                $jobdeskGuruIdsMap[$jId][] = $gId;
            }
            $guruJobdeskMap[$gId] = $jId;
        }

        // Get list of guru_ids that have an active piket schedule in guru_piket
        $scheduledRows = $guruPiketModel->select('guru_id')
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('is_active', 1)
            ->groupBy('guru_id')
            ->findAll();

        $scheduledGuruMap = [];
        foreach ($scheduledRows as $sr) {
            $scheduledGuruMap[(int)$sr['guru_id']] = true;
        }

        foreach ($jobdeskList as &$j) {
            $j['assigned_guru_ids'] = $jobdeskGuruIdsMap[$j['id']] ?? [];
            $j['total_guru'] = count($j['assigned_guru_ids']);
        }

        $data = [
            'title'              => 'Master Jobdesk Guru Piket',
            'pageTitle'          => 'Master Jobdesk Piket',
            'pageDescription'    => 'Kelola template rincian tugas piket guru',
            'user'               => $this->getUserData(),
            'jobdeskList'        => $jobdeskList,
            'guruList'           => $guruList,
            'guruJobdeskMap'     => $guruJobdeskMap,
            'scheduledGuruMap'   => $scheduledGuruMap,
            'hariList'           => ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'],
            'tahunAjaran'        => $tahunAjaran,
            'semester'           => $semester,
        ];

        return view('admin/master_jobdesk/index', $data);
    }

    /**
     * Store new master jobdesk
     */
    public function store()
    {
        $rules = [
            'kode_jobdesk'  => 'required|min_length[3]|max_length[20]|is_unique[master_jobdesk_piket.kode_jobdesk]',
            'nama_jobdesk'  => 'required|min_length[3]|max_length[100]',
            'rincian_tugas' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan kode jobdesk unik.');
        }

        $data = [
            'kode_jobdesk'  => strtoupper(trim($this->request->getPost('kode_jobdesk'))),
            'nama_jobdesk'  => trim($this->request->getPost('nama_jobdesk')),
            'rincian_tugas' => trim($this->request->getPost('rincian_tugas')),
            'is_active'     => $this->request->getPost('is_active') ?? 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $this->masterJobdeskModel->insert($data);

        return redirect()->to('/admin/master-jobdesk')->with('success', 'Master jobdesk piket berhasil ditambahkan');
    }

    /**
     * Update master jobdesk
     */
    public function update($id)
    {
        $jobdesk = $this->masterJobdeskModel->find($id);
        if (!$jobdesk) {
            return redirect()->to('/admin/master-jobdesk')->with('error', 'Master jobdesk tidak ditemukan');
        }

        $rules = [
            'kode_jobdesk'  => "required|min_length[3]|max_length[20]|is_unique[master_jobdesk_piket.kode_jobdesk,id,{$id}]",
            'nama_jobdesk'  => 'required|min_length[3]|max_length[100]',
            'rincian_tugas' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan kode jobdesk unik.');
        }

        $data = [
            'kode_jobdesk'  => strtoupper(trim($this->request->getPost('kode_jobdesk'))),
            'nama_jobdesk'  => trim($this->request->getPost('nama_jobdesk')),
            'rincian_tugas' => trim($this->request->getPost('rincian_tugas')),
            'is_active'     => $this->request->getPost('is_active') ?? 1,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $this->masterJobdeskModel->update($id, $data);

        return redirect()->to('/admin/master-jobdesk')->with('success', 'Master jobdesk piket berhasil diperbarui');
    }

    /**
     * Delete master jobdesk
     */
    public function delete($id)
    {
        $jobdesk = $this->masterJobdeskModel->find($id);
        if (!$jobdesk) {
            return redirect()->to('/admin/master-jobdesk')->with('error', 'Master jobdesk tidak ditemukan');
        }

        $this->masterJobdeskModel->delete($id);

        return redirect()->to('/admin/master-jobdesk')->with('success', 'Master jobdesk piket berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus($id)
    {
        $jobdesk = $this->masterJobdeskModel->find($id);
        if (!$jobdesk) {
            return redirect()->to('/admin/master-jobdesk')->with('error', 'Master jobdesk tidak ditemukan');
        }

        $newStatus = $jobdesk['is_active'] ? 0 : 1;
        $this->masterJobdeskModel->update($id, ['is_active' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->to('/admin/master-jobdesk')->with('success', "Master jobdesk berhasil {$statusText}");
    }

    /**
     * Bulk assign/map Master Jobdesk to multiple teachers on a specific day (AJAX / POST)
     */
    public function bulkAssign()
    {
        $guruIds = $this->request->getPost('guru_ids') ?? [];
        $jobdeskId = (int) $this->request->getPost('jobdesk_id');
        $hari = $this->request->getPost('hari') ?: 'semua';
        $keterangan = $this->request->getPost('keterangan');

        $tahunAjaran = get_active_tahun_ajaran();
        $semester = $this->request->getPost('semester') ?: 'ganjil';

        $guruPiketService = new \App\Services\GuruPiketService();
        $result = $guruPiketService->bulkAssignJobdesk($guruIds, $jobdeskId, $hari, $tahunAjaran, $semester, $keterangan);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        return redirect()->to('/admin/master-jobdesk')->with('success', $result['message']);
    }
}

