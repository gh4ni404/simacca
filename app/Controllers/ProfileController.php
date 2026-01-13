<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\GuruModel;
use App\Models\SiswaModel;

class ProfileController extends BaseController
{
    protected $userModel;
    protected $guruModel;
    protected $siswaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->guruModel = new GuruModel();
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Display profile page
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu dong ??');
        }

        $userId = session()->get('userId');
        $role = session()->get('role');

        $data = [
            'title' => 'Profil Saya',
            'user' => $this->getUserData(),
            'userData' => $this->userModel->find($userId),
            'validation' => \Config\Services::validation()
        ];

        // Get additional data based on role
        switch ($role) {
            case 'guru_mapel':
            case 'wali_kelas':
                $guru = $this->guruModel->getGuruWithMapel($this->guruModel->where('user_id', $userId)->first()['id'] ?? null);
                $data['guru'] = $guru;
                break;

            case 'siswa':
                $siswa = $this->siswaModel->getSiswaWithKelas($this->siswaModel->where('user_id', $userId)->first()['id'] ?? null);
                $data['siswa'] = $siswa;
                break;
        }

        return view('profile/index', $data);
    }

    /**
     * Update profile
     */
    public function update()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Login dulu dong ??');
        }

        $userId = session()->get('userId');
        $role = session()->get('role');

        $rules = [
            'email' => 'valid_email',
        ];

        // Jika username berubah
        $userData = $this->userModel->find($userId);
        if ($this->request->getPost('username') != $userData['username']) {
            $rules['username'] = 'required|is_unique[users.username,id,' . $userId . ']';
        }

        // Jika password diisi
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['confirm_password'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update user data
        $updateData = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email')
        ];

        // Update password jika diisi
        if ($this->request->getPost('password')) {
            $updateData['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $updateData);

        // Update session data
        session()->set([
            'username' => $updateData['username'],
            'email' => $updateData['email']
        ]);

        session()->setFlashdata('success', 'Profil updated! Looking good 😎✨');
        return redirect()->back();
    }
}
