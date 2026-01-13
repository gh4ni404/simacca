<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-user-circle mr-2 text-purple-600"></i>
            Profil Saya
        </h1>
        <p class="text-gray-600 mt-1">Kelola informasi profil dan keamanan akun Anda</p>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <!-- Profile Picture -->
                    <div class="flex flex-col items-center mb-6">
                        <div class="h-32 w-32 rounded-full bg-gradient-to-r from-blue-400 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-4">
                            <?= strtoupper(substr($siswa['nama_lengkap'], 0, 2)); ?>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 text-center"><?= esc($siswa['nama_lengkap']); ?></h2>
                        <p class="text-sm text-gray-600 mt-1"><?= esc($siswa['nis']); ?></p>
                        <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-xs font-medium <?= $siswa['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <i class="fas <?= $siswa['is_active'] ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                            <?= $siswa['is_active'] ? 'Aktif' : 'Tidak Aktif'; ?>
                        </span>
                    </div>

                    <!-- Quick Info -->
                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-school w-8 text-gray-400"></i>
                            <div>
                                <p class="text-xs text-gray-500">Kelas</p>
                                <p class="font-medium text-gray-800"><?= esc($siswa['nama_kelas']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-id-card w-8 text-gray-400"></i>
                            <div>
                                <p class="text-xs text-gray-500">NISN</p>
                                <p class="font-medium text-gray-800"><?= esc($siswa['nisn'] ?? '-'); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas <?= $siswa['jenis_kelamin'] == 'L' ? 'fa-mars' : 'fa-venus'; ?> w-8 <?= $siswa['jenis_kelamin'] == 'L' ? 'text-blue-400' : 'text-pink-400'; ?>"></i>
                            <div>
                                <p class="text-xs text-gray-500">Jenis Kelamin</p>
                                <p class="font-medium text-gray-800"><?= $siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                            </div>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user w-8 text-gray-400"></i>
                            <div>
                                <p class="text-xs text-gray-500">Username</p>
                                <p class="font-medium text-gray-800"><?= esc($siswa['username']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow p-6 mt-6 text-white">
                <h3 class="font-semibold mb-3 flex items-center">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Keamanan Akun
                </h3>
                <ul class="text-sm space-y-2 opacity-90">
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Gunakan password yang kuat</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Jangan bagikan password Anda</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Update profil secara berkala</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-t-lg">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profil
                    </h2>
                    <p class="text-sm opacity-80 mt-1">Update informasi profil Anda</p>
                </div>
                <div class="p-6">
                    <form action="<?= base_url('siswa/profil/update'); ?>" method="POST">
                        <?= csrf_field(); ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="<?= esc($siswa['email'] ?? ''); ?>"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <!-- No Telp -->
                            <div>
                                <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-2 text-green-500"></i>
                                    No. Telepon
                                </label>
                                <input type="tel" 
                                       id="no_telp" 
                                       name="no_telp" 
                                       value="<?= esc($siswa['no_telp'] ?? ''); ?>"
                                       placeholder="08xxxxxxxxxx"
                                       pattern="[0-9]{10,15}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Format: 10-15 digit angka</p>
                            </div>

                            <!-- Alamat -->
                            <div class="md:col-span-2">
                                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                                    Alamat
                                </label>
                                <textarea id="alamat" 
                                          name="alamat" 
                                          rows="3"
                                          placeholder="Masukkan alamat lengkap Anda"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= esc($siswa['alamat'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6">
                            <button type="submit" 
                                    class="w-full md:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-t-lg">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-key mr-2"></i>
                        Ubah Password
                    </h2>
                    <p class="text-sm opacity-80 mt-1">Perbarui password untuk keamanan akun</p>
                </div>
                <div class="p-6">
                    <form action="<?= base_url('siswa/profil/change-password'); ?>" method="POST" id="changePasswordForm">
                        <?= csrf_field(); ?>

                        <!-- Current Password -->
                        <div class="mb-6">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-gray-500"></i>
                                Password Lama <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password" 
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                <button type="button" 
                                        onclick="togglePassword('current_password')" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="current_password-icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="mb-6">
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-purple-500"></i>
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="new_password" 
                                       name="new_password" 
                                       required
                                       minlength="6"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                <button type="button" 
                                        onclick="togglePassword('new_password')" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="new_password-icon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-purple-500"></i>
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       required
                                       minlength="6"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                <button type="button" 
                                        onclick="togglePassword('confirm_password')" 
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-eye" id="confirm_password-icon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1" id="passwordMatch"></p>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    class="w-full md:w-auto px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                                <i class="fas fa-key mr-2"></i>
                                Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-600 text-xl mr-3 mt-1"></i>
                    <div class="text-sm text-yellow-800">
                        <p class="font-semibold mb-1">Catatan Penting:</p>
                        <ul class="list-disc list-inside space-y-1 ml-2">
                            <li>Data yang dapat diubah: Email, No. Telepon, dan Alamat</li>
                            <li>Data seperti NIS, NISN, Nama, dan Kelas hanya dapat diubah oleh Admin</li>
                            <li>Jika ada kesalahan data, hubungi Admin atau Wali Kelas</li>
                            <li>Pastikan email yang digunakan valid dan dapat diakses</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password match validation
const newPassword = document.getElementById('new_password');
const confirmPassword = document.getElementById('confirm_password');
const passwordMatch = document.getElementById('passwordMatch');

confirmPassword.addEventListener('input', function() {
    if (confirmPassword.value === '') {
        passwordMatch.textContent = '';
        return;
    }
    
    if (newPassword.value === confirmPassword.value) {
        passwordMatch.textContent = '✓ Password cocok';
        passwordMatch.classList.remove('text-red-500');
        passwordMatch.classList.add('text-green-500');
    } else {
        passwordMatch.textContent = '✗ Password tidak cocok';
        passwordMatch.classList.remove('text-green-500');
        passwordMatch.classList.add('text-red-500');
    }
});

// Form validation
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    if (newPassword.value !== confirmPassword.value) {
        e.preventDefault();
        alert('Password baru dan konfirmasi password tidak cocok!');
        confirmPassword.focus();
        return false;
    }
    
    if (newPassword.value.length < 6) {
        e.preventDefault();
        alert('Password baru minimal 6 karakter!');
        newPassword.focus();
        return false;
    }
    
    if (!confirm('Apakah Anda yakin ingin mengubah password?')) {
        e.preventDefault();
        return false;
    }
});
</script>
<?= $this->endSection() ?>
