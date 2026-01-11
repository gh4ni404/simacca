<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;

class AbsensiController extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $absensiModel;
    protected $absensiDetailModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
    }

    public function index()
    {
        // Get guru data
        $userId = session()->get('user_id');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru || !$guru['is_wali_kelas']) {
            return redirect()->to('/access-denied')->with('error', 'Anda bukan wali kelas');
        }

        // Get kelas data
        $kelas = $this->kelasModel->getByWaliKelas($guru['id']);

        if (!$kelas) {
            return redirect()->to('/access-denied')->with('error', 'Anda belum ditugaskan sebagai wali kelas');
        }

        // Get filter params
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        // Get absensi data
        $absensiData = $this->absensiModel->getByKelas($kelas['id'], $startDate, $endDate);

        // Get detail statistik untuk setiap absensi
        foreach ($absensiData as &$absen) {
            $detail = $this->absensiDetailModel
                ->select('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
                ')
                ->where('absensi_id', $absen['id'])
                ->first();
            
            $absen['detail'] = $detail;
            $absen['persentase_hadir'] = $detail['total'] > 0 ? round(($detail['hadir'] / $detail['total']) * 100, 1) : 0;
        }

        $data = [
            'title' => 'Monitoring Absensi Kelas',
            'guru' => $guru,
            'kelas' => $kelas,
            'absensiData' => $absensiData,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('walikelas/absensi/index', $data);
    }
}
