<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\JadwalMengajarModel;
use App\Models\GuruModel;

class JadwalController extends BaseController
{
    protected $jadwalModel;
    protected $guruModel;

    public function __construct()
    {
        $this->jadwalModel = new JadwalMengajarModel();
        $this->guruModel = new GuruModel();
    }

    public function index()
    {
        // Get guru data from session
        // Support both 'user_id' and 'userId' for backward compatibility
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guru = $this->guruModel->getByUserId($userId);

        if (!$guru) {
            return redirect()->to('/guru/dashboard')->with('error', 'Data guru nggak ketemu 🤔');
        }

        // Get jadwal mengajar untuk guru yang sedang login (filtered by tahun_ajaran)
        $jadwal = $this->jadwalModel->getByGuru($guru['id'], null, get_active_tahun_ajaran());

        // Group jadwal by hari
        $jadwalByHari = [
            'Senin' => [],
            'Selasa' => [],
            'Rabu' => [],
            'Kamis' => [],
            'Jumat' => [],
        ];

        foreach ($jadwal as $item) {
            $jadwalByHari[$item['hari']][] = $item;
        }

        // Get jadwal hari ini (filtered by tahun_ajaran)
        $jadwalHariIni = $this->jadwalModel->getJadwalHariIni($guru['id'], get_active_tahun_ajaran());

        $data = [
            'title' => 'Jadwal Mengajar',
            'guru' => $guru,
            'jadwal' => $jadwal,
            'jadwalByHari' => $jadwalByHari,
            'jadwalHariIni' => $jadwalHariIni
        ];

        return view('guru/jadwal/index', $data);
    }
}
