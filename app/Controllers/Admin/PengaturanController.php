<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\SiswaService;

class PengaturanController extends BaseController
{
    protected $siswaService;

    public function __construct()
    {
        $this->siswaService = new SiswaService();

        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/access-denied');
        }
    }

    public function index()
    {
        $settingModel = new \App\Models\SettingModel();
        $rolloverBackup = $settingModel->get('rollover_backup');
        $rolloverBackupData = $rolloverBackup ? json_decode($rolloverBackup, true) : null;

        $data = [
            'title' => 'Pengaturan',
            'pageTitle' => 'Pengaturan Sistem',
            'pageDescription' => 'Kelola pengaturan sistem seperti tahun ajaran aktif dan jurnal PKL',
            'user' => $this->getUserData(),
            'activeTahunAjaran' => get_active_tahun_ajaran(),
            'tahunAjaranList' => get_tahun_ajaran_list(),
            'rolloverBackup' => $rolloverBackupData,
            'jurnalPklStartDate' => get_jurnal_pkl_start_date(),
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
        $newTahunAjaran = $this->request->getPost('tahun_ajaran') ?? get_active_tahun_ajaran();

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
        $result = $this->siswaService->revertRollover();

        if ($result['success']) {
            session()->setFlashdata('success', $result['data']['message']);
        } else {
            session()->setFlashdata('error', $result['message']);
        }

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
