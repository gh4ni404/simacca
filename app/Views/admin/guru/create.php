<?= $this->extend('templates/main_layout') ?>

<?= $this->section('styles') ?>
<style>
    .form-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Form -->
    <form action="<?= base_url('admin/guru/simpan') ?>" method="POST" id="guruForm">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Left Column: Data Akun -->
            <div class="form-section rounded-xl p-6 text-white">
                <h3 class="text-lg font-semibold mb-4 flex items-center">
                    <i class="fas fa-user-circle mr-2"></i> Data Akun
                </h3>

                <div class="space-y-4">
                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Username *</label>
                        <input type="text" name="username"
                            class="w-full px-4 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white"
                            value="<?= old('username') ?>"
                            required>
                        <div id="usernameFeedback" class="text-xs mt-1"></div>
                        <?php if ($validation->hasError('username')): ?>
                            <p class="text-red-200 text-xs mt-1"><?= $validation->getError('username') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Password *</label>
                        <input type="password" name="password"
                            class="w-full px-4 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white"
                            required>
                        <?php if ($validation->hasError('password')): ?>
                            <p class="text-red-200 text-xs mt-1"><?= $validation->getError('password') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email"
                            class="w-full px-4 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white"
                            value="<?= old('email') ?>">
                        <?php if ($validation->hasError('email')): ?>
                            <p class="text-red-200 text-xs mt-1"><?= $validation->getError('email') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Role *</label>
                        <select name="role"
                            class="w-full px-4 py-2 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white"
                            id="roleSelect"
                            required>
                            <option value="">Pilih Role</option>
                            <option value="guru_mapel" <?= old('role') == 'guru_mapel' ? 'selected' : '' ?>>Guru Mata Pelajaran</option>
                            <option value="wali_kelas" <?= old('role') == 'wali_kelas' ? 'selected' : '' ?>>Wali Kelas</option>
                        </select>
                        <?php if ($validation->hasError('role')): ?>
                            <p class="text-red-200 text-xs mt-1"><?= $validation->getError('role') ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Data Pribadi -->
            <div class="border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-id-card mr-2 text-gray-600"></i> Data Pribadi
                </h3>

                <div class="space-y-4">
                    <!-- NIP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP *</label>
                        <input type="text" name="nip"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="<?= old('nip') ?>"
                            required>
                        <div id="nipFeedback" class="text-xs mt-1"></div>
                        <?php if ($validation->hasError('nip')): ?>
                            <p class="text-red-600 text-xs mt-1"><?= $validation->getError('nip') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Nama Lengkap -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            value="<?= old('nama_lengkap') ?>"
                            required>
                        <?php if ($validation->hasError('nama_lengkap')): ?>
                            <p class="text-red-600 text-xs mt-1"><?= $validation->getError('nama_lengkap') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin *</label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="jenis_kelamin" value="L"
                                    class="text-indigo-600 focus:ring-indigo-500"
                                    <?= old('jenis_kelamin') == 'L' ? 'checked' : '' ?> required>
                                <span class="ml-2">Laki-laki</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="jenis_kelamin" value="P"
                                    class="text-indigo-600 focus:ring-indigo-500"
                                    <?= old('jenis_kelamin') == 'P' ? 'checked' : '' ?> required>
                                <span class="ml-2">Perempuan</span>
                            </label>
                        </div>
                        <?php if ($validation->hasError('jenis_kelamin')): ?>
                            <p class="text-red-600 text-xs mt-1"><?= $validation->getError('jenis_kelamin') ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            id="mapelSelect">
                            <option value="">Pilih Mata Pelajaran</option>
                            <?php foreach ($mapelList as $id => $mapel): ?>
                                <option value="<?= $id ?>" <?= old('mata_pelajaran_id') == $id ? 'selected' : '' ?>>
                                    <?= esc($mapel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Wali Kelas Section -->
                    <div id="waliKelasSection" class="border-t pt-4 <?= old('role') != 'wali_kelas' ? 'hidden' : '' ?>">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_wali_kelas" value="1"
                                class="rounded text-indigo-600 focus:ring-indigo-500"
                                id="isWaliKelasCheckbox"
                                <?= old('is_wali_kelas') ? 'checked' : '' ?>>
                            <span class="ml-2 font-medium text-gray-700">Jadikan sebagai Wali Kelas</span>
                        </label>

                        <!-- Kelas Selection -->
                        <div id="kelasSelection" class="mt-3 <?= !old('is_wali_kelas') ? 'hidden' : '' ?>">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kelas</label>
                            <select name="kelas_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelasList as $id => $kelas): ?>
                                    <option value="<?= $id ?>" <?= old('kelas_id') == $id ? 'selected' : '' ?>>
                                        <?= esc($kelas) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 border-t pt-6">
            <a href="<?= base_url('admin/guru') ?>"
                class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-save mr-2"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<script>
    // Toggle wali kelas section based on role selection
    document.getElementById('roleSelect').addEventListener('change', function() {
        const waliKelasSection = document.getElementById('waliKelasSection');
        if (this.value === 'wali_kelas') {
            waliKelasSection.classList.remove('hidden');
        } else {
            waliKelasSection.classList.add('hidden');
            document.getElementById('isWaliKelasCheckbox').checked = false;
            document.getElementById('kelasSelection').classList.add('hidden');
        }
    });

    // Toggle kelas selection based on wali kelas checkbox
    document.getElementById('isWaliKelasCheckbox').addEventListener('change', function() {
        const kelasSelection = document.getElementById('kelasSelection');
        if (this.checked) {
            kelasSelection.classList.remove('hidden');
        } else {
            kelasSelection.classList.add('hidden');
        }
    });

    // Real-time NIP validation
    document.querySelector('input[name="nip"]').addEventListener('blur', function() {
        const nip = this.value;
        if (!nip) return;

        fetch('<?= base_url('admin/guru/check-nip') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    nip: nip
                })
            })
            .then(response => response.json())
            .then(data => {
                const feedback = document.getElementById('nipFeedback');
                if (data.available) {
                    feedback.innerHTML = '<span class="text-green-600"><i class="fas fa-check mr-1"></i> ' + data.message + '</span>';
                } else {
                    feedback.innerHTML = '<span class="text-red-600"><i class="fas fa-times mr-1"></i> ' + data.message + '</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    // Real-time username validation
    document.querySelector('input[name="username"]').addEventListener('blur', function() {
        const username = this.value;
        if (!username) return;

        fetch('<?= base_url('admin/guru/check-username') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    username: username
                })
            })
            .then(response => response.json())
            .then(data => {
                const feedback = document.getElementById('usernameFeedback');
                if (data.available) {
                    feedback.innerHTML = '<span class="text-green-200"><i class="fas fa-check mr-1"></i> ' + data.message + '</span>';
                } else {
                    feedback.innerHTML = '<span class="text-red-200"><i class="fas fa-times mr-1"></i> ' + data.message + '</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    // Form validation before submit
    document.getElementById('guruForm').addEventListener('submit', function(e) {
        const role = document.getElementById('roleSelect').value;
        const isWaliKelas = document.getElementById('isWaliKelasCheckbox').checked;
        const kelasId = document.querySelector('select[name="kelas_id"]')?.value;

        if (role === 'wali_kelas' && isWaliKelas && !kelasId) {
            e.preventDefault();
            alert('Silakan pilih kelas untuk wali kelas');
            document.getElementById('kelasSelection').classList.remove('hidden');
        }
    });
</script>
<?= $this->endSection() ?>