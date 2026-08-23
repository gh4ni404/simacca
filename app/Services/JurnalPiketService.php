<?php

namespace App\Services;

use App\Models\JurnalPiketModel;
use App\Models\GuruPiketModel;

/**
 * Jurnal Piket Service
 *
 * Handles business logic for Teacher Duty Journal (Jurnal Piket):
 * - CRUD operations
 * - Photo uploads & optimization
 * - Fetching default task guidelines based on duty schedule or system template
 */
class JurnalPiketService extends BaseService
{
    protected $jurnalPiketModel;
    protected $guruPiketModel;

    public function __construct()
    {
        parent::__construct();
        $this->jurnalPiketModel = new JurnalPiketModel();
        $this->guruPiketModel   = new GuruPiketModel();
    }

    /**
     * Get journals by specific guru
     */
    public function getJurnalByGuru(int $guruId, ?string $startDate = null, ?string $endDate = null): array
    {
        try {
            $data = $this->jurnalPiketModel->getJurnalByGuru($guruId, $startDate, $endDate);
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get jurnal piket by guru: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data jurnal piket');
        }
    }

    /**
     * Get journals for admin monitoring
     */
    public function getJurnalWithGuru(?string $startDate = null, ?string $endDate = null, ?int $guruId = null): array
    {
        try {
            $data = $this->jurnalPiketModel->getJurnalWithGuru($startDate, $endDate, $guruId);
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get jurnal piket with guru: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data jurnal piket');
        }
    }

    /**
     * Get single journal by ID
     */
    public function getById(int $id): array
    {
        try {
            $jurnal = $this->jurnalPiketModel->select('jurnal_piket.*, guru.nama_lengkap, guru.nip, users.profile_photo')
                ->join('guru', 'guru.id = jurnal_piket.guru_id')
                ->join('users', 'users.id = guru.user_id', 'left')
                ->where('jurnal_piket.id', $id)
                ->first();

            if (!$jurnal) {
                return $this->errorResponse('Jurnal piket tidak ditemukan');
            }

            return $this->successResponse($jurnal);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get jurnal piket by ID: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil detail jurnal piket');
        }
    }

    /**
     * Get rincian tugas for a guru on a specific date
     */
    public function getRincianTugasForGuruAndDate(int $guruId, string $tanggal): string
    {
        $dayEnglish = date('l', strtotime($tanggal));
        $dayMap = [
            'Monday'    => 'senin',
            'Tuesday'   => 'selasa',
            'Wednesday' => 'rabu',
            'Thursday'  => 'kamis',
            'Friday'    => 'jumat',
            'Saturday'  => 'sabtu',
            'Sunday'    => 'minggu',
        ];
        $hari = $dayMap[$dayEnglish] ?? 'senin';

        $tahunAjaran = get_active_tahun_ajaran();
        $month = (int) date('m', strtotime($tanggal));
        $semester = ($month >= 7 && $month <= 12) ? 'ganjil' : 'genap';

        $assignment = $this->guruPiketModel->where('guru_id', $guruId)
            ->where('hari', $hari)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('semester', $semester)
            ->where('is_active', 1)
            ->first();

        if ($assignment && !empty($assignment['jobdesk_id']) && !empty($assignment['rincian_tugas'])) {
            return $assignment['rincian_tugas'];
        }

        return '';
    }

    /**
     * Create new jurnal piket entry
     */
    public function create(array $data, ?object $file = null): array
    {
        $rules = [
            'guru_id'      => 'required|integer',
            'tanggal'      => 'required|valid_date',
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap,Ganjil,Genap]',
            'deskripsi'    => 'required',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal', $this->getErrors());
        }

        // Check if journal already exists for this guru & date
        if ($this->jurnalPiketModel->isJurnalExist($data['guru_id'], $data['tanggal'])) {
            return $this->errorResponse('Jurnal piket untuk tanggal ' . date('d/m/Y', strtotime($data['tanggal'])) . ' sudah pernah diisi');
        }

