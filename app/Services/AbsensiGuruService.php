<?php

namespace App\Services;

use App\Models\AbsensiGuruModel;
use App\Models\IzinGuruModel;
use App\Models\GuruModel;
use CodeIgniter\I18n\Time;

class AbsensiGuruService extends BaseService
{
    protected $absensiGuruModel;
    protected $izinGuruModel;
    protected $guruModel;

    public function __construct()
    {
        $this->absensiGuruModel = new AbsensiGuruModel();
        $this->izinGuruModel = new IzinGuruModel();
        $this->guruModel = new GuruModel();
    }

    /**
     * Check if guru has already checked in today
     */
    public function hasCheckedInToday(int $guruId): bool
    {
        $today = Time::today()->toDateString();
        
        $absensi = $this->absensiGuruModel
            ->where('guru_id', $guruId)
            ->where('tanggal', $today)
            ->first();
        
        return $absensi !== null;
    }

    /**
     * Check if guru has already checked out today
     */
    public function hasCheckedOutToday(int $guruId): bool
    {
        $today = Time::today()->toDateString();
        
        $absensi = $this->absensiGuruModel
            ->where('guru_id', $guruId)
            ->where('tanggal', $today)
            ->where('check_out IS NOT NULL')
            ->first();
        
        return $absensi !== null;
    }

    /**
     * Get today's absensi record for guru
     */
    public function getTodayAbsensi(int $guruId): ?array
    {
        $today = Time::today()->toDateString();
        
        return $this->absensiGuruModel
            ->where('guru_id', $guruId)
            ->where('tanggal', $today)
            ->first();
    }

