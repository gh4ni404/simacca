<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('guru/izin-guru') ?>" 
               class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                <p class="mt-1 text-sm text-gray-500"><?= $pageDescription ?></p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600">
                    <h2 class="text-lg font-semibold text-white">Form Pengajuan Izin</h2>
                </div>

                <form action="<?= base_url('guru/izin-guru/store') ?>" method="POST" enctype="multipart/form-data" class="p-6">
                    <?= csrf_field() ?>

                    <!-- Jenis Izin -->
                    <div class="mb-6">
                        <label for="jenis_izin" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Izin <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_izin" id="jenis_izin" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Pilih Jenis Izin --</option>
                            <option value="izin" <?= old('jenis_izin') === 'izin' ? 'selected' : '' ?>>Izin</option>
                            <option value="sakit" <?= old('jenis_izin') === 'sakit' ? 'selected' : '' ?>>Sakit</option>
                            <option value="cuti" <?= old('jenis_izin') === 'cuti' ? 'selected' : '' ?>>Cuti</option>
                            <option value="dinas_luar" <?= old('jenis_izin') === 'dinas_luar' ? 'selected' : '' ?>>Dinas Luar</option>
                            <option value="lainnya" <?= old('jenis_izin') === 'lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                        <?php if (isset($errors['jenis_izin'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['jenis_izin'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Tanggal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" id="tanggal_mulai" required
                                   value="<?= old('tanggal_mulai', date('Y-m-d')) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php if (isset($errors['tanggal_mulai'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= $errors['tanggal_mulai'] ?></p>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="tanggal_selesai" id="tanggal_selesai" required
                                   value="<?= old('tanggal_selesai', date('Y-m-d')) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php if (isset($errors['tanggal_selesai'])): ?>
                                <p class="mt-1 text-sm text-red-600"><?= $errors['tanggal_selesai'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Durasi Info -->
                    <div id="durasi-info" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span class="text-sm text-blue-800">
                                Durasi izin: <strong id="durasi-text">-</strong>
                            </span>
                        </div>
                    </div>

                    <!-- Alasan -->
                    <div class="mb-6">
                        <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan/Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alasan" id="alasan" rows="4" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Jelaskan alasan pengajuan izin (minimal 10 karakter)"><?= old('alasan') ?></textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            <span id="char-count">0</span>/500 karakter
                        </p>
                        <?php if (isset($errors['alasan'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['alasan'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Upload Berkas -->
                    <div class="mb-6">
                        <label for="berkas" class="block text-sm font-medium text-gray-700 mb-2">
                            Berkas Pendukung <span class="text-gray-500">(Opsional)</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="berkas"
                                           class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Upload file</span>
                                        <input id="berkas" name="berkas" type="file" class="sr-only"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               onchange="displayFileName(this)">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PDF, JPG, PNG up to 2MB
                                </p>
                                <p id="file-name" class="text-sm text-gray-700 font-medium mt-2"></p>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Upload surat keterangan dokter untuk sakit, atau dokumen pendukung lainnya
                        </p>
                        <?php if (isset($errors['berkas'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?= $errors['berkas'] ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-4 border-t border-gray-200">
                        <a href="<?= base_url('guru/izin-guru') ?>"
                           class="flex-1 px-6 py-3 bg-gray-200 text-gray-800 text-center font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </a>
                        <button type="submit"
                                class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Panduan Pengajuan
                </h3>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Pilih Jenis Izin</h4>
                            <p class="text-sm text-gray-600">Pilih jenis izin yang sesuai dengan kebutuhan Anda</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">2</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Tentukan Periode</h4>
                            <p class="text-sm text-gray-600">Isi tanggal mulai dan selesai izin</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">3</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Jelaskan Alasan</h4>
                            <p class="text-sm text-gray-600">Berikan penjelasan yang jelas dan lengkap</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">4</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Upload Dokumen</h4>
                            <p class="text-sm text-gray-600">Lampirkan surat keterangan jika diperlukan</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-semibold text-yellow-900 text-sm mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Catatan Penting
                    </h4>
                    <ul class="text-xs text-yellow-800 space-y-1">
                        <li>• Izin sakit >2 hari wajib surat dokter</li>
                        <li>• Ajukan minimal H-1 untuk izin terencana</li>
                        <li>• Pengajuan akan diproses maksimal 1x24 jam</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter
const alasanInput = document.getElementById('alasan');
const charCount = document.getElementById('char-count');

alasanInput?.addEventListener('input', function() {
    const count = this.value.length;
    charCount.textContent = count;
    
    if (count < 10) {
        charCount.classList.add('text-red-600');
        charCount.classList.remove('text-gray-500');
    } else {
        charCount.classList.remove('text-red-600');
        charCount.classList.add('text-gray-500');
    }
});

// Display file name
function displayFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        fileNameDisplay.textContent = `File: ${fileName}`;
    } else {
        fileNameDisplay.textContent = '';
    }
}

// Calculate duration
const tanggalMulai = document.getElementById('tanggal_mulai');
const tanggalSelesai = document.getElementById('tanggal_selesai');
const durasiInfo = document.getElementById('durasi-info');
const durasiText = document.getElementById('durasi-text');

function calculateDuration() {
    const start = new Date(tanggalMulai.value);
    const end = new Date(tanggalSelesai.value);
    
    if (start && end && end >= start) {
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        durasiText.textContent = `${diffDays} hari`;
        durasiInfo.classList.remove('hidden');
    } else {
        durasiInfo.classList.add('hidden');
    }
}

tanggalMulai?.addEventListener('change', calculateDuration);
tanggalSelesai?.addEventListener('change', calculateDuration);

// Initial calculation
if (tanggalMulai?.value && tanggalSelesai?.value) {
    calculateDuration();
}

// Set minimum date to today
const today = new Date().toISOString().split('T')[0];
tanggalMulai?.setAttribute('min', today);
tanggalSelesai?.setAttribute('min', today);

// Update end date minimum when start date changes
tanggalMulai?.addEventListener('change', function() {
    tanggalSelesai?.setAttribute('min', this.value);
});
</script>

<?= $this->endSection() ?>
