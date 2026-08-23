<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
@keyframes pulse-error {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
    50% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0.15); }
}
.is-field-error {
    border-color: #ef4444 !important;
    background-color: #fef2f2 !important;
    animation: pulse-error 2s infinite;
}
</style>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 p-4 md:p-6 lg:p-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Isi Jurnal Piket Guru</h1>
                <p class="text-sm text-gray-600 mt-1">Lengkapi rincian kegiatan dan dokumentasi piket Anda hari ini</p>
            </div>
            <a href="<?= base_url('guru/jurnal-piket') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition inline-flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <?= view('components/alerts') ?>

        <form id="jurnalForm" action="<?= base_url('guru/jurnal-piket/simpan') ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <?= csrf_field() ?>

            <div class="p-6 space-y-6">
                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Piket <span class="text-red-500 font-bold">*</span>
                    </label>
                    <div class="relative">
                        <input type="date" id="tanggal" name="tanggal" 
                            value="<?= esc(old('tanggal', $tanggal ?? date('Y-m-d'))) ?>" 
                            class="w-full px-4 py-3 rounded-xl border <?= session('errors.tanggal') ? 'is-field-error border-red-500' : 'border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500' ?> text-sm transition"
                            onchange="onTanggalChange(this)">
                    </div>
                    <?php if (session('errors.tanggal')): ?>
                        <p id="error_msg_tanggal" class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1.5 animate-bounce">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= esc(session('errors.tanggal')) ?>
                        </p>
                    <?php else: ?>
                        <p id="error_msg_tanggal" class="hidden mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1.5"></p>
                    <?php endif; ?>
                </div>

                <!-- Rincian Tugas Guidelines -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="rincian_tugas" class="block text-sm font-semibold text-gray-700">
                            Rincian / Panduan Tugas Piket
                        </label>
                        <span class="text-xs text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full font-medium border border-indigo-100">Panduan Otomatis</span>
                    </div>
                    <textarea id="rincian_tugas" name="rincian_tugas" rows="4" 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50/70 text-gray-700 transition" 
                        placeholder="Rincian tugas piket..."><?= esc(old('rincian_tugas', $rincianTugas ?? '')) ?></textarea>
                    <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                        <i class="fas fa-info-circle text-indigo-500"></i> Diisi otomatis dari panduan tugas Anda. Anda dapat menyesuaikannya jika ada tugas tambahan.
                    </p>
                </div>

                <!-- Deskripsi Kegiatan -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700">
                            Deskripsi Laporan / Uraian Kegiatan Piket <span class="text-red-500 font-bold">*</span>
                        </label>
                        <span id="deskripsi_counter" class="text-xs font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 transition-all">0 karakter</span>
                    </div>
                    <textarea id="deskripsi" name="deskripsi" rows="4" 
                        placeholder="Tuliskan uraian kegiatan piket yang telah dilaksanakan hari ini (misal: penertiban gerbang pagi, monitoring keliling kelas, pencatatan izin siswa)..." 
                        class="w-full px-4 py-3 rounded-xl border <?= session('errors.deskripsi') ? 'is-field-error border-red-500' : 'border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500' ?> text-sm transition"
                        oninput="updateCharCount(this); clearFieldError(this)"><?= esc(old('deskripsi')) ?></textarea>
                    <?php if (session('errors.deskripsi')): ?>
                        <p id="error_msg_deskripsi" class="mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1.5 animate-bounce">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= esc(session('errors.deskripsi')) ?>
                        </p>
                    <?php else: ?>
                        <p id="error_msg_deskripsi" class="hidden mt-1.5 text-xs text-red-600 font-semibold flex items-center gap-1.5"></p>
                    <?php endif; ?>
                    <p id="deskripsi_hint" class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <i class="fas fa-check-circle text-indigo-500 text-[10px]"></i> Minimal 5 karakter agar laporan piket jelas dan mudah diverifikasi.
                    </p>
                </div>

                <!-- Catatan / Kejadian Khusus -->
                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan Kejadian / Kejadian Khusus <span class="text-xs font-normal text-gray-500">(Opsional)</span>
                    </label>
                    <textarea id="catatan" name="catatan" rows="3" 
                        placeholder="Contoh: Siswa terlambat 3 orang sudah dicatat dan diberi pembinaan, situasi gerbang tertib dan kondusif..." 
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm transition"><?= esc(old('catatan')) ?></textarea>
                </div>

                <!-- Foto Dokumentasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Dokumentasi Piket <span class="text-xs font-normal text-gray-500">(Opsional)</span>
                    </label>
                    <div id="foto_dropzone" class="border-2 border-dashed <?= session('errors.foto_dokumentasi') ? 'is-field-error border-red-400 bg-red-50/30' : 'border-gray-200 bg-gray-50/50 hover:border-indigo-400' ?> rounded-2xl p-6 text-center transition">
                        <input type="file" id="foto_dokumentasi" name="foto_dokumentasi" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="previewImage(this)">
                        
                        <label for="foto_dokumentasi" class="cursor-pointer block">
                            <div id="upload_placeholder">
                                <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl shadow-sm border border-indigo-100 group-hover:scale-105 transition">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-700">Klik untuk mengunggah foto dokumentasi</p>
                                <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG, WEBP &bull; Maksimal: 2 MB</p>
                            </div>
                        </label>

                        <div id="image_preview_container" class="hidden">
                            <div class="relative inline-block max-w-sm mx-auto">
                                <img id="image_preview" src="#" alt="Preview Foto" class="max-h-64 rounded-xl shadow-md border border-gray-200 object-cover mx-auto">
                                <div class="mt-3 flex items-center justify-center gap-2">
                                    <label for="foto_dokumentasi" class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition border border-indigo-200">
                                        <i class="fas fa-sync-alt"></i> Ganti Foto
                                    </label>
                                    <button type="button" onclick="cancelPhoto()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-lg transition border border-red-200">
                                        <i class="fas fa-trash-alt"></i> Hapus Foto
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (session('errors.foto_dokumentasi')): ?>
                        <p id="error_msg_foto_dokumentasi" class="mt-2 text-xs text-red-600 font-semibold flex items-center justify-center gap-1.5">
                            <i class="fas fa-exclamation-circle"></i>
                            <?= esc(session('errors.foto_dokumentasi')) ?>
                        </p>
                    <?php else: ?>
                        <p id="error_msg_foto_dokumentasi" class="hidden mt-2 text-xs text-red-600 font-semibold flex items-center justify-center gap-1.5"></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="p-6 bg-gray-50/80 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="<?= base_url('guru/jurnal-piket') ?>" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-xl transition shadow-sm">
                    Batal
                </a>
                <button type="submit" id="btnSubmit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/20 transition transform active:scale-95 inline-flex items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Jurnal Piket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateCharCount(el) {
    const counter = document.getElementById('deskripsi_counter');
    if (counter) {
        const len = el.value.trim().length;
        counter.textContent = `${len} karakter`;
        if (len === 0) {
            counter.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-500';
        } else if (len < 5) {
            counter.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-amber-100 text-amber-700 border border-amber-200';
        } else {
            counter.className = 'text-xs font-semibold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 border border-emerald-200';
        }
    }
}

