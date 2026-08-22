<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\PrayerSessionModel;
use App\Models\AbsensiShalatModel;
use App\Models\SiswaModel;

class AttendanceScanController extends BaseController
{
    protected $prayerSessionModel;
    protected $absensiShalatModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->prayerSessionModel = new PrayerSessionModel();
        $this->absensiShalatModel = new AbsensiShalatModel();
        $this->siswaModel = new SiswaModel();
    }

    /**
     * POST /api/attendance/scan
     * 
     * Student or Guru scans QR code and submits the token.
     * Identity is derived from the active session.
     * 
     * Payload: { "token": "abc123xyz" }
     */
    public function scan()
    {
        // Must be logged in (session-based auth)
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
            ]);
        }

        // Get token from request
        $token = $this->request->getPost('token') ?? null;

        // Also support JSON body
        if (!$token) {
            $json = $this->request->getJSON(true);
            $token = $json['token'] ?? null;
        }

        if (empty($token)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Token tidak ditemukan',
            ]);
        }

        $userId = session()->get('user_id') ?? session()->get('userId');
        $guruModel = new \App\Models\GuruModel();

        // Check if user is a Guru or a Siswa
        $guru  = $guruModel->getByUserId($userId);
        $siswa = null;

        if (!$guru) {
            $siswa = $this->siswaModel->getByUserId($userId);
        }

        if (!$guru && !$siswa) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Data profil siswa/guru tidak ditemukan',
            ]);
        }

        // Validate token (checks existence, active status, and expiry)
        $session = $this->prayerSessionModel->validateToken($token);

        if (!$session) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Token tidak valid atau sudah kedaluwarsa. Silakan scan ulang.',
            ]);
        }

        // Record attendance (INSERT IGNORE via unique index)
        if ($guru) {
            $result = $this->absensiShalatModel->recordAttendance($session['id'], null, $guru['id'], 'guru');
            $nama = $guru['nama_lengkap'];
            $unit = 'Guru';
        } else {
            $result = $this->absensiShalatModel->recordAttendance($session['id'], $siswa['id'], null, 'siswa');
            $nama = $siswa['nama_lengkap'];
            $unit = $siswa['nama_kelas'] ?? 'Siswa';
        }

        if ($result === 'duplicate') {
            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Kamu sudah absen sebelumnya',
                'status'    => 'duplicate',
                'waktu'     => date('H:i:s'),
            ]);
        }

        if ($result === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal mencatat absensi. Silakan coba lagi.',
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Absensi shalat berhasil!',
            'status'    => 'inserted',
            'waktu'     => date('H:i:s'),
            'siswa'     => $nama,
            'kelas'     => $unit,
        ]);
    }

    /**
     * GET /api/attendance/scan-page
     * 
     * Render the scan page for students or teachers
     */
    public function scanPage()
    {
        $token = $this->request->getGet('token');

        // If user is not logged in, redirect to login
        if (!session()->get('isLoggedIn')) {
            session()->set('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Silakan login untuk melakukan absensi');
        }

        // Get user data
        $userId = session()->get('user_id') ?? session()->get('userId');
        $guruModel = new \App\Models\GuruModel();

        $guru  = $guruModel->getByUserId($userId);
        $siswa = null;
        if (!$guru) {
            $siswa = $this->siswaModel->getByUserId($userId);
        }

        $userProfile = $guru ?: $siswa;

        $data = [
            'title'       => 'Absensi Shalat',
            'token'       => $token,
            'siswa'       => $userProfile,
            'userType'    => $guru ? 'guru' : 'siswa',
        ];

        return view('siswa/absensi_shalat/scan', $data);
    }

}