        // Handle photo upload
        $fotoName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = WRITEPATH . 'uploads/jurnal_piket';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $fotoName = $file->getRandomName();
            $file->move($uploadPath, $fotoName);

            $filePath = $uploadPath . '/' . $fotoName;
            if (file_exists($filePath) && $file->isImage()) {
                helper('image');
                if (function_exists('optimize_jurnal_photo')) {
                    optimize_jurnal_photo($filePath, $filePath);
                }
            }
        }

        return $this->executeInTransaction(function () use ($data, $fotoName) {
            $insertData = [
                'guru_id'          => $data['guru_id'],
                'tanggal'          => $data['tanggal'],
                'tahun_ajaran'     => $data['tahun_ajaran'],
                'semester'         => strtolower($data['semester']),
                'rincian_tugas'    => $data['rincian_tugas'] ?? null,
                'deskripsi'        => $data['deskripsi'],
                'catatan'          => $data['catatan'] ?? null,
                'foto_dokumentasi' => $fotoName,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $id = $this->jurnalPiketModel->insert($insertData);

            if (!$id) {
                throw new \Exception('Gagal menyimpan jurnal piket');
            }

            $this->log('info', "Jurnal piket created: ID={$id}, guru_id={$data['guru_id']}, tanggal={$data['tanggal']}");

            return ['id' => $id];
        });
    }

    /**
     * Update existing jurnal piket entry
     */
    public function update(int $id, array $data, ?object $file = null): array
    {
        $jurnal = $this->jurnalPiketModel->find($id);

        if (!$jurnal) {
            return $this->errorResponse('Jurnal piket tidak ditemukan');
        }

        $rules = [
            'guru_id'   => 'required|integer',
            'tanggal'   => 'required|valid_date',
            'deskripsi' => 'required',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal', $this->getErrors());
        }

        // Check if journal already exists for this guru & date (excluding current record)
        if ($this->jurnalPiketModel->isJurnalExist($data['guru_id'], $data['tanggal'], $id)) {
            return $this->errorResponse('Jurnal piket untuk tanggal ' . date('d/m/Y', strtotime($data['tanggal'])) . ' sudah ada');
        }

        $fotoName = $jurnal['foto_dokumentasi'];

        // Handle new photo upload if provided
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = WRITEPATH . 'uploads/jurnal_piket';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Remove old photo if exists
            if ($fotoName && file_exists($uploadPath . '/' . $fotoName)) {
                unlink($uploadPath . '/' . $fotoName);
            }

            $fotoName = $file->getRandomName();
            $file->move($uploadPath, $fotoName);

            $filePath = $uploadPath . '/' . $fotoName;
            if (file_exists($filePath) && $file->isImage()) {
                helper('image');
                if (function_exists('optimize_jurnal_photo')) {
                    optimize_jurnal_photo($filePath, $filePath);
                }
            }
        }

        return $this->executeInTransaction(function () use ($id, $data, $fotoName) {
            $updateData = [
                'tanggal'          => $data['tanggal'],
                'rincian_tugas'    => $data['rincian_tugas'] ?? null,
                'deskripsi'        => $data['deskripsi'],
                'catatan'          => $data['catatan'] ?? null,
                'foto_dokumentasi' => $fotoName,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $this->jurnalPiketModel->update($id, $updateData);

            $this->log('info', "Jurnal piket updated: ID={$id}");

            return ['id' => $id];
        });
    }

    /**
     * Delete jurnal piket entry
     */
    public function delete(int $id): array
    {
        $jurnal = $this->jurnalPiketModel->find($id);

        if (!$jurnal) {
            return $this->errorResponse('Jurnal piket tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id, $jurnal) {
            // Delete photo file if exists
            if (!empty($jurnal['foto_dokumentasi'])) {
                $filePath = WRITEPATH . 'uploads/jurnal_piket/' . $jurnal['foto_dokumentasi'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $this->jurnalPiketModel->delete($id);

            $this->log('info', "Jurnal piket deleted: ID={$id}");

            return ['id' => $id];
        });
    }
}