function clearFieldError(el) {
    el.classList.remove('is-field-error', 'border-red-500');
    el.classList.add('border-gray-200');
    const msgEl = document.getElementById('error_msg_' + el.id);
    if (msgEl) {
        msgEl.classList.add('hidden');
        msgEl.innerHTML = '';
    }
    const generalErr = document.getElementById('general_error_box');
    if (generalErr) generalErr.classList.add('hidden');
}

function clearAllErrors() {
    document.querySelectorAll('.is-field-error').forEach(el => {
        el.classList.remove('is-field-error', 'border-red-500', 'border-red-400');
        if (el.id !== 'foto_dropzone') {
            el.classList.add('border-gray-200');
        }
    });
    document.querySelectorAll('[id^="error_msg_"]').forEach(el => {
        el.classList.add('hidden');
        el.innerHTML = '';
    });
    const generalErr = document.getElementById('general_error_box');
    if (generalErr) generalErr.classList.add('hidden');
}

function showFieldError(field, message) {
    const input = document.getElementById(field);
    const msgEl = document.getElementById('error_msg_' + field);

    if (input) {
        input.classList.add('is-field-error', 'border-red-500');
        input.classList.remove('border-gray-200');
    }
    if (msgEl) {
        msgEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
        msgEl.classList.remove('hidden');
    }
    if (field === 'foto_dokumentasi') {
        const dropzone = document.getElementById('foto_dropzone');
        if (dropzone) {
            dropzone.classList.add('is-field-error', 'border-red-400');
        }
    }
}

