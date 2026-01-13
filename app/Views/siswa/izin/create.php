<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center">
            <a href="<?= base_url('siswa/izin'); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-paper-plane mr-2 text-blue-600"></i>
                    Ajukan Izin Baru
                </h1>
                <p class="text-gray-600 mt-1">Isi form di bawah untuk mengajukan izin tidak hadir</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <form action="<?= base_url('siswa/izin/simpan'); ?>" method="POST" enctype="multipart/form-data" id="izinForm">
                        <?= csrf_field(); ?>

                        <!-- Tanggal Izin -->
                        <div class="mb-6">
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                Tanggal Izin <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal" 
                                   name="tanggal" 
                                   min="<?= date('Y-m-d'); ?>"
                                   value="<?= old('tanggal'); ?>"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pilih tanggal ketika Anda tidak dapat hadir
                            </p>
                        </div>

                        <!-- Jenis Izin -->
                        <div class="mb-6">
                            <label for="jenis_izin" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-purple-500"></i>
                                Jenis Izin <span class="text-red-500">*</span>
                            </label>
                            <select id="jenis_izin" 
                                    name="jenis_izin" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">-- Pilih Jenis Izin --</option>
                                <option value="Sakit" <?= old('jenis_izin') == 'Sakit' ? 'selected' : ''; ?>>Sakit</option>
                                <option value="Izin" <?= old('jenis_izin') == 'Izin' ? 'selected' : ''; ?>>Izin (Keperluan Keluarga/Lainnya)</option>
                            </select>
                        </div>

                        <!-- Alasan -->
                        <div class="mb-6">
                            <label for="alasan" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-comment-alt mr-2 text-green-500"></i>
                                Alasan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="alasan" 
                                      name="alasan" 
                                      rows="4" 
                                      required
                                      minlength="10"
                                      placeholder="Jelaskan alasan Anda tidak dapat hadir (minimal 10 karakter)"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('alasan'); ?></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Jelaskan dengan detail dan jelas
                                </p>
                                <p class="text-xs text-gray-500" id="charCount">0 / 10 karakter</p>
                            </div>
                        </div>

                        <!-- Upload Dokumen -->
                        <div class="mb-6">
                            <label for="berkas" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-paperclip mr-2 text-yellow-500"></i>
                                Dokumen Pendukung (Opsional)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="berkas" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload file</span>
                                            <input id="berkas" 
                                                   name="berkas" 
                                                   type="file" 
                                                   accept=".jpg,.jpeg,.png,.pdf"
                                                   class="sr-only"
                                                   onchange="displayFileName(this)">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        JPG, JPEG, PNG atau PDF (Max. 2MB)
                                    </p>
                                    <p id="fileName" class="text-sm text-blue-600 font-medium mt-2"></p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Upload surat keterangan dokter untuk izin sakit, atau dokumen pendukung lainnya
                            </p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-3">
                            <button type="submit" 
                                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Ajukan Izin
                            </button>
                            <a href="<?= base_url('siswa/izin'); ?>" 
                               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <!-- Panduan -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Panduan Pengajuan Izin
                </h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">1</span>
                        </div>
                        <p>Pilih tanggal ketika Anda tidak dapat hadir</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">2</span>
                        </div>
                        <p>Pilih jenis izin (Sakit atau Izin)</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">3</span>
                        </div>
                        <p>Jelaskan alasan dengan detail dan jelas (minimal 10 karakter)</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">4</span>
                        </div>
                        <p>Upload dokumen pendukung jika ada (surat keterangan, dll)</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">5</span>
                        </div>
                        <p>Klik "Ajukan Izin" dan tunggu persetujuan wali kelas</p>
                    </div>
                </div>
            </div>

            <!-- Tips -->
            <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg shadow p-6 text-white mb-6">
                <h3 class="text-lg font-semibold mb-3">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Tips
                </h3>
                <ul class="text-sm space-y-2 opacity-90">
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Ajukan izin minimal 1 hari sebelumnya</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Sertakan dokumen untuk mempercepat proses</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Jelaskan alasan dengan jelas dan sopan</span>
                    </li>
                </ul>
            </div>

            <!-- Informasi Siswa -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user mr-2 text-purple-500"></i>
                    Informasi Anda
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Nama</span>
                        <span class="font-medium text-gray-800"><?= esc($siswa['nama_lengkap']); ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">NIS</span>
                        <span class="font-medium text-gray-800"><?= esc($siswa['nis']); ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-600">Kelas</span>
                        <span class="font-medium text-gray-800"><?= esc($siswa['nama_kelas']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter
const alasanTextarea = document.getElementById('alasan');
const charCount = document.getElementById('charCount');

alasanTextarea.addEventListener('input', function() {
    const length = this.value.length;
    charCount.textContent = length + ' / 10 karakter';
    
    if (length < 10) {
        charCount.classList.add('text-red-500');
        charCount.classList.remove('text-green-500');
    } else {
        charCount.classList.add('text-green-500');
        charCount.classList.remove('text-red-500');
    }
});

// Display selected file name
function displayFileName(input) {
    const fileName = document.getElementById('fileName');
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
        
        if (fileSize > 2) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
            input.value = '';
            fileName.textContent = '';
            return;
        }
        
        fileName.textContent = '✓ ' + file.name + ' (' + fileSize + ' MB)';
    }
}

// Form validation
document.getElementById('izinForm').addEventListener('submit', function(e) {
    const alasan = document.getElementById('alasan').value;
    
    if (alasan.length < 10) {
        e.preventDefault();
        alert('Alasan harus minimal 10 karakter!');
        document.getElementById('alasan').focus();
        return false;
    }
    
    // Confirm before submit
    if (!confirm('Apakah Anda yakin ingin mengajukan izin ini?')) {
        e.preventDefault();
        return false;
    }
});

// Set minimum date (today)
const tanggalInput = document.getElementById('tanggal');
const today = new Date().toISOString().split('T')[0];
tanggalInput.setAttribute('min', today);
</script>
<?= $this->endSection() ?>
