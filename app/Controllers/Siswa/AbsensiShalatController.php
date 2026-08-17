<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\SiswaModel;

class AbsensiShalatController extends BaseController
{
    protected $siswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Student scan page (redirects to scan route)
     */
    public function scan()
    {
        $userId = session()->get('user_id') ?? session()->get('userId');
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            $this->session->setFlashdata('error', 'Data siswa nggak ketemu');
            return redirect()->to('/siswa/dashboard');
        }

        $data = [
            'title' => 'Scan Absensi Shalat',
            'siswa' => $siswa,
        ];

        return view('siswa/absensi_shalat/scan', $data);
    }
}
