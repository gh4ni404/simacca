<?php

namespace App\Services;

use App\Models\AbsensiModel;
use App\Models\AbsensiDetailModel;
use App\Models\JurnalKbmModel;
use App\Models\IzinSiswaModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\MataPelajaranModel;

/**
 * LaporanService
 * 
 * Business logic layer for generating various reports
 * Handles data aggregation, statistics, and report generation
 */
class LaporanService extends BaseService
{
    protected AbsensiModel $absensiModel;
    protected AbsensiDetailModel $absensiDetailModel;
    protected JurnalKbmModel $jurnalModel;
    protected IzinSiswaModel $izinModel;
    protected GuruModel $guruModel;
    protected SiswaModel $siswaModel;
    protected KelasModel $kelasModel;
    protected MataPelajaranModel $mapelModel;

    public function __construct()
    {
        parent::__construct();
        $this->absensiModel = new AbsensiModel();
        $this->absensiDetailModel = new AbsensiDetailModel();
        $this->jurnalModel = new JurnalKbmModel();
        $this->izinModel = new IzinSiswaModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->mapelModel = new MataPelajaranModel();
    }

    /**
     * Generate laporan absensi
     * 
     * @param array $filters (kelas_id, mapel_id, start_date, end_date)
     * @return array
     */
    public function getLaporanAbsensi(array $filters = []): array
    {
        try {
            $builder = $this->db->table('absensi')
                ->select('absensi.*,
                         jadwal_mengajar.hari,
                         jadwal_mengajar.jam_mulai,
                         jadwal_mengajar.jam_selesai,
                         guru.nama_lengkap as nama_guru,
                         guru.nip,
                         mata_pelajaran.nama_mapel,
                         kelas.nama_kelas,
                         COUNT(absensi_detail.id) as total_siswa,
                         SUM(CASE WHEN absensi_detail.status = "hadir" THEN 1 ELSE 0 END) as hadir,
                         SUM(CASE WHEN absensi_detail.status = "izin" THEN 1 ELSE 0 END) as izin,
                         SUM(CASE WHEN absensi_detail.status = "sakit" THEN 1 ELSE 0 END) as sakit,
                         SUM(CASE WHEN absensi_detail.status = "alpha" THEN 1 ELSE 0 END) as alpha')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id')
                ->join('absensi_detail', 'absensi_detail.absensi_id = absensi.id', 'left')
                ->groupBy('absensi.id');

            // Apply filters
            if (!empty($filters['kelas_id'])) {
                $builder->where('jadwal_mengajar.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['guru_id'])) {
                $builder->where('jadwal_mengajar.guru_id', $filters['guru_id']);
            }

            if (!empty($filters['mapel_id'])) {
                $builder->where('jadwal_mengajar.mata_pelajaran_id', $filters['mapel_id']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('absensi.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('absensi.tanggal <=', $filters['end_date']);
            }

            $builder->orderBy('absensi.tanggal', 'DESC');

            $data = $builder->get()->getResultArray();

            // Calculate summary
            $summary = [
                'total_pertemuan' => count($data),
                'total_hadir' => 0,
                'total_izin' => 0,
                'total_sakit' => 0,
                'total_alpha' => 0,
                'total_siswa_tercatat' => 0
            ];

            foreach ($data as $row) {
                $summary['total_hadir'] += $row['hadir'];
                $summary['total_izin'] += $row['izin'];
                $summary['total_sakit'] += $row['sakit'];
                $summary['total_alpha'] += $row['alpha'];
                $summary['total_siswa_tercatat'] += $row['total_siswa'];
            }

            return $this->success([
                'data' => $data,
                'summary' => $summary,
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in LaporanService::getLaporanAbsensi: ' . $e->getMessage());
            return $this->error('Gagal membuat laporan absensi: ' . $e->getMessage());
        }
    }

    /**
     * Generate laporan absensi detail per siswa
     * 
     * @param array $filters (kelas_id, siswa_id, start_date, end_date)
     * @return array
     */
    public function getLaporanAbsensiDetail(array $filters = []): array
    {
        try {
            $builder = $this->db->table('absensi_detail')
                ->select('absensi_detail.*,
                         siswa.nama_lengkap,
                         siswa.nis,
                         kelas.nama_kelas,
                         absensi.tanggal,
                         absensi.pertemuan_ke,
                         mata_pelajaran.nama_mapel,
                         guru.nama_lengkap as nama_guru')
                ->join('siswa', 'siswa.id = absensi_detail.siswa_id AND siswa.deleted_at IS NULL')
                ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->join('guru', 'guru.id = jadwal_mengajar.guru_id');

            // Apply filters
            if (!empty($filters['siswa_id'])) {
                $builder->where('absensi_detail.siswa_id', $filters['siswa_id']);
            }

            if (!empty($filters['kelas_id'])) {
                $builder->where('siswa.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('absensi.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('absensi.tanggal <=', $filters['end_date']);
            }

            $builder->orderBy('absensi.tanggal', 'DESC');

            $data = $builder->get()->getResultArray();

            // Calculate summary per siswa
            $summary = [];
            foreach ($data as $row) {
                $siswaId = $row['siswa_id'];
                
                if (!isset($summary[$siswaId])) {
                    $summary[$siswaId] = [
                        'siswa_id' => $siswaId,
                        'nama_lengkap' => $row['nama_lengkap'],
                        'nis' => $row['nis'],
                        'nama_kelas' => $row['nama_kelas'],
                        'hadir' => 0,
                        'izin' => 0,
                        'sakit' => 0,
                        'alpha' => 0,
                        'total' => 0
                    ];
                }

                $summary[$siswaId]['total']++;
                $summary[$siswaId][$row['status']]++;
            }

            return $this->success([
                'data' => $data,
                'summary' => array_values($summary),
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in LaporanService::getLaporanAbsensiDetail: ' . $e->getMessage());
            return $this->error('Gagal membuat laporan detail absensi: ' . $e->getMessage());
        }
    }

    /**
     * Generate laporan kehadiran siswa
     * 
     * @param int $siswaId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getLaporanKehadiranSiswa(int $siswaId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            // Get siswa data
            $siswa = $this->siswaModel->find($siswaId);
            if (!$siswa) {
                return $this->error('Siswa tidak ditemukan', 404);
            }

            $builder = $this->db->table('absensi_detail')
                ->select('absensi_detail.*,
                         absensi.tanggal,
                         absensi.pertemuan_ke,
                         mata_pelajaran.nama_mapel,
                         guru.nama_lengkap as nama_guru')
                ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                ->where('absensi_detail.siswa_id', $siswaId);

            if ($startDate) {
                $builder->where('absensi.tanggal >=', $startDate);
            }

            if ($endDate) {
                $builder->where('absensi.tanggal <=', $endDate);
            }

            $builder->orderBy('absensi.tanggal', 'DESC');

            $data = $builder->get()->getResultArray();

            // Calculate statistics
            $stats = [
                'total_pertemuan' => count($data),
                'hadir' => 0,
                'izin' => 0,
                'sakit' => 0,
                'alpha' => 0,
                'persentase_kehadiran' => 0
            ];

            foreach ($data as $row) {
                $stats[$row['status']]++;
            }

            if ($stats['total_pertemuan'] > 0) {
                $stats['persentase_kehadiran'] = round(($stats['hadir'] / $stats['total_pertemuan']) * 100, 2);
            }

            return $this->success([
                'siswa' => $siswa,
                'data' => $data,
                'statistik' => $stats,
                'periode' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in LaporanService::getLaporanKehadiranSiswa: ' . $e->getMessage());
            return $this->error('Gagal membuat laporan kehadiran siswa: ' . $e->getMessage());
        }
    }

    /**
     * Generate laporan statistik per kelas
     * 
     * @param int $kelasId
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getLaporanStatistikKelas(int $kelasId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            // Get kelas data
            $kelas = $this->kelasModel->find($kelasId);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            // Get all siswa in this class
            $siswaList = $this->siswaModel->where('kelas_id', $kelasId)->findAll();

            $statistikSiswa = [];

            foreach ($siswaList as $siswa) {
                $builder = $this->db->table('absensi_detail')
                    ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
                    ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                    ->where('absensi_detail.siswa_id', $siswa['id'])
                    ->where('jadwal_mengajar.kelas_id', $kelasId);

                if ($startDate) {
                    $builder->where('absensi.tanggal >=', $startDate);
                }

                if ($endDate) {
                    $builder->where('absensi.tanggal <=', $endDate);
                }

                $absensiData = $builder->select('absensi_detail.status')->get()->getResultArray();

                $stats = [
                    'siswa_id' => $siswa['id'],
                    'nama_lengkap' => $siswa['nama_lengkap'],
                    'nis' => $siswa['nis'],
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0,
                    'total' => count($absensiData),
                    'persentase_kehadiran' => 0
                ];

                foreach ($absensiData as $row) {
                    $stats[$row['status']]++;
                }

                if ($stats['total'] > 0) {
                    $stats['persentase_kehadiran'] = round(($stats['hadir'] / $stats['total']) * 100, 2);
                }

                $statistikSiswa[] = $stats;
            }

            // Calculate class average
            $totalSiswa = count($statistikSiswa);
            $avgStats = [
                'rata_rata_hadir' => 0,
                'rata_rata_izin' => 0,
                'rata_rata_sakit' => 0,
                'rata_rata_alpha' => 0,
                'rata_rata_persentase' => 0
            ];

            if ($totalSiswa > 0) {
                foreach ($statistikSiswa as $stat) {
                    $avgStats['rata_rata_hadir'] += $stat['hadir'];
                    $avgStats['rata_rata_izin'] += $stat['izin'];
                    $avgStats['rata_rata_sakit'] += $stat['sakit'];
                    $avgStats['rata_rata_alpha'] += $stat['alpha'];
                    $avgStats['rata_rata_persentase'] += $stat['persentase_kehadiran'];
                }

                $avgStats['rata_rata_hadir'] = round($avgStats['rata_rata_hadir'] / $totalSiswa, 2);
                $avgStats['rata_rata_izin'] = round($avgStats['rata_rata_izin'] / $totalSiswa, 2);
                $avgStats['rata_rata_sakit'] = round($avgStats['rata_rata_sakit'] / $totalSiswa, 2);
                $avgStats['rata_rata_alpha'] = round($avgStats['rata_rata_alpha'] / $totalSiswa, 2);
                $avgStats['rata_rata_persentase'] = round($avgStats['rata_rata_persentase'] / $totalSiswa, 2);
            }

            return $this->success([
                'kelas' => $kelas,
                'statistik_siswa' => $statistikSiswa,
                'rata_rata_kelas' => $avgStats,
                'periode' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in LaporanService::getLaporanStatistikKelas: ' . $e->getMessage());
            return $this->error('Gagal membuat laporan statistik kelas: ' . $e->getMessage());
        }
    }

    /**
     * Generate laporan jurnal KBM
     * 
     * @param array $filters (guru_id, kelas_id, mapel_id, start_date, end_date)
     * @return array
     */
    public function getLaporanJurnal(array $filters = []): array
    {
        try {
            $builder = $this->db->table('jurnal_kbm')
                ->select('jurnal_kbm.*,
                         absensi.tanggal,
                         absensi.pertemuan_ke,
                         absensi.materi_pembelajaran,
                         guru.nama_lengkap as nama_guru,
                         mata_pelajaran.nama_mapel,
                         kelas.nama_kelas')
                ->join('absensi', 'absensi.id = jurnal_kbm.absensi_id')
                ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
                ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
                ->join('kelas', 'kelas.id = jadwal_mengajar.kelas_id');

            // Apply filters
            if (!empty($filters['guru_id'])) {
                $builder->where('jadwal_mengajar.guru_id', $filters['guru_id']);
            }

            if (!empty($filters['kelas_id'])) {
                $builder->where('jadwal_mengajar.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['mapel_id'])) {
                $builder->where('jadwal_mengajar.mata_pelajaran_id', $filters['mapel_id']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('absensi.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('absensi.tanggal <=', $filters['end_date']);
            }

            $builder->orderBy('absensi.tanggal', 'DESC');

            $data = $builder->get()->getResultArray();

            $summary = [
                'total_jurnal' => count($data),
                'jurnal_dengan_foto' => 0,
                'jurnal_tanpa_foto' => 0
            ];

            foreach ($data as $row) {
                if (!empty($row['foto_dokumentasi'])) {
                    $summary['jurnal_dengan_foto']++;
                } else {
                    $summary['jurnal_tanpa_foto']++;
                }
            }

            return $this->success([
                'data' => $data,
                'summary' => $summary,
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in LaporanService::getLaporanJurnal: ' . $e->getMessage());
            return $this->error('Gagal membuat laporan jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Generate laporan izin siswa
     * 
     * @param array $filters (kelas_id, status, start_date, end_date)
     * @return array
     */
    public function getLaporanIzin(array $filters = []): array
    {
        try {
            $builder = $this->db->table('izin_siswa')
                ->select('izin_siswa.*,
                         siswa.nama_lengkap,
                         siswa.nis,
                         kelas.nama_kelas,
                         users.name as disetujui_oleh_nama')
                ->join('siswa', 'siswa.id = izin_siswa.siswa_id AND siswa.deleted_at IS NULL')
                ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                ->join('users', 'users.id = izin_siswa.disetujui_oleh', 'left');

            // Apply filters
            if (!empty($filters['kelas_id'])) {
                $builder->where('siswa.kelas_id', $filters['kelas_id']);
            }

            if (!empty($filters['status'])) {
                $builder->where('izin_siswa.status', $filters['status']);
            }

            if (!empty($filters['jenis_izin'])) {
                $builder->where('izin_siswa.jenis_izin', $filters['jenis_izin']);
            }

            if (!empty($filters['start_date'])) {
                $builder->where('izin_siswa.tanggal >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('izin_siswa.tanggal <=', $filters['end_date']);
            }

            $builder->orderBy('izin_siswa.tanggal', 'DESC');

            $data = $builder->get()->getResultArray();

            $summary = [
                'total_izin' => count($data),
                'pending' => 0,
                'disetujui' => 0,
                'ditolak' => 0,
                'sakit' => 0,
                'izin' => 0
            ];

            foreach ($data as $row) {
                $summary[$row['status']]++;
                $summary[strtolower($row['jenis_izin'])]++;
            }

            return $this->success([
                'data' => $data,
                'summary' => $summary,
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in LaporanService::getLaporanIzin: ' . $e->getMessage());
            return $this->error('Gagal membuat laporan izin: ' . $e->getMessage());
        }
    }

    /**
     * Export laporan to array for Excel/PDF generation
     * 
     * @param string $type Type of report (absensi, detail, kehadiran, jurnal, izin)
     * @param array $filters
     * @return array
     */
    public function exportLaporan(string $type, array $filters = []): array
    {
        try {
            switch ($type) {
                case 'absensi':
                    return $this->getLaporanAbsensi($filters);
                case 'detail':
                    return $this->getLaporanAbsensiDetail($filters);
                case 'kehadiran':
                    if (empty($filters['siswa_id'])) {
                        return $this->error('Siswa ID wajib diisi untuk laporan kehadiran');
                    }
                    return $this->getLaporanKehadiranSiswa(
                        $filters['siswa_id'],
                        $filters['start_date'] ?? null,
                        $filters['end_date'] ?? null
                    );
                case 'statistik':
                    if (empty($filters['kelas_id'])) {
                        return $this->error('Kelas ID wajib diisi untuk laporan statistik');
                    }
                    return $this->getLaporanStatistikKelas(
                        $filters['kelas_id'],
                        $filters['start_date'] ?? null,
                        $filters['end_date'] ?? null
                    );
                case 'jurnal':
                    return $this->getLaporanJurnal($filters);
                case 'izin':
                    return $this->getLaporanIzin($filters);
                default:
                    return $this->error('Tipe laporan tidak valid');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in LaporanService::exportLaporan: ' . $e->getMessage());
            return $this->error('Gagal export laporan: ' . $e->getMessage());
        }
    }
}
