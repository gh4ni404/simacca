<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        -webkit-tap-highlight-color: transparent;
    }

    .hidden-section {
        display: none !important;
    }

    /* Segmented Picker */
    .segmented-picker {
        background-color: #f3f4f6;
        border-radius: 12px;
        padding: 4px;
        border: 1px solid #e5e7eb;
    }

    .segmented-picker label {
        transition: all 0.2s ease;
    }

    .segmented-picker input:checked+div {
        background-color: #ffffff;
        color: #3b82f6;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
        border-radius: 8px;
        font-weight: 700;
    }

    .segmented-picker div {
        color: #4b5563;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 10px 0;
        border-radius: 8px;
        text-align: center;
        transition: all 0.2s ease;
    }

    .btn-ubah {
        padding: 6px 14px;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        color: #3b82f6;
        font-size: 0.75rem;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-ubah:hover {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.05);
    }

    /* Select dropdown */
    .custom-select-wrapper {
        position: relative;
    }

    .custom-select {
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 40px 12px 14px;
        appearance: none;
        -webkit-appearance: none;
        font-size: 0.875rem;
        color: #1f2937;
        font-weight: 500;
        outline: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .custom-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .select-arrow {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Textarea */
    .custom-textarea {
        width: 100%;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 0.875rem;
        color: #1f2937;
        outline: none;
        resize: vertical;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: all 0.2s ease;
        line-height: 1.6;
    }

    .custom-textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .custom-textarea::placeholder {
        color: #9ca3af;
    }

    /* Upload area */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 24px 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background-color: #f9fafb;
        cursor: pointer;
        transition: all 0.2s ease;
        gap: 12px;
    }

    .upload-area:hover {
        border-color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.02);
    }

    .upload-area.drag-over {
        border-color: #3b82f6;
        background: #f0f4ff;
    }

    .upload-icon {
        width: 48px;
        height: 48px;
        background-color: #eff6ff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 1.25rem;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .upload-area:hover .upload-icon {
        background-color: #3b82f6;
        color: #ffffff;
    }

    .upload-title {
        font-size: 0.875rem;
        font-weight: 700;
        color: #1f2937;
    }

    .upload-subtitle {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 2px;
    }

    .btn-simpan {
        width: 100%;
        background-color: #3b82f6;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 14px 24px;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.25);
        letter-spacing: 0.01em;
    }

    .btn-simpan:hover {
        background-color: #2563eb;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35);
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
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 10px 16px;
        font-size: 0.875rem;
        color: #1f2937;
        outline: none;
        transition: all 0.2s ease;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .custom-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* Langkah row */
    .langkah-row {
        transition: all 0.2s ease;
    }

    .langkah-num {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 9999px;
        background-color: #eff6ff;
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        border: 1px solid #dbeafe;
    }

    .btn-remove-langkah {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 9999px;
        background-color: #fef2f2;
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        border: 1px solid #fee2e2;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-remove-langkah:hover {
        background-color: #fee2e2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .wajib-badge {
        font-size: 0.75rem;
        color: #ef4444;
        font-weight: 600;
    }

    /* Preview image container */
    .preview-container {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        margin-top: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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
        animation: toastIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
    }

    .toast.toast-error  { background: #ef4444; }
    .toast.toast-success { background: #10b981; }
    .toast.toast-info   { background: #3b82f6; }

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
    #confirmOverlay.show {
        opacity: 1;
        pointer-events: all;
    }
    #confirmBox {
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        width: 100%;
        max-width: 400px;
        transform: scale(0.95);
        transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
        text-align: center;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    #confirmOverlay.show #confirmBox {
        transform: scale(1);
    }
    .confirm-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 9999px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 1.5rem;
        color: #3b82f6;
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
    .confirm-cancel {
        flex: 1;
        padding: 10px 16px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        color: #374151;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: background 0.2s;
    }
    .confirm-cancel:hover { background: #f3f4f6; }
    .confirm-ok {
        flex: 1;
        padding: 10px 16px;
        border-radius: 10px;
        border: none;
        background: #3b82f6;
        color: #fff;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
        transition: background 0.2s, transform 0.15s;
    }
    .confirm-ok:hover  { background: #2563eb; }
    .confirm-ok:active { transform: scale(0.97); }

    /* ── Zoom in/out click effect ── */
    .btn-simpan { position: relative; overflow: hidden; }
    @keyframes btnZoom {
        0%   { transform: scale(1); }
        35%  { transform: scale(0.92); }
        65%  { transform: scale(1.04); }
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

    /* ── Segmented Upload Mode ── */
    .upload-mode-picker {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2px;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 3px;
        margin-bottom: 12px;
    }
    .upload-mode-picker label {
        cursor: pointer;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .upload-mode-picker input:checked + div {
        background: #ffffff;
        color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        border-radius: 10px;
        font-weight: 700;
    }
    .upload-mode-picker div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 0;
        color: #6b7280;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    /* ── Camera Modal ── */
    .camera-overlay {
        position: fixed;
        inset: 0;
        z-index: 9998;
        background: #000000;
        display: flex;
        flex-direction: column;
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
    }
    .camera-overlay.show {
        opacity: 1;
        pointer-events: all;
    }
    .camera-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        padding-top: max(12px, env(safe-area-inset-top));
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 10;
    }
    .camera-header-title {
        color: #ffffff;
        font-size: 0.9rem;
        font-weight: 700;
    }
    .camera-close-btn {
        width: 36px;
        height: 36px;
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        transition: background 0.2s;
    }
    .camera-close-btn:hover { background: rgba(255, 255, 255, 0.25); }
    .camera-viewfinder {
        flex: 1;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000;
    }
    .camera-viewfinder video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .camera-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 32px;
        padding: 20px 16px;
        padding-bottom: max(20px, env(safe-area-inset-bottom));
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
    }
    .camera-shutter {
        width: 68px;
        height: 68px;
        border-radius: 9999px;
        border: 4px solid #ffffff;
        background: transparent;
        cursor: pointer;
        position: relative;
        transition: all 0.15s ease;
    }
    .camera-shutter::after {
        content: '';
        position: absolute;
        inset: 4px;
        border-radius: 9999px;
        background: #ffffff;
        transition: all 0.15s ease;
    }
    .camera-shutter:active { transform: scale(0.92); }
    .camera-shutter:active::after { background: #ef4444; }
    .camera-flash {
        position: absolute;
        inset: 0;
        background: #ffffff;
        z-index: 5;
        opacity: 0;
        pointer-events: none;
    }
    .camera-flash.flash {
        animation: camFlash 0.3s ease-out;
    }
    @keyframes camFlash {
        0%   { opacity: 0.8; }
        100% { opacity: 0; }
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

<!-- Camera Modal -->
<div id="cameraOverlay" class="camera-overlay">
    <div class="camera-flash" id="cameraFlash"></div>
    <div class="camera-header">
        <span class="camera-header-title">Ambil Foto Dokumentasi</span>
        <button type="button" class="camera-close-btn" onclick="closeCamera()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="camera-viewfinder">
        <video id="cameraVideo" autoplay playsinline></video>
        <canvas id="cameraCanvas" style="display: none;"></canvas>
    </div>
    <div class="camera-footer">
        <button type="button" class="camera-shutter" id="shutterBtn" onclick="capturePhoto()" aria-label="Ambil Foto"></button>
    </div>
</div>

<!-- Alerts -->
<div class="p-4 md:p-6 pb-0 max-w-6xl mx-auto">
    <?= view('components/alerts') ?>
</div>

<form action="<?= base_url('siswa/jurnal-pkl/simpan'); ?>" method="POST" enctype="multipart/form-data" id="pklForm">
    <?= csrf_field(); ?>
    <input type="hidden" name="task_choice" id="taskChoice" value="<?= old('task_choice', 'existing') ?>">

    <div class="p-4 md:p-6 max-w-6xl mx-auto">
        <!-- Header with Back Button -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center">
                <a href="<?= base_url('siswa/jurnal-pkl'); ?>" class="mr-4 w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-all shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Tambah Aktivitas PKL</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Catat progress dan dokumentasikan kegiatan magang Anda hari ini</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: Form Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-6">

                    <!-- Tanggal Section -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-medium">Tanggal Aktivitas</p>
                                <h3 id="tanggalDisplay" class="text-base font-bold text-gray-800">
                                    <?= date('d F Y') ?>
                                </h3>
                            </div>
                        </div>
                        <button type="button" onclick="const p = document.getElementById('tanggalPicker'); try { p.showPicker(); } catch(e) { p.click(); }" class="btn-ubah">
                            Ubah
                        </button>
                        <input type="date" id="tanggalPicker" name="tanggal" value="<?= old('tanggal', date('Y-m-d')); ?>"
                            style="position: absolute; width: 0; height: 0; opacity: 0; pointer-events: none;" onchange="updateTanggalDisplay(this)">
                    </div>

                    <!-- Jenis Aktivitas -->
                    <div>
                        <p class="block text-sm font-semibold text-gray-700 mb-2">Jenis Aktivitas</p>
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
                            style="margin-top: 16px;">
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
                            style="margin-top: 16px;">
                            <div style="display: flex; flex-direction: column; gap: 12px;">
                                <div>
                                    <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 6px; font-weight: 500;">Nama pekerjaan baru</p>
                                    <input class="custom-input" id="input-job-name" type="text" name="judul"
                                        value="<?= old('judul'); ?>" placeholder="E.g., Logo UMKM" maxlength="255" />
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 6px; font-weight: 500;">Kategori (opsional)</p>
                                        <div class="custom-select-wrapper">
                                            <select name="kategori_id" class="custom-select">
                                                <option value="">-- Kategori (opsional) --</option>
                                                <?php foreach ($categories as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>" <?= old('kategori_id') == $cat['id'] ? 'selected' : '' ?>>
                                                        <?= esc($cat['nama']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <i class="fas fa-chevron-down select-arrow"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.8rem; color: #6b7280; margin-bottom: 6px; font-weight: 500;">Estimasi Waktu</p>
                                        <input type="text" name="estimasi" value="<?= old('estimasi'); ?>"
                                            placeholder="Estimasi waktu, contoh: 3 hari" maxlength="30" class="custom-input">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Pengerjaan -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <label class="block text-sm font-semibold text-gray-700">Detail Pengerjaan <span class="text-red-500">*</span></label>
                            <span class="wajib-badge">* Wajib</span>
                        </div>
                        <textarea class="custom-textarea" id="description" name="deskripsi"
                            placeholder="Ceritakan secara mendalam apa yang kamu kerjakan hari ini..."
                            rows="4" required minlength="3"><?= old('deskripsi'); ?></textarea>
                        <p style="font-size: 0.72rem; color: #b0b7c3; margin-top: 4px;">Minimal 3 karakter</p>
                    </div>

                    <!-- Langkah Kerja -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Perencanaan dan Persiapan Kerja
                        </label>
                        <div id="langkahKerjaContainer" style="display: flex; flex-direction: column; gap: 10px;">
                            <?php
                            $langkah = old('langkah_kerja');
                            if ($langkah && is_array($langkah)):
                                foreach ($langkah as $i => $val): ?>
                                    <div class="langkah-row" style="display: flex; align-items: center; gap: 10px;">
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
                                <div class="langkah-row" style="display: flex; align-items: center; gap: 10px;">
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
                            style="margin-top: 12px; display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: #eef0ff; color: #2036bd; border-radius: 10px; border: none; font-size: 0.8rem; font-weight: 700; cursor: pointer; transition: background 0.2s; font-family: 'Plus Jakarta Sans', sans-serif;">
                            <i class="fas fa-plus" style="font-size: 0.7rem;"></i> Tambah Langkah
                        </button>
                    </div>

                    <!-- Dokumentasi (Foto) -->
                    <div>
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                            <label class="block text-sm font-semibold text-gray-700">Dokumentasi <span class="text-red-500">*</span></label>
                            <span class="wajib-badge">* Wajib</span>
                        </div>

                        <!-- Segmented: Kamera / Upload -->
                        <div class="upload-mode-picker">
                            <label>
                                <input type="radio" name="upload_mode" value="camera" checked class="sr-only"
                                       onchange="switchUploadMode('camera')">
                                <div><i class="fas fa-camera"></i> Ambil Foto</div>
                            </label>
                            <label>
                                <input type="radio" name="upload_mode" value="upload" class="sr-only"
                                       onchange="switchUploadMode('upload')">
                                <div><i class="fas fa-images"></i> Upload / Galeri</div>
                            </label>
                        </div>

                        <!-- Upload Area (visible in upload mode) -->
                        <div id="uploadArea" class="upload-area hidden" style="display: none;" onclick="document.getElementById('foto').click()">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div>
                                <p class="upload-title">Upload Foto</p>
                                <p class="upload-subtitle">JPG, PNG (Maks 5MB, dikompres otomatis)</p>
                            </div>
                            <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only"
                                onchange="previewImage(this)">
                        </div>

                        <!-- Camera hint (visible in camera mode) -->
                        <div id="cameraHint">
                            <div class="upload-area" id="cameraArea" onclick="openCamera()" style="cursor: pointer;">
                                <div class="upload-icon" style="background-color: #ecfdf5; color: #10b981;">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div>
                                    <p class="upload-title">Tap untuk Buka Kamera</p>
                                    <p class="upload-subtitle">Ambil foto langsung dari kamera perangkat</p>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Container -->
                        <div id="previewContainer" class="hidden">
                            <div class="preview-container">
                                <img id="preview" class="w-full" style="max-height: 220px; object-fit: cover; display: block;">
                                <div style="padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; background: #ffffff;">
                                    <p id="fileName" style="font-size: 0.75rem; color: #374151; font-weight: 600; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></p>
                                    <button type="button" onclick="removeImage(); event.stopPropagation();"
                                        style="flex-shrink: 0; font-size: 0.75rem; color: #ef4444; font-weight: 700; background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                                        <i class="fas fa-times"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-100">
                        <button type="submit" id="submitBtn" class="btn-simpan flex-1">
                            <i class="fas fa-rocket" id="submitIcon" style="font-size: 1rem;"></i>
                            <span id="submitText">Simpan Aktivitas</span>
                        </button>
                        <a href="<?= base_url('siswa/jurnal-pkl'); ?>"
                           style="padding: 14px 24px; border-radius: 50px; background-color: #f3f4f6; color: #4b5563; font-size: 0.95rem; font-weight: 700; text-align: center; transition: all 0.2s; border: none; text-decoration: none;"
                           onmouseover="this.style.backgroundColor='#e5e7eb'" onmouseout="this.style.backgroundColor='#f3f4f6'">
                            Batal
                        </a>
                    </div>

                </div>
            </div>

            <!-- Right Side: Sidebar Info -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i> Petunjuk Pengisian
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                <p class="font-bold text-gray-800">Pilih Tanggal</p>
                                <p class="mt-0.5">Pastikan tanggal sesuai dengan hari pelaksanaan aktivitas.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                <p class="font-bold text-gray-800">Jenis Aktivitas</p>
                                <p class="mt-0.5">Gunakan <strong>Lanjut Kerja</strong> jika melanjutkan pekerjaan sebelumnya, atau <strong>Kerja Baru</strong> jika memulai pekerjaan baru.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                <p class="font-bold text-gray-800">Perencanaan dan Persiapan Kerja</p>
                                <p class="mt-0.5">Tuliskan rencana langkah/tahapan yang dirancang untuk menyelesaikan tugas tersebut.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">4</span>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                <p class="font-bold text-gray-800">Detail Pengerjaan</p>
                                <p class="mt-0.5">Jelaskan kegiatan yang telah diselesaikan hari ini secara spesifik, minimal 3 karakter.</p>
                            </div>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold">5</span>
                            <div class="text-xs text-gray-600 leading-relaxed">
                                <p class="font-bold text-gray-800">Foto Dokumentasi</p>
                                <p class="mt-0.5">Wajib mengunggah foto sebagai bukti. Bisa dari galeri atau langsung ambil dari kamera.</p>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5">
                    <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-2">Informasi Status</h4>
                    <p class="text-xs text-blue-800 leading-relaxed">Setelah disimpan, aktivitas akan secara otomatis berstatus <strong>Submitted</strong> (Menunggu Verifikasi Instruktur Industri/Pembimbing).</p>
                </div>
            </div>
        </div>
    </div>
</form>

<?= view('components/upload_script') ?>
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
        row.style.cssText = 'display: flex; align-items: center; gap: 10px;';
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
        <div class="langkah-row" style="display: flex; align-items: center; gap: 10px;">
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
            row.style.cssText = 'display: flex; align-items: center; gap: 10px;';
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

    // ═══════════════════════════════════════════════
    // --- Upload Mode Switching ---
    // ═══════════════════════════════════════════════
    function switchUploadMode(mode) {
        const uploadArea = document.getElementById('uploadArea');
        const cameraHint = document.getElementById('cameraHint');
        if (mode === 'camera') {
            uploadArea.style.display = 'none';
            uploadArea.classList.add('hidden');
            cameraHint.style.display = '';
            cameraHint.classList.remove('hidden');
        } else {
            uploadArea.style.display = '';
            uploadArea.classList.remove('hidden');
            cameraHint.style.display = 'none';
            cameraHint.classList.add('hidden');
            closeCamera();
        }
    }

    // ═══════════════════════════════════════════════
    // --- Camera Capture ---
    // ═══════════════════════════════════════════════
    let cameraStream = null;
    let capturedBlob = null;

    function openCamera() {
        const overlay = document.getElementById('cameraOverlay');
        const video = document.getElementById('cameraVideo');
        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } },
            audio: false
        }).then(function(stream) {
            cameraStream = stream;
            video.srcObject = stream;
            overlay.classList.add('show');
        }).catch(function(err) {
            console.error('Camera error:', err);
            showToast('Tidak dapat mengakses kamera. Pastikan izin kamera diberikan.', 'error');
        });
    }

    function capturePhoto() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');
        const flash = document.getElementById('cameraFlash');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        flash.classList.remove('flash');
        void flash.offsetWidth;
        flash.classList.add('flash');
        canvas.toBlob(function(blob) {
            const file = new File([blob], 'camera_' + Date.now() + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
            compressImage(file, function(compressed) {
                capturedBlob = compressed;
                const url = URL.createObjectURL(compressed);
                document.getElementById('preview').src = url;
                document.getElementById('previewContainer').classList.remove('hidden');
                const origKB = (blob.size / 1024).toFixed(1);
                const compKB = (compressed.size / 1024).toFixed(1);
                document.getElementById('fileName').textContent = 'Foto Kamera (' + compKB + ' KB' + (compressed.size < blob.size ? ' | dikompres dari ' + origKB + ' KB' : '') + ')';
                const ua = document.getElementById('uploadArea');
                ua.style.borderColor = '#22c55e';
                ua.style.background = '#f0fdf4';
                closeCamera();
                showToast('Foto berhasil diambil!', 'success');
            });
        }, 'image/jpeg');
    }

    function closeCamera() {
        const overlay = document.getElementById('cameraOverlay');
        const video = document.getElementById('cameraVideo');
        if (cameraStream) {
            cameraStream.getTracks().forEach(function(t) { t.stop(); });
            cameraStream = null;
        }
        video.srcObject = null;
        overlay.classList.remove('show');
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', closeCamera);

    // ═══════════════════════════════════════════════
    // --- Foto Upload with Client-Side Compression ---
    // ═══════════════════════════════════════════════
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
        const ua = document.getElementById('uploadArea');
        ua.style.borderColor = '';
        ua.style.background = '';
        capturedBlob = null;
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
            showToast('Minimal isi 1 perencanaan dan persiapan kerja!', 'error');
            return;
        }

        const fotoInput = document.getElementById('foto');
        const hasFileUpload = fotoInput.files && fotoInput.files[0];
        const hasCaptured = capturedBlob !== null;
        if (!hasFileUpload && !hasCaptured) {
            showToast('Foto dokumentasi wajib diupload atau diambil dari kamera!', 'error');
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

            if (hasCaptured) {
                // Submit via FormData with captured blob
                const fd = new FormData(form);
                fd.delete('foto');
                fd.append('foto', capturedBlob, 'foto_dokumentasi.jpg');
                fetch(form.action, { method: 'POST', body: fd })
                    .then(r => { window.location.href = '<?= base_url('siswa/jurnal-pkl') ?>'; })
                    .catch(() => {
                        btn.disabled = false;
                        btn.style.background = '';
                        document.getElementById('submitIcon').className = 'fas fa-rocket';
                        document.getElementById('submitText').textContent = 'Simpan Aktivitas';
                        showToast('Gagal menyimpan. Coba lagi.', 'error');
                    });
            } else {
                setTimeout(() => form.submit(), 300);
            }
        });
    });
</script>
<?= $this->endSection() ?>