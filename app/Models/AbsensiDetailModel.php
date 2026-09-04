<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiDetailModel extends Model
{
    protected $table            = 'absensi_detail';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'absensi_id',
        'siswa_id',
        'status',
        'keterangan',
        'waktu_absen',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'absensi_id'    => 'required|numeric',
        'siswa_id'      => 'required|numeric',
        'status'        => 'required|in_list[hadir,izin,sakit,alpa]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get detail absensi by absensi_id
     */
    public function getByAbsensi($absensiId)
    {
        return $this->select('absensi_detail.*,
                            siswa.nama_lengkap,
                            siswa.nis')
            ->join('siswa', 'siswa.id = absensi_detail.siswa_id AND siswa.deleted_at IS NULL')
            ->where('absensi_id', $absensiId)
            ->orderBy('siswa.nama_lengkap')
            ->findAll();
    }

    /**
     * Get absensi siswa dalam periode tertentu
     */
    public function getAbsensiSiswa($siswaId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('absensi_detail.*,
                                absensi.tanggal,
                                absensi.pertemuan_ke,
                                mata_pelajaran.nama_mapel,
                                guru.nama_lengkap as nama_guru')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_mengajar.mata_pelajaran_id')
            ->join('guru', 'guru.id = jadwal_mengajar.guru_id')
            ->where('absensi_detail.siswa_id', $siswaId)
            ->orderBy('absensi.tanggal', 'DESC');

        if ($startDate && $endDate) {
            $builder->where('absensi.tanggal >=', $startDate);
            $builder->where('absensi.tanggal <=', $endDate);
        }

        return $builder->findAll();
    }

    /**
     * Get statistik kehadiran siswa
     */
    public function getStatistikSiswa($siswaId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('status, COUNT(*) as jumlah')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('siswa_id', $siswaId)
            ->groupBy('status');

        if ($startDate && $endDate) {
            $builder->where('absensi.tanggal >=', $startDate);
            $builder->where('absensi.tanggal <=', $endDate);
        }

        $result = $builder->findAll();

        $statistik = [
            'hadir' => 0,
            'izin'  => 0,
            'sakit' => 0,
            'alpa'  => 0,
            'total_sesi' => 0,
        ];

        foreach ($result as $row) {
            $status = strtolower($row['status']);
            if (isset($statistik[$status])) {
                $statistik[$status] = (int) $row['jumlah'];
                $statistik['total_sesi'] += (int) $row['jumlah'];
            }
        }

        return $statistik;
    }

    /**
     * Insert batch absensi detail
     */
    public function insertBatchAbsensi($absensiId, $dataSiswa)
    {
        $batchData = [];
        foreach ($dataSiswa as $siswaId => $status) {
            $batchData[] = [
                'absensi_id' => $absensiId,
                'siswa_id'      => $siswaId,
                'status'    => $status['status'],
                'keterangan' => $status['keterangan'] ?? null,
                'waktu_absen' =>  date('Y-m-d H:i:s')
            ];
        }

        return $this->insertBatch($batchData);
    }

    /**
     * Update absensi detail
     */
    public function updateAbsensi($absensiId, $siswaId, $data)
    {
        return $this->where('absensi_id', $absensiId)
            ->where('siswa_id', $siswaId)
            ->set($data)
            ->update();
    }

    public function getAbsensiToday($today, $kelasId = null)
    {
        $builder = $this->select('absensi_detail.*')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('absensi.tanggal', $today);

        if ($kelasId) {
            $builder->where('jadwal_mengajar.kelas_id', $kelasId);
        }

        return $builder->countAllResults();
    }

    public function getAbsensiThisMonth($first, $last, $kelasId)
    {
        return $this->select('absensi_detail.*')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->join('jadwal_mengajar', 'jadwal_mengajar.id = absensi.jadwal_mengajar_id')
            ->where('jadwal_mengajar.kelas_id', $kelasId)
            ->where('absensi.tanggal >=', $first)
            ->where('absensi.tanggal <=', $last)
            ->countAllResults();
    }

    public function getStatistikBulanan($month, $year)
    {
        return $this->select('status, COUNT(*) as total')
            ->join('absensi', 'absensi.id = absensi_detail.absensi_id')
            ->where('MONTH(absensi.tanggal)', $month)
            ->where('YEAR(absensi.tanggal)', $year)
            ->groupBy('status')
            ->findAll();
    }

    public function getDetailStats($id) {
        return $this->select('status, COUNT(*) as total')
            ->where('absensi_id', $id)
            ->groupBy('status')
            ->findAll();
    }

    /**
     * Rekap per kelas untuk periode tanggal tertentu
     */
    public function getRekapPerKelas(string $from, string $to, ?int $kelasId = null, ?string $tahunAjaran = null): array
    {
        $builder = $this->db->table('absensi_detail ad')
            ->select('k.nama_kelas, 
                SUM(CASE WHEN ad.status = "hadir" THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN ad.status = "izin" THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN ad.status = "sakit" THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN ad.status = "alpa" THEN 1 ELSE 0 END) as alpa,
                COUNT(*) as total_sesi')
            ->join('absensi a', 'a.id = ad.absensi_id AND a.deleted_at IS NULL')
            ->join('jadwal_mengajar jm', 'jm.id = a.jadwal_mengajar_id AND jm.deleted_at IS NULL')
            ->join('kelas k', 'k.id = jm.kelas_id')
            ->where('a.tanggal >=', $from)
            ->where('a.tanggal <=', $to);

        if (!empty($kelasId)) {
            $builder->where('k.id', $kelasId);
        }

        if (!empty($tahunAjaran)) {
            $builder->where('jm.tahun_ajaran', $tahunAjaran);
        }

        $builder->groupBy('k.id')
            ->orderBy('k.nama_kelas', 'ASC');

        $rows = $builder->get()->getResultArray();
        foreach ($rows as &$r) {
            $r['hadir'] = (int)($r['hadir'] ?? 0);
            $r['izin'] = (int)($r['izin'] ?? 0);
            $r['sakit'] = (int)($r['sakit'] ?? 0);
            $r['alpa'] = (int)($r['alpa'] ?? 0);
            $r['total_sesi'] = (int)($r['total_sesi'] ?? 0);
        }
        return $rows;
    }

    /**
     * Get statistik kehadiran per siswa (student-centric approach)
     * Menghitung berapa siswa yang hadir sempurna, ada alpa, dll
     * 
     * @param string $tanggal Tanggal yang dicek
     * @param int|null $kelasId Filter kelas (null = semua kelas)
     * @param string|null $tahunAjaran Filter tahun ajaran aktif
     * @return array Statistik per siswa dengan kategori kehadiran
     */
    public function getStatistikPerSiswa(string $tanggal, ?int $kelasId = null, ?string $tahunAjaran = null): array
    {
        // Step 1: Hitung total jadwal yang sudah terisi PER KELAS pada tanggal tersebut
        $builderJadwalPerKelas = $this->db->table('absensi a')
            ->select('jm.kelas_id, COUNT(DISTINCT a.id) as total_jadwal_terisi')
            ->join('jadwal_mengajar jm', 'jm.id = a.jadwal_mengajar_id AND jm.deleted_at IS NULL')
            ->where('a.deleted_at IS NULL')
            ->where('a.tanggal', $tanggal);
        
        if ($kelasId) {
            $builderJadwalPerKelas->where('jm.kelas_id', $kelasId);
        }

        if ($tahunAjaran) {
            $builderJadwalPerKelas->where('jm.tahun_ajaran', $tahunAjaran);
        }
        
        $builderJadwalPerKelas->groupBy('jm.kelas_id');
        $jadwalPerKelasData = $builderJadwalPerKelas->get()->getResultArray();
        
        // Buat lookup untuk total jadwal per kelas
        $jadwalPerKelasLookup = [];
        $totalJadwalTerisi = 0;
        foreach ($jadwalPerKelasData as $row) {
            $jadwalPerKelasLookup[$row['kelas_id']] = (int)$row['total_jadwal_terisi'];
            $totalJadwalTerisi += (int)$row['total_jadwal_terisi'];
        }

        // Jika tidak ada jadwal terisi, return data kosong
        if ($totalJadwalTerisi === 0) {
            return [
                'total_siswa' => 0,
                'hadir_semua' => 0,
                'sakit_semua' => 0,
                'izin_semua' => 0,
                'alpa_semua' => 0,
                'hadir_sakit' => 0,
                'hadir_izin' => 0,
                'hadir_alpa' => 0,
                'tidak_tercatat' => 0,
                'lainnya' => 0,
                'total_jadwal_terisi' => 0,
                'detail_siswa' => []
            ];
        }

        // Step 2a: Get kelas-kelas yang ada jadwal pada tanggal ini
        $kelasYangAdaJadwal = array_keys($jadwalPerKelasLookup);
        
        if (empty($kelasYangAdaJadwal)) {
            return [
                'total_siswa' => 0,
                'hadir_semua' => 0,
                'sakit_semua' => 0,
                'izin_semua' => 0,
                'alpa_semua' => 0,
                'hadir_sakit' => 0,
                'hadir_izin' => 0,
                'hadir_alpa' => 0,
                'tidak_tercatat' => 0,
                'lainnya' => 0,
                'total_jadwal_terisi' => 0,
                'detail_siswa' => []
            ];
        }

        // Step 2b: Get SEMUA siswa dari kelas yang ada jadwal (termasuk yang tidak tercatat)
        $builderAllSiswa = $this->db->table('siswa s')
            ->select('s.id as siswa_id,
                s.nama_lengkap,
                s.nis,
                s.kelas_id,
                k.nama_kelas')
            ->join('kelas k', 'k.id = s.kelas_id')
            ->where('s.deleted_at IS NULL', null, false)
            ->whereIn('s.kelas_id', $kelasYangAdaJadwal);
        
        if ($kelasId) {
            $builderAllSiswa->where('s.kelas_id', $kelasId);
        }

        if ($tahunAjaran) {
            $builderAllSiswa->where('s.tahun_ajaran', $tahunAjaran);
        }
        
        $allSiswaData = $builderAllSiswa->get()->getResultArray();

        // Step 2c: Get statistik untuk siswa yang TERCATAT di absensi_detail
        $builderStats = $this->db->table('absensi_detail ad')
            ->select('s.id as siswa_id,
                COUNT(DISTINCT ad.absensi_id) as total_sesi_diikuti,
                SUM(CASE WHEN ad.status = "hadir" THEN 1 ELSE 0 END) as total_hadir,
                SUM(CASE WHEN ad.status = "sakit" THEN 1 ELSE 0 END) as total_sakit,
                SUM(CASE WHEN ad.status = "izin" THEN 1 ELSE 0 END) as total_izin,
                SUM(CASE WHEN ad.status = "alpa" THEN 1 ELSE 0 END) as total_alpa')
            ->join('absensi a', 'a.id = ad.absensi_id AND a.deleted_at IS NULL')
            ->join('siswa s', 's.id = ad.siswa_id AND s.deleted_at IS NULL')
            ->where('a.tanggal', $tanggal)
            ->whereIn('s.kelas_id', $kelasYangAdaJadwal);

        if ($kelasId) {
            $builderStats->where('s.kelas_id', $kelasId);
        }

        if ($tahunAjaran) {
            $builderStats->where('s.tahun_ajaran', $tahunAjaran);
        }

        $builderStats->groupBy('s.id');
        $statsData = $builderStats->get()->getResultArray();
        
        // Buat lookup untuk stats
        $statsLookup = [];
        foreach ($statsData as $stat) {
            $statsLookup[$stat['siswa_id']] = $stat;
        }
        
        // Total siswa (semua siswa dari kelas yang ada jadwal)
        $totalSiswa = count($allSiswaData);

        // Step 3: Kategorikan siswa dan hitung statistik
        // SIMPLIFIKASI: 7 KATEGORI UTAMA + 2 KATEGORI TAMBAHAN
        $kategorisasi = [
            'hadir_semua' => 0,      // Hadir di semua mapel
            'sakit_semua' => 0,      // Sakit di semua mapel
            'izin_semua' => 0,       // Izin di semua mapel
            'alpa_semua' => 0,       // Alpa di semua mapel
            'hadir_sakit' => 0,      // Hadir sebagian + Sakit sebagian
            'hadir_izin' => 0,       // Hadir sebagian + Izin sebagian
            'hadir_alpa' => 0,       // Hadir sebagian + Alpa sebagian
            'tidak_tercatat' => 0,   // Tidak ada record sama sekali
            'lainnya' => 0           // Kombinasi lainnya
        ];

        $detailSiswa = [];

        // Loop semua siswa dari kelas yang ada jadwal
        foreach ($allSiswaData as $siswa) {
            $siswaId = (int)$siswa['siswa_id'];
            $kelasIdSiswa = (int)$siswa['kelas_id'];
            
            // Get stats untuk siswa ini (jika ada)
            $stats = $statsLookup[$siswaId] ?? null;
            
            // Get total jadwal terisi untuk kelas siswa ini
            $totalJadwalKelasIni = $jadwalPerKelasLookup[$kelasIdSiswa] ?? 0;
            
            // Tentukan kategori
            $kategori = '';
            
            if ($stats === null) {
                // Siswa TIDAK TERCATAT sama sekali di tanggal ini
                $kategori = 'tidak_tercatat';
                $kategorisasi['tidak_tercatat']++;
                
                $detailSiswa[] = [
                    'siswa_id' => $siswaId,
                    'nama_lengkap' => $siswa['nama_lengkap'],
                    'nis' => $siswa['nis'],
                    'nama_kelas' => $siswa['nama_kelas'],
                    'kelas_id' => $kelasIdSiswa,
                    'total_hadir' => 0,
                    'total_sakit' => 0,
                    'total_izin' => 0,
                    'total_alpa' => 0,
                    'total_sesi_diikuti' => 0,
                    'total_jadwal_kelas' => $totalJadwalKelasIni,
                    'kategori' => $kategori,
                    'persentase_hadir' => 0
                ];
            } else {
                // Siswa tercatat, hitung dari stats
                $totalHadir = (int)$stats['total_hadir'];
                $totalSakit = (int)$stats['total_sakit'];
                $totalIzin = (int)$stats['total_izin'];
                $totalAlpa = (int)$stats['total_alpa'];
                $totalSesiDiikuti = (int)$stats['total_sesi_diikuti'];
                
                // SIMPLIFIKASI: 7 KATEGORI JELAS
                
                if ($totalHadir === $totalJadwalKelasIni && $totalHadir === $totalSesiDiikuti) {
                    // 1. Hadir di SEMUA jadwal kelasnya
                    $kategori = 'hadir_semua';
                    $kategorisasi['hadir_semua']++;
                    
                } elseif ($totalSakit === $totalJadwalKelasIni && $totalSakit === $totalSesiDiikuti) {
                    // 2. Sakit di SEMUA jadwal kelasnya
                    $kategori = 'sakit_semua';
                    $kategorisasi['sakit_semua']++;
                    
                } elseif ($totalIzin === $totalJadwalKelasIni && $totalIzin === $totalSesiDiikuti) {
                    // 3. Izin di SEMUA jadwal kelasnya
                    $kategori = 'izin_semua';
                    $kategorisasi['izin_semua']++;
                    
                } elseif ($totalAlpa === $totalJadwalKelasIni && $totalAlpa === $totalSesiDiikuti) {
                    // 4. Alpa di SEMUA jadwal kelasnya
                    $kategori = 'alpa_semua';
                    $kategorisasi['alpa_semua']++;
                    
                } elseif ($totalHadir > 0 && $totalSakit > 0 && $totalIzin === 0 && $totalAlpa === 0) {
                    // 5. Hadir di beberapa mapel, Sakit di beberapa mapel
                    $kategori = 'hadir_sakit';
                    $kategorisasi['hadir_sakit']++;
                    
                } elseif ($totalHadir > 0 && $totalIzin > 0 && $totalSakit === 0 && $totalAlpa === 0) {
                    // 6. Hadir di beberapa mapel, Izin di beberapa mapel
                    $kategori = 'hadir_izin';
                    $kategorisasi['hadir_izin']++;
                    
                } elseif ($totalHadir > 0 && $totalAlpa > 0) {
                    // 7. Hadir di beberapa mapel, Alpa di beberapa mapel
                    $kategori = 'hadir_alpa';
                    $kategorisasi['hadir_alpa']++;
                    
                } else {
                    // Kategori lainnya yang tidak masuk 7 kategori utama
                    // Contoh: kombinasi sakit+izin, sakit+alpa, izin+alpa, atau campuran 3
                    $kategori = 'lainnya';
                    $kategorisasi['lainnya']++;
                }

                $detailSiswa[] = [
                    'siswa_id' => $siswaId,
                    'nama_lengkap' => $siswa['nama_lengkap'],
                    'nis' => $siswa['nis'],
                    'nama_kelas' => $siswa['nama_kelas'],
                    'kelas_id' => $kelasIdSiswa,
                    'total_hadir' => $totalHadir,
                    'total_sakit' => $totalSakit,
                    'total_izin' => $totalIzin,
                    'total_alpa' => $totalAlpa,
                    'total_sesi_diikuti' => $totalSesiDiikuti,
                    'total_jadwal_kelas' => $totalJadwalKelasIni,
                    'kategori' => $kategori,
                    'persentase_hadir' => $totalJadwalKelasIni > 0 ? round(($totalHadir / $totalJadwalKelasIni) * 100, 2) : 0
                ];
            }
        }

        return [
            'total_siswa' => $totalSiswa,
            'hadir_semua' => $kategorisasi['hadir_semua'],
            'sakit_semua' => $kategorisasi['sakit_semua'],
            'izin_semua' => $kategorisasi['izin_semua'],
            'alpa_semua' => $kategorisasi['alpa_semua'],
            'hadir_sakit' => $kategorisasi['hadir_sakit'],
            'hadir_izin' => $kategorisasi['hadir_izin'],
            'hadir_alpa' => $kategorisasi['hadir_alpa'],
            'tidak_tercatat' => $kategorisasi['tidak_tercatat'],
            'lainnya' => $kategorisasi['lainnya'],
            'total_jadwal_terisi' => $totalJadwalTerisi,
            'detail_siswa' => $detailSiswa
        ];
    }
}
