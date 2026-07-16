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
                    <i class="fas fa-plus-circle mr-2 text-blue-600"></i>
                    Tambah Aktivitas PKL
                </h1>
                <p class="text-gray-600 mt-1">Catat kegiatan PKL Anda hari ini</p>
            </div>
        </div>
    </div>

    <?= view('components/alerts') ?>

    <?php if (!empty($tasks)): ?>
    <!-- Quick Task Cards -->
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-3">
            <i class="fas fa-bolt mr-1 text-yellow-500"></i>
            Pilih Task Aktif
        </h2>
        <div class="flex gap-3 overflow-x-auto pb-2">
            <?php foreach ($tasks as $task): ?>
            <button type="button" onclick="selectTask(<?= $task['id'] ?>)"
                    class="flex-shrink-0 bg-white rounded-lg shadow hover:shadow-md border-2 border-transparent hover:border-blue-400 transition-all p-4 text-left min-w-[200px] task-card"
                    data-task-id="<?= $task['id'] ?>">
                <h3 class="text-sm font-semibold text-gray-800 truncate"><?= esc($task['judul']) ?></h3>
                <?php if (!empty($task['kategori_nama'])): ?>
                <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                    <?= match ($task['kategori_nama']) {
                        'Desain' => 'bg-purple-100 text-purple-700',
                        'Programming' => 'bg-blue-100 text-blue-700',
                        'Administrasi' => 'bg-green-100 text-green-700',
                        'Marketing' => 'bg-orange-100 text-orange-700',
                        default => 'bg-gray-100 text-gray-600'
                    } ?>"><?= esc($task['kategori_nama']) ?></span>
                <?php endif; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <form action="<?= base_url('siswa/jurnal-pkl/simpan'); ?>" method="POST" enctype="multipart/form-data" id="pklForm">
                        <?= csrf_field(); ?>

                        <!-- Tanggal -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                Tanggal
                            </label>
                            <input type="date"
                                   name="tanggal"
                                   value="<?= old('tanggal', date('Y-m-d')); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
                            <p class="text-xs text-gray-400 mt-1">Tanggal otomatis hari ini</p>
                        </div>

                        <!-- Pilih Task -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tasks mr-2 text-purple-500"></i>
                                Pilih Task <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <select name="task_id" id="taskSelect"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent <?= old('task_choice') === 'new' ? 'hidden' : '' ?>">
                                    <option value="">-- Pilih Task --</option>
                                    <?php if (!empty($tasks)): ?>
                                    <optgroup label="Task Aktif">
                                        <?php foreach ($tasks as $task): ?>
                                        <option value="<?= $task['id'] ?>" <?= old('task_id') == $task['id'] ? 'selected' : '' ?>>
                                            <?= esc($task['judul']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <?php endif; ?>
                                    <?php if (!empty($taskTemplates)): ?>
                                    <optgroup label="Task Dari Instruktur">
                                        <?php foreach ($taskTemplates as $tpl): ?>
                                        <option value="tpl:<?= $tpl['id'] ?>" <?= old('task_id') == 'tpl:' . $tpl['id'] ? 'selected' : '' ?>>
                                            <?= esc($tpl['judul']) ?><?= !empty($tpl['kategori_nama']) ? ' (' . esc($tpl['kategori_nama']) . ')' : '' ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <input type="hidden" name="task_choice" id="taskChoice" value="<?= old('task_choice', 'existing') ?>">
                        </div>

                        <!-- Task Baru (Hidden by default) -->
                        <div id="newTaskSection" class="mb-5 <?= old('task_choice') === 'new' ? '' : 'hidden' ?>">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-plus-circle mr-1 text-green-500"></i>
                                    Task Baru
                                </h3>
                                <div class="space-y-3">
                                    <div>
                                        <input type="text"
                                               name="judul"
                                               value="<?= old('judul'); ?>"
                                               placeholder="Nama task (contoh: Desain Brosur)"
                                               maxlength="255"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    </div>
                                    <div>
                                        <select name="kategori_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                            <option value="">-- Kategori (opsional) --</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['id'] ?>" <?= old('kategori_id') == $cat['id'] ? 'selected' : '' ?>>
                                                <?= esc($cat['nama']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="text" name="estimasi"
                                               value="<?= old('estimasi'); ?>"
                                               placeholder="Contoh: 3 hari, 1 minggu"
                                               maxlength="30"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                                    </div>
                                </div>
                                <button type="button" onclick="switchToExisting()"
                                        class="mt-3 text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-arrow-left mr-1"></i>Pilih task yang sudah ada
                                </button>
                            </div>
                        </div>

                        <!-- Toggle New/Existing -->
                        <div id="toggleSection" class="mb-5 <?= old('task_choice') === 'new' ? 'hidden' : '' ?>">
                            <button type="button" onclick="switchToNew()"
                                    class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors text-xs font-medium">
                                <i class="fas fa-plus mr-1"></i>
                                Task Baru
                            </button>
                        </div>

                        <!-- Langkah Kerja (Perencanaan & Persiapan) -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-list-ol mr-2 text-indigo-500"></i>
                                Langkah Kerja <span class="text-gray-400 font-normal">(Perencanaan & Persiapan)</span>
                            </label>
                            <div id="langkahKerjaContainer" class="space-y-2">
                                <?php
                                $langkah = old('langkah_kerja');
                                if ($langkah && is_array($langkah)):
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
                            <p class="text-xs text-gray-400 mt-1">Isi langkah persiapan sebelum mengerjakan</p>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-green-500"></i>
                                Aktivitas Hari Ini <span class="text-red-500">*</span>
                            </label>
                            <textarea name="deskripsi"
                                      rows="4"
                                      required
                                      minlength="3"
                                      placeholder="Hari ini saya..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= old('deskripsi'); ?></textarea>
                            <p class="text-xs text-gray-400 mt-1">Minimal 3 karakter</p>
                        </div>

                        <!-- Foto -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-camera mr-2 text-yellow-500"></i>
                                Foto Dokumentasi <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
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
                                    <p class="text-xs text-gray-500">JPG, JPEG, PNG atau WebP (Max. 5MB)</p>
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
                                <span id="submitText">Simpan</span>
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
            <div class="bg-white rounded-lg shadow p-5 mb-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Panduan
                </h3>
                <div class="space-y-2 text-xs text-gray-600">
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">1</span>
                        <p>Pilih task yang sedang dikerjakan</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">2</span>
                        <p>Buat task baru jika belum ada</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">3</span>
                        <p>Tuliskan aktivitas hari ini</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">4</span>
                        <p>Upload foto (opsional)</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg shadow p-5 text-white">
                <h3 class="text-sm font-semibold mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>Tips
                </h3>
                <ul class="text-xs space-y-1.5 opacity-90">
                    <li><i class="fas fa-check mr-1"></i>Isi setiap hari setelah selesai kegiatan</li>
                    <li><i class="fas fa-check mr-1"></i>Deskripsikan aktivitas dengan jelas</li>
                    <li><i class="fas fa-check mr-1"></i>Anda bisa mengisi lebih dari 1 aktivitas per hari</li>
                    <li><i class="fas fa-check mr-1"></i>1 task bisa berlangsung beberapa hari</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function selectTask(taskId) {
    const select = document.getElementById('taskSelect');
    select.value = taskId;
    select.dispatchEvent(new Event('change'));

    document.querySelectorAll('.task-card').forEach(card => {
        card.classList.remove('border-blue-500', 'bg-blue-50');
        card.classList.add('border-transparent');
    });
    const selectedCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('border-blue-500', 'bg-blue-50');
        selectedCard.classList.remove('border-transparent');
    }
}

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
function switchToNew() {
    document.getElementById('taskSelect').classList.add('hidden');
    document.getElementById('toggleSection').classList.add('hidden');
    document.getElementById('newTaskSection').classList.remove('hidden');
    document.getElementById('taskChoice').value = 'new';
    resetLangkahKerja();
}

function switchToExisting() {
    document.getElementById('newTaskSection').classList.add('hidden');
    document.getElementById('taskSelect').classList.remove('hidden');
    document.getElementById('toggleSection').classList.remove('hidden');
    document.getElementById('taskChoice').value = 'existing';
}

function resetLangkahKerja() {
    const container = document.getElementById('langkahKerjaContainer');
    container.innerHTML = `
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
    `;
}

// Handle template selection (tpl:ID) and task selection for langkah_kerja auto-fill
document.getElementById('taskSelect').addEventListener('change', function() {
    const val = this.value;
    if (val.startsWith('tpl:')) {
        document.getElementById('taskChoice').value = 'template';
        const templateId = val.replace('tpl:', '');
        fetchLangkahKerja('template', templateId);
    } else if (val) {
        document.getElementById('taskChoice').value = 'existing';
        fetchLangkahKerja('task', val);
    } else {
        document.getElementById('taskChoice').value = 'existing';
    }
});

function fetchLangkahKerja(type, id) {
    const baseUrl = '<?= base_url('siswa/jurnal-pkl') ?>';
    let url;
    if (type === 'task') {
        url = baseUrl + '/get-task-langkah-kerja?task_id=' + id;
    } else {
        url = baseUrl + '/get-template-langkah-kerja?template_id=' + id;
    }

    fetch(url)
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data && result.data.length > 0) {
                populateLangkahKerja(result.data);
            } else {
                resetLangkahKerja();
            }
        })
        .catch(err => {
            console.error('Gagal mengambil langkah kerja:', err);
            resetLangkahKerja();
        });
}