function cancelPhoto() {
    const input = document.getElementById('foto_dokumentasi');
    const placeholder = document.getElementById('upload_placeholder');
    const previewContainer = document.getElementById('image_preview_container');
    const preview = document.getElementById('image_preview');

    input.value = '';
    preview.src = '#';
    previewContainer.classList.add('hidden');
    placeholder.classList.remove('hidden');

    const msgEl = document.getElementById('error_msg_foto_dokumentasi');
    if (msgEl) {
        msgEl.classList.add('hidden');
        msgEl.innerHTML = '';
    }
    const dropzone = document.getElementById('foto_dropzone');
    if (dropzone) {
        dropzone.classList.remove('is-field-error', 'border-red-400');
    }
}

function previewImage(input) {
    const placeholder = document.getElementById('upload_placeholder');
    const previewContainer = document.getElementById('image_preview_container');
    const preview = document.getElementById('image_preview');

    const msgEl = document.getElementById('error_msg_foto_dokumentasi');
    if (msgEl) {
        msgEl.classList.add('hidden');
        msgEl.innerHTML = '';
    }
    const dropzone = document.getElementById('foto_dropzone');
    if (dropzone) {
        dropzone.classList.remove('is-field-error', 'border-red-400');
    }

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        // SweetAlert for invalid format
        if (!validTypes.includes(file.type)) {
            input.value = '';
            Swal.fire({
                icon: 'error',
                title: 'Format Foto Tidak Didukung',
                html: '<p class="text-sm text-gray-600">File yang Anda pilih memiliki format <b>' + (file.type || 'tidak dikenal') + '</b>.<br>Silakan pilih file berformat <b>JPG, JPEG, PNG, atau WEBP</b>.</p>',
                confirmButtonColor: '#4F46E5',
                confirmButtonText: '<i class="fas fa-redo mr-1"></i> Pilih Ulang',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-gray-100',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
                }
            });
            return;
        }

        // SweetAlert for oversize file
        if (file.size > 2 * 1024 * 1024) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            input.value = '';
            Swal.fire({
                icon: 'warning',
                title: 'Ukuran Foto Terlalu Besar',
                html: '<div class="text-center"><p class="text-sm text-gray-600">Ukuran foto: <span class="font-bold text-red-600">' + sizeMB + ' MB</span></p><p class="text-xs text-gray-500 mt-1">Batas maksimal ukuran foto yang diperbolehkan adalah <b>2 MB</b>.</p></div>',
                confirmButtonColor: '#4F46E5',
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Mengerti, Pilih Foto Lain',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-gray-100',
                    confirmButton: 'rounded-xl px-5 py-2.5 font-semibold text-sm'
                }
            });
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            placeholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

