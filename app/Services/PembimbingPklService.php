<?php

namespace App\Services;

use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\TempatPklModel;
use App\Models\PembimbingPklModel;
use App\Models\SiswaPklModel;
use App\Models\InstrukturPklModel;
use App\Models\UserModel;

class PembimbingPklService extends BaseService
{
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $tempatPklModel;
    protected $pembimbingPklModel;
    protected $siswaPklModel;
    protected $instrukturPklModel;
    protected $userModel;

    public function __construct()
    {
        parent::__construct();

        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->tempatPklModel = new TempatPklModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
        $this->siswaPklModel = new SiswaPklModel();
        $this->instrukturPklModel = new InstrukturPklModel();
        $this->userModel = new UserModel();
    }

    public function getAllPembimbingPkl(array $filters = []): array
    {
        try {
            $data = $this->pembimbingPklModel->getAllPembimbingPkl($filters);
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get all pembimbing PKL: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data pembimbing PKL');
        }
    }

    public function getFilterLists(): array
    {
        try {
            $data = [
                'guruList'       => $this->pembimbingPklModel->getFilterGuruList(),
                'tempatPklList'  => $this->pembimbingPklModel->getFilterTempatPklList(),
                'kotaList'       => $this->pembimbingPklModel->getFilterKotaList(),
                'tahunAjaranList' => $this->pembimbingPklModel->getTahunAjaranList(),
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get filter lists: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data filter');
        }
    }

    public function getPembimbingPklById(int $id): array
    {
        try {
            $data = $this->pembimbingPklModel->find($id);

            if (!$data) {
                return $this->errorResponse('Data pembimbing PKL tidak ditemukan');
            }

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get pembimbing PKL by ID: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data pembimbing PKL');
        }
    }

    public function createPembimbingPkl(array $data): array
    {
        $rules = [
            'guru_id'       => 'required|numeric',
            'tempat_pkl_id' => 'required|numeric',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($data) {
            $tahunAjaran = get_active_tahun_ajaran();

            // Check if a record already exists (including soft-deleted)
            $existing = $this->db->table('pembimbing_pkl')
                ->where('guru_id', $data['guru_id'])
                ->where('tempat_pkl_id', $data['tempat_pkl_id'])
                ->where('tahun_ajaran', $tahunAjaran)
                ->get()
                ->getRowArray();

            if ($existing) {
                if (!is_null($existing['deleted_at'])) {
                    // Restore soft-deleted record
                    $this->db->table('pembimbing_pkl')
                        ->where('id', $existing['id'])
                        ->update(['deleted_at' => null]);
                    return ['id' => $existing['id']];
                }
                throw new \Exception('Data pembimbing PKL untuk guru dan tempat PKL ini sudah ada pada tahun ajaran ini');
            }

            $insertData = [
                'guru_id'       => $data['guru_id'],
                'tempat_pkl_id' => $data['tempat_pkl_id'],
                'tahun_ajaran'  => $tahunAjaran,
                'created_at'    => date('Y-m-d H:i:s'),
            ];

            $id = $this->pembimbingPklModel->insert($insertData);

            if (!$id) {
                throw new \Exception('Gagal menyimpan data pembimbing PKL');
            }

            $this->log('info', "Pembimbing PKL created successfully: ID {$id}");

            return ['id' => $id];
        });
    }

    public function updatePembimbingPkl(int $id, array $data): array
    {
        $existing = $this->pembimbingPklModel->find($id);

        if (!$existing) {
            return $this->errorResponse('Data pembimbing PKL tidak ditemukan');
        }

        $rules = [
            'guru_id'       => 'required|numeric',
            'tempat_pkl_id' => 'required|numeric',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($id, $data) {
            $updateData = [
                'guru_id'       => $data['guru_id'],
                'tempat_pkl_id' => $data['tempat_pkl_id'],
            ];

            $this->pembimbingPklModel->update($id, $updateData);

            $this->log('info', "Pembimbing PKL updated successfully: ID {$id}");

            return ['id' => $id];
        });
    }

    public function deletePembimbingPkl(int $id): array
    {
        $existing = $this->pembimbingPklModel->find($id);

        if (!$existing) {
            return $this->errorResponse('Data pembimbing PKL tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id) {
            $this->pembimbingPklModel->delete($id);

            $this->log('info', "Pembimbing PKL deleted successfully: ID {$id}");

            return ['id' => $id];
        });
    }

    public function getFormLists(): array
    {
        try {
            $guruList = $this->guruModel
                ->select('guru.id, guru.nama_lengkap, guru.nip')
                ->orderBy('guru.nama_lengkap', 'ASC')
                ->findAll();

            $guruDropdown = [];
            foreach ($guruList as $g) {
                $guruDropdown[$g['id']] = $g['nama_lengkap'] . ' (' . $g['nip'] . ')';
            }

            $data = [
                'guruList'       => $guruDropdown,
                'tempatPklList'  => $this->tempatPklModel->getListTempatPkl(),
                'tahunAjaranList' => $this->pembimbingPklModel->getTahunAjaranList(),
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get form lists: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data list');
        }
    }

    public function getAllTempatPkl(): array
    {
        try {
            $data = $this->tempatPklModel
                ->select('tempat_pkl.*, instruktur_pkl.id AS instruktur_id, instruktur_pkl.nama_lengkap AS nama_instruktur, instruktur_pkl.email AS email_instruktur, instruktur_pkl.telepon AS telepon_instruktur, users.username AS username_instruktur')
                ->join('instruktur_pkl', 'instruktur_pkl.tempat_pkl_id = tempat_pkl.id AND instruktur_pkl.deleted_at IS NULL', 'left')
                ->join('users', 'users.id = instruktur_pkl.user_id AND users.deleted_at IS NULL', 'left')
                ->orderBy('tempat_pkl.nama_perusahaan', 'ASC')
                ->findAll();
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get all tempat PKL: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data tempat PKL');
        }
    }

    public function createTempatPkl(array $data): array
    {
        $rules = [
            'nama_perusahaan' => 'required|min_length[3]',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        $hasInstruktur = !empty($data['instruktur_nama']);

        if ($hasInstruktur) {
            $instrukturRules = [
                'instruktur_nama'    => 'required|min_length[3]',
                'instruktur_email'   => 'required|valid_email|is_unique[users.email]',
                'instruktur_username' => 'required|is_unique[users.username]',
                'instruktur_password' => 'required|min_length[6]',
            ];

            if (!$this->validate($data, $instrukturRules)) {
                return $this->errorResponse('Validasi data instruktur gagal');
            }
        }

        return $this->executeInTransaction(function () use ($data, $hasInstruktur) {
            $insertData = [
                'nama_perusahaan' => $data['nama_perusahaan'],
                'alamat'          => $data['alamat'] ?? null,
                'kota'            => $data['kota'] ?? null,
                'kontak'          => $data['kontak'] ?? null,
                'telepon'         => $data['telepon'] ?? null,
                'created_at'      => date('Y-m-d H:i:s'),
            ];

            $id = $this->tempatPklModel->insert($insertData);

            if (!$id) {
                throw new \Exception('Gagal menyimpan data tempat PKL');
            }

            if ($hasInstruktur) {
                $userId = $this->userModel->insert([
                    'username'   => $data['instruktur_username'],
                    'password'   => $data['instruktur_password'],
                    'role'       => 'instruktur',
                    'email'      => $data['instruktur_email'],
                    'is_active'  => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                if (!$userId) {
                    $errors = $this->userModel->errors();
                    $errorStr = !empty($errors) ? implode(', ', $errors) : 'Kesalahan tidak diketahui';
                    throw new \Exception('Gagal membuat akun instruktur: ' . $errorStr);
                }

                $this->instrukturPklModel->insert([
                    'tempat_pkl_id' => $id,
                    'user_id'       => $userId,
                    'nama_lengkap'  => $data['instruktur_nama'],
                    'email'         => $data['instruktur_email'],
                    'telepon'       => $data['instruktur_telepon'] ?? null,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            }

            return $this->successResponse(['id' => $id], 'Tempat PKL berhasil ditambahkan');
        });
    }

    public function getTempatPklWithInstruktur(int $id): array
    {
        try {
            $tempat = $this->tempatPklModel->find($id);

            if (!$tempat) {
                return $this->errorResponse('Tempat PKL tidak ditemukan');
            }

            $instruktur = $this->instrukturPklModel->getByTempatPkl($id);

            if ($instruktur && !empty($instruktur['user_id'])) {
                $user = $this->userModel->find($instruktur['user_id']);
                $instruktur['username'] = $user['username'] ?? '';
            }

            $tempat['instruktur'] = $instruktur;

            return $this->successResponse($tempat);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get tempat PKL with instruktur: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data tempat PKL');
        }
    }

    public function updateTempatPkl(int $id, array $data): array
    {
        $existing = $this->tempatPklModel->find($id);

        if (!$existing) {
            return $this->errorResponse('Tempat PKL tidak ditemukan');
        }

        $rules = [
            'nama_perusahaan' => 'required|min_length[3]',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        return $this->executeInTransaction(function () use ($id, $data) {
            $updateData = [
                'nama_perusahaan' => $data['nama_perusahaan'],
                'alamat'          => $data['alamat'] ?? null,
                'kota'            => $data['kota'] ?? null,
                'kontak'          => $data['kontak'] ?? null,
                'telepon'         => $data['telepon'] ?? null,
            ];

            $this->tempatPklModel->update($id, $updateData);

            $existingInstruktur = $this->instrukturPklModel->getByTempatPkl($id);
            $hasInstrukturData = !empty($data['instruktur_nama']);

            if ($hasInstrukturData) {
                $instrukturRules = [
                    'instruktur_nama'     => 'required|min_length[3]',
                    'instruktur_email'    => 'required|valid_email',
                    'instruktur_username' => 'required',
                    'instruktur_password' => 'permit_empty|min_length[6]',
                ];

                if (!$this->validate($data, $instrukturRules)) {
                    throw new \Exception('Validasi data instruktur gagal');
                }

                if ($existingInstruktur) {
                    $this->userModel->update($existingInstruktur['user_id'], [
                        'username' => $data['instruktur_username'],
                        'email'    => $data['instruktur_email'],
                    ]);

                    if (!empty($data['instruktur_password'])) {
                        $this->userModel->update($existingInstruktur['user_id'], [
                            'password' => $data['instruktur_password'],
                        ]);
                    }

                    $this->instrukturPklModel->update($existingInstruktur['id'], [
                        'nama_lengkap' => $data['instruktur_nama'],
                        'email'        => $data['instruktur_email'],
                        'telepon'      => $data['instruktur_telepon'] ?? null,
                    ]);
                } else {
                    $password = !empty($data['instruktur_password']) ? $data['instruktur_password'] : 'instruktur123';

                    $userId = $this->userModel->insert([
                        'username'   => $data['instruktur_username'],
                        'password'   => $password,
                        'role'       => 'instruktur',
                        'email'      => $data['instruktur_email'],
                        'is_active'  => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    if (!$userId) {
                        $errors = $this->userModel->errors();
                        $errorStr = !empty($errors) ? implode(', ', $errors) : 'Kesalahan tidak diketahui';
                        throw new \Exception('Gagal membuat akun instruktur: ' . $errorStr);
                    }

                    $this->instrukturPklModel->insert([
                        'tempat_pkl_id' => $id,
                        'user_id'       => $userId,
                        'nama_lengkap'  => $data['instruktur_nama'],
                        'email'         => $data['instruktur_email'],
                        'telepon'       => $data['instruktur_telepon'] ?? null,
                        'created_at'    => date('Y-m-d H:i:s'),
                    ]);
                }
            } elseif ($existingInstruktur && empty($data['instruktur_nama'])) {
                $this->instrukturPklModel->delete($existingInstruktur['id']);
                $this->userModel->delete($existingInstruktur['user_id']);
            }

            return $this->successResponse(['id' => $id], 'Tempat PKL berhasil diperbarui');
        });
    }

    public function deleteTempatPkl(int $id): array
    {
        $existing = $this->tempatPklModel->find($id);

        if (!$existing) {
            return $this->errorResponse('Tempat PKL tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id) {
            $this->tempatPklModel->delete($id);
            return $this->successResponse(['id' => $id], 'Tempat PKL berhasil dihapus');
        });
    }

    public function getAllSiswaPkl($tahunAjaran = null): array
    {
        try {
            $data = $this->siswaPklModel->getAllSiswaPkl($tahunAjaran);
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get all siswa PKL: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data siswa PKL');
        }
    }

    public function getSiswaXII(): array
    {
        try {
            $kelasXII = $this->kelasModel->getByTingkat('12', get_active_tahun_ajaran());

            if (empty($kelasXII)) {
                return $this->successResponse([]);
            }

            $kelasIds = array_column($kelasXII, 'id');

            $siswa = $this->siswaModel
                ->select('siswa.id, siswa.nis, siswa.nama_lengkap, siswa.kelas_id, kelas.nama_kelas, users.is_active')
                ->join('kelas', 'kelas.id = siswa.kelas_id')
                ->join('users', 'users.id = siswa.user_id')
                ->whereIn('siswa.kelas_id', $kelasIds)
                ->where('users.is_active', 1)
                ->orderBy('kelas.nama_kelas', 'ASC')
                ->orderBy('siswa.nama_lengkap', 'ASC')
                ->findAll();

            return $this->successResponse($siswa);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get siswa XII: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data siswa kelas XII');
        }
    }

    public function getSiswaXIIWithPlacement(string $tahunAjaran): array
    {
        try {
            $kelasXII = $this->kelasModel->getByTingkat('12', get_active_tahun_ajaran());

            if (empty($kelasXII)) {
                return $this->successResponse([]);
            }

            $kelasIds = array_column($kelasXII, 'id');

            $siswa = $this->siswaModel
                ->select('siswa.id, siswa.nis, siswa.nama_lengkap, siswa.kelas_id, kelas.nama_kelas')
                ->join('kelas', 'kelas.id = siswa.kelas_id')
                ->join('users', 'users.id = siswa.user_id')
                ->whereIn('siswa.kelas_id', $kelasIds)
                ->where('users.is_active', 1)
                ->orderBy('kelas.nama_kelas', 'ASC')
                ->orderBy('siswa.nama_lengkap', 'ASC')
                ->findAll();

            $existingPlacements = $this->siswaPklModel
                ->where('tahun_ajaran', $tahunAjaran)
                ->findAll();

            $placedSiswaIds = array_column($existingPlacements, 'siswa_id');

            $siswa = array_filter($siswa, function ($s) use ($placedSiswaIds) {
                return !in_array($s['id'], $placedSiswaIds);
            });
            $siswa = array_values($siswa);

            return $this->successResponse($siswa);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get siswa XII with placement: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data siswa kelas XII');
        }
    }

    public function createSiswaPkl(array $data): array
    {
        $tahunAjaran = get_active_tahun_ajaran();
        $rules = [
            'siswa_id'      => 'required|numeric',
            'tempat_pkl_id' => 'required|numeric',
        ];

        if (!$this->validate($data, $rules)) {
            return $this->errorResponse('Validasi gagal');
        }

        $existing = $this->siswaPklModel->getBySiswaAndTahun($data['siswa_id'], $tahunAjaran);
        if ($existing) {
            return $this->errorResponse('Siswa sudah memiliki penempatan PKL di tahun ajaran ini');
        }

        return $this->executeInTransaction(function () use ($data, $tahunAjaran) {
            $insertData = [
                'siswa_id'          => $data['siswa_id'],
                'tempat_pkl_id'     => $data['tempat_pkl_id'],
                'pembimbing_pkl_id' => $data['pembimbing_pkl_id'] ?? null,
                'tahun_ajaran'      => $tahunAjaran,
                'created_at'        => date('Y-m-d H:i:s'),
            ];

            $id = $this->siswaPklModel->insert($insertData);

            if (!$id) {
                throw new \Exception('Gagal menyimpan data penempatan siswa PKL');
            }

            return $this->successResponse(['id' => $id], 'Siswa berhasil ditempatkan');
        });
    }

    public function createSiswaPklBatch(array $siswaIds, int $tempatPklId, ?int $pembimbingPklId = null): array
    {
        if (empty($siswaIds)) {
            return $this->errorResponse('Tidak ada siswa yang dipilih');
        }

        $tahunAjaran = get_active_tahun_ajaran();

        // Otomatis cari pembimbing_pkl_id dari tempat PKL jika tidak dikirim
        if (empty($pembimbingPklId)) {
            $pembimbing = $this->pembimbingPklModel
                ->where('tempat_pkl_id', $tempatPklId)
                ->where('tahun_ajaran', $tahunAjaran)
                ->first();
            $pembimbingPklId = $pembimbing['id'] ?? null;
        }

        return $this->executeInTransaction(function () use ($siswaIds, $tempatPklId, $pembimbingPklId, $tahunAjaran) {
            $success = 0;
            $skipped = 0;

            foreach ($siswaIds as $siswaId) {
                // Check including soft-deleted records via raw query
                $existing = $this->db->table('siswa_pkl')
                    ->where('siswa_id', $siswaId)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    if (!is_null($existing['deleted_at'])) {
                        // Restore soft-deleted record dan update pembimbing_pkl_id
                        $this->db->table('siswa_pkl')
                            ->where('id', $existing['id'])
                            ->update([
                                'tempat_pkl_id'     => $tempatPklId,
                                'pembimbing_pkl_id' => $pembimbingPklId,
                                'deleted_at'        => null,
                            ]);
                        $success++;
                    } else {
                        $skipped++;
                    }
                    continue;
                }

                $insertData = [
                    'siswa_id'          => $siswaId,
                    'tempat_pkl_id'     => $tempatPklId,
                    'pembimbing_pkl_id' => $pembimbingPklId,
                    'tahun_ajaran'      => $tahunAjaran,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];

                $id = $this->siswaPklModel->insert($insertData);
                if ($id) {
                    $success++;
                }
            }

            $message = "Berhasil menempatkan {$success} siswa";
            if ($skipped > 0) {
                $message .= ", {$skipped} siswa sudah memiliki penempatan sebelumnya";
            }

            return $this->successResponse([
                'success' => $success,
                'skipped' => $skipped,
            ], $message);
        });
    }

    public function deleteSiswaPkl(int $id): array
    {
        $existing = $this->siswaPklModel->find($id);

        if (!$existing) {
            return $this->errorResponse('Data penempatan siswa PKL tidak ditemukan');
        }

        return $this->executeInTransaction(function () use ($id) {
            $this->siswaPklModel->delete($id);
            return $this->successResponse(['id' => $id], 'Penempatan siswa PKL berhasil dihapus');
        });
    }

    public function bulkDeleteSiswaPkl(array $ids): array
    {
        if (empty($ids)) {
            return $this->errorResponse('Tidak ada data yang dipilih untuk dihapus');
        }

        return $this->executeInTransaction(function () use ($ids) {
            $deleted = 0;
            foreach ($ids as $id) {
                $existing = $this->siswaPklModel->find((int) $id);
                if ($existing) {
                    $this->siswaPklModel->delete((int) $id);
                    $deleted++;
                }
            }
            return $this->successResponse(
                ['deleted' => $deleted],
                $deleted . ' penempatan siswa PKL berhasil dihapus'
            );
        });
    }

    public function getPembimbingByTempatPkl(int $tempatPklId, string $tahunAjaran): array
    {
        try {
            $pembimbing = $this->pembimbingPklModel
                ->select('pembimbing_pkl.*, guru.nama_lengkap AS nama_guru, guru.nip')
                ->join('guru', 'guru.id = pembimbing_pkl.guru_id')
                ->where('pembimbing_pkl.tempat_pkl_id', $tempatPklId)
                ->where('pembimbing_pkl.tahun_ajaran', $tahunAjaran)
                ->findAll();

            return $this->successResponse($pembimbing);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get pembimbing by tempat PKL: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data pembimbing');
        }
    }

    public function getSiswaPklByTempatPkl(int $tempatPklId, string $tahunAjaran): array
    {
        try {
            $siswa = $this->siswaPklModel
                ->select('siswa_pkl.*, siswa.nama_lengkap AS nama_siswa, siswa.nis, kelas.nama_kelas')
                ->join('siswa', 'siswa.id = siswa_pkl.siswa_id AND siswa.deleted_at IS NULL')
                ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
                ->where('siswa_pkl.tempat_pkl_id', $tempatPklId)
                ->where('siswa_pkl.tahun_ajaran', $tahunAjaran)
                ->orderBy('kelas.nama_kelas', 'ASC')
                ->orderBy('siswa.nama_lengkap', 'ASC')
                ->findAll();

            return $this->successResponse($siswa);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get siswa PKL by tempat PKL: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data siswa');
        }
    }

    public function getFormListsSiswa(): array
    {
        try {
            $kelasXII = $this->kelasModel->getByTingkat('12', get_active_tahun_ajaran());

            $siswaDropdown = [];
            if (!empty($kelasXII)) {
                $kelasIds = array_column($kelasXII, 'id');
                $siswaList = $this->siswaModel
                    ->select('siswa.id, siswa.nama_lengkap, siswa.nis, kelas.nama_kelas')
                    ->join('kelas', 'kelas.id = siswa.kelas_id')
                    ->join('users', 'users.id = siswa.user_id')
                    ->whereIn('siswa.kelas_id', $kelasIds)
                    ->where('users.is_active', 1)
                    ->orderBy('kelas.nama_kelas', 'ASC')
                    ->orderBy('siswa.nama_lengkap', 'ASC')
                    ->findAll();

                foreach ($siswaList as $s) {
                    $siswaDropdown[$s['id']] = $s['nama_lengkap'] . ' (' . $s['nis'] . ') - ' . $s['nama_kelas'];
                }
            }

            $data = [
                'siswaList'      => $siswaDropdown,
                'tempatPklList'  => $this->tempatPklModel->getListTempatPkl(),
                'tahunAjaranList' => $this->pembimbingPklModel->getTahunAjaranList(),
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get form lists siswa: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data list');
        }
    }

    public function getSiswaPklStats(): array
    {
        try {
            $kelasXII = $this->kelasModel->getByTingkat('12', get_active_tahun_ajaran());

            $totalSiswaXII = 0;
            if (!empty($kelasXII)) {
                $kelasIds = array_column($kelasXII, 'id');
                $totalSiswaXII = $this->siswaModel
                    ->join('users', 'users.id = siswa.user_id')
                    ->whereIn('siswa.kelas_id', $kelasIds)
                    ->where('users.is_active', 1)
                    ->countAllResults();
            }

            $sudahDitempatkan = $this->siswaPklModel
                ->where('tahun_ajaran', get_active_tahun_ajaran())
                ->countAllResults();

            $data = [
                'totalSiswaXII'     => $totalSiswaXII,
                'sudahDitempatkan'  => $sudahDitempatkan,
                'belumDitempatkan'  => $totalSiswaXII - $sudahDitempatkan,
            ];

            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get siswa PKL stats: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil statistik');
        }
    }
}
