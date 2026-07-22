<?= $this->extend(get_device_layout()) ?>

<?= $this->section('styles') ?>
<style>
    /* ── Zoom in/out click effect ── */
    @keyframes btnZoom {
        0% {
            transform: scale(1);
        }

        35% {
            transform: scale(0.92);
        }

        65% {
            transform: scale(1.04);
        }

        100% {
            transform: scale(1);
        }
    }

    .zoom-click {
        animation: btnZoom 0.38s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }

    /* ── Toast Notification ── */
    #toastContainer {
        position: fixed;
        top: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: calc(100% - 32px);
        max-width: 400px;
        pointer-events: none;
    }

    .toast {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #fff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        pointer-events: all;
        animation: toastIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    .toast.toast-error {
        background: #ef4444;
    }

    .toast.toast-success {
        background: #10b981;
    }

    .toast.toast-info {
        background: #3b82f6;
    }

    .toast-icon {
        font-size: 1rem;
        flex-shrink: 0;
    }

    @keyframes toastIn {
        from {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes toastOut {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        to {
            opacity: 0;
            transform: translateY(-12px) scale(0.95);
        }
    }

    /* ── Confirm Modal ── */
    .confirm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.6);
        backdrop-filter: blur(4px);
        z-index: 9000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
        padding: 16px;
    }

    .confirm-overlay.show {
        opacity: 1;
        pointer-events: all;
    }

    .confirm-box {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        width: 100%;
        max-width: 400px;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-align: center;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .confirm-overlay.show .confirm-box {
        transform: scale(1);
    }

    .confirm-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.5rem;
    }

    .confirm-icon-wrap.blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .confirm-icon-wrap.red {
        background: #fef2f2;
        color: #ef4444;
    }

    .confirm-icon-wrap.gray {
        background: #f3f4f6;
        color: #6b7280;
    }

    .confirm-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }

    .confirm-desc {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 24px;
        line-height: 1.5;
    }

    .confirm-actions {
        display: flex;
        gap: 12px;
    }

    .confirm-cancel-btn {
        flex: 1;
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #374151;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .confirm-cancel-btn:hover {
        background: #f3f4f6;
    }

    .confirm-ok-btn {
        flex: 1;
        padding: 10px 16px;
        border-radius: 10px;
        border: none;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
    }

    .confirm-ok-btn.blue {
        background: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .confirm-ok-btn.blue:hover {
        background: #2563eb;
    }

    .confirm-ok-btn.red {
        background: #ef4444;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .confirm-ok-btn.red:hover {
        background: #dc2626;
    }

    .confirm-ok-btn:active {
        transform: scale(0.97);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Toast Container -->
<div id="toastContainer"></div>

<!-- Confirm Modal: Simpan -->
<div id="confirmSimpanOverlay" class="confirm-overlay">
    <div class="confirm-box">
        <div class="confirm-icon-wrap blue">
            <i class="fas fa-save"></i>
        </div>
        <p class="confirm-title">Simpan Perubahan?</p>
        <p class="confirm-desc">Pastikan semua data sudah benar sebelum menyimpan.</p>
        <div class="confirm-actions">
            <button class="confirm-cancel-btn" id="confirmSimpanCancelBtn">Cek Lagi</button>
            <button class="confirm-ok-btn blue" id="confirmSimpanOkBtn">
                <i class="fas fa-save" style="margin-right:6px;"></i>Ya, Simpan!
            </button>
        </div>
    </div>
</div>

<!-- Confirm Modal: Hapus -->
<div id="confirmHapusOverlay" class="confirm-overlay">
    <div class="confirm-box">
        <div class="confirm-icon-wrap red">
            <i class="fas fa-trash"></i>
        </div>
        <p class="confirm-title">Hapus Aktivitas?</p>
        <p class="confirm-desc">Tindakan ini tidak dapat dibatalkan.<br>Aktivitas akan dihapus permanen.</p>
        <div class="confirm-actions">
            <button class="confirm-cancel-btn" id="confirmHapusCancelBtn">Batal</button>
            <button class="confirm-ok-btn red" id="confirmHapusOkBtn">
                <i class="fas fa-trash" style="margin-right:6px;"></i>Ya, Hapus!
            </button>
        </div>
    </div>
</div>

<!-- Confirm Modal: Hapus Foto -->
<div id="confirmFotoOverlay" class="confirm-overlay">
    <div class="confirm-box">
        <div class="confirm-icon-wrap gray">
            <i class="fas fa-image"></i>
        </div>
        <p class="confirm-title">Hapus Foto?</p>
        <p class="confirm-desc">Foto ini akan dihapus.<br>Anda wajib mengupload foto baru.</p>
        <div class="confirm-actions">
            <button class="confirm-cancel-btn" id="confirmFotoCancelBtn">Batal</button>
            <button class="confirm-ok-btn blue" id="confirmFotoOkBtn">
                <i class="fas fa-trash" style="margin-right:6px;"></i>Hapus Foto
            </button>
        </div>
    </div>
</div>

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
                    <form action="<?= base_url('siswa/jurnal-pkl/update-progress/' . $progress['id']); ?>" method="POST"
                        enctype="multipart/form-data" id="pklForm">
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
                            <input type="date" name="tanggal" value="<?= old('tanggal', $progress['tanggal']); ?>"
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
                                            <span
                                                class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold langkah-num"><?= ($i + 1) ?></span>
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
                                        <span
                                            class="flex-shrink-0 w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold langkah-num">1</span>
                                        <input type="text" name="langkah_kerja[]" value="" placeholder="Langkah 1"
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
                            <textarea name="deskripsi" rows="4" required minlength="3" placeholder="Hari ini saya..."
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
                                    <p class="text-xs text-gray-500 mt-1">Klik <i class="fas fa-times text-red-500"></i>
                                        untuk hapus foto</p>
                                    <input type="hidden" name="hapus_foto" id="hapusFoto" value="0">
                                </div>
                            <?php endif; ?>

                            <div id="uploadArea"
                                class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 hover:bg-blue-50/50 transition-all cursor-pointer"
                                onclick="document.getElementById('foto').click()">
                                <div class="space-y-1 text-center pointer-events-none">
                                    <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <span class="font-medium text-blue-600">Klik untuk upload foto</span>
                                        <span class="pl-1">atau drag & drop</span>
                                    </div>
                                    <p class="text-xs text-gray-500">JPG, JPEG, PNG atau WebP (Max. 5MB) <span
                                            class="text-red-500 font-medium">Wajib diisi</span></p>
                                    <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png,.webp"
                                        class="sr-only" onchange="previewImage(this)">
                                    <div id="previewContainer" class="mt-3 hidden pointer-events-auto">
                                        <img id="preview" class="mx-auto max-h-40 rounded-lg shadow">
                                        <button type="button" onclick="removeImage(); event.stopPropagation();"
                                            class="mt-2 text-xs text-red-600 hover:text-red-800 pointer-events-auto">
                                            <i class="fas fa-times mr-1"></i>Hapus foto
                                        </button>
                                    </div>
                                    <p id="fileName" class="text-sm text-blue-600 font-medium mt-2"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex flex-row gap-2 md:gap-3">
                            <!-- Tombol Simpan -->
                            <button type="submit" id="submitBtn"
                                class="order-3 md:order-1 flex-1 md:flex-initial md:flex-1 h-12 md:h-auto px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-150 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-save" id="submitIcon"></i>
                                <span id="submitText">
                                    <span class="md:hidden">Simpan</span>
                                    <span class="hidden md:inline">Simpan Perubahan</span>
                                </span>
                            </button>

                            <!-- Tombol Hapus -->
                            <button type="button" onclick="confirmDelete()" id="deleteBtn"
                                class="order-2 md:order-2 w-12 h-12 md:w-auto md:h-auto md:px-6 md:py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-150 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-trash text-lg md:text-base"></i>
                                <span class="hidden md:inline">Hapus</span>
                            </button>

                            <!-- Tombol Batal -->
                            <a href="<?= base_url('siswa/jurnal-pkl'); ?>" id="cancelBtn"
                                class="order-1 md:order-3 w-12 h-12 md:w-auto md:h-auto md:px-6 md:py-3 border border-gray-300 md:border-0 bg-white md:bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 md:hover:bg-gray-300 transition-all duration-150 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-ban md:hidden text-lg"></i>
                                <i class="fas fa-times hidden md:inline"></i>
                                <span class="hidden md:inline">Batal</span>
                            </a>
                        </div>
                    </form>

                    <form id="deleteForm"
                        action="<?= base_url('siswa/jurnal-pkl/hapus-progress/' . $progress['id']); ?>" method="POST"
                        class="hidden">
                        <?= csrf_field(); ?>
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
                        <span
                            class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">i</span>
                        <p>Anda dapat memperbarui deskripsi dan foto aktivitas</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <span
                            class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">i</span>
                        <p>Task dan tanggal tidak dapat diubah</p>
                    </div>
                    <?php if ($progress['status'] === 'revision'): ?>
                        <div class="flex items-start gap-2">
                            <span
                                class="flex-shrink-0 w-5 h-5 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold">!</span>
                            <p class="text-orange-700">Aktivitas ini statusnya revisi. Setelah diedit akan kembali ke status
                                submitted (menunggu verifikasi).</p>
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
        previewAndCompress(input, {
            maxSizeMB: 5,
            previewId: 'preview',
            containerId: 'previewContainer',
            fileNameId: 'fileName',
            uploadAreaId: 'uploadArea',
        });
    }

    function removeImage() {
        document.getElementById('foto').value = '';
        document.getElementById('previewContainer').classList.add('hidden');
        document.getElementById('fileName').textContent = '';
        document.getElementById('uploadArea').classList.remove('border-green-400', 'bg-green-50/50');
        document.getElementById('uploadArea').classList.add('border-gray-300');
    }

    function removeExistingFoto() {
        document.getElementById('confirmFotoOverlay').classList.add('show');
    }

    function applyZoomEffect(element) {
        if (!element) return;
        element.classList.remove('zoom-click');
        void element.offsetWidth; // force reflow agar animasi restart
        element.classList.add('zoom-click');
        element.addEventListener('animationend', () => element.classList.remove('zoom-click'), { once: true });
    }

    // ── Toast helper ──
    function showToast(message, type = 'error') {
        const icons = { error: 'fa-circle-xmark', success: 'fa-circle-check', info: 'fa-circle-info' };
        const t = document.createElement('div');
        t.className = `toast toast-${type}`;
        t.innerHTML = `<i class="fas ${icons[type]} toast-icon"></i><span>${message}</span>`;
        document.getElementById('toastContainer').appendChild(t);
        setTimeout(() => {
            t.style.animation = 'toastOut 0.3s ease forwards';
            setTimeout(() => t.remove(), 300);
        }, 3200);
    }

    // ── Function pemicu Modal Hapus ──
    function confirmDelete() {
        applyZoomEffect(document.getElementById('deleteBtn'));
        document.getElementById('confirmHapusOverlay').classList.add('show');
    }

    // ── Zoom in/out click effect ──
    document.getElementById('submitBtn').addEventListener('click', function () {
        applyZoomEffect(this);
    });

    const cancelBtn = document.getElementById('cancelBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function (e) {
            e.preventDefault();
            applyZoomEffect(this);
            const href = this.getAttribute('href');
            setTimeout(() => { window.location.href = href; }, 150);
        });
    }

    // ── Confirm foto modal ──
    document.getElementById('confirmFotoOkBtn').addEventListener('click', function () {
        document.getElementById('confirmFotoOverlay').classList.remove('show');
        document.getElementById('existingPhoto').classList.add('hidden');
        document.getElementById('hapusFoto').value = '1';
        document.getElementById('uploadArea').classList.add('border-orange-400', 'bg-orange-50/50');
    });
    document.getElementById('confirmFotoCancelBtn').addEventListener('click', function () {
        document.getElementById('confirmFotoOverlay').classList.remove('show');
    });
    document.getElementById('confirmFotoOverlay').addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
    });

    // ── Confirm simpan modal ──
    document.getElementById('confirmSimpanOkBtn').addEventListener('click', function () {
        document.getElementById('confirmSimpanOverlay').classList.remove('show');
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.classList.add('bg-blue-400', 'cursor-not-allowed');
        
        const icon = document.getElementById('submitIcon');
        icon.classList.remove('fa-save');
        icon.classList.add('fa-spinner', 'fa-spin');
        
        // Ubah teks tanpa menghilangkan struktur responsif jika diperlukan
        document.getElementById('submitText').innerHTML = 'Menyimpan...';
        
        showToast('Menyimpan perubahan...', 'info');
        document.getElementById('pklForm').submit();
    });
    document.getElementById('confirmSimpanCancelBtn').addEventListener('click', function () {
        document.getElementById('confirmSimpanOverlay').classList.remove('show');
    });
    document.getElementById('confirmSimpanOverlay').addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
    });

    // ── Confirm hapus modal ──
    document.getElementById('confirmHapusOkBtn').addEventListener('click', function () {
        document.getElementById('confirmHapusOverlay').classList.remove('show');
        const deleteBtn = document.getElementById('deleteBtn');
        if (deleteBtn) applyZoomEffect(deleteBtn);
        setTimeout(() => document.getElementById('deleteForm').submit(), 150);
    });
    document.getElementById('confirmHapusCancelBtn').addEventListener('click', function () {
        document.getElementById('confirmHapusOverlay').classList.remove('show');
    });
    document.getElementById('confirmHapusOverlay').addEventListener('click', function (e) {
        if (e.target === this) this.classList.remove('show');
    });

    // ── Form Submit Validation ──
    document.getElementById('pklForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const deskripsi = document.querySelector('textarea[name="deskripsi"]').value.trim();
        if (deskripsi.length < 3) {
            showToast('Deskripsi harus minimal 3 karakter!', 'error');
            return;
        }
        const langkahInputs = document.querySelectorAll('input[name="langkah_kerja[]"]');
        let hasLangkah = false;
        langkahInputs.forEach(inp => { if (inp.value.trim().length > 0) hasLangkah = true; });
        if (!hasLangkah) {
            showToast('Minimal isi 1 langkah kerja!', 'error');
            return;
        }
        const fotoInput = document.getElementById('foto');
        const hapusFoto = document.getElementById('hapusFoto');
        const existingPhotoEl = document.getElementById('existingPhoto');
        const hasExistingPhoto = existingPhotoEl && !existingPhotoEl.classList.contains('hidden');
        const hasNewPhoto = fotoInput.files && fotoInput.files[0];
        const photoDeleted = hapusFoto && hapusFoto.value === '1';
        if (!hasNewPhoto && (!hasExistingPhoto || photoDeleted)) {
            showToast('Foto dokumentasi wajib diupload!', 'error');
            return;
        }
        // Semua validasi lulus → tampilkan confirm modal
        document.getElementById('confirmSimpanOverlay').classList.add('show');
    });
</script>
<?= view('components/upload_script') ?>
<?= $this->endSection() ?>