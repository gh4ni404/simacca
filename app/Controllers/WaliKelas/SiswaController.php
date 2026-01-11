<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\AbsensiDetailModel;

class SiswaController extends BaseController
{
    protected $guruModel;
    protected $kelasModel;
    protected $siswaModel;
    protected $absensiDetailModel;

    public function __construct()
    {
        $this->guruModel = new GuruModel();
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
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

        // Get siswa dengan statistik kehadiran
        $siswa = $this->siswaModel
            ->select('siswa.*, users.username, users.email, users.is_active')
            ->join('users', 'users.id = siswa.user_id')
            ->where('siswa.kelas_id', $kelas['id'])
            ->orderBy('siswa.nama_lengkap', 'ASC')
            ->findAll();

        // Add statistik kehadiran untuk setiap siswa (bulan ini)
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');

        foreach ($siswa as &$s) {
            $kehadiran = $this->absensiDetailModel
                ->select('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
                ')
                ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                ->where('absensi_detail.siswa_id', $s['id'])
                ->where('absensi.tanggal >=', $startDate)
                ->where('absensi.tanggal <=', $endDate)
                ->first();

            $s['kehadiran'] = $kehadiran;
            
            // Calculate percentage
            if ($kehadiran['total'] > 0) {
                $s['persentase_hadir'] = round(($kehadiran['hadir'] / $kehadiran['total']) * 100, 1);
            } else {
                $s['persentase_hadir'] = 0;
            }
        }

        $data = [
            'title' => 'Data Siswa',
            'guru' => $guru,
            'kelas' => $kelas,
            'siswa' => $siswa
        ];

        return view('walikelas/siswa/index', $data);
    }
}
