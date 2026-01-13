<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-xl shadow p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h2>
        <p class="text-gray-600"><?= $pageDescription ?></p>
        
        <!-- Class Info -->
        <div class="mt-4 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-indigo-800 text-lg">Kelas <?= esc($kelas['nama_kelas']) ?></h3>
                    <p class="text-indigo-600 text-sm">
                        Tingkat <?= $kelas['tingkat'] ?> • <?= esc($kelas['jurusan']) ?>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-indigo-600">Jumlah Siswa</p>
                    <p class="text-2xl font-bold text-indigo-800"><?= $siswaCount ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Form -->
    <form action="<?= base_url('admin/kelas/update/' . $kelas['id']) ?>" method="POST" id="kelasForm">
        <?= csrf_field() ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Left Column: Data Kelas -->
            <div class="space-y-6">
                <!-- Nama Kelas -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas *</label>
                    <input type="text" name="nama_kelas" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="Contoh: X-RPL, XI-TKJ"
                           value="<?= old('nama_kelas', $kelas['nama_kelas']) ?>"
                           required>
                    <?php if ($validation->hasError('nama_kelas')): ?>
                        <p class="text-red-600 text-xs mt-1"><?= $validation->getError('nama_kelas') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Tingkat -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat *</label>
                    <select name="tingkat" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                        <option value="">Pilih Tingkat</option>
                        <?php foreach ($tingkatList as $value => $label): ?>
                            <option value="<?= $value ?>" <?= old('tingkat', $kelas['tingkat']) == $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($validation->hasError('tingkat')): ?>
                        <p class="text-red-600 text-xs mt-1"><?= $validation->getError('tingkat') ?></p>
                    <?php endif; ?>
                </div>

                <!-- Jurusan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jurusan *</label>
                    <select name="jurusan" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            required>
                        <option value="">Pilih Jurusan</option>
                        <?php foreach ($jurusanList as $value => $label): ?>
                            <option value="<?= $value ?>" <?= old('jurusan', $kelas['jurusan']) == $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($validation->hasError('jurusan')): ?>
                        <p class="text-red-600 text-xs mt-1"><?= $validation->getError('jurusan') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Wali Kelas -->
            <div class="space-y-6">
                <!-- Current Wali Kelas -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3">Wali Kelas Saat Ini</h4>
                    <?php if ($waliKelas): ?>
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                                <i class="fas fa-user-tie text-indigo-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800"><?= esc($waliKelas['nama_lengkap']) ?></p>
                                <p class="text-sm text-gray-600">NIP: <?= esc($waliKelas['nip']) ?></p>
                                <?php if ($waliKelas['nama_mapel']): ?>
                                    <p class="text-sm text-gray-600">Mata Pelajaran: <?= $waliKelas['nama_mapel'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="confirmRemoveWali()"
                                class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i> Hapus Wali Kelas
                        </button>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-user-slash text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada wali kelas</p>
                            <p class="text-sm text-gray-400 mt-1">Pilih wali kelas dari daftar di bawah</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- New Wali Kelas Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ganti Wali Kelas (Opsional)</label>
                    <select name="wali_kelas_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Pilih Wali Kelas Baru</option>
                        <?php foreach ($guruList as $id => $nama): ?>
                            <option value="<?= $id ?>" <?= old('wali_kelas_id', $kelas['wali_kelas_id']) == $id ? 'selected' : '' ?>>
                                <?= esc($nama) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Pilih guru yang akan menjadi wali kelas baru</p>
                </div>

                <!-- Warning -->
                <?php if ($siswaCount > 0): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="font-medium text-yellow-800 mb-2 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Perhatian
                        </h4>
                        <p class="text-sm text-yellow-700">
                            Kelas ini memiliki <?= $siswaCount ?> siswa. 
                            Perubahan tingkat/jurusan akan mempengaruhi data siswa.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-between space-x-3 border-t pt-6">
            <div>
                <?php if ($siswaCount == 0): ?>
                    <button type="button" 
                            onclick="confirmDelete(<?= $kelas['id'] ?>, '<?= esc($kelas['nama_kelas']) ?>')"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
                        <i class="fas fa-trash mr-2"></i> Hapus Kelas
                    </button>
                <?php else: ?>
                    <button type="button" 
                            onclick="alert('Tidak dapat menghapus kelas karena masih memiliki siswa')"
                            class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed flex items-center"
                            title="Kelas tidak dapat dihapus karena memiliki siswa">
                        <i class="fas fa-trash mr-2"></i> Hapus Kelas
                    </button>
                <?php endif; ?>
            </div>
            <div class="flex space-x-3">
                <a href="<?= base_url('admin/kelas') ?>" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                    <i class="fas fa-save mr-2"></i> Update Data
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Remove Wali Confirmation Modal -->
<div id="removeWaliModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Hapus Wali Kelas</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus wali kelas dari kelas <?= esc($kelas['nama_kelas']) ?>?
                </p>
                <p class="text-xs text-gray-500 mt-2">Guru akan tetap ada di sistem namun tidak lagi menjadi wali kelas.</p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="submitRemoveWali()" 
                        class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-300">
                    Ya, Hapus
                </button>
                <button onclick="closeRemoveModal()" 
                        class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Konfirmasi Hapus</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus kelas <span id="kelasName" class="font-semibold"></span>?
                </p>
                <p class="text-xs text-red-500 mt-2">Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="GET">
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Ya, Hapus
                    </button>
                </form>
                <button onclick="closeDeleteModal()" 
                        class="mt-2 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Remove wali kelas confirmation
    function confirmRemoveWali() {
        document.getElementById('removeWaliModal').classList.remove('hidden');
    }

    function closeRemoveModal() {
        document.getElementById('removeWaliModal').classList.add('hidden');
    }

    function submitRemoveWali() {
        fetch('<?= base_url('admin/kelas/remove-wali-kelas/' . $kelas['id']) ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Reload to see changes
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
        
        closeRemoveModal();
    }

    // Delete confirmation
    function confirmDelete(id, name) {
        document.getElementById('kelasName').textContent = name;
        document.getElementById('deleteForm').action = `<?= base_url('admin/kelas/hapus/') ?>${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const removeModal = document.getElementById('removeWaliModal');
        const deleteModal = document.getElementById('deleteModal');
        
        if (event.target === removeModal) {
            closeRemoveModal();
        }
        if (event.target === deleteModal) {
            closeDeleteModal();
        }
    }
</script>
<?= $this->endSection() ?>