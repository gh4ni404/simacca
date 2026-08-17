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
     * Student scans QR code and submits the token.
     * Identity is derived from the active session (student must be logged in).
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

        // Only siswa can scan
        $userId = session()->get('user_id') ?? session()->get('userId');
        $allRoles = session()->get('all_roles') ?? [session()->get('role')];

        if (!in_array('siswa', $allRoles)) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Hanya siswa yang dapat melakukan absensi',
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

        // Get siswa data
        $siswa = $this->siswaModel->getByUserId($userId);

        if (!$siswa) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan',
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
        $result = $this->absensiShalatModel->recordAttendance($session['id'], $siswa['id']);

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
            'siswa'     => $siswa['nama_lengkap'],
            'kelas'     => $siswa['nama_kelas'] ?? '',
        ]);
    }

    /**
     * GET /api/attendance/scan-page
     * 
     * Render the scan page for students
     */
    public function scanPage()
    {
        $token = $this->request->getGet('token');

        // If student is not logged in, redirect to login
        if (!session()->get('isLoggedIn')) {
            session()->set('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Silakan login untuk melakukan absensi');
        }

        // Get student data
        $userId = session()->get('user_id') ?? session()->get('userId');
        $siswa = $this->siswaModel->getByUserId($userId);

        $data = [
            'title'  => 'Absensi Shalat',
            'token'  => $token,
            'siswa'  => $siswa,
        ];

        return view('siswa/absensi_shalat/scan', $data);
    }
}
