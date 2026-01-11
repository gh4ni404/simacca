<?php

namespace App\Controllers\WaliKelas;

use App\Controllers\BaseController;
use App\Models\GuruModel;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\AbsensiDetailModel;

class LaporanController extends BaseController
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

        // Get filter params
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');
        $siswaId = $this->request->getGet('siswa_id') ?? null;

        // Get siswa list
        $siswaList = $this->siswaModel->getByKelas($kelas['id']);

        // Get laporan data
        if ($siswaId) {
            // Laporan per siswa
            $laporan = $this->absensiDetailModel
                ->select('
                    siswa.nama_lengkap,
                    siswa.nis,
                    absensi.tanggal,
                    mata_pelajaran.nama_mapel,
                    absensi_detail.status,
                    absensi_detail.keterangan
                ')
                ->join('siswa', 'siswa.id = absensi_detail.siswa_id')
                ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->where('absensi_detail.siswa_id', $siswaId)
                ->where('absensi.tanggal >=', $startDate)
                ->where('absensi.tanggal <=', $endDate)
                ->orderBy('absensi.tanggal', 'DESC')
                ->findAll();

            // Get summary
            $summary = $this->absensiDetailModel
                ->select('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
                ')
                ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                ->where('absensi_detail.siswa_id', $siswaId)
                ->where('absensi.tanggal >=', $startDate)
                ->where('absensi.tanggal <=', $endDate)
                ->first();
        } else {
            // Laporan semua siswa (rekapitulasi)
            $laporan = $this->absensiDetailModel
                ->select('
                    siswa.id,
                    siswa.nama_lengkap,
                    siswa.nis,
                    COUNT(*) as total,
                    SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN absensi_detail.status = "sakit" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN absensi_detail.status = "izin" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN absensi_detail.status = "alpa" THEN 1 ELSE 0 END) as alpa
                ')
                ->join('siswa', 'siswa.id = absensi_detail.siswa_id')
                ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                ->where('siswa.kelas_id', $kelas['id'])
                ->where('absensi.tanggal >=', $startDate)
                ->where('absensi.tanggal <=', $endDate)
                ->groupBy('siswa.id')
                ->orderBy('siswa.nama_lengkap', 'ASC')
                ->findAll();

            // Calculate percentage for each
            foreach ($laporan as &$l) {
                $l['persentase_hadir'] = $l['total'] > 0 ? round(($l['hadir'] / $l['total']) * 100, 1) : 0;
            }

            $summary = null;
        }

        $data = [
            'title' => 'Laporan Kehadiran',
            'guru' => $guru,
            'kelas' => $kelas,
            'siswaList' => $siswaList,
            'laporan' => $laporan,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'siswaId' => $siswaId
        ];

        return view('walikelas/laporan/index', $data);
    }
}
