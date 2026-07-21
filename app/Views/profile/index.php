<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
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

                        $profilePhoto = $userData['profile_photo'] ?? null;
                        $photoUrl = $profilePhoto ? base_url('profile-photo/' . $profilePhoto) : null;
                        ?>

                        <div class="relative group mb-4">
                            <?php if ($photoUrl): ?>
                                <img src="<?= esc($photoUrl); ?>" alt="<?= esc($displayName); ?>" class="h-32 w-32 rounded-full object-cover shadow-lg border-4 border-white">
                            <?php else: ?>
                                <div class="h-32 w-32 rounded-full bg-gradient-to-r from-blue-400 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg border-4 border-white">
                                    <?= strtoupper(substr($displayName, 0, 2)); ?>
                                </div>
                            <?php endif; ?>

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

                        <?= status_badge($statusActive ? 'active' : 'inactive') ?>
                    </div>

                    <!-- Quick Info -->
                    <div class="space-y-3 border-t border-gray-200 pt-4">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user-tag w-8 text-purple-400"></i>
                            <div>
                                <p class="text-xs text-gray-500">Role</p>
                                <p class="font-medium text-gray-800"><?= esc($roleLabel); ?></p>
                            </div>
                        </div>

                        <?php if (isset($guru)): ?>
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

                        <div class="flex items-center text-sm">
                            <i class="fas fa-user w-8 text-gray-400"></i>
                            <div>
                                <p class="text-xs text-gray-500">Username</p>
                                <p class="font-medium text-gray-800"><?= esc($userData['username']); ?></p>
                            </div>
                        </div>

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
                    <li class="flex items-start"><i class="fas fa-check mr-2 mt-1"></i><span>Gunakan password yang kuat</span></li>
                    <li class="flex items-start"><i class="fas fa-check mr-2 mt-1"></i><span>Jangan bagikan password Anda</span></li>
                    <li class="flex items-start"><i class="fas fa-check mr-2 mt-1"></i><span>Update profil secara berkala</span></li>
                </ul>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Edit Profile Form -->
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
                                <div class="md:col-span-2">
                                    <?= form_input('username', 'Username', old('username', $userData['username']), ['required' => true]) ?>
                                </div>
                                <div class="md:col-span-2">
                                    <?= form_input('email', 'Email', old('email', $userData['email'] ?? ''), ['type' => 'email']) ?>
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
                            <?= button('primary', 'Simpan Semua Perubahan', 'save', ['type' => 'submit', 'class' => 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg transform hover:scale-105']) ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <?= info_card('info-circle', 'Catatan Penting', '
                <ul class="list-disc list-inspace space-y-1 ml-2">
                    <li>Username dan Email dapat diubah sesuai kebutuhan</li>
                    <li>Password baru minimal 6 karakter untuk keamanan</li>
                    ' . (isset($siswa) ? '<li>Data pribadi seperti NIS, NISN, Nama, dan Kelas hanya dapat diubah oleh Admin</li>' : '') . '
                    ' . (isset($guru) ? '<li>Data pribadi seperti NIP, Nama, dan Mata Pelajaran hanya dapat diubah oleh Admin</li>' : '') . '
                    <li>Jika ada kesalahan data, hubungi Administrator</li>
                </ul>
            ', 'yellow') ?>
        </div>
    </div>
</div>

<script>
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

const password = document.getElementById('password');
const confirmPassword = document.getElementById('confirm_password');
const passwordMatch = document.getElementById('passwordMatch');

if (confirmPassword) {
    confirmPassword.addEventListener('input', function() {
        if (confirmPassword.value === '') { passwordMatch.textContent = ''; return; }
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

document.getElementById('profileForm').addEventListener('submit', function(e) {
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
            <form action="<?= base_url('profile/upload-photo'); ?>" method="POST" enctype="multipart/form-data" id="photoUploadForm">
                <?= csrf_field(); ?>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-image mr-2 text-blue-500"></i>
                        Pilih Foto
                    </label>

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

                        <?= form_file('profile_photo', 'Pilih Foto', ['accept' => 'image/jpeg,image/jpg,image/png', 'help' => 'Format: JPG, JPEG, PNG. Maksimal 1MB (dikompres otomatis)']) ?>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <?php if ($profilePhoto): ?>
                        <?= button('danger', 'Hapus Foto', 'trash', ['type' => 'button', 'onclick' => 'confirmDeletePhoto()', 'class' => 'flex-1']) ?>
                    <?php endif; ?>

                    <?= button('primary', 'Upload Foto', 'upload', ['type' => 'submit', 'class' => ($profilePhoto ? 'flex-1' : 'w-full')]) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Photo Form (Hidden) -->
<form id="deletePhotoForm" action="<?= base_url('profile/delete-photo'); ?>" method="POST" class="hidden">
    <?= csrf_field(); ?>
</form>

<?= view('components/upload_script') ?>
<script>
function previewPhoto(event) {
    const file = event.target.files[0];
    if (file) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!validTypes.includes(file.type)) { alert('Format file tidak valid! Gunakan JPG, JPEG, atau PNG.'); event.target.value = ''; return; }
        compressImage(file, function(compressedFile) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('photoPreview').classList.remove('hidden');
            };
            reader.readAsDataURL(compressedFile);
            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            event.target.files = dt.files;
        });
    }
}

function clearPhotoPreview() {
    document.getElementById('profile_photo').value = '';
    document.getElementById('photoPreview').classList.add('hidden');
    document.getElementById('previewImage').src = '';
}

function confirmDeletePhoto() {
    if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
        document.getElementById('deletePhotoForm').submit();
    }
}

document.getElementById('photoUploadModal').addEventListener('click', function(e) {
    if (e.target === this) { this.classList.add('hidden'); clearPhotoPreview(); }
});
</script>

<?= $this->endSection() ?>
