<?= $this->extend(get_device_layout()) ?>

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
                        <?php
                        // Determine name and role-specific data
                        $displayName = $userData['username'];
                        $identifier = '';
                        $statusActive = $userData['is_active'];
                        $roleLabel = ucfirst(str_replace('_', ' ', $userData['role']));
                        
                        if (isset($guru)) {
                            $displayName = $guru['nama_lengkap'];
                            $identifier = $guru['nip'];
                        } elseif (isset($siswa)) {
                            $displayName = $siswa['nama_lengkap'];
                            $identifier = $siswa['nis'];
                        }

                        // Profile photo
                        $profilePhoto = $userData['profile_photo'] ?? null;
                        $photoUrl = $profilePhoto ? base_url('profile-photo/' . $profilePhoto) : null;
                        ?>
                        
                        <div class="relative group mb-4">
                            <?php if ($photoUrl): ?>
                                <img src="<?= esc($photoUrl); ?>" 
                                     alt="<?= esc($displayName); ?>" 
                                     class="h-32 w-32 rounded-full object-cover shadow-lg border-4 border-white">
                            <?php else: ?>
                                <div class="h-32 w-32 rounded-full bg-gradient-to-r from-blue-400 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-white">
                                    <?= strtoupper(substr($displayName, 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Upload/Change Photo Button -->
                            <button type="button" 
                                    onclick="document.getElementById('photoUploadModal').classList.remove('hidden')"
                                    class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full shadow-lg transition-all transform hover:scale-110">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800 text-center"><?= esc($displayName); ?></h2>
                        
                        <?php if ($identifier): ?>
                            <p class="text-sm text-gray-600 mt-1"><?= esc($identifier); ?></p>
                        <?php endif; ?>
                        
                        <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-xs font-medium <?= $statusActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <i class="fas <?= $statusActive ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                            <?= $statusActive ? 'Aktif' : 'Tidak Aktif'; ?>
                        </span>
                    </div>

                    <!-- Quick Info -->
                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <!-- Role -->
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user-tag w-8 text-purple-400"></i>
                            <div>
                                <p class="text-xs text-gray-500">Role</p>
                                <p class="font-medium text-gray-800"><?= esc($roleLabel); ?></p>
                            </div>
                        </div>

                        <?php if (isset($guru)): ?>
                            <!-- Guru-specific info -->
                            <?php if (!empty($guru['nama_mapel'])): ?>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-book w-8 text-blue-400"></i>
                                    <div>
                                        <p class="text-xs text-gray-500">Mata Pelajaran</p>
                                        <p class="font-medium text-gray-800"><?= esc($guru['nama_mapel']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex items-center text-sm">
                                <i class="fas fa-id-card w-8 text-gray-400"></i>
                                <div>
                                    <p class="text-xs text-gray-500">NIP</p>
                                    <p class="font-medium text-gray-800"><?= esc($guru['nip']); ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center text-sm">
                                <i class="fas <?= $guru['jenis_kelamin'] == 'L' ? 'fa-mars' : 'fa-venus'; ?> w-8 <?= $guru['jenis_kelamin'] == 'L' ? 'text-blue-400' : 'text-pink-400'; ?>"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Jenis Kelamin</p>
                                    <p class="font-medium text-gray-800"><?= $guru['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
                                </div>
                            </div>

                        <?php elseif (isset($siswa)): ?>
                            <!-- Siswa-specific info -->
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

                        <?php endif; ?>

                        <!-- Username (for all roles) -->
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user w-8 text-gray-400"></i>
                            <div>
                                <p class="text-xs text-gray-500">Username</p>
                                <p class="font-medium text-gray-800"><?= esc($userData['username']); ?></p>
                            </div>
                        </div>

                        <!-- Email (for all roles) -->
                        <?php if (!empty($userData['email'])): ?>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope w-8 text-gray-400"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Email</p>
                                    <p class="font-medium text-gray-800"><?= esc($userData['email']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
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
            <!-- Edit Profile Form - Combined -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-t-lg">
                    <h2 class="text-lg font-semibold flex items-center">
                        <i class="fas fa-user-edit mr-2"></i>
                        Edit Profil & Keamanan
                    </h2>
                    <p class="text-sm opacity-80 mt-1">Update informasi akun dan password Anda dalam satu kali simpan</p>
                </div>
                <div class="p-6">
                    <form action="<?= base_url('profile/update'); ?>" method="POST" id="profileForm">
                        <?= csrf_field(); ?>

                        <!-- Profile Information Section -->
                        <div class="mb-8">
                            <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                                <i class="fas fa-id-card mr-2 text-blue-500"></i>
                                Informasi Akun
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Username -->
                                <div class="md:col-span-2">
                                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-user mr-2 text-gray-500"></i>
                                        Username <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="username" 
                                           name="username" 
                                           value="<?= esc(old('username', $userData['username'])); ?>"
                                           required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <?php if (session()->has('errors') && isset(session('errors')['username'])): ?>
                                        <p class="text-xs text-red-500 mt-1"><?= esc(session('errors')['username']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Email -->
                                <div class="md:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-envelope mr-2 text-blue-500"></i>
                                        Email
                                    </label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="<?= esc(old('email', $userData['email'] ?? '')); ?>"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <?php if (session()->has('errors') && isset(session('errors')['email'])): ?>
                                        <p class="text-xs text-red-500 mt-1"><?= esc(session('errors')['email']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Password Change Section -->
                        <div class="mb-8">
                            <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                                <i class="fas fa-key mr-2 text-purple-500"></i>
                                Ubah Password (Opsional)
                            </h3>
                            <div class="space-y-6">
                                <!-- New Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-lock mr-2 text-purple-500"></i>
                                        Password Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="password" 
                                               name="password" 
                                               minlength="6"
                                               placeholder="Kosongkan jika tidak ingin mengubah"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                        <button type="button" 
                                                onclick="togglePassword('password')" 
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="password-icon"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah password.</p>
                                    <?php if (session()->has('errors') && isset(session('errors')['password'])): ?>
                                        <p class="text-xs text-red-500 mt-1"><?= esc(session('errors')['password']); ?></p>
                                    <?php endif; ?>
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-lock mr-2 text-purple-500"></i>
                                        Konfirmasi Password Baru
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               minlength="6"
                                               placeholder="Masukkan ulang password baru"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                        <button type="button" 
                                                onclick="togglePassword('confirm_password')" 
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="confirm_password-icon"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1" id="passwordMatch"></p>
                                    <?php if (session()->has('errors') && isset(session('errors')['confirm_password'])): ?>
                                        <p class="text-xs text-red-500 mt-1"><?= esc(session('errors')['confirm_password']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                Semua perubahan akan disimpan sekaligus
                            </p>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-medium shadow-md hover:shadow-lg transform hover:scale-105">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Semua Perubahan
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
                            <li>Username dan Email dapat diubah sesuai kebutuhan</li>
                            <li>Password baru minimal 6 karakter untuk keamanan</li>
                            <?php if (isset($siswa)): ?>
                                <li>Data pribadi seperti NIS, NISN, Nama, dan Kelas hanya dapat diubah oleh Admin</li>
                            <?php elseif (isset($guru)): ?>
                                <li>Data pribadi seperti NIP, Nama, dan Mata Pelajaran hanya dapat diubah oleh Admin</li>
                            <?php endif; ?>
                            <li>Jika ada kesalahan data, hubungi Administrator</li>
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
const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirm_password');
const passwordMatch = document.getElementById('passwordMatch');

if (confirmPassword) {
    confirmPassword.addEventListener('input', function() {
        if (confirmPassword.value === '') {
            passwordMatch.textContent = '';
            return;
        }
        
        if (password.value === confirmPassword.value) {
            passwordMatch.textContent = '✓ Password cocok';
            passwordMatch.classList.remove('text-red-500');
            passwordMatch.classList.add('text-green-500');
        } else {
            passwordMatch.textContent = '✗ Password tidak cocok';
            passwordMatch.classList.remove('text-green-500');
            passwordMatch.classList.add('text-red-500');
        }
    });
}

// Form validation
document.getElementById('profileForm').addEventListener('submit', function(e) {
    // Only validate if password field is filled
    if (password.value) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak cocok!');
            confirmPassword.focus();
            return false;
        }
        
        if (password.value.length < 6) {
            e.preventDefault();
            alert('Password baru minimal 6 karakter!');
            password.focus();
            return false;
        }
    }
});
</script>

<!-- Photo Upload Modal -->
<div id="photoUploadModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-t-lg">
            <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-camera mr-2"></i>
                Upload Foto Profil
            </h3>
            <button type="button" 
                    onclick="document.getElementById('photoUploadModal').classList.add('hidden')"
                    class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <!-- Upload Form -->
            <form action="<?= base_url('profile/upload-photo'); ?>" method="POST" enctype="multipart/form-data" id="photoUploadForm">
                <?= csrf_field(); ?>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-image mr-2 text-blue-500"></i>
                        Pilih Foto
                    </label>
                    
                    <!-- Preview Area -->
                    <div id="photoPreview" class="hidden mb-4">
                        <div class="relative inline-block">
                            <img id="previewImage" src="" alt="Preview" class="h-48 w-48 rounded-lg object-cover shadow-md border-2 border-gray-300">
                            <button type="button" 
                                    onclick="clearPhotoPreview()"
                                    class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <input type="file" 
                           id="profile_photo" 
                           name="profile_photo" 
                           accept="image/jpeg,image/jpg,image/png"
                           onchange="previewPhoto(event)"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Format: JPG, JPEG, PNG. Maksimal 5MB (otomatis dioptimasi)
                    </p>
                    
                    <?php if (session()->has('errors') && isset(session('errors')['profile_photo'])): ?>
                        <p class="text-xs text-red-500 mt-1"><?= esc(session('errors')['profile_photo']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center justify-between gap-3">
                    <?php if ($profilePhoto): ?>
                        <!-- Delete Photo Button -->
                        <button type="button" 
                                onclick="confirmDeletePhoto()"
                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-trash mr-2"></i>
                            Hapus Foto
                        </button>
                    <?php endif; ?>
                    
                    <button type="submit" 
                            class="<?= $profilePhoto ? 'flex-1' : 'w-full' ?> px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-upload mr-2"></i>
                        Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Photo Form (Hidden) -->
<form id="deletePhotoForm" action="<?= base_url('profile/delete-photo'); ?>" method="POST" class="hidden">
    <?= csrf_field(); ?>
</form>

<script>
// Preview photo before upload
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        // Check file size (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            alert('Ukuran file terlalu besar! Maksimal 2MB.');
            event.target.value = '';
            return;
        }
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) {
            alert('Format file tidak valid! Gunakan JPG, JPEG, atau PNG.');
            event.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('photoPreview').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

// Clear photo preview
function clearPhotoPreview() {
    document.getElementById('profile_photo').value = '';
    document.getElementById('photoPreview').classList.add('hidden');
    document.getElementById('previewImage').src = '';
}

// Confirm delete photo
function confirmDeletePhoto() {
    if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
        document.getElementById('deletePhotoForm').submit();
    }
}

// Close modal when clicking outside
document.getElementById('photoUploadModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        clearPhotoPreview();
    }
});
</script>

<?= $this->endSection() ?>
