<?php

namespace App\Services;

use App\Models\GuruModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\TempatPklModel;
use App\Models\PembimbingPklModel;
use App\Models\SiswaPklModel;

class PembimbingPklService extends BaseService
{
    protected $guruModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $tempatPklModel;
    protected $pembimbingPklModel;
    protected $siswaPklModel;

    public function __construct()
    {
        parent::__construct();

        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
        $this->kelasModel = new KelasModel();
        $this->tempatPklModel = new TempatPklModel();
        $this->pembimbingPklModel = new PembimbingPklModel();
        $this->siswaPklModel = new SiswaPklModel();
    }

    public function getAllPembimbingPkl($tahunAjaran = null): array
    {
        try {
            $data = $this->pembimbingPklModel->getAllPembimbingPkl($tahunAjaran);
            return $this->successResponse($data);
        } catch (\Exception $e) {
            $this->log('error', 'Failed to get all pembimbing PKL: ' . $e->getMessage());
            return $this->errorResponse('Gagal mengambil data pembimbing PKL');
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
            $insertData = [
                'guru_id'       => $data['guru_id'],
                'tempat_pkl_id' => $data['tempat_pkl_id'],
                'tahun_ajaran'  => get_active_tahun_ajaran(),
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
            $data = $this->tempatPklModel->orderBy('nama_perusahaan', 'ASC')->findAll();
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

        return $this->executeInTransaction(function () use ($data) {
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

            return $this->successResponse(['id' => $id], 'Tempat PKL berhasil ditambahkan');
        });
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
            $kelasXII = $this->kelasModel->getByTingkat('12');

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
            $kelasXII = $this->kelasModel->getByTingkat('12');

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

            $placementMap = [];
            foreach ($existingPlacements as $p) {
                $placementMap[$p['siswa_id']] = $p['tempat_pkl_id'];
            }

            $tempatPklMap = [];
            $allTempatPkl = $this->tempatPklModel->findAll();
            foreach ($allTempatPkl as $t) {
                $tempatPklMap[$t['id']] = $t['nama_perusahaan'];
            }

            foreach ($siswa as &$s) {
                $s['status'] = isset($placementMap[$s['id']]) ? 'sudah' : 'belum';
                $s['tempat_pkl_id'] = $placementMap[$s['id']] ?? null;
                $s['nama_perusahaan'] = isset($placementMap[$s['id']]) ? ($tempatPklMap[$placementMap[$s['id']]] ?? '-') : '-';
            }

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
                'siswa_id'      => $data['siswa_id'],
                'tempat_pkl_id' => $data['tempat_pkl_id'],
                'tahun_ajaran'  => $tahunAjaran,
                'created_at'    => date('Y-m-d H:i:s'),
            ];

            $id = $this->siswaPklModel->insert($insertData);

            if (!$id) {
                throw new \Exception('Gagal menyimpan data penempatan siswa PKL');
            }

            return $this->successResponse(['id' => $id], 'Siswa berhasil ditempatkan');
        });
    }

    public function createSiswaPklBatch(array $siswaIds, int $tempatPklId): array
    {
        if (empty($siswaIds)) {
            return $this->errorResponse('Tidak ada siswa yang dipilih');
        }

        $tahunAjaran = get_active_tahun_ajaran();

        return $this->executeInTransaction(function () use ($siswaIds, $tempatPklId, $tahunAjaran) {
            $success = 0;
            $skipped = 0;

            foreach ($siswaIds as $siswaId) {
                $existing = $this->siswaPklModel->getBySiswaAndTahun($siswaId, $tahunAjaran);
                if ($existing) {
                    $skipped++;
                    continue;
                }

                $insertData = [
                    'siswa_id'      => $siswaId,
                    'tempat_pkl_id' => $tempatPklId,
                    'tahun_ajaran'  => $tahunAjaran,
                    'created_at'    => date('Y-m-d H:i:s'),
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
                ->join('siswa', 'siswa.id = siswa_pkl.siswa_id')
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
            $kelasXII = $this->kelasModel->getByTingkat('12');

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
            $kelasXII = $this->kelasModel->getByTingkat('12');

            $totalSiswaXII = 0;
            if (!empty($kelasXII)) {
                $kelasIds = array_column($kelasXII, 'id');
                $totalSiswaXII = $this->siswaModel
                    ->join('users', 'users.id = siswa.user_id')
                    ->whereIn('siswa.kelas_id', $kelasIds)
                    ->where('users.is_active', 1)
                    ->countAllResults();
            }

            $sudahDitempatkan = $this->siswaPklModel->countAll();

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
