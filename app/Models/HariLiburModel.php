<?php

namespace App\Models;

use CodeIgniter\Model;

class HariLiburModel extends Model
{
    protected $table            = 'hari_libur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['tanggal', 'keterangan', 'created_by'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'tanggal'     => 'required|valid_date[Y-m-d]',
        'keterangan'  => 'required|max_length[200]',
    ];
    protected $validationMessages = [
        'tanggal'    => ['required' => 'Tanggal wajib diisi.', 'valid_date' => 'Format tanggal tidak valid.'],
        'keterangan' => ['required' => 'Keterangan wajib diisi.'],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil semua hari libur, diurutkan by tanggal ASC.
     */
    public function getAllSorted(): array
    {
        return $this->orderBy('tanggal', 'ASC')->findAll();
    }

    /**
     * Ambil hari libur dalam rentang tahun ajaran / periode PKL.
     */
    public function getByRange(string $from, string $to): array
    {
        return $this->where('tanggal >=', $from)
                    ->where('tanggal <=', $to)
                    ->orderBy('tanggal', 'ASC')
                    ->findAll();
    }

    /**
     * Cek apakah sebuah tanggal adalah hari libur.
     */
    public function isHariLibur(string $tanggal): bool
    {
        return $this->where('tanggal', $tanggal)->countAllResults() > 0;
    }

    /**
     * Ambil semua tanggal libur sebagai array string 'Y-m-d'.
     * Berguna untuk highlight di kalender JS.
     */
    public function getTanggalList(): array
    {
        $rows = $this->select('tanggal, keterangan')->orderBy('tanggal', 'ASC')->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['tanggal']] = $row['keterangan'];
        }
        return $result;
    }
}