// AJAX Form Submission (No Page Reload, Preserves Selected Image)
document.getElementById('jurnalForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    clearAllErrors();

    const form = this;
    const btnSubmit = document.getElementById('btnSubmit');
    const originalBtnHtml = btnSubmit.innerHTML;

    // Client-side pre-validation
    const tanggal = document.getElementById('tanggal');
    const deskripsi = document.getElementById('deskripsi');
    let hasError = false;

    if (!tanggal || !tanggal.value) {
        showFieldError('tanggal', 'Tanggal piket wajib diisi.');
        hasError = true;
    }

    const deskripsiVal = deskripsi ? deskripsi.value.trim() : '';
    if (!deskripsiVal) {
        showFieldError('deskripsi', 'Uraian / deskripsi kegiatan piket wajib diisi.');
        hasError = true;
    } else if (deskripsiVal.length < 5) {
        showFieldError('deskripsi', 'Uraian kegiatan piket minimal 5 karakter agar informasi lebih jelas.');
        hasError = true;
    }

    if (hasError) {
        const firstErr = document.querySelector('.is-field-error');
        if (firstErr) {
            firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (typeof firstErr.focus === 'function') firstErr.focus();
        }
        return;
    }

    // Set loading state
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Menyimpan...';

    try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        // Update CSRF token if returned
        if (data.csrf_token && data.csrf_hash) {
            const csrfInput = form.querySelector('input[name="' + data.csrf_token + '"]');
            if (csrfInput) {
                csrfInput.value = data.csrf_hash;
            }
        }

        if (data.success) {
            window.location.href = data.redirect_url || '<?= base_url('guru/jurnal-piket') ?>';
            return;
        }

        // Display server-side field errors
        if (data.errors && Object.keys(data.errors).length > 0) {
            for (const [field, msg] of Object.entries(data.errors)) {
                showFieldError(field, msg);
            }
        } else if (data.message) {
            let generalErr = document.getElementById('general_error_box');
            if (!generalErr) {
                generalErr = document.createElement('div');
                generalErr.id = 'general_error_box';
                generalErr.className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm mb-6 animate-fade-in';
                form.parentNode.insertBefore(generalErr, form);
            }
            generalErr.innerHTML = '<div class="flex items-center"><div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500 text-xl"></i></div><div class="ml-3 flex-1"><p class="text-red-800 font-medium text-sm">' + data.message + '</p></div></div>';
            generalErr.classList.remove('hidden');
        }

        const firstErr = document.querySelector('.is-field-error') || document.getElementById('general_error_box');
        if (firstErr) {
            firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (typeof firstErr.focus === 'function') firstErr.focus();
        }
    } catch (err) {
        console.error('Submit error:', err);
        let generalErr = document.getElementById('general_error_box');
        if (!generalErr) {
            generalErr = document.createElement('div');
            generalErr.id = 'general_error_box';
            generalErr.className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm mb-6 animate-fade-in';
            form.parentNode.insertBefore(generalErr, form);
        }
        generalErr.innerHTML = '<div class="flex items-center"><div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500 text-xl"></i></div><div class="ml-3 flex-1"><p class="text-red-800 font-medium text-sm">Terjadi kesalahan koneksi. Silakan coba lagi.</p></div></div>';
        generalErr.classList.remove('hidden');
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = originalBtnHtml;
    }
});

function onTanggalChange(el) {
    clearFieldError(el);
    fetchRincianTugas(el.value);
}

async function fetchRincianTugas(tanggal) {
    if (!tanggal) return;
    const rincianEl = document.getElementById('rincian_tugas');
    try {
        const res = await fetch(`<?= base_url('guru/jurnal-piket/get-rincian-tugas') ?>?tanggal=${tanggal}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        if (data.success && data.rincian_tugas) {
            if (rincianEl && (!rincianEl.value || rincianEl.value.trim() === '' || rincianEl.dataset.autoFilled === 'true')) {
                rincianEl.value = data.rincian_tugas;
                rincianEl.dataset.autoFilled = 'true';
            }
        }
    } catch (e) {
        console.error('Error fetching rincian tugas:', e);
    }
}

// Auto focus and smooth scroll to first error if present on initial load
document.addEventListener('DOMContentLoaded', function() {
    const deskripsi = document.getElementById('deskripsi');
    if (deskripsi && deskripsi.value) {
        updateCharCount(deskripsi);
    }

    const rincianEl = document.getElementById('rincian_tugas');
    const tanggalEl = document.getElementById('tanggal');
    if (rincianEl && !rincianEl.value.trim() && tanggalEl && tanggalEl.value) {
        fetchRincianTugas(tanggalEl.value);
    }

    const firstInvalid = document.querySelector('.is-field-error');
    if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        firstInvalid.focus();
    }
});
</script>
<?= $this->endSection() ?>


