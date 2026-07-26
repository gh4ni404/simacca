<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use App\Models\AbsensiDetailModel;

class AbsensiController extends BaseController
{
    protected $siswaModel;
    protected $absensiDetailModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
    }

    public function index()
    {
        // Get siswa data
        $userId = session()->get('user_id');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return redirect()->to('/access-denied')->with('error', 'Data siswa nggak ketemu nih 🤔');
        }

        // Get filter params
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        // Get absensi data
        $absensiData = $this->absensiDetailModel
            ->select('
                absensi_detail.*,
                absensi.tanggal,
                absensi.pertemuan_ke,
                absensi.materi_pembelajaran,
                mata_pelajaran.nama_mapel,
                guru.nama_lengkap as nama_guru
            ')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->where('absensi_detail.siswa_id', $siswa['id'])
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->orderBy('absensi.tanggal', 'DESC')
            ->findAll();

        // Get summary statistik
        $summary = $this->absensiDetailModel
            ->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = "alpa" THEN 1 ELSE 0 END) as alpa
            ')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('absensi_detail.siswa_id', $siswa['id'])
            ->where('absensi.tanggal >=', $startDate)
            ->where('absensi.tanggal <=', $endDate)
            ->first();

        // Calculate percentage
        $persentaseKehadiran = 0;
        if ($summary['total'] > 0) {
            $persentaseKehadiran = round(($summary['hadir'] / $summary['total']) * 100, 1);
        }

        $data = [
            'title' => 'Riwayat Absensi',
            'siswa' => $siswa,
            'absensiData' => $absensiData,
            'summary' => $summary,
            'persentaseKehadiran' => $persentaseKehadiran,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('siswa/absensi/index', $data);
    }
}
