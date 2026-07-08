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
                    <i class="fas fa-edit mr-2 text-blue-600"></i>
                    Edit Jurnal PKL
                </h1>
                <p class="text-gray-600 mt-1">Perbaiki jurnal PKL Anda</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <form action="<?= base_url('siswa/jurnal-pkl/update/' . $jurnal['id']); ?>" method="POST" enctype="multipart/form-data" id="jurnalForm">
                        <?= csrf_field(); ?>

                        <div class="mb-6">
                            <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input type="date"
                                   id="tanggal"
                                   name="tanggal"
                                   value="<?= old('tanggal', $jurnal['tanggal']); ?>"
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
                                   value="<?= old('nama_kegiatan', $jurnal['nama_kegiatan']); ?>"
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
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('deskripsi', $jurnal['deskripsi']); ?></textarea>
                            <div class="flex justify-between items-center mt-1">
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Minimal 10 karakter
                                </p>
                                <p class="text-xs text-gray-500" id="charCount"><?= strlen($jurnal['deskripsi']); ?> / 10 karakter</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-camera mr-2 text-yellow-500"></i>
                                Foto Dokumentasi
                            </label>

                            <?php if (!empty($jurnal['foto'])): ?>
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 mb-2">Foto saat ini:</p>
                                <img src="<?= base_url('files/jurnal-pkl/' . $jurnal['foto']); ?>"
                                     class="max-h-48 rounded-lg shadow">
                                <label class="inline-flex items-center mt-3 text-sm text-red-600 hover:text-red-800 cursor-pointer">
                                    <input type="checkbox" name="remove_foto" value="1" class="mr-2">
                                    Hapus foto ini
                                </label>
                            </div>
                            <?php endif; ?>

                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                            <span>Upload foto baru</span>
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

                        <?php if (!empty($jurnal['catatan_pembimbing'])): ?>
                        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-comment-dots text-orange-500 text-xl mr-3 mt-1"></i>
                                <div>
                                    <p class="font-medium text-orange-800">Catatan Pembimbing:</p>
                                    <p class="text-sm text-orange-700 mt-1"><?= esc($jurnal['catatan_pembimbing']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
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
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Status Jurnal
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Status</span>
                        <span>
                            <?php if ($jurnal['status'] == 'pending'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            <?php elseif ($jurnal['status'] == 'revisi'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-edit mr-1"></i>Revisi
                                </span>
                            <?php elseif ($jurnal['status'] == 'disetujui'): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Disetujui
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Ditolak
                                </span>
                            <?php endif; ?>
                        </span>
                    </div>
                    <?php if ($jurnal['verified_at']): ?>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-600">Diverifikasi</span>
                        <span class="font-medium"><?= date('d M Y H:i', strtotime($jurnal['verified_at'])); ?></span>
                    </div>
                    <?php endif; ?>
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
    if (!confirm('Apakah Anda yakin ingin menyimpan perubahan?')) {
        e.preventDefault();
        return false;
    }
});
</script>
<?= $this->endSection() ?>