    /**
     * Perform check-in for guru
     */
    public function checkIn(int $guruId, array $data): array
    {
        try {
            // Rate limiting: 3 attempts per 5 minutes
            $rateLimitResult = $this->checkRateLimit($guruId, 'checkin');
            if (!$rateLimitResult['allowed']) {
                return $this->error($rateLimitResult['message']);
            }

            // Validate guru hasn't checked in yet today
            if ($this->hasCheckedInToday($guruId)) {
                return $this->error('Anda sudah melakukan check-in hari ini');
            }

            // Handle foto upload
            $fotoPath = null;
            if (isset($data['foto']) && $data['foto'] !== null) {
                $fotoPath = $this->handleFotoUpload($data['foto'], 'check-in', $guruId);
                if (!$fotoPath) {
                    return $this->error('Gagal mengupload foto selfie');
                }
            }

            // Determine status based on check-in time
            $checkIn = $data['check_in'] ?? Time::now()->toTimeString();
            $status = $this->determineStatus($checkIn);

            // Get user_id from guru_id
            $guru = $this->guruModel->find($guruId);
            $userId = $guru['user_id'] ?? session()->get('user_id');

            // Prepare absensi data
            $absensiData = [
                'guru_id' => $guruId,
                'tanggal' => $data['tanggal'] ?? Time::today()->toDateString(),
                'check_in' => $checkIn,
                'foto_check_in' => $fotoPath,
                'catatan' => $data['catatan'] ?? null,
                'latitude_check_in' => $data['latitude'] ?? null,
                'longitude_check_in' => $data['longitude'] ?? null,
                'status' => $status,
                'created_by' => $userId,
            ];

            // Save to database
            if ($this->absensiGuruModel->insert($absensiData)) {
                return $this->success([
                    'id' => $this->absensiGuruModel->getInsertID(),
                    'status' => $status,
                    'check_in' => $checkIn
                ], 'Check-in berhasil');
            }

            return $this->error('Gagal menyimpan data check-in');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::checkIn - ' . $e->getMessage());
            return $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Perform check-out for guru
     */
    public function checkOut(int $guruId, array $data): array
    {
        try {
            // Rate limiting: 3 attempts per 5 minutes
            $rateLimitResult = $this->checkRateLimit($guruId, 'checkout');
            if (!$rateLimitResult['allowed']) {
                return $this->error($rateLimitResult['message']);
            }

            // Get today's absensi record
            $absensi = $this->getTodayAbsensi($guruId);
            
            if (!$absensi) {
                return $this->error('Anda belum melakukan check-in hari ini');
            }

            if ($absensi['check_out'] !== null) {
                return $this->error('Anda sudah melakukan check-out hari ini');
            }

            // Handle foto upload (optional for checkout)
            $fotoPath = null;
            if (isset($data['foto']) && $data['foto'] !== null) {
                $fotoPath = $this->handleFotoUpload($data['foto'], 'check-out', $guruId);
                if (!$fotoPath) {
                    return $this->error('Gagal mengupload foto');
                }
            }

            $checkOut = $data['check_out'] ?? Time::now()->toTimeString();
            
            // Calculate duration
            $checkInTime = strtotime($absensi['check_in']);
            $checkOutTime = strtotime($checkOut);
            $durasiMenit = round(($checkOutTime - $checkInTime) / 60);
            
            // Check for early checkout (less than 8 hours = 480 minutes)
            $earlyCheckout = $durasiMenit < 480;

            // Prepare update data
            $updateData = [
                'check_out' => $checkOut,
                'foto_check_out' => $fotoPath,
                'durasi_menit' => $durasiMenit,
                'early_checkout' => $earlyCheckout,
                'early_checkout_reason' => $data['keterangan_keluar'] ?? null,
                'latitude_check_out' => $data['latitude'] ?? null,
                'longitude_check_out' => $data['longitude'] ?? null,
            ];

            // Update database
            if ($this->absensiGuruModel->update($absensi['id'], $updateData)) {
                return $this->success([
                    'id' => $absensi['id'],
                    'check_out' => $checkOut,
                    'durasi_menit' => $durasiMenit,
                    'early_checkout' => $earlyCheckout
                ], 'Check-out berhasil');
            }

            return $this->error('Gagal menyimpan data check-out');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::checkOut - ' . $e->getMessage());
            return $this->error('Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Determine status based on check-in time
     */
    protected function determineStatus(string $checkIn): string
    {
        // Default work start time: 07:15 (as per migration comments)
        $batasWaktu = Time::parse('07:15:00');
        $waktuMasuk = Time::parse($checkIn);

        if ($waktuMasuk->isAfter($batasWaktu)) {
            return 'terlambat';
        }

        return 'hadir';
    }

    /**
     * Handle foto upload
     */
    protected function handleFotoUpload($foto, string $type, int $guruId): ?string
    {
        try {
            // Create date hierarchy directory structure (YYYY/MM/DD)
            $year = date('Y');
            $month = date('m');
            $day = date('d');
            $uploadPath = WRITEPATH . "uploads/absensi_guru/{$year}/{$month}/{$day}/";
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate filename with simplified format
            $timestamp = date('His'); // HourMinuteSecond only
            $extension = $foto->getExtension();
            $filename = "{$type}_guru{$guruId}_{$timestamp}.jpg"; // Always save as JPG
            $filepath = $uploadPath . $filename;

            // Move uploaded file
            if ($foto->move($uploadPath, $filename)) {
                // Optimize image using helper (max 1024px, 85% quality)
                optimize_image($filepath, $filepath, 1024, 1024, 85);
                
                // Return relative path from writable/
                return "uploads/absensi_guru/{$year}/{$month}/{$day}/{$filename}";
            }

            return null;

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::handleFotoUpload - ' . $e->getMessage());
            return null;
        }
    }



    /**
     * Check rate limit for anti-fraud protection
     * 
     * @param int $guruId
     * @param string $action 'checkin' or 'checkout'
     * @return array ['allowed' => bool, 'message' => string]
     */
    protected function checkRateLimit(int $guruId, string $action): array
    {
        $cache = \Config\Services::cache();
        $cacheKey = "absensi_guru_ratelimit_{$action}_{$guruId}";
        
        // Get current attempts
        $attempts = $cache->get($cacheKey);
        
        if ($attempts === null) {
            // First attempt - set to 1 with 5 minute TTL
            $cache->save($cacheKey, 1, 300); // 300 seconds = 5 minutes
            return [
                'allowed' => true,
                'message' => ''
            ];
        }
        
        // Check if limit exceeded
        if ($attempts >= 3) {
            return [
                'allowed' => false,
                'message' => 'Terlalu banyak percobaan. Silakan tunggu 5 menit sebelum mencoba lagi.'
            ];
        }
        
        // Increment attempts
        $cache->save($cacheKey, $attempts + 1, 300);
        
        return [
            'allowed' => true,
            'message' => ''
        ];
    }

    /**
     * Get absensi history for guru with pagination
     */
    public function getHistory(int $guruId, array $filters = []): array
    {
        try {
            $builder = $this->absensiGuruModel
                ->select('absensi_guru.*')
                ->where('guru_id', $guruId)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('check_in', 'DESC');

            // Apply filters
            if (!empty($filters['bulan'])) {
                $builder->where('MONTH(tanggal)', $filters['bulan']);
            }

            if (!empty($filters['tahun'])) {
                $builder->where('YEAR(tanggal)', $filters['tahun']);
            }

            if (!empty($filters['status'])) {
                $builder->where('status', $filters['status']);
            }

            // Pagination
            $perPage = $filters['per_page'] ?? 20;
            $data = $builder->paginate($perPage);
            $pager = $this->absensiGuruModel->pager;

            return $this->success([
                'data' => $data,
                'pager' => $pager
            ], 'Data berhasil diambil');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::getHistory - ' . $e->getMessage());
            return $this->error('Gagal mengambil data history');
        }
    }

    /**
     * Get monthly statistics for guru
     */
    public function getMonthlyStats(int $guruId, ?int $bulan = null, ?int $tahun = null): array
    {
        try {
            $bulan = $bulan ?? Time::now()->month;
            $tahun = $tahun ?? Time::now()->year;

            // Get all absensi records for the month
            $absensi = $this->absensiGuruModel
                ->where('guru_id', $guruId)
                ->where('MONTH(tanggal)', $bulan)
                ->where('YEAR(tanggal)', $tahun)
                ->findAll();

            // Count statistics
            $stats = [
                'total_hadir' => 0,
                'total_terlambat' => 0,
                'total_izin' => 0,
                'total_sakit' => 0,
                'total_alpha' => 0,
            ];

            foreach ($absensi as $record) {
                switch ($record['status']) {
                    case 'hadir':
                        $stats['total_hadir']++;
                        break;
                    case 'terlambat':
                        $stats['total_terlambat']++;
                        break;
                    case 'izin':
                        $stats['total_izin']++;
                        break;
                    case 'sakit':
                        $stats['total_sakit']++;
                        break;
                    case 'alpha':
                        $stats['total_alpha']++;
                        break;
                }
            }

            // Get izin records
            $izin = $this->izinGuruModel
                ->where('guru_id', $guruId)
                ->where('MONTH(tanggal_mulai)', $bulan)
                ->where('YEAR(tanggal_mulai)', $tahun)
                ->where('status', 'disetujui')
                ->findAll();

            $stats['total_izin'] = count($izin);

            return $this->success($stats, 'Statistik berhasil diambil');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::getMonthlyStats - ' . $e->getMessage());
            return $this->error('Gagal mengambil statistik');
        }
    }

    /**
     * Get all absensi records for admin monitoring
     */
    public function getAllAbsensiForAdmin(array $filters = []): array
    {
        try {
            $builder = $this->absensiGuruModel
                ->select('absensi_guru.*, guru.nama_lengkap as nama_guru, guru.nip, users.email')
                ->join('guru', 'guru.id = absensi_guru.guru_id')
                ->join('users', 'users.id = guru.user_id')
                ->orderBy('absensi_guru.tanggal', 'DESC')
                ->orderBy('absensi_guru.check_in', 'DESC');

            // Apply filters
            if (!empty($filters['tanggal'])) {
                $builder->where('absensi_guru.tanggal', $filters['tanggal']);
            }

            if (!empty($filters['bulan'])) {
                $builder->where('MONTH(absensi_guru.tanggal)', $filters['bulan']);
            }

            if (!empty($filters['tahun'])) {
                $builder->where('YEAR(absensi_guru.tanggal)', $filters['tahun']);
            }

            if (!empty($filters['status'])) {
                $builder->where('absensi_guru.status', $filters['status']);
            }

            if (!empty($filters['guru_id'])) {
                $builder->where('absensi_guru.guru_id', $filters['guru_id']);
            }

            // Pagination
            $perPage = $filters['per_page'] ?? 20;
            $data = $builder->paginate($perPage);
            $pager = $this->absensiGuruModel->pager;

            return $this->success([
                'data' => $data,
                'pager' => $pager
            ], 'Data berhasil diambil');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::getAllAbsensiForAdmin - ' . $e->getMessage());
            return $this->error('Gagal mengambil data absensi');
        }
    }

    /**
     * Get today's summary for admin dashboard
     */
    public function getTodaySummary(): array
    {
        try {
            $today = Time::today()->toDateString();
            
            // Get today's absensi
            $absensiToday = $this->absensiGuruModel
                ->select('absensi_guru.*, guru.nama_lengkap as nama_guru')
                ->join('guru', 'guru.id = absensi_guru.guru_id')
                ->where('absensi_guru.tanggal', $today)
                ->findAll();

            // Get total guru count
            $totalGuru = $this->guruModel->countAll();

            // Calculate statistics
            $stats = [
                'total_guru' => $totalGuru,
                'sudah_checkin' => count($absensiToday),
                'belum_checkin' => $totalGuru - count($absensiToday),
                'hadir' => 0,
                'terlambat' => 0,
                'izin' => 0,
                'sakit' => 0,
                'sudah_checkout' => 0,
            ];

            foreach ($absensiToday as $record) {
                if ($record['status'] === 'hadir') {
                    $stats['hadir']++;
                } elseif ($record['status'] === 'terlambat') {
                    $stats['terlambat']++;
                } elseif ($record['status'] === 'izin') {
                    $stats['izin']++;
                } elseif ($record['status'] === 'sakit') {
                    $stats['sakit']++;
                }

                if ($record['check_out'] !== null) {
                    $stats['sudah_checkout']++;
                }
            }

            return $this->success($stats, 'Data berhasil diambil');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::getTodaySummary - ' . $e->getMessage());
            return $this->error('Gagal mengambil data summary');
        }
    }

    /**
     * Update absensi status by admin
     */
    public function updateStatusByAdmin(int $absensiId, string $newStatus, ?string $keterangan = null): array
    {
        try {
            // Validate status
            $validStatuses = ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'];
            if (!in_array($newStatus, $validStatuses)) {
                return $this->error('Status tidak valid');
            }

            // Get absensi record
            $absensi = $this->absensiGuruModel->find($absensiId);
            if (!$absensi) {
                return $this->error('Data absensi tidak ditemukan');
            }

            // Update status
            $updateData = [
                'status' => $newStatus,
            ];

            if ($keterangan !== null) {
                $updateData['keterangan_masuk'] = $keterangan;
            }

            if ($this->absensiGuruModel->update($absensiId, $updateData)) {
                return $this->success(null, 'Status berhasil diupdate');
            }

            return $this->error('Gagal mengupdate status');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::updateStatusByAdmin - ' . $e->getMessage());
            return $this->error('Terjadi kesalahan saat mengupdate status');
        }
    }

    /**
     * Generate laporan for export
     */
    public function generateLaporan(array $filters = []): array
    {
        try {
            $builder = $this->absensiGuruModel
                ->select('absensi_guru.*, guru.nama_lengkap as nama_guru, guru.nip, users.email')
                ->join('guru', 'guru.id = absensi_guru.guru_id')
                ->join('users', 'users.id = guru.user_id')
                ->orderBy('absensi_guru.tanggal', 'ASC')
                ->orderBy('guru.nama_lengkap', 'ASC');

            // Apply filters
            if (!empty($filters['tanggal_mulai'])) {
                $builder->where('absensi_guru.tanggal >=', $filters['tanggal_mulai']);
            }

            if (!empty($filters['tanggal_selesai'])) {
                $builder->where('absensi_guru.tanggal <=', $filters['tanggal_selesai']);
            }

            if (!empty($filters['bulan'])) {
                $builder->where('MONTH(absensi_guru.tanggal)', $filters['bulan']);
            }

            if (!empty($filters['tahun'])) {
                $builder->where('YEAR(absensi_guru.tanggal)', $filters['tahun']);
            }

            if (!empty($filters['status'])) {
                $builder->where('absensi_guru.status', $filters['status']);
            }

            if (!empty($filters['guru_id'])) {
                $builder->where('absensi_guru.guru_id', $filters['guru_id']);
            }

            $data = $builder->findAll();

            return $this->success($data, 'Laporan berhasil digenerate');

        } catch (\Exception $e) {
            log_message('error', 'AbsensiGuruService::generateLaporan - ' . $e->getMessage());
            return $this->error('Gagal generate laporan');
        }
    }
}
