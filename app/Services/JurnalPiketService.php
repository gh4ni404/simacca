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
    public function getJurnalByGuru(int $guruId, ?string $startDate = null, ?string $endDate = null, string $order = 'DESC'): array
    {
        try {
            $data = $this->jurnalPiketModel->getJurnalByGuru($guruId, $startDate, $endDate, $order);
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get jurnal piket by guru: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data jurnal piket');
        }
    }

    /**
     * Get journals for admin monitoring
     */
    public function getJurnalWithGuru(?string $startDate = null, ?string $endDate = null, ?int $guruId = null, string $order = 'DESC'): array
    {
        try {
            $data = $this->jurnalPiketModel->getJurnalWithGuru($startDate, $endDate, $guruId, $order);
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
     * Get rincian tugas for a guru based on active master jobdesk mapping
     */
    public function getRincianTugasForGuru(int $guruId, ?string $tanggal = null): string
    {
        $tanggal = $tanggal ?: date('Y-m-d');
        $tahunAjaran = get_active_tahun_ajaran();
        $month = (int) date('m', strtotime($tanggal));
        $semester = ($month >= 7 && $month <= 12) ? 'ganjil' : 'genap';

        // Ambil mapping jobdesk guru pada tahun ajaran & semester aktif (tidak terikat hari tertentu)
        $assignment = $this->guruPiketModel
            ->select('master_jobdesk_piket.nama_jobdesk, master_jobdesk_piket.rincian_tugas AS master_rincian_tugas')
            ->join('master_jobdesk_piket', 'master_jobdesk_piket.id = guru_piket.jobdesk_id', 'left')
            ->where('guru_piket.guru_id', $guruId)
            ->where('guru_piket.tahun_ajaran', $tahunAjaran)
            ->where('guru_piket.semester', $semester)
            ->where('guru_piket.is_active', 1)
            ->first();

        if ($assignment) {
            $namaJobdesk = trim($assignment['nama_jobdesk'] ?? '');
            $masterRincian = trim($assignment['master_rincian_tugas'] ?? '');

            if ($namaJobdesk !== '') {
                return "Jobdesk: " . $namaJobdesk . "\n\n" . $masterRincian;
            }
            return $masterRincian;
        }

        return '';
    }

    /**
     * Create new jurnal piket entry
     */
    public function create(array $data, ?array $files = null): array
    {
        $rules = [
            'guru_id'      => 'required|integer',
            'tanggal'      => 'required|valid_date[Y-m-d]',
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[ganjil,genap]',
            'deskripsi'    => 'required|min_length[5]',
        ];

        $messages = [
            'guru_id' => [
                'required' => 'Identitas guru piket tidak valid.',
                'integer'  => 'Identitas guru piket harus berupa angka.',
            ],
            'tanggal' => [
                'required'   => 'Tanggal piket wajib diisi.',
                'valid_date' => 'Format tanggal piket tidak valid (YYYY-MM-DD).',
            ],
            'tahun_ajaran' => [
                'required' => 'Tahun ajaran aktif belum ditentukan.',
            ],
            'semester' => [
                'required' => 'Semester wajib ditentukan.',
                'in_list'  => 'Semester harus berupa ganjil atau genap.',
            ],
            'deskripsi' => [
                'required'   => 'Uraian / deskripsi kegiatan piket wajib diisi.',
                'min_length' => 'Uraian kegiatan piket minimal 5 karakter agar informasi lebih jelas.',
            ],
        ];

        if (!$this->validate($data, $rules, $messages)) {
            $errors = $this->getErrors();
            $errorSummary = 'Validasi gagal: ' . implode('. ', array_values($errors));
            return $this->errorResponse($errorSummary, $errors);
        }

        // Handle photo upload
        $uploadedPhotos = [];
        $uploadInfo = [
            'temp_dir_default'   => sys_get_temp_dir(),
            'upload_tmp_dir_ini' => ini_get('upload_tmp_dir') ?: 'OS Default (' . sys_get_temp_dir() . ')',
            'files_received'     => 0,
            'valid_files_count'  => 0,
            'files_detail'       => [],
            'uploaded_photos'    => [],
        ];

        if (!empty($files)) {
            $uploadInfo['files_received'] = count($files);
            $validFilesCount = 0;
            foreach ($files as $file) {
                if (!$file) continue;
                $tempPath = method_exists($file, 'getTempName') ? $file->getTempName() : null;
                $tempDir  = !empty($tempPath) ? dirname($tempPath) : sys_get_temp_dir();
                $isValid  = method_exists($file, 'isValid') ? $file->isValid() : false;
                $errCode  = method_exists($file, 'getError') ? $file->getError() : null;
                $errMsg   = method_exists($file, 'getErrorString') ? $file->getErrorString() : null;

                $uploadInfo['files_detail'][] = [
                    'client_name' => method_exists($file, 'getClientName') ? $file->getClientName() : 'unknown',
                    'size'        => method_exists($file, 'getSize') ? $file->getSize() : 0,
                    'mime_type'   => method_exists($file, 'getMimeType') ? $file->getMimeType() : null,
                    'temp_file'   => $tempPath,
                    'temp_dir'    => $tempDir,
                    'is_valid'    => $isValid,
                    'error_code'  => $errCode,
                    'error_msg'   => $errMsg,
                ];

                if ($isValid && $errCode !== UPLOAD_ERR_NO_FILE) {
                    $validFilesCount++;
                }
            }
            $uploadInfo['valid_files_count'] = $validFilesCount;

            log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - Temp Dir: {$uploadInfo['temp_dir_default']}, Upload tmp ini: {$uploadInfo['upload_tmp_dir_ini']}, Files received: {$uploadInfo['files_received']}, Valid files: {$validFilesCount}");

            if ($validFilesCount > 4) {
                log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - Valid files count exceeds limit 4: {$validFilesCount}");
                return $this->errorResponse('Validasi gagal: Jumlah foto melebihi batas maksimal 4 foto.', 422, [], ['upload_info' => $uploadInfo]);
            }

            foreach ($files as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                    $mimeType = $file->getMimeType();
                    if (!in_array($mimeType, $validMimes)) {
                        $err = ['foto_dokumentasi' => 'Format file foto tidak didukung. Gunakan format JPG, JPEG, PNG, atau WEBP.'];
                        log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - Invalid MIME: {$mimeType}");
                        return $this->errorResponse('Validasi gagal: Format foto harus JPG, JPEG, PNG, atau WEBP.', $err, [], ['upload_info' => $uploadInfo]);
                    }

                    if ($file->getSize() > 1048576) {
                        $err = ['foto_dokumentasi' => 'Ukuran file foto melebihi batas 1MB. Silakan pilih foto dengan ukuran lebih kecil.'];
                        log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - File size exceeds 1MB: {$file->getSize()} bytes");
                        return $this->errorResponse('Validasi gagal: Ukuran file foto melebihi 1MB.', $err, [], ['upload_info' => $uploadInfo]);
                    }

                    $tempPath   = $file->getTempName();
                    $tempDir    = !empty($tempPath) ? dirname($tempPath) : sys_get_temp_dir();
                    $clientName = $file->getClientName();
                    $fileSize   = $file->getSize();

                    $uploadPath = WRITEPATH . 'uploads/jurnal_piket';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $fotoName = $file->getRandomName();
                    log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - Moving photo: '{$clientName}' from Temp [{$tempDir}/{$tempPath}] to Target [{$uploadPath}/{$fotoName}]");
                    $this->log('info', "[JURNAL PIKET FOTO UPLOAD] Temp Dir: {$tempDir} | Temp File: {$tempPath} | Original: {$clientName} ({$fileSize} bytes) -> Destination: {$uploadPath}/{$fotoName}");
                    $file->move($uploadPath, $fotoName);

                    $filePath = $uploadPath . '/' . $fotoName;
                    if (file_exists($filePath)) {
                        helper('image');
                        if (function_exists('optimize_jurnal_photo')) {
                            optimize_jurnal_photo($filePath, $filePath);
                        }
                    }
                    $uploadedPhotos[] = $fotoName;
                    $uploadInfo['uploaded_photos'][] = $fotoName;
                } elseif ($file && !$file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
                    $tempPath = method_exists($file, 'getTempName') ? $file->getTempName() : 'N/A';
                    $tempDir  = !empty($tempPath) && $tempPath !== 'N/A' ? dirname($tempPath) : sys_get_temp_dir();
                    $err = ['foto_dokumentasi' => 'Gagal mengunggah file foto (' . $file->getClientName() . '): ' . $file->getErrorString() . ' (Error code: ' . $file->getError() . ')'];
                    log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - File upload invalid: Name={$file->getClientName()}, Error={$file->getError()} ({$file->getErrorString()}), TempDir={$tempDir}");
                    return $this->errorResponse('Validasi gagal: Gagal mengunggah file foto dokumentasi.', $err, [], ['upload_info' => $uploadInfo]);
                }
            }
        }

        $fotoNamesString = !empty($uploadedPhotos) ? implode(',', $uploadedPhotos) : null;
        log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - Final foto_dokumentasi string: " . ($fotoNamesString ?: 'null'));

        $txResult = $this->executeInTransaction(function () use ($data, $fotoNamesString, $uploadInfo) {
            $insertData = [
                'guru_id'          => $data['guru_id'],
                'tanggal'          => $data['tanggal'],
                'tahun_ajaran'     => $data['tahun_ajaran'],
                'semester'         => $data['semester'],
                'rincian_tugas'    => $data['rincian_tugas'] ?? null,
                'deskripsi'        => $data['deskripsi'],
                'catatan'          => $data['catatan'] ?? null,
                'foto_dokumentasi' => $fotoNamesString,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $id = $this->jurnalPiketModel->insert($insertData);

            if (!$id) {
                throw new \Exception('Gagal menyimpan jurnal piket');
            }

            log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::create() - Jurnal piket created successfully: ID={$id}, guru_id={$data['guru_id']}, tanggal={$data['tanggal']}");
            $this->log('info', "Jurnal piket created: ID={$id}, guru_id={$data['guru_id']}, tanggal={$data['tanggal']}");

            return [
                'id'               => $id,
                'foto_dokumentasi' => $fotoNamesString,
                'upload_info'      => $uploadInfo,
            ];
        });

        if (!$txResult['success']) {
            $txResult['upload_info'] = $uploadInfo;
        }

        return $txResult;
    }

    /**
     * Update existing jurnal piket entry
     */
    public function update(int $id, array $data, ?array $files = null): array
    {
        $jurnal = $this->jurnalPiketModel->find($id);

        if (!$jurnal) {
            return $this->errorResponse('Jurnal piket tidak ditemukan');
        }

        $rules = [
            'guru_id'   => 'required|integer',
            'tanggal'   => 'required|valid_date[Y-m-d]',
            'deskripsi' => 'required|min_length[5]',
        ];

        $messages = [
            'guru_id' => [
                'required' => 'Identitas guru piket tidak valid.',
                'integer'  => 'Identitas guru piket harus berupa angka.',
            ],
            'tanggal' => [
                'required'   => 'Tanggal piket wajib diisi.',
                'valid_date' => 'Format tanggal piket tidak valid (YYYY-MM-DD).',
            ],
            'deskripsi' => [
                'required'   => 'Uraian / deskripsi kegiatan piket wajib diisi.',
                'min_length' => 'Uraian kegiatan piket minimal 5 karakter agar informasi lebih jelas.',
            ],
        ];

        if (!$this->validate($data, $rules, $messages)) {
            $errors = $this->getErrors();
            $errorSummary = 'Validasi gagal: ' . implode('. ', array_values($errors));
            return $this->errorResponse($errorSummary, $errors);
        }

        // Handle existing photos to keep
        $existingPhotos = !empty($jurnal['foto_dokumentasi']) ? explode(',', $jurnal['foto_dokumentasi']) : [];
        $keepPhotos = $data['keep_photos'] ?? [];
        if (!is_array($keepPhotos)) {
            $keepPhotos = [];
        }
        $keepPhotos = array_map('trim', $keepPhotos);
        $keepPhotos = array_intersect($existingPhotos, $keepPhotos);

        $uploadPath = WRITEPATH . 'uploads/jurnal_piket';

        // Delete photos that are not kept
        $photosToDelete = array_diff($existingPhotos, $keepPhotos);
        foreach ($photosToDelete as $ptd) {
            if (!empty($ptd) && file_exists($uploadPath . '/' . $ptd)) {
                @unlink($uploadPath . '/' . $ptd);
                log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - Deleted old photo: {$ptd}");
            }
        }

        $uploadInfo = [
            'temp_dir_default'   => sys_get_temp_dir(),
            'upload_tmp_dir_ini' => ini_get('upload_tmp_dir') ?: 'OS Default (' . sys_get_temp_dir() . ')',
            'files_received'     => 0,
            'valid_files_count'  => 0,
            'keep_photos_count'  => count($keepPhotos),
            'files_detail'       => [],
            'uploaded_photos'    => $keepPhotos,
        ];

        // Validate file count (keep + new)
        $validFilesCount = 0;
        if (!empty($files)) {
            $uploadInfo['files_received'] = count($files);
            foreach ($files as $file) {
                if (!$file) continue;
                $tempPath = method_exists($file, 'getTempName') ? $file->getTempName() : null;
                $tempDir  = !empty($tempPath) ? dirname($tempPath) : sys_get_temp_dir();
                $isValid  = method_exists($file, 'isValid') ? $file->isValid() : false;
                $errCode  = method_exists($file, 'getError') ? $file->getError() : null;
                $errMsg   = method_exists($file, 'getErrorString') ? $file->getErrorString() : null;

                $uploadInfo['files_detail'][] = [
                    'client_name' => method_exists($file, 'getClientName') ? $file->getClientName() : 'unknown',
                    'size'        => method_exists($file, 'getSize') ? $file->getSize() : 0,
                    'mime_type'   => method_exists($file, 'getMimeType') ? $file->getMimeType() : null,
                    'temp_file'   => $tempPath,
                    'temp_dir'    => $tempDir,
                    'is_valid'    => $isValid,
                    'error_code'  => $errCode,
                    'error_msg'   => $errMsg,
                ];

                if ($isValid && $errCode !== UPLOAD_ERR_NO_FILE) {
                    $validFilesCount++;
                }
            }
        }
        $uploadInfo['valid_files_count'] = $validFilesCount;

        log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - ID={$id}: Keep=" . count($keepPhotos) . ", New valid files={$validFilesCount}, Total=" . (count($keepPhotos) + $validFilesCount));

        if (count($keepPhotos) + $validFilesCount > 4) {
            log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - Exceeds 4 photos limit: Keep=" . count($keepPhotos) . ", New={$validFilesCount}");
            return $this->errorResponse('Validasi gagal: Total foto (foto lama + foto baru) melebihi batas maksimal 4 foto.', 422, [], ['upload_info' => $uploadInfo]);
        }

        // Handle new photo uploads
        $uploadedPhotos = $keepPhotos;
        if (!empty($files)) {
            foreach ($files as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                    $mimeType = $file->getMimeType();
                    if (!in_array($mimeType, $validMimes)) {
                        $err = ['foto_dokumentasi' => 'Format file foto tidak didukung. Gunakan format JPG, JPEG, PNG, atau WEBP.'];
                        log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - Invalid MIME: {$mimeType}");
                        return $this->errorResponse('Validasi gagal: Format foto harus JPG, JPEG, PNG, atau WEBP.', $err, [], ['upload_info' => $uploadInfo]);
                    }

                    if ($file->getSize() > 1048576) {
                        $err = ['foto_dokumentasi' => 'Ukuran file foto melebihi batas 1MB. Silakan pilih foto dengan ukuran lebih kecil.'];
                        log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - File size exceeds 1MB: {$file->getSize()} bytes");
                        return $this->errorResponse('Validasi gagal: Ukuran file foto melebihi 1MB.', $err, [], ['upload_info' => $uploadInfo]);
                    }

                    $tempPath   = $file->getTempName();
                    $tempDir    = !empty($tempPath) ? dirname($tempPath) : sys_get_temp_dir();
                    $clientName = $file->getClientName();
                    $fileSize   = $file->getSize();

                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    $fotoName = $file->getRandomName();
                    log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - Moving photo: '{$clientName}' from Temp [{$tempDir}/{$tempPath}] to Target [{$uploadPath}/{$fotoName}]");
                    $this->log('info', "[JURNAL PIKET FOTO UPDATE] Temp Dir: {$tempDir} | Temp File: {$tempPath} | Original: {$clientName} ({$fileSize} bytes) -> Destination: {$uploadPath}/{$fotoName}");
                    $file->move($uploadPath, $fotoName);

                    $filePath = $uploadPath . '/' . $fotoName;
                    if (file_exists($filePath)) {
                        helper('image');
                        if (function_exists('optimize_jurnal_photo')) {
                            optimize_jurnal_photo($filePath, $filePath);
                        }
                    }
                    $uploadedPhotos[] = $fotoName;
                    $uploadInfo['uploaded_photos'][] = $fotoName;
                } elseif ($file && !$file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
                    $tempPath = method_exists($file, 'getTempName') ? $file->getTempName() : 'N/A';
                    $tempDir  = !empty($tempPath) && $tempPath !== 'N/A' ? dirname($tempPath) : sys_get_temp_dir();
                    $err = ['foto_dokumentasi' => 'Gagal mengunggah file foto (' . $file->getClientName() . '): ' . $file->getErrorString() . ' (Error code: ' . $file->getError() . ')'];
                    log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - File upload invalid: Name={$file->getClientName()}, Error={$file->getError()} ({$file->getErrorString()}), TempDir={$tempDir}");
                    return $this->errorResponse('Validasi gagal: Gagal mengunggah file foto dokumentasi.', $err, [], ['upload_info' => $uploadInfo]);
                }
            }
        }

        $fotoNamesString = !empty($uploadedPhotos) ? implode(',', $uploadedPhotos) : null;
        log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - Final updated foto_dokumentasi string: " . ($fotoNamesString ?: 'null'));

        $txResult = $this->executeInTransaction(function () use ($id, $data, $fotoNamesString, $uploadInfo) {
            $updateData = [
                'tanggal'          => $data['tanggal'],
                'rincian_tugas'    => $data['rincian_tugas'] ?? null,
                'deskripsi'        => $data['deskripsi'],
                'catatan'          => $data['catatan'] ?? null,
                'foto_dokumentasi' => $fotoNamesString,
                'updated_at'       => date('Y-m-d H:i:s'),
            ];

            $this->jurnalPiketModel->update($id, $updateData);

            log_message('error', "[JURNAL_PIKET_DEBUG] JurnalPiketService::update() - Jurnal piket ID={$id} updated successfully in database");
            $this->log('info', "Jurnal piket updated: ID={$id}");

            return [
                'id'               => $id,
                'foto_dokumentasi' => $fotoNamesString,
                'upload_info'      => $uploadInfo,
            ];
        });

        if (!$txResult['success']) {
            $txResult['upload_info'] = $uploadInfo;
        }

        return $txResult;
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
            // Delete photo files if they exist
            if (!empty($jurnal['foto_dokumentasi'])) {
                $fotos = explode(',', $jurnal['foto_dokumentasi']);
                foreach ($fotos as $f) {
                    $f = trim($f);
                    if (!empty($f)) {
                        $filePath = WRITEPATH . 'uploads/jurnal_piket/' . $f;
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                    }
                }
            }

            $this->jurnalPiketModel->delete($id);

            $this->log('info', "Jurnal piket deleted: ID={$id}");

        });
    }
}
