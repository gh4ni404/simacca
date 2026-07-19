<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-4 md:p-6">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="<?= base_url('siswa/jurnal-pkl'); ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-edit mr-2 text-blue-600"></i>
                    Edit Aktivitas PKL
                </h1>
                <p class="text-gray-600 mt-1">Perbarui catatan kegiatan PKL Anda</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <form action="<?= base_url('siswa/jurnal-pkl/update-progress/' . $progress['id']); ?>" method="POST" enctype="multipart/form-data" id="pklForm">
                        <?= csrf_field(); ?>

                        <!-- Task info (readonly) -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tasks mr-2 text-purple-500"></i>
                                Task
                            </label>
                            <input type="text" value="<?= esc($progress['nama_task']) ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                                   readonly>
                            <p class="text-xs text-gray-400 mt-1">Task tidak dapat diubah</p>
                        </div>

                        <!-- Tanggal -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                Tanggal
                            </label>
                            <input type="date"
                                   name="tanggal"
                                   value="<?= old('tanggal', $progress['tanggal']); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100"
                                   readonly>
                            <p class="text-xs text-gray-400 mt-1">Tanggal tidak dapat diubah</p>
                        </div>

                        <!-- Langkah Kerja -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-list-ol mr-2 text-indigo-500"></i>
                                Langkah Kerja <span class="text-gray-400 font-normal">(Perencanaan & Persiapan)</span>
                            </label>
                            <div id="langkahKerjaContainer" class="space-y-2">
                                <?php
                                $existingLangkah = [];
                                if (!empty($progress['langkah_kerja'])) {
                                    $decoded = json_decode($progress['langkah_kerja'], true);
                                    if (is_array($decoded)) {
                                        $existingLangkah = $decoded;
                                    }
                                }
                                $langkah = old('langkah_kerja', $existingLangkah);
                                if (!empty($langkah)):
                                    foreach ($langkah as $i => $val): ?>
                                    <div class="flex items-center gap-2 langkah-row">
                                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold langkah-num"><?= ($i + 1) ?></span>
                                        <input type="text" name="langkah_kerja[]" value="<?= esc($val) ?>"
                                               placeholder="Langkah <?= ($i + 1) ?>"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                        <button type="button" onclick="removeLangkah(this)"
                                                class="flex-shrink-0 w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center hover:bg-red-200 transition-colors text-xs <?= count($langkah) <= 1 ? 'hidden' : '' ?> remove-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <?php endforeach;
                                else: ?>
                                    <div class="flex items-center gap-2 langkah-row">
                                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold langkah-num">1</span>
                                        <input type="text" name="langkah_kerja[]" value=""
                                               placeholder="Langkah 1"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                        <button type="button" onclick="removeLangkah(this)"
                                                class="flex-shrink-0 w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center hover:bg-red-200 transition-colors text-xs hidden remove-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button type="button" onclick="addLangkah()"
                                    class="mt-2 inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-xs font-medium">
                                <i class="fas fa-plus mr-1"></i> Tambah Langkah
                            </button>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-green-500"></i>
                                Aktivitas <span class="text-red-500">*</span>
                            </label>
                            <textarea name="deskripsi"
                                      rows="4"
                                      required
                                      minlength="3"
                                      placeholder="Hari ini saya..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('deskripsi', $progress['deskripsi']); ?></textarea>
                            <p class="text-xs text-gray-400 mt-1">Minimal 3 karakter</p>
                        </div>

                        <!-- Foto -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-camera mr-2 text-yellow-500"></i>
                                Foto Dokumentasi <span class="text-red-500">*</span>
                            </label>

                            <?php if (!empty($progress['foto'])): ?>
                            <div id="existingPhoto" class="mb-3">
                                <div class="relative inline-block">
                                    <img src="<?= base_url('files/pkl-progress/' . $progress['foto']); ?>"
                                         class="h-40 rounded-lg shadow object-cover">
                                    <button type="button" onclick="removeExistingFoto()"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Klik <i class="fas fa-times text-red-500"></i> untuk hapus foto</p>
                                <input type="hidden" name="hapus_foto" id="hapusFoto" value="0">
                            </div>
                            <?php endif; ?>

                            <div id="uploadArea"
                                 class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 hover:bg-blue-50/50 transition-all cursor-pointer"
                                 onclick="document.getElementById('foto').click()">
                                <div class="space-y-1 text-center pointer-events-none">
                                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <span class="font-medium text-blue-600">Klik untuk upload foto</span>
                                        <span class="pl-1">atau drag & drop</span>
                                    </div>
                                    <p class="text-xs text-gray-500">JPG, JPEG, PNG atau WebP (Max. 5MB) <span class="text-red-500 font-medium">Wajib diisi</span></p>
                                    <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only" onchange="previewImage(this)">
                                    <div id="previewContainer" class="mt-3 hidden pointer-events-auto">
                                        <img id="preview" class="mx-auto max-h-40 rounded-lg shadow">
                                        <button type="button" onclick="removeImage(); event.stopPropagation();" class="mt-2 text-xs text-red-600 hover:text-red-800 pointer-events-auto">
                                            <i class="fas fa-times mr-1"></i>Hapus foto
                                        </button>
                                    </div>
                                    <p id="fileName" class="text-sm text-blue-600 font-medium mt-2"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex gap-3">
                            <button type="submit" id="submitBtn"
                                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-save" id="submitIcon"></i>
                                <span id="submitText">Simpan Perubahan</span>
                            </button>
                            <a href="<?= base_url('siswa/jurnal-pkl'); ?>"
                               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                <i class="fas fa-times mr-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Informasi
                </h3>
                <div class="space-y-2 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">i</span>
                        <p>Anda dapat memperbarui deskripsi dan foto aktivitas</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">i</span>
                        <p>Task dan tanggal tidak dapat diubah</p>
                    </div>
                    <?php if ($progress['status'] === 'revision'): ?>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold">!</span>
                        <p class="text-orange-700">Aktivitas ini statusnya revisi. Setelah diedit akan kembali ke status submitted (menunggu verifikasi).</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addLangkah() {
    const container = document.getElementById('langkahKerjaContainer');
    const count = container.querySelectorAll('.langkah-row').length + 1;
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 langkah-row';
    row.innerHTML = `
        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold langkah-num">${count}</span>
        <input type="text" name="langkah_kerja[]" value=""
               placeholder="Langkah ${count}"
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
        <button type="button" onclick="removeLangkah(this)"
                class="flex-shrink-0 w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center hover:bg-red-200 transition-colors text-xs remove-btn">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(row);
    row.querySelector('input').focus();
    updateLangkahVisibility();
}

function removeLangkah(btn) {
    const row = btn.closest('.langkah-row');
    row.remove();
    updateLangkahNumbers();
    updateLangkahVisibility();
}

function updateLangkahNumbers() {
    const rows = document.querySelectorAll('#langkahKerjaContainer .langkah-row');
    rows.forEach((row, i) => {
        row.querySelector('.langkah-num').textContent = i + 1;
        row.querySelector('input').placeholder = 'Langkah ' + (i + 1);
    });
}

function updateLangkahVisibility() {
    const rows = document.querySelectorAll('#langkahKerjaContainer .langkah-row');
    const btns = document.querySelectorAll('#langkahKerjaContainer .remove-btn');
    btns.forEach(btn => {
        if (rows.length <= 1) {
            btn.classList.add('hidden');
        } else {
            btn.classList.remove('hidden');
        }
    });
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        if (fileSize > 5) {
            alert('Ukuran file terlalu besar! Maksimal 5MB');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').src = e.target.result;
            document.getElementById('previewContainer').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
        document.getElementById('fileName').textContent = file.name + ' (' + fileSize + ' MB)';
        document.getElementById('uploadArea').classList.add('border-green-400', 'bg-green-50/50');
        document.getElementById('uploadArea').classList.remove('border-gray-300');
    }
}

function removeImage() {
    document.getElementById('foto').value = '';
    document.getElementById('previewContainer').classList.add('hidden');
    document.getElementById('fileName').textContent = '';
    document.getElementById('uploadArea').classList.remove('border-green-400', 'bg-green-50/50');
    document.getElementById('uploadArea').classList.add('border-gray-300');
}

function removeExistingFoto() {
    if (!confirm('Foto wajib diupload. Hapus foto ini dan upload foto baru?')) return;
    document.getElementById('existingPhoto').classList.add('hidden');
    document.getElementById('hapusFoto').value = '1';
    document.getElementById('uploadArea').classList.add('border-orange-400', 'bg-orange-50/50');
}

document.getElementById('pklForm').addEventListener('submit', function(e) {
    const deskripsi = document.querySelector('textarea[name="deskripsi"]').value.trim();
    if (deskripsi.length < 3) {
        e.preventDefault();
        alert('Deskripsi harus minimal 3 karakter!');
        return false;
    }
    const langkahInputs = document.querySelectorAll('input[name="langkah_kerja[]"]');
    let hasLangkah = false;
    langkahInputs.forEach(inp => {
        if (inp.value.trim().length > 0) hasLangkah = true;
    });
    if (!hasLangkah) {
        e.preventDefault();
        alert('Minimal isi 1 langkah kerja!');
        return false;
    }
    const fotoInput = document.getElementById('foto');
    const hapusFoto = document.getElementById('hapusFoto');
    const hasExistingPhoto = !document.getElementById('existingPhoto').classList.contains('hidden');
    const hasNewPhoto = fotoInput.files && fotoInput.files[0];
    const photoDeleted = hapusFoto && hapusFoto.value === '1';
    if (!hasNewPhoto && (!hasExistingPhoto || photoDeleted)) {
        e.preventDefault();
        alert('Foto dokumentasi wajib diupload!');
        return false;
    }
    if (!confirm('Simpan perubahan aktivitas ini?')) {
        e.preventDefault();
        return false;
    }
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.classList.add('bg-blue-400', 'cursor-not-allowed');
    document.getElementById('submitIcon').classList.add('fa-spinner', 'fa-spin');
    document.getElementById('submitText').textContent = 'Menyimpan...';
});
</script>
<?= $this->endSection() ?>