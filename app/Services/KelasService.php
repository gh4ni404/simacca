<?php

namespace App\Services;

use App\Models\KelasModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class KelasService extends BaseService
{
    protected KelasModel $kelasModel;
    protected GuruModel $guruModel;
    protected SiswaModel $siswaModel;

    public function __construct()
    {
        parent::__construct();
        $this->kelasModel = new KelasModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
    }

    public function getAllKelas(int $perPage = 20, ?string $search = null): array
    {
        try {
            $tahunAjaran = get_active_tahun_ajaran();

            $builder = $this->kelasModel
                ->select('kelas.*, 
                         guru.nama_lengkap as nama_wali_kelas,
                         guru.nip as nip_wali_kelas,
                         COUNT(siswa.id) as jumlah_siswa')
                ->where('kelas.tahun_ajaran', $tahunAjaran)
                ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left')
                ->join('siswa', 'siswa.kelas_id = kelas.id AND siswa.tahun_ajaran = kelas.tahun_ajaran AND siswa.deleted_at IS NULL', 'left')
                ->groupBy('kelas.id');

            if ($search) {
                $builder->groupStart()
                    ->like('kelas.nama_kelas', $search)
                    ->orLike('kelas.jurusan', $search)
                    ->orLike('kelas.tingkat', $search)
                    ->orLike('guru.nama_lengkap', $search)
                    ->groupEnd();
            }

            $builder->orderBy('kelas.tingkat', 'ASC')
                ->orderBy('kelas.nama_kelas', 'ASC');

            return $this->success([
                'kelas' => $builder->paginate($perPage),
                'pager' => $this->kelasModel->pager
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getAllKelas: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }

    public function getKelasById(int $id): array
    {
        try {
            $kelas = $this->kelasModel->getKelasWithJumlahSiswa($id);

            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            $siswa = $this->siswaModel->where('kelas_id', $id)
                ->where('tahun_ajaran', $kelas['tahun_ajaran'])
                ->orderBy('nama_lengkap', 'ASC')
                ->findAll();

            $kelas['siswa'] = $siswa;

            return $this->success($kelas);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasById: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }

    public function createKelas(array $data): array
    {
        try {
            $this->db->transStart();

            $tahunAjaran = $data['tahun_ajaran'] ?? get_active_tahun_ajaran();

            // Validate wali kelas if provided
            if (!empty($data['wali_kelas_id'])) {
                $waliKelas = $this->guruModel->find($data['wali_kelas_id']);
                if (!$waliKelas) {
                    return $this->error('Guru tidak ditemukan');
                }

                $existingKelas = $this->kelasModel
                    ->where('wali_kelas_id', $data['wali_kelas_id'])
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->first();
                if ($existingKelas) {
                    return $this->error('Guru ini sudah menjadi wali kelas di kelas ' . $existingKelas['nama_kelas']);
                }
            }

            // Check if nama_kelas + tahun_ajaran already exists
            if (!$this->kelasModel->isUnique($data['nama_kelas'], $tahunAjaran)) {
                return $this->error('Nama kelas sudah digunakan di tahun ajaran ini');
            }

            $data['tahun_ajaran'] = $tahunAjaran;
            $kelasId = $this->kelasModel->insert($data);

            if (!$kelasId) {
                $this->db->transRollback();
                return $this->error('Gagal membuat kelas: ' . implode(', ', $this->kelasModel->errors()));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal membuat kelas');
            }

            return $this->success([
                'id' => $kelasId,
                'message' => 'Kelas berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in KelasService::createKelas: ' . $e->getMessage());
            return $this->error('Gagal membuat kelas: ' . $e->getMessage());
        }
    }

    public function updateKelas(int $id, array $data): array
    {
        try {
            $this->db->transStart();

            $kelas = $this->kelasModel->find($id);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            // Validate wali kelas if provided
            if (!empty($data['wali_kelas_id'])) {
                $waliKelas = $this->guruModel->find($data['wali_kelas_id']);
                if (!$waliKelas) {
                    return $this->error('Guru tidak ditemukan');
                }

                $existingKelas = $this->kelasModel
                    ->where('wali_kelas_id', $data['wali_kelas_id'])
                    ->where('id !=', $id)
                    ->where('tahun_ajaran', $kelas['tahun_ajaran'])
                    ->first();
                if ($existingKelas) {
                    return $this->error('Guru ini sudah menjadi wali kelas di kelas ' . $existingKelas['nama_kelas']);
                }
            }

            // Check if nama_kelas + tahun_ajaran already exists (excluding current class)
            if (isset($data['nama_kelas'])) {
                if (!$this->kelasModel->isUnique($data['nama_kelas'], $kelas['tahun_ajaran'], $id)) {
                    return $this->error('Nama kelas sudah digunakan di tahun ajaran ini');
                }
            }

            $success = $this->kelasModel->update($id, $data);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal mengupdate kelas: ' . implode(', ', $this->kelasModel->errors()));
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal mengupdate kelas');
            }

            return $this->success([
                'id' => $id,
                'message' => 'Kelas berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in KelasService::updateKelas: ' . $e->getMessage());
            return $this->error('Gagal mengupdate kelas: ' . $e->getMessage());
        }
    }

    public function deleteKelas(int $id): array
    {
        try {
            $this->db->transStart();

            $kelas = $this->kelasModel->find($id);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            $siswaCount = $this->siswaModel->where('kelas_id', $id)
                ->where('tahun_ajaran', $kelas['tahun_ajaran'])
                ->countAllResults();
            if ($siswaCount > 0) {
                return $this->error('Tidak dapat menghapus kelas yang masih memiliki siswa. Silakan pindahkan atau hapus siswa terlebih dahulu.');
            }

            $success = $this->kelasModel->delete($id);

            if (!$success) {
                $this->db->transRollback();
                return $this->error('Gagal menghapus kelas');
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->error('Gagal menghapus kelas');
            }

            return $this->success([
                'message' => 'Kelas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error in KelasService::deleteKelas: ' . $e->getMessage());
            return $this->error('Gagal menghapus kelas: ' . $e->getMessage());
        }
    }

    public function getKelasStatistics(): array
    {
        try {
            $tahunAjaran = get_active_tahun_ajaran();

            $statistics = [
                'total_kelas' => $this->kelasModel->where('tahun_ajaran', $tahunAjaran)->countAllResults(),
                'kelas_per_tingkat' => [],
                'kelas_tanpa_wali' => $this->kelasModel->getKelasWithoutWali($tahunAjaran),
                'rata_rata_siswa_per_kelas' => 0
            ];

            $tingkatStats = $this->kelasModel
                ->select('tingkat, COUNT(*) as total')
                ->where('tahun_ajaran', $tahunAjaran)
                ->groupBy('tingkat')
                ->orderBy('tingkat', 'ASC')
                ->findAll();

            foreach ($tingkatStats as $stat) {
                $statistics['kelas_per_tingkat'][$stat['tingkat']] = $stat['total'];
            }

            $avgResult = $this->db->table('kelas')
                ->select('AVG(IFNULL(siswa_count, 0)) as avg_siswa')
                ->where('kelas.tahun_ajaran', $tahunAjaran)
                ->join('(SELECT kelas_id, COUNT(*) as siswa_count FROM siswa WHERE tahun_ajaran = ' . $this->db->escape($tahunAjaran) . ' AND deleted_at IS NULL GROUP BY kelas_id) as siswa_stats', 
                       'siswa_stats.kelas_id = kelas.id', 'left')
                ->get()
                ->getRowArray();

            $statistics['rata_rata_siswa_per_kelas'] = round($avgResult['avg_siswa'] ?? 0, 2);

            return $this->success($statistics);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasStatistics: ' . $e->getMessage());
            return $this->error('Gagal mengambil statistik kelas: ' . $e->getMessage());
        }
    }

    public function getKelasForDropdown(?int $tingkat = null): array
    {
        try {
            $tahunAjaran = get_active_tahun_ajaran();
            $builder = $this->kelasModel
                ->where('tahun_ajaran', $tahunAjaran)
                ->orderBy('tingkat, nama_kelas');

            if ($tingkat) {
                $builder->where('tingkat', $tingkat);
            }

            $kelas = $builder->findAll();
            $dropdown = [];

            foreach ($kelas as $k) {
                $dropdown[$k['id']] = $k['nama_kelas'] . ' - ' . $k['jurusan'];
            }

            return $this->success($dropdown);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasForDropdown: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }

    public function assignWaliKelas(int $kelasId, int $guruId): array
    {
        try {
            $kelas = $this->kelasModel->find($kelasId);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            $guru = $this->guruModel->find($guruId);
            if (!$guru) {
                return $this->error('Guru tidak ditemukan', 404);
            }

            $existingKelas = $this->kelasModel
                ->where('wali_kelas_id', $guruId)
                ->where('tahun_ajaran', $kelas['tahun_ajaran'])
                ->first();
            if ($existingKelas && $existingKelas['id'] != $kelasId) {
                return $this->error('Guru ini sudah menjadi wali kelas di kelas ' . $existingKelas['nama_kelas']);
            }

            return $this->updateKelas($kelasId, ['wali_kelas_id' => $guruId]);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::assignWaliKelas: ' . $e->getMessage());
            return $this->error('Gagal menugaskan wali kelas: ' . $e->getMessage());
        }
    }

    public function removeWaliKelas(int $kelasId): array
    {
        try {
            $kelas = $this->kelasModel->find($kelasId);
            if (!$kelas) {
                return $this->error('Kelas tidak ditemukan', 404);
            }

            return $this->updateKelas($kelasId, ['wali_kelas_id' => null]);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::removeWaliKelas: ' . $e->getMessage());
            return $this->error('Gagal menghapus wali kelas: ' . $e->getMessage());
        }
    }

    public function getKelasByWaliKelas(int $guruId): array
    {
        try {
            $tahunAjaran = get_active_tahun_ajaran();
            $kelas = $this->kelasModel->getByWaliKelas($guruId, $tahunAjaran);

            if (!$kelas) {
                return $this->error('Guru ini tidak menjadi wali kelas', 404);
            }

            $siswaCount = $this->siswaModel->where('kelas_id', $kelas['id'])
                ->where('tahun_ajaran', $kelas['tahun_ajaran'])
                ->countAllResults();
            $kelas['jumlah_siswa'] = $siswaCount;

            return $this->success($kelas);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasByWaliKelas: ' . $e->getMessage());
            return $this->error('Gagal mengambil data kelas: ' . $e->getMessage());
        }
    }

    public function getKelasWithoutWali(): array
    {
        try {
            $tahunAjaran = get_active_tahun_ajaran();
            return $this->kelasModel->getKelasWithoutWali($tahunAjaran);
        } catch (\Exception $e) {
            log_message('error', 'Error in KelasService::getKelasWithoutWali: ' . $e->getMessage());
            return [];
        }
    }
}
