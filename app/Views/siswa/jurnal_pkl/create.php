<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-6">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="<?= base_url('siswa/jurnal-pkl'); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle mr-2 text-blue-600"></i>
                    Tambah Jurnal PKL
                </h1>
                <p class="text-gray-600 mt-1">Catat kegiatan PKL Anda hari ini</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <form action="<?= base_url('siswa/jurnal-pkl/simpan'); ?>" method="POST" enctype="multipart/form-data" id="jurnalForm">
                        <?= csrf_field(); ?>

                        <div class="mb-6">
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   id="tanggal"
                                   name="tanggal"
                                   value="<?= old('tanggal', date('Y-m-d')); ?>"
                                   required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-6">
                            <label for="nama_kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tasks mr-2 text-purple-500"></i>
                                Nama Kegiatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="nama_kegiatan"
                                   name="nama_kegiatan"
                                   value="<?= old('nama_kegiatan'); ?>"
                                   required
                                   minlength="3"
                                   maxlength="255"
                                   placeholder="Contoh: Membuat laporan keuangan"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div class="mb-6">
                            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-green-500"></i>
                                Deskripsi Kegiatan <span class="text-red-500">*</span>
                            </label>
                            <textarea id="deskripsi"
                                      name="deskripsi"
                                      rows="5"
                                      required
                                      minlength="10"
                                      placeholder="Jelaskan kegiatan yang dilakukan hari ini secara detail"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('deskripsi'); ?></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Minimal 10 karakter
                                </p>
                                <p class="text-xs text-gray-500" id="charCount">0 / 10 karakter</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-camera mr-2 text-yellow-500"></i>
                                Foto Dokumentasi (Opsional)
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload foto</span>
                                            <input id="foto"
                                                   name="foto"
                                                   type="file"
                                                   accept=".jpg,.jpeg,.png,.webp"
                                                   class="sr-only"
                                                   onchange="previewImage(this)">
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">JPG, JPEG, PNG atau WebP (Max. 5MB)</p>
                                    <div id="previewContainer" class="mt-3 hidden">
                                        <img id="preview" class="mx-auto max-h-48 rounded-lg shadow">
                                        <button type="button" onclick="removeImage()" class="mt-2 text-xs text-red-600 hover:text-red-800">
                                            <i class="fas fa-times mr-1"></i>Hapus foto
                                        </button>
                                    </div>
                                    <p id="fileName" class="text-sm text-blue-600 font-medium mt-2"></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Jurnal
                            </button>
                            <a href="<?= base_url('siswa/jurnal-pkl'); ?>"
                               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Panduan Pengisian
                </h3>
                <div class="space-y-3 text-sm text-gray-600">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">1</span>
                        </div>
                        <p>Pilih tanggal kegiatan</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">2</span>
                        </div>
                        <p>Isi nama kegiatan dengan jelas</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">3</span>
                        </div>
                        <p>Deskripsikan kegiatan secara detail</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">4</span>
                        </div>
                        <p>Upload foto dokumentasi jika ada</p>
                    </div>
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 mt-0.5">
                            <span class="text-xs font-bold">5</span>
                        </div>
                        <p>Klik "Simpan" dan tunggu verifikasi pembimbing</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg shadow p-6 text-white mb-6">
                <h3 class="text-lg font-semibold mb-3">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Tips
                </h3>
                <ul class="text-sm space-y-2 opacity-90">
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Isi jurnal setiap hari setelah selesai kegiatan</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Deskripsikan kegiatan dengan detail dan jelas</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mr-2 mt-1"></i>
                        <span>Dokumentasi foto membantu verifikasi</span>
                    </li>
                </ul>
            </div>

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
const descTextarea = document.getElementById('deskripsi');
const charCount = document.getElementById('charCount');

descTextarea.addEventListener('input', function() {
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

function previewImage(input) {
    const previewContainer = document.getElementById('previewContainer');
    const preview = document.getElementById('preview');
    const fileName = document.getElementById('fileName');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);

        if (fileSize > 5) {
            alert('Ukuran file terlalu besar! Maksimal 5MB');
            input.value = '';
            previewContainer.classList.add('hidden');
            fileName.textContent = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(file);

        fileName.textContent = '✓ ' + file.name + ' (' + fileSize + ' MB)';
    }
}

function removeImage() {
    const input = document.getElementById('foto');
    const previewContainer = document.getElementById('previewContainer');
    const fileName = document.getElementById('fileName');
    input.value = '';
    previewContainer.classList.add('hidden');
    fileName.textContent = '';
}

document.getElementById('jurnalForm').addEventListener('submit', function(e) {
    const deskripsi = document.getElementById('deskripsi').value;
    if (deskripsi.length < 10) {
        e.preventDefault();
        alert('Deskripsi harus minimal 10 karakter!');
        document.getElementById('deskripsi').focus();
        return false;
    }
    if (!confirm('Apakah Anda yakin ingin menyimpan jurnal ini?')) {
        e.preventDefault();
        return false;
    }
});
</script>
<?= $this->endSection() ?>
