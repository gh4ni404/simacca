<?php

if (!function_exists('get_casual_message')) {
    /**
     * Mengambil template pesan kasual dan menyisipkan Trace Code untuk debugging.
     * 
     * @param string $type       Tipe pesan (success, info, warning, error)
     * @param string $role       Peran (siswa, internal, eksternal)
     * @param string $action     Konteks aksi (mapping ke key array)
     * @param string $traceCode  Kode unik untuk pencarian di VS Code (opsional)
     * @param string $customText Teks kustom dinamis (jika diisi, akan mengabaikan template array)
     * @return string
     */
    function get_casual_message(string $type, string $role = 'siswa', string $action = 'default', string $traceCode = '', string $customText = ''): string
    {
        // ==========================================
        // 1. KAMUS PESAN (Berdasarkan Daftar Siswa)
        // ==========================================
        $messages = [
            'success' => [
                'siswa' => [
                    'auth_logout' => 'Logout berhasil ya! 👋',
                    'auth_forgot' => 'Cek email ya! Instruksi reset sudah dikirim 📧✨',
                    'auth_reset' => 'Mantap! Password baru siap dipakai 🎉 Yuk login!',
                    'auth_change' => 'Password updated! Jangan lupa dicatat ya 🔐✨',
                    'izin_submit' => 'Izin dikirim! Tunggu persetujuan wali kelas ya 📨✨',
                    'profil_update' => 'Yeay! Profil udah diperbarui nih 🎉✨',
                    'profil_foto' => 'Foto profil udah diupdate nih 📸✨',
                    'profil_hapus_foto' => 'Foto profil udah dihapus ya 📸',
                    'layout_switch' => 'Tampilan berhasil disesuaikan 🖥️📱',
                    'jurnal_simpan' => 'Aktivitas berhasil dicatat 📝✨',
                    'jurnal_kirim' => 'Progress berhasil dikirim untuk diverifikasi 🚀',
                    'jurnal_edit' => 'Aktivitas berhasil diperbarui 📝✨',
                    'jurnal_hapus' => 'Progress berhasil dihapus 🗑️',
                    'jurnal_selesai' => 'Task berhasil diselesaikan. Menunggu verifikasi instruktur ✅',
                    'default' => 'Proses berhasil dilakukan! 🎉'
                ]
            ],
            'info' => [
                'siswa' => [
                    'jurnal_verifikasi' => 'Progress ini menunggu verifikasi instruktur. Silakan hubungi Instruktur PKL Anda ⏳',
                    'default' => 'Sekadar info untuk kamu nih.'
                ]
            ],
            'warning' => [
                'siswa' => [
                    'auth_guard' => 'Login dulu ya biar bisa akses halaman ini 🚪',
                    'profil_hapus_foto' => 'Nggak ada foto profil yang bisa dihapus 🤔',
                    'jurnal_kirim' => 'Hanya progress draft yang bisa dikirim ⚠️',
                    'jurnal_edit' => 'Progress yang sudah disetujui tidak dapat diedit atau dihapus 🔒',
                    'jurnal_selesai' => 'Hanya task aktif yang bisa diselesaikan ⚠️',
                    'default' => 'Tunggu dulu! Pastikan semuanya sudah benar ya.'
                ]
            ],
            'error' => [
                'siswa' => [
                    'auth_login' => 'Hmm, username atau password kayaknya salah deh 🤔',
                    'auth_forgot' => 'Gagal mengirim email nih 😅 Hubungi admin ya!',
                    'auth_reset' => 'Token nggak valid atau udah expired. Request reset password lagi ya 🔄',
                    'auth_change' => 'Password salah nih 🤔',
                    'data_not_found' => 'Data siswa nggak ketemu nih 🤔',
                    'task_not_found' => 'Data task atau progress nggak ketemu 🔍',
                    'izin_submit' => 'Upload file gagal nih 📁😬',
                    'profil_update' => 'Gagal memperbarui profil 😅 Coba lagi ya.',
                    'profil_password' => 'Password lama tidak sesuai 🤔',
                    'profil_foto' => 'File nggak valid atau ukurannya kebesaran 😅',
                    'file_not_found' => 'File nggak ketemu atau bukan gambar 🔍',
                    'jurnal_template' => 'Pilih template task terlebih dahulu dan pastikan valid ya!',
                    'jurnal_simpan' => 'Foto dokumentasi wajib diupload atau data belum lengkap 📸',
                    'default' => 'Waduh, sepertinya sistem lagi ngambek. Coba refresh halaman ini ya 😅'
                ]
            ]
        ];

        // Validasi Role
        if (!isset($messages[$type][$role])) {
            $role = 'internal'; // Fallback
        }

        // ==========================================
        // 2. TENTUKAN PESAN UTAMA
        // ==========================================
        // Jika ada custom text (pesan dinamis), gunakan itu. Jika tidak, ambil dari array.
        if (!empty($customText)) {
            $message = $customText;
        } else {
            $message = $messages[$type][$role][$action] ?? $messages[$type][$role]['default'] ?? 'Sistem sedang memproses data...';
        }

        // ==========================================
        // 3. FITUR DEBUGGING TRACE CODE
        // ==========================================
        if (ENVIRONMENT !== 'production') {
            $tipePendek = strtoupper(substr($type, 0, 3)); // SUC, ERR, WAR, INF
            $rolePendek = strtoupper(substr($role, 0, 3)); // SIS, INT, EKS
            $autoCode = "{$tipePendek}-{$rolePendek}-" . strtoupper($action);

            $debugString = $traceCode ? "[{$autoCode} | {$traceCode}]" : "[{$autoCode}]";

            // Bungkus dengan div/small agar tidak merusak layout tapi tetap terbaca
            $message .= " <small style='opacity: 0.4; font-family: monospace; font-size: 0.8em; display: inline-block; margin-left: 5px;'>{$debugString}</small>";
        }

        return $message;
    }
}

if (!function_exists('set_casual_alert')) {
    /**
     * Set flashdata langsung menggunakan template kasual & trace code.
     * 
     * @param string $type       Tipe pesan
     * @param string $role       Peran
     * @param string $action     Konteks aksi
     * @param string $traceCode  Kode unik kustom
     * @param string $customText Teks dinamis (opsional)
     */
    function set_casual_alert(string $type, string $role = 'siswa', string $action = 'default', string $traceCode = '', string $customText = '')
    {
        $message = get_casual_message($type, $role, $action, $traceCode, $customText);

        // Simpan ke Flashdata
        session()->setFlashdata($type, $message);
    }
}