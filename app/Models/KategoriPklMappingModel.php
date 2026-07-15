<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriPklMappingModel extends Model
{
    protected $table            = 'kategori_pkl_mapping';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['tempat_pkl_id', 'kategori_id', 'created_at'];

    protected $useTimestamps = false;
    protected $deletedField  = 'deleted_at';

    public function getMappedKategoriIds(int $tempatPklId): array
    {
        $rows = $this->where('tempat_pkl_id', $tempatPklId)->findAll();
        return array_column($rows, 'kategori_id');
    }

    public function getByTempatPkl(int $tempatPklId): array
    {
        return $this->select('kategori_pkl_mapping.*, pkl_categories.nama AS kategori_nama')
            ->join('pkl_categories', 'pkl_categories.id = kategori_pkl_mapping.kategori_id', 'left')
            ->where('kategori_pkl_mapping.tempat_pkl_id', $tempatPklId)
            ->orderBy('pkl_categories.nama', 'ASC')
            ->findAll();
    }

    public function toggleMapping(int $tempatPklId, int $kategoriId): string
    {
        $row = $this->db->table($this->table)
            ->where('tempat_pkl_id', $tempatPklId)
            ->where('kategori_id', $kategoriId)
            ->get()
            ->getRowArray();

        if ($row) {
            if ($row['deleted_at'] === null) {
                $this->delete($row['id']);
                return 'removed';
            }

            $this->db->table($this->table)
                ->where('id', $row['id'])
                ->update([
                    'deleted_at' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            return 'added';
        }

        $this->insert([
            'tempat_pkl_id' => $tempatPklId,
            'kategori_id'   => $kategoriId,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        return 'added';
    }

    public function getMappingSummary(): array
    {
        return $this->select('tempat_pkl.nama_perusahaan, tempat_pkl.kota, tempat_pkl.id AS tempat_pkl_id, COUNT(kategori_pkl_mapping.id) AS jumlah_kategori')
            ->join('tempat_pkl', 'tempat_pkl.id = kategori_pkl_mapping.tempat_pkl_id', 'left')
            ->groupBy('tempat_pkl.id')
            ->orderBy('tempat_pkl.nama_perusahaan', 'ASC')
            ->findAll();
    }
}
