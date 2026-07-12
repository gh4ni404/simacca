<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SiswaService;
use App\Models\RolloverHistoryModel;
use App\Models\RolloverBackupModel;

class PengaturanController extends BaseController
{
    protected $siswaService;
    protected $rolloverHistoryModel;

    public function __construct()
    {
        $this->siswaService = new SiswaService();
        $this->rolloverHistoryModel = new RolloverHistoryModel();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    public function index()
    {
        $rolloverHistory = $this->rolloverHistoryModel->getAllHistory();
        $latestActive = $this->rolloverHistoryModel->getLatestActive();

        $rolloverBackupModel = new RolloverBackupModel();
        $historyIdsWithBackup = $rolloverBackupModel->getHistoryIdsWithBackup();

        $data = [
            'title' => 'Pengaturan',
            'pageTitle' => 'Pengaturan Sistem',
            'pageDescription' => 'Kelola pengaturan sistem seperti tahun ajaran aktif dan jurnal PKL',
            'user' => $this->getUserData(),
            'activeTahunAjaran' => get_active_tahun_ajaran(),
            'tahunAjaranList' => get_tahun_ajaran_list(),
            'rolloverHistory' => $rolloverHistory,
            'latestActiveRollover' => $latestActive,
            'historyIdsWithBackup' => $historyIdsWithBackup,
            'jurnalPklStartDate' => get_jurnal_pkl_start_date(),
            'jurnalPklEndDate' => get_jurnal_pkl_end_date(),
            'jurnalPklDurationDays' => get_jurnal_pkl_duration_days(),
        ];

        return view('admin/pengaturan/index', $data);
    }

    public function update()
    {
        $tahunAjaran = $this->request->getPost('tahun_ajaran');

        if (!$tahunAjaran) {
            session()->setFlashdata('error', 'Tahun ajaran harus diisi.');
            return redirect()->to('/admin/pengaturan');
        }

        $error = validate_tahun_ajaran($tahunAjaran);
        if ($error) {
            session()->setFlashdata('errors', [$error]);
            return redirect()->to('/admin/pengaturan');
        }

        $result = set_active_tahun_ajaran($tahunAjaran);

        if ($result) {
            session()->setFlashdata('success', 'Tahun ajaran aktif berhasil diperbarui menjadi ' . $tahunAjaran);
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui tahun ajaran.');
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function rollover()
    {
        $activeTahunAjaran = get_active_tahun_ajaran();
        $parts = explode('/', $activeTahunAjaran);
        $nextTahunAjaran = ($parts[0] + 1) . '/' . ($parts[1] + 1);

        $newTahunAjaran = $this->request->getPost('tahun_ajaran') ?? $nextTahunAjaran;

        if ($newTahunAjaran !== $nextTahunAjaran) {
            session()->setFlashdata('error', 'Tahun ajaran rollover harus tahun berikutnya dari tahun aktif (' . $nextTahunAjaran . ')');
            return redirect()->to('/admin/pengaturan');
        }

        $result = $this->siswaService->rolloverTahunAjaran($newTahunAjaran);

        if ($result['success']) {
            $data = $result['data'];
            $message = $data['message'];
            session()->setFlashdata('rollover_result', $data);
            session()->setFlashdata('success', $message);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function revert()
    {
        $historyId = (int) $this->request->getPost('history_id');

        if (!$historyId) {
            session()->setFlashdata('error', 'ID rollover tidak valid.');
            return redirect()->to('/admin/pengaturan');
        }

        $result = $this->siswaService->revertRollover($historyId);

        if ($result['success']) {
            session()->setFlashdata('success', $result['data']['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

        return redirect()->to('/admin/pengaturan');
    }

    public function updateJurnalPklPeriod()
    {
        $clear = $this->request->getPost('clear');
        $startDate = $this->request->getPost('jurnal_pkl_start_date');
        $endDate = $this->request->getPost('jurnal_pkl_end_date');

        if ($clear) {
            $settingModel = new \App\Models\SettingModel();
            $settingModel->setSetting('jurnal_pkl_start_date', '');
            $settingModel->setSetting('jurnal_pkl_end_date', '');
            session()->setFlashdata('success', 'Pengaturan periode jurnal PKL berhasil direset.');
            return redirect()->to('/admin/pengaturan');
        }

        if (!empty($startDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            session()->setFlashdata('error', 'Format tanggal mulai tidak valid.');
            return redirect()->to('/admin/pengaturan');
        }

        if (!empty($endDate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            session()->setFlashdata('error', 'Format tanggal akhir tidak valid.');
            return redirect()->to('/admin/pengaturan');
        }

        if (!empty($startDate) && !empty($endDate) && strtotime($endDate) < strtotime($startDate)) {
            session()->setFlashdata('error', 'Tanggal akhir tidak boleh sebelum tanggal mulai.');
            return redirect()->to('/admin/pengaturan');
        }

        set_jurnal_pkl_start_date($startDate ?: '');
        set_jurnal_pkl_end_date($endDate ?: '');

        session()->setFlashdata('success', 'Pengaturan periode jurnal PKL berhasil disimpan.');
        return redirect()->to('/admin/pengaturan');
    }

    public function updateJurnalPklStart()
    {
        $clear = $this->request->getPost('clear');
        $startDate = $this->request->getPost('jurnal_pkl_start_date');

        if ($clear || !$startDate) {
            // Clear the setting
            $settingModel = new \App\Models\SettingModel();
            $existing = $settingModel->get('jurnal_pkl_start_date');
            if ($existing) {
                $settingModel->setSetting('jurnal_pkl_start_date', '');
            }
            session()->setFlashdata('success', 'Tanggal mulai minggu ke-1 jurnal PKL berhasil dihapus (menggunakan ISO week).');
            return redirect()->to('/admin/pengaturan');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            session()->setFlashdata('error', 'Format tanggal tidak valid. Gunakan format YYYY-MM-DD.');
            return redirect()->to('/admin/pengaturan');
        }

        $result = set_jurnal_pkl_start_date($startDate);

        if ($result) {
            session()->setFlashdata('success', 'Tanggal mulai minggu ke-1 jurnal PKL berhasil disimpan.');
        } else {
            session()->setFlashdata('error', 'Gagal menyimpan pengaturan.');
        }

        return redirect()->to('/admin/pengaturan');
    }
}