function populateLangkahKerja(steps) {
    const container = document.getElementById('langkahKerjaContainer');
    container.innerHTML = '';

    steps.forEach((step, i) => {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 langkah-row';
        row.innerHTML = `
            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold langkah-num">${i + 1}</span>
            <input type="text" name="langkah_kerja[]" value="${step.replace(/"/g, '&quot;')}"
                   placeholder="Langkah ${i + 1}"
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
            <button type="button" onclick="removeLangkah(this)"
                    class="flex-shrink-0 w-7 h-7 rounded-full bg-red-100 text-red-500 flex items-center justify-center hover:bg-red-200 transition-colors text-xs remove-btn">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(row);
    });

    updateLangkahVisibility();
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

document.getElementById('pklForm').addEventListener('submit', function(e) {
    const choice = document.getElementById('taskChoice').value;
    if (choice === 'new') {
        const judul = document.querySelector('input[name="judul"]').value.trim();
        if (judul.length < 3) {
            e.preventDefault();
            alert('Nama task harus minimal 3 karakter!');
            return false;
        }
    } else if (choice === 'template') {
        const val = document.getElementById('taskSelect').value;
        if (!val.startsWith('tpl:')) {
            e.preventDefault();
            alert('Pilih template task terlebih dahulu!');
            return false;
        }
    } else {
        const taskId = document.getElementById('taskSelect').value;
        if (!taskId) {
            e.preventDefault();
            alert('Pilih task terlebih dahulu!');
            return false;
        }
    }
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
    if (!confirm('Simpan aktivitas ini?')) {
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
