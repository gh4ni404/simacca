<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        -webkit-tap-highlight-color: transparent;
        background-color: #f0f2f5;
    }

    .hidden-section {
        display: none !important;
    }

    /* Segmented Picker */
    .segmented-picker {
        background-color: #e8eaed;
        border-radius: 12px;
        padding: 4px;
    }

    .segmented-picker label {
        transition: all 0.2s ease;
    }

    .segmented-picker input:checked+div {
        background-color: #ffffff;
        color: #2036bd;
        box-shadow: 0 2px 8px rgba(32, 54, 189, 0.15);
        border-radius: 10px;
        font-weight: 700;
    }

    .segmented-picker div {
        color: #6b7280;
        font-weight: 500;
        font-size: 0.9rem;
        padding: 10px 0;
        border-radius: 10px;
        text-align: center;
        transition: all 0.2s ease;
    }

    /* Section label style */
    .section-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 8px;
    }

    /* Card style */
    .card-section {
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 16px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }

    /* Date card */
    .date-card {
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }

    .date-icon {
        width: 38px;
        height: 38px;
        background: #eef0ff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2036bd;
        font-size: 1rem;
    }

    .date-label {
        font-size: 0.72rem;
        color: #9ca3af;
        font-weight: 500;
        margin-bottom: 2px;
    }

    .date-value {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a2e;
    }

    .btn-ubah {
        color: #2036bd;
        font-size: 0.85rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 8px;
        transition: background 0.2s;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .btn-ubah:hover {
        background: #eef0ff;
    }

    /* Select dropdown */
    .custom-select-wrapper {
        position: relative;
    }

    .custom-select {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 40px 12px 14px;
        appearance: none;
        -webkit-appearance: none;
        font-size: 0.9rem;
        color: #1a1a2e;
        font-weight: 500;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        cursor: pointer;
    }

    .custom-select:focus {
        border-color: #2036bd;
        box-shadow: 0 0 0 3px rgba(32, 54, 189, 0.1);
    }

    .select-arrow {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #6b7280;
        font-size: 0.75rem;
    }

    /* Textarea */
    .custom-textarea {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 0.875rem;
        color: #1a1a2e;
        outline: none;
        resize: none;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        line-height: 1.5;
    }

    .custom-textarea:focus {
        border-color: #2036bd;
        box-shadow: 0 0 0 3px rgba(32, 54, 189, 0.1);
    }

    .custom-textarea::placeholder {
        color: #b0b7c3;
    }

    /* Upload area */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 14px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        background: #fafbfc;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .upload-area:hover {
        border-color: #2036bd;
        background: #f0f2ff;
    }

    .upload-area.drag-over {
        border-color: #2036bd;
        background: #eef0ff;
    }

    .upload-icon {
        width: 48px;
        height: 48px;
        background: #2036bd;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .upload-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 2px;
    }

    .upload-subtitle {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    /* Bottom action bar */
    .bottom-bar {
        position: fixed;
        bottom: 60px; /* di atas bottom navigation (~56px) */
        left: 0;
        right: 0;
        background: #ffffff;
        border-top: 1px solid #f0f0f0;
        padding: 12px 16px;
        z-index: 45;
        box-shadow: 0 -4px 20px rgba(0,0,0,0.06);
    }

    .btn-simpan {
        width: 100%;
        background: #2036bd;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 14px 20px;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 16px rgba(32, 54, 189, 0.3);
        letter-spacing: 0.01em;
    }

    .btn-simpan:hover {
        background: #1a2fa0;
        box-shadow: 0 6px 20px rgba(32, 54, 189, 0.4);
    }

    .btn-simpan:active {
        transform: scale(0.98);
    }

    .btn-simpan:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Input normal */
    .custom-input {
        width: 100%;
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 0.875rem;
        color: #1a1a2e;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .custom-input:focus {
        border-color: #2036bd;
        box-shadow: 0 0 0 3px rgba(32, 54, 189, 0.1);
    }

    /* Langkah row */
    .langkah-row {
        transition: all 0.2s ease;
    }

    .langkah-num {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #eef0ff;
        color: #2036bd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .btn-remove-langkah {
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #fee2e2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-remove-langkah:hover {
        background: #fecaca;
    }

    .wajib-badge {
        font-size: 0.78rem;
        color: #ef4444;
        font-weight: 600;
    }

    /* Preview image container */
    .preview-container {
        background: #ffffff;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        margin-top: 10px;
    }

    /* ── Toast Notification ── */
    #toastContainer {
        position: fixed;
        top: 16px;
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
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #fff;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        pointer-events: all;
        animation: toastIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
    }

    .toast.toast-error  { background: #ef4444; }
    .toast.toast-success { background: #10b981; }
    .toast.toast-info   { background: #2036bd; }

    .toast-icon { font-size: 1rem; flex-shrink: 0; }

    @keyframes toastIn {
        from { opacity: 0; transform: translateY(-20px) scale(0.95); }
        to   { opacity: 1; transform: translateY(0)   scale(1); }
    }
    @keyframes toastOut {
        from { opacity: 1; transform: translateY(0)   scale(1); }
        to   { opacity: 0; transform: translateY(-12px) scale(0.95); }
    }

    /* ── Confirm Modal ── */
    #confirmOverlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 8000;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
    }
    #confirmOverlay.show {
        opacity: 1;
        pointer-events: all;
    }
    #confirmBox {
        background: #fff;
        border-radius: 24px 24px 0 0;
        padding: 28px 24px 32px;
        width: 100%;
        max-width: 480px;
        transform: translateY(100%);
        transition: transform 0.35s cubic-bezier(0.34,1.2,0.64,1);
        text-align: center;
    }
    #confirmOverlay.show #confirmBox {
        transform: translateY(0);
    }
    .confirm-icon-wrap {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #eef0ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.6rem;
        color: #2036bd;
    }
    .confirm-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 6px;
    }
    .confirm-desc {
        font-size: 0.83rem;
        color: #6b7280;
        margin-bottom: 22px;
        line-height: 1.5;
    }
    .confirm-actions {
        display: flex;
        gap: 10px;
    }
    .confirm-cancel {
        flex: 1;
        padding: 13px;
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        background: #f9fafb;
        color: #374151;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: background 0.2s;
    }
    .confirm-cancel:hover { background: #f3f4f6; }
    .confirm-ok {
        flex: 1;
        padding: 13px;
        border-radius: 12px;
        border: none;
        background: #2036bd;
        color: #fff;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-shadow: 0 4px 12px rgba(32,54,189,0.3);
        transition: background 0.2s, transform 0.15s;
    }
    .confirm-ok:hover  { background: #1a2fa0; }
    .confirm-ok:active { transform: scale(0.97); }

    /* ── Zoom in/out click effect ── */
    .btn-simpan { position: relative; overflow: hidden; }
    @keyframes btnZoom {
        0%   { transform: scale(1); }
        35%  { transform: scale(0.92); }
        65%  { transform: scale(1.06); }
        100% { transform: scale(1); }
    }
    .btn-simpan.zoom-click {
        animation: btnZoom 0.38s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
    }

    /* ── Button loading progress bar ── */
    .btn-simpan .btn-progress {
        position: absolute;
        left: 0; bottom: 0;
        height: 3px;
        width: 0%;
        background: rgba(255,255,255,0.5);
        border-radius: 0 0 50px 50px;
        transition: width 2.5s ease;
    }
</style>

<!-- Toast Container -->
<div id="toastContainer"></div>

<!-- Confirm Modal -->
<div id="confirmOverlay">
    <div id="confirmBox">
        <div class="confirm-icon-wrap">
            <i class="fas fa-rocket"></i>
        </div>
        <p class="confirm-title">Simpan Aktivitas?</p>
        <p class="confirm-desc">Pastikan semua data sudah benar.<br>Aktivitas yang tersimpan tidak dapat diubah kembali.</p>
        <div class="confirm-actions">
            <button class="confirm-cancel" id="confirmCancelBtn">Cek Lagi</button>
            <button class="confirm-ok" id="confirmOkBtn">
                <i class="fas fa-rocket" style="margin-right:6px;"></i>Ya, Simpan!
            </button>
        </div>
    </div>
</div>

<!-- Alerts -->
<div class="px-4 pt-3 max-w-md mx-auto">
    <?= view('components/alerts') ?>
</div>

<form action="<?= base_url('siswa/jurnal-pkl/simpan'); ?>" method="POST" enctype="multipart/form-data" id="pklForm">
    <?= csrf_field(); ?>
    <input type="hidden" name="task_choice" id="taskChoice" value="<?= old('task_choice', 'existing') ?>">

    <main style="padding: 16px 16px 160px; max-width: 480px; margin: 0 auto;">
        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Tanggal Section -->
            <div class="date-card">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="date-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="date-label">Tanggal Aktivitas</p>
                        <h2 id="tanggalDisplay" class="date-value">
                            <?= date('d F Y') ?>
                        </h2>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('tanggalPicker').click()" class="btn-ubah">
                    Ubah
                </button>
                <input type="date" id="tanggalPicker" name="tanggal" value="<?= old('tanggal', date('Y-m-d')); ?>"
                    class="sr-only" onchange="updateTanggalDisplay(this)">
            </div>

            <!-- Jenis Aktivitas -->
            <div>
                <p class="section-label">Jenis Aktivitas</p>
                <div class="segmented-picker" style="display: flex; gap: 2px;">
                    <label style="flex: 1; cursor: pointer;">
                        <input <?= old('task_choice', 'existing') !== 'new' ? 'checked' : '' ?> class="sr-only"
                            name="job_type_ui" onchange="switchToExisting()" type="radio" value="continue" />
                        <div>Lanjut Kerja</div>
                    </label>
                    <label style="flex: 1; cursor: pointer;">
                        <input <?= old('task_choice') === 'new' ? 'checked' : '' ?> class="sr-only" name="job_type_ui"
                            onchange="switchToNew()" type="radio" value="new" />
                        <div>Kerja Baru</div>
                    </label>
                </div>

                <!-- Conditional: Pilih Task yang Sudah Ada -->
                <div class="<?= old('task_choice') === 'new' ? 'hidden-section' : '' ?>" id="section-continue"
                    style="margin-top: 12px;">
                    <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 6px; font-weight: 500;">Pilih pekerjaan yang sedang berjalan</p>
                    <div class="custom-select-wrapper">
                        <select name="task_id" id="taskSelect" class="custom-select">
                            <option value="">-- Pilih Pekerjaan --</option>
                            <?php if (!empty($tasks)): ?>
                                <optgroup label="Pekerjaan Aktif">
                                    <?php foreach ($tasks as $task): ?>
                                        <option value="<?= $task['id'] ?>" <?= old('task_id') == $task['id'] ? 'selected' : '' ?>>
                                            <?= esc($task['judul']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                            <?php if (!empty($taskTemplates)): ?>
                                <optgroup label="Pekerjaan Dari Instruktur">
                                    <?php foreach ($taskTemplates as $tpl): ?>
                                        <option value="tpl:<?= $tpl['id'] ?>" <?= old('task_id') == 'tpl:' . $tpl['id'] ? 'selected' : '' ?>>
                                            <?= esc($tpl['judul']) ?>        <?= !empty($tpl['kategori_nama']) ? ' (' . esc($tpl['kategori_nama']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                        <i class="fas fa-chevron-down select-arrow"></i>
                    </div>
                </div>

                <!-- Conditional: Task Baru -->
                <div class="<?= old('task_choice') !== 'new' ? 'hidden-section' : '' ?>" id="section-new"
                    style="margin-top: 12px;">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div>
                        <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 6px; font-weight: 500;">Nama pekerjaan baru</p>
                        <input class="custom-input" id="input-job-name" type="text" name="judul"
                            value="<?= old('judul'); ?>" placeholder="E.g., Logo UMKM" maxlength="255" />
                    </div>
                    <div class="custom-select-wrapper">
                        <select name="kategori_id" class="custom-select" style="padding-right: 40px;">
                            <option value="">-- Kategori (opsional) --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= old('kategori_id') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= esc($cat['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-chevron-down select-arrow"></i>
                    </div>
                    <input type="text" name="estimasi" value="<?= old('estimasi'); ?>"
                        placeholder="Estimasi waktu, contoh: 3 hari" maxlength="30" class="custom-input">
                    </div>
                </div>
            </div>

            <!-- Langkah Kerja -->
            <div>
                <p class="section-label">
                    Langkah Kerja <span style="font-size: 0.78rem; color: #9ca3af; font-weight: 400;">(Perencanaan)</span>
                </p>
                <div id="langkahKerjaContainer" style="display: flex; flex-direction: column; gap: 8px;">
                    <?php
                    $langkah = old('langkah_kerja');
                    if ($langkah && is_array($langkah)):
                        foreach ($langkah as $i => $val): ?>
                            <div class="langkah-row" style="display: flex; align-items: center; gap: 8px;">
                                <span class="langkah-num"><?= ($i + 1) ?></span>
                                <input type="text" name="langkah_kerja[]" value="<?= esc($val) ?>"
                                    placeholder="Langkah <?= ($i + 1) ?>" class="custom-input" style="flex: 1;">
                                <button type="button" onclick="removeLangkah(this)"
                                    class="btn-remove-langkah <?= count($langkah) <= 1 ? 'hidden' : '' ?> remove-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        <?php endforeach;
                    else: ?>
                        <div class="langkah-row" style="display: flex; align-items: center; gap: 8px;">
                            <span class="langkah-num">1</span>
                            <input type="text" name="langkah_kerja[]" value="" placeholder="Langkah 1"
                                class="custom-input" style="flex: 1;">
                            <button type="button" onclick="removeLangkah(this)"
                                class="btn-remove-langkah hidden remove-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" onclick="addLangkah()"
                    style="margin-top: 10px; display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: #eef0ff; color: #2036bd; border-radius: 8px; border: none; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: background 0.2s; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <i class="fas fa-plus" style="font-size: 0.7rem;"></i> Tambah Langkah
                </button>
            </div>

            <!-- Detail Pengerjaan -->
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <p class="section-label" style="margin-bottom: 0;">Detail Pengerjaan</p>
                    <span class="wajib-badge">* Wajib</span>
                </div>
                <textarea class="custom-textarea" id="description" name="deskripsi"
                    placeholder="Ceritakan apa yang kamu kerjakan hari ini..."
                    rows="4" required minlength="3"><?= old('deskripsi'); ?></textarea>
                <p style="font-size: 0.72rem; color: #b0b7c3; margin-top: 4px;">Minimal 3 karakter</p>
            </div>

            <!-- Dokumentasi (Foto) -->
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <p class="section-label" style="margin-bottom: 0;">Dokumentasi</p>
                </div>
                <div id="uploadArea" class="upload-area" onclick="document.getElementById('foto').click()">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div>
                        <p class="upload-title">Upload Foto/File</p>
                        <p class="upload-subtitle">JPG, PNG, PDF (Maks 5MB)</p>
                    </div>
                    <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only"
                        onchange="previewImage(this)">
                </div>
                <!-- Preview Container -->
                <div id="previewContainer" class="hidden">
                    <div class="preview-container">
                        <img id="preview" class="w-full" style="max-height: 200px; object-fit: cover; display: block;">
                        <div style="padding: 10px 14px; display: flex; align-items: center; justify-content: space-between;">
                            <p id="fileName" style="font-size: 0.75rem; color: #6b7280; font-weight: 500; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></p>
                            <button type="button" onclick="removeImage(); event.stopPropagation();"
                                style="flex-shrink: 0; font-size: 0.75rem; color: #ef4444; font-weight: 700; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-times"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Bottom Action Bar -->
    <div class="bottom-bar">
        <div style="max-width: 480px; margin: 0 auto;">
            <button type="submit" id="submitBtn" class="btn-simpan">
                <i class="fas fa-rocket" id="submitIcon" style="font-size: 1rem;"></i>
                <span id="submitText">Simpan Aktivitas</span>
            </button>
        </div>
    </div>

</form>

<script>
    // --- Tanggal Display ---
    function updateTanggalDisplay(input) {
        const date = new Date(input.value + 'T00:00:00');
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('tanggalDisplay').textContent = date.toLocaleDateString('id-ID', options);
    }

    // --- Toggle Jenis Aktivitas ---
    function switchToNew() {
        document.getElementById('section-continue').classList.add('hidden-section');
        document.getElementById('section-new').classList.remove('hidden-section');
        document.getElementById('taskChoice').value = 'new';
        resetLangkahKerja();
    }

    function switchToExisting() {
        const sectionNew = document.getElementById('section-new');
        sectionNew.classList.add('hidden-section');
        document.getElementById('section-continue').classList.remove('hidden-section');
        document.getElementById('taskChoice').value = 'existing';
    }

    // --- Langkah Kerja ---
    function addLangkah() {
        const container = document.getElementById('langkahKerjaContainer');
        const count = container.querySelectorAll('.langkah-row').length + 1;
        const row = document.createElement('div');
        row.className = 'langkah-row';
        row.style.cssText = 'display: flex; align-items: center; gap: 8px;';
        row.innerHTML = `
        <span class="langkah-num">${count}</span>
        <input type="text" name="langkah_kerja[]" value=""
               placeholder="Langkah ${count}"
               class="custom-input" style="flex: 1;">
        <button type="button" onclick="removeLangkah(this)" class="btn-remove-langkah remove-btn">
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
            rows.length <= 1 ? btn.classList.add('hidden') : btn.classList.remove('hidden');
        });
    }

    function resetLangkahKerja() {
        const container = document.getElementById('langkahKerjaContainer');
        container.innerHTML = `
        <div class="langkah-row" style="display: flex; align-items: center; gap: 8px;">
            <span class="langkah-num">1</span>
            <input type="text" name="langkah_kerja[]" value=""
                   placeholder="Langkah 1"
                   class="custom-input" style="flex: 1;">
            <button type="button" onclick="removeLangkah(this)"
                    class="btn-remove-langkah hidden remove-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    }

    // --- Task Select Change ---
    document.getElementById('taskSelect').addEventListener('change', function () {
        const val = this.value;
        if (val.startsWith('tpl:')) {
            document.getElementById('taskChoice').value = 'template';
            fetchLangkahKerja('template', val.replace('tpl:', ''));
        } else if (val) {
            document.getElementById('taskChoice').value = 'existing';
            fetchLangkahKerja('task', val);
        } else {
            document.getElementById('taskChoice').value = 'existing';
        }
    });

    function fetchLangkahKerja(type, id) {
        const baseUrl = '<?= base_url('siswa/jurnal-pkl') ?>';
        const url = type === 'task'
            ? baseUrl + '/get-task-langkah-kerja?task_id=' + id
            : baseUrl + '/get-template-langkah-kerja?template_id=' + id;

        fetch(url)
            .then(r => r.json())
            .then(result => {
                if (result.success && result.data && result.data.length > 0) {
                    populateLangkahKerja(result.data);
                } else {
                    resetLangkahKerja();
                }
            })
            .catch(() => resetLangkahKerja());
    }

    function populateLangkahKerja(steps) {
        const container = document.getElementById('langkahKerjaContainer');
        container.innerHTML = '';
        steps.forEach((step, i) => {
            const row = document.createElement('div');
            row.className = 'langkah-row';
            row.style.cssText = 'display: flex; align-items: center; gap: 8px;';
            row.innerHTML = `
            <span class="langkah-num">${i + 1}</span>
            <input type="text" name="langkah_kerja[]" value="${step.replace(/"/g, '&quot;')}"
                   placeholder="Langkah ${i + 1}"
                   class="custom-input" style="flex: 1;">
            <button type="button" onclick="removeLangkah(this)"
                    class="btn-remove-langkah remove-btn">
                <i class="fas fa-times"></i>
            </button>
        `;
            container.appendChild(row);
        });
        updateLangkahVisibility();
    }

    // --- Foto Upload ---
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            if (parseFloat(fileSize) > 5) {
                alert('Ukuran file terlalu besar! Maksimal 5MB');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('previewContainer').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
            document.getElementById('fileName').textContent = file.name + ' (' + fileSize + ' MB)';
            const ua = document.getElementById('uploadArea');
            ua.style.borderColor = '#22c55e';
            ua.style.background = '#f0fdf4';
        }
    }

    function removeImage() {
        document.getElementById('foto').value = '';
        document.getElementById('previewContainer').classList.add('hidden');
        document.getElementById('fileName').textContent = '';
        const ua = document.getElementById('uploadArea');
        ua.style.borderColor = '';
        ua.style.background = '';
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

    // ── Confirm modal helper ──
    function showConfirm(onOk) {
        const overlay = document.getElementById('confirmOverlay');
        overlay.classList.add('show');
        document.getElementById('confirmOkBtn').onclick = () => {
            overlay.classList.remove('show');
            onOk();
        };
        document.getElementById('confirmCancelBtn').onclick = () => {
            overlay.classList.remove('show');
        };
        overlay.onclick = (e) => { if (e.target === overlay) overlay.classList.remove('show'); };
    }

    // ── Zoom in/out click effect ──
    document.getElementById('submitBtn').addEventListener('click', function () {
        const btn = this;
        btn.classList.remove('zoom-click');
        void btn.offsetWidth; // force reflow agar animasi restart
        btn.classList.add('zoom-click');
        btn.addEventListener('animationend', () => btn.classList.remove('zoom-click'), { once: true });
    });

    // ── Form Submit ──
    document.getElementById('pklForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const form  = this;
        const choice = document.getElementById('taskChoice').value;

        // Validasi
        if (choice === 'new') {
            const judul = document.querySelector('input[name="judul"]').value.trim();
            if (judul.length < 3) {
                showToast('Nama pekerjaan harus minimal 3 karakter!', 'error');
                return;
            }
        } else if (choice === 'template') {
            const val = document.getElementById('taskSelect').value;
            if (!val.startsWith('tpl:')) {
                showToast('Pilih template pekerjaan terlebih dahulu!', 'error');
                return;
            }
        } else {
            const taskId = document.getElementById('taskSelect').value;
            if (!taskId) {
                showToast('Pilih pekerjaan terlebih dahulu!', 'error');
                return;
            }
        }

        const deskripsi = document.querySelector('textarea[name="deskripsi"]').value.trim();
        if (deskripsi.length < 3) {
            showToast('Detail pengerjaan harus minimal 3 karakter!', 'error');
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
        if (!fotoInput.files || !fotoInput.files[0]) {
            showToast('Foto dokumentasi wajib diupload!', 'error');
            return;
        }

        // Konfirmasi dengan modal cantik
        showConfirm(() => {
            // Loading state pada tombol
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;

            // Tambah progress bar
            const bar = document.createElement('span');
            bar.className = 'btn-progress';
            btn.appendChild(bar);
            requestAnimationFrame(() => { bar.style.width = '85%'; });

            // Animasi teks & ikon
            document.getElementById('submitIcon').className = 'fas fa-spinner fa-spin';
            document.getElementById('submitText').textContent = 'Menyimpan...';
            btn.style.background = '#1a2fa0';

            showToast('Menyimpan aktivitas...', 'info');

            // Submit form
            setTimeout(() => form.submit(), 300);
        });
    });
</script>
<?= $this->endSection() ?>