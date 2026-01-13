<?= $this->extend('templates/main_layout') ?>

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
    <form action="<?= base_url('admin/kelas/simpan') ?>" method="POST" id="kelasForm">
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
                           value="<?= old('nama_kelas') ?>"
                           required>
                    <p class="text-xs text-gray-500 mt-2">Format: [Tingkat]-[Jurusan] (Contoh: X-RPL, XI-TKJ)</p>
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
                            <option value="<?= $value ?>" <?= old('tingkat') == $value ? 'selected' : '' ?>>
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
                            <option value="<?= $value ?>" <?= old('jurusan') == $value ? 'selected' : '' ?>>
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
                <!-- Wali Kelas Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wali Kelas (Opsional)</label>
                    <select name="wali_kelas_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Pilih Wali Kelas</option>
                        <?php foreach ($guruList as $guru): ?>
                            <option value="<?= $guru['id'] ?>" <?= old('wali_kelas_id') == $guru['id'] ? 'selected' : '' ?>>
                                <?= esc($guru['nama_lengkap']) ?> - <?= $guru['nama_mapel'] ?? '-' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">Pilih guru yang akan menjadi wali kelas</p>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i> Informasi Penting
                    </h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Wali kelas dapat ditugaskan nanti</li>
                        <li>• Pastikan guru yang dipilih belum menjadi wali kelas lain</li>
                        <li>• Wali kelas akan otomatis diubah statusnya</li>
                        <li>• Kelas tanpa wali akan ditandai khusus di dashboard</li>
                    </ul>
                </div>

                <!-- Preview Card -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-700 mb-3">Preview Kelas</h4>
                    <div class="space-y-2">
                        <div class="flex">
                            <span class="w-24 text-gray-600">Nama:</span>
                            <span id="previewNama" class="font-medium">-</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-gray-600">Tingkat:</span>
                            <span id="previewTingkat" class="font-medium">-</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-gray-600">Jurusan:</span>
                            <span id="previewJurusan" class="font-medium">-</span>
                        </div>
                        <div class="flex">
                            <span class="w-24 text-gray-600">Wali Kelas:</span>
                            <span id="previewWali" class="font-medium">Belum ada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 border-t pt-6">
            <a href="<?= base_url('admin/kelas') ?>" 
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
    // Real-time preview
    const namaInput = document.querySelector('input[name="nama_kelas"]');
    const tingkatSelect = document.querySelector('select[name="tingkat"]');
    const jurusanSelect = document.querySelector('select[name="jurusan"]');
    const waliSelect = document.querySelector('select[name="wali_kelas_id"]');
    const guruList = <?= json_encode($guruList) ?>;

    function updatePreview() {
        // Update nama
        document.getElementById('previewNama').textContent = 
            namaInput.value || '-';
        
        // Update tingkat
        const tingkatText = tingkatSelect.options[tingkatSelect.selectedIndex]?.text || '-';
        document.getElementById('previewTingkat').textContent = tingkatText;
        
        // Update jurusan
        const jurusanText = jurusanSelect.options[jurusanSelect.selectedIndex]?.text || '-';
        document.getElementById('previewJurusan').textContent = jurusanText;
        
        // Update wali kelas
        if (waliSelect.value) {
            const selectedGuru = guruList.find(g => g.id == waliSelect.value);
            document.getElementById('previewWali').textContent = 
                selectedGuru ? selectedGuru.nama_lengkap : 'Belum ada';
        } else {
            document.getElementById('previewWali').textContent = 'Belum ada';
        }
    }

    // Add event listeners
    namaInput.addEventListener('input', updatePreview);
    tingkatSelect.addEventListener('change', updatePreview);
    jurusanSelect.addEventListener('change', updatePreview);
    waliSelect.addEventListener('change', updatePreview);

    // Initial preview
    updatePreview();

    // Auto-generate kelas name suggestion
    tingkatSelect.addEventListener('change', function() {
        if (!namaInput.value) {
            const tingkat = this.value;
            const jurusan = jurusanSelect.value;
            
            if (tingkat && jurusan) {
                // Get jurusan abbreviation
                let jurusanAbbr = '';
                switch(jurusan) {
                    case 'Agribisnis Tanaman':
                        jurusanAbbr = 'AT';
                        break;
                    case 'Manajemen Perkantoran dan Layanan Bisnis':
                        jurusanAbbr = 'MPLB';
                        break;
                    case 'Desain Komunikasi Visual':
                        jurusanAbbr = 'DKV';
                        break;
                    // case 'Akuntansi':
                    //     jurusanAbbr = 'AK';
                    //     break;
                    // case 'Administrasi Perkantoran':
                    //     jurusanAbbr = 'AP';
                    //     break;
                    // case 'Pemasaran':
                    //     jurusanAbbr = 'PM';
                    //     break;
                    // case 'Tata Boga':
                    //     jurusanAbbr = 'TB';
                    //     break;
                    // case 'Tata Busana':
                    //     jurusanAbbr = 'TBS';
                    //     break;
                }
                
                if (jurusanAbbr) {
                    namaInput.value = tingkat + '-' + jurusanAbbr;
                    updatePreview();
                }
            }
        }
    });

    // Form validation
    document.getElementById('kelasForm').addEventListener('submit', function(e) {
        const tingkat = tingkatSelect.value;
        const jurusan = jurusanSelect.value;
        
        if (!tingkat || !jurusan) {
            e.preventDefault();
            alert('Silakan lengkapi tingkat dan jurusan terlebih dahulu');
            return false;
        }
        
        return true;
    });
</script>
<?= $this->endSection() ?>