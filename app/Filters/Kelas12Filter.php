<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\SiswaModel;

class Kelas12Filter implements FilterInterface
{
    /**
     * Sebelum request diproses - periksa apakah siswa kelas 12
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Hanya cek jika user adalah siswa
        if (session()->get('role') === 'siswa') {
            $siswaModel = new SiswaModel();
            $userId = session()->get('user_id');
            $siswa = $siswaModel->getByUserId($userId);
            
            // Jika data siswa tidak ada atau tingkatnya bukan '12', batasi akses
            if (!$siswa || ($siswa['tingkat'] ?? '') !== '12') {
                return redirect()->to('/siswa/dashboard')->with('error', 'Akses Jurnal PKL hanya untuk siswa kelas 12 🔐');
            }
        }
        
        return $request;
    }

    /**
     * Setelah request diproses
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
