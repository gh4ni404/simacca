<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header Section with Back Button -->
    <div class="bg-white border-b sticky top-0 z-10">
        <div class="flex items-center justify-between p-4">
            <div class="flex items-center gap-3 flex-1">
                <a href="<?= base_url('guru/absensi-pkl') ?>" class="text-gray-700 hover:text-gray-900">
                    <i class="fas fa-chevron-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Edit Absensi PKL</h1>
                    <p class="text-xs text-gray-500"><?= esc($absensi['nama_perusahaan'] ?? '') ?> &bull; <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= render_flash_message() ?>

    <div class="p-4">
        <!-- Info Card -->
        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-4 mb-4 shadow-sm">
            <!-- Date -->
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Tanggal</p>
                    <p class="text-sm font-bold text-gray-900"><?= date('l, d M Y', strtotime($absensi['tanggal'])) ?></p>
                </div>
            </div>

            <!-- Tempat PKL -->
            <div class="flex items-start gap-3 mb-3">
                <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-building text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Tempat PKL</p>
                    <p class="text-sm font-bold text-gray-900"><?= esc($absensi['nama_perusahaan'] ?? '-') ?></p>
                </div>
            </div>

            <!-- Pembimbing -->
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 bg-pink-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-tie text-white"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-600">Pembimbing</p>
                    <p class="text-sm font-bold text-gray-900"><?= esc($absensi['nama_pembimbing'] ?? '-') ?> (<?= count($siswaList) ?> Siswa)</p>
                </div>
            </div>
        </div>

        <!-- Quick Action Buttons -->
        <div class="mb-4">
            <p class="text-sm font-bold text-gray-700 mb-2">AKSI CEPAT</p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button"
                        onclick="setAllStatus('hadir')"
                        class="flex items-center justify-center gap-2 py-3 px-4 bg-green-50 border-2 border-green-200 rounded-xl text-green-700 font-medium text-sm hover:bg-green-100 transition-all">
                    <i class="fas fa-check-circle"></i>
                    <span>Hadir Semua</span>
                </button>
                <button type="button"
                        onclick="setAllStatus('izin')"
                        class="flex items-center justify-center gap-2 py-3 px-4 bg-blue-50 border-2 border-blue-200 rounded-xl text-blue-700 font-medium text-sm hover:bg-blue-100 transition-all">
                    <i class="fas fa-file-alt"></i>
                    <span>Izin Semua</span>
                </button>
                <button type="button"
                        onclick="setAllStatus('sakit')"
                        class="flex items-center justify-center gap-2 py-3 px-4 bg-yellow-50 border-2 border-yellow-200 rounded-xl text-yellow-700 font-medium text-sm hover:bg-yellow-100 transition-all">
                    <i class="fas fa-medkit"></i>
                    <span>Sakit Semua</span>
                </button>
                <button type="button"
                        onclick="setAllStatus('alpa')"
                        class="flex items-center justify-center gap-2 py-3 px-4 bg-red-50 border-2 border-red-200 rounded-xl text-red-700 font-medium text-sm hover:bg-red-100 transition-all col-span-2">
                    <i class="fas fa-times-circle"></i>
                    <span>Alpa Semua</span>
                </button>
            </div>
        </div>

        <form action="<?= base_url('guru/absensi-pkl/update/' . $absensi['id']) ?>" method="post" id="absensiPklForm">
            <?= csrf_field() ?>
            <input type="hidden" name="pembimbing_pkl_id" value="<?= esc($absensi['pembimbing_pkl_id']) ?>">

            <!-- Students List -->
            <div class="space-y-3">
                <?php
                $existingDetails = [];
                foreach ($details as $detail) {
                    $existingDetails[$detail['siswa_id']] = $detail;
                }
                $no = 1;
                foreach ($siswaList as $siswa):
                    $sid = $siswa['siswa_id'];
                    $detail = $existingDetails[$sid] ?? null;
                    $currentStatus = $detail ? strtolower($detail['status']) : '';
                    $currentKeterangan = $detail ? ($detail['keterangan'] ?? '') : '';
                ?>

                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                        <!-- Student Header -->
                        <div class="flex items-center justify-between p-4 bg-gray-50">
                            <div class="flex items-center gap-3">
                                <?php if (!empty($siswa['foto'])): ?>
                                    <img src="<?= base_url('writable/uploads/' . $siswa['foto']) ?>"
                                         alt="<?= esc($siswa['nama_lengkap']) ?>"
                                         class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-sm">
                                        <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= esc($siswa['nama_lengkap']) ?></p>
                                    <p class="text-xs text-gray-500">NIS: <?= esc($siswa['nis'] ?? '-') ?></p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 font-medium">#<?= $no++ ?></span>
                        </div>

                        <!-- Status Buttons -->
    <div class="px-4">
                            <input type="hidden" name="siswa[<?= $sid ?>][status]" value="<?= $currentStatus ?>" class="status-input" data-siswa-id="<?= $sid ?>">

                            <div class="grid grid-cols-4 gap-1.5">
                                <!-- Hadir -->
                                <button type="button"
                                        class="status-btn flex flex-col items-center justify-center py-2.5 rounded-xl border-2 cursor-pointer transition-all active:scale-95 <?= $currentStatus === 'hadir' ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-700 border-gray-300' ?>"
                                        data-siswa-id="<?= $sid ?>" data-status="hadir"
                                        onclick="selectStatus('<?= $sid ?>', 'hadir')">
                                    <i class="fas fa-check-circle text-lg mb-1"></i>
                                    <span class="text-xs font-semibold">Hadir</span>
                                </button>

                                <!-- Izin -->
                                <button type="button"
                                        class="status-btn flex flex-col items-center justify-center py-2.5 rounded-xl border-2 cursor-pointer transition-all active:scale-95 <?= $currentStatus === 'izin' ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300' ?>"
                                        data-siswa-id="<?= $sid ?>" data-status="izin"
                                        onclick="selectStatus('<?= $sid ?>', 'izin')">
                                    <i class="fas fa-file-alt text-lg mb-1"></i>
                                    <span class="text-xs font-semibold">Izin</span>
                                </button>

                                <!-- Sakit -->
                                <button type="button"
                                        class="status-btn flex flex-col items-center justify-center py-2.5 rounded-xl border-2 cursor-pointer transition-all active:scale-95 <?= $currentStatus === 'sakit' ? 'bg-yellow-500 text-white border-yellow-500' : 'bg-white text-gray-700 border-gray-300' ?>"
                                        data-siswa-id="<?= $sid ?>" data-status="sakit"
                                        onclick="selectStatus('<?= $sid ?>', 'sakit')">
                                    <i class="fas fa-medkit text-lg mb-1"></i>
                                    <span class="text-xs font-semibold">Sakit</span>
                                </button>

                                <!-- Alpa -->
                                <button type="button"
                                        class="status-btn flex flex-col items-center justify-center py-2.5 rounded-xl border-2 cursor-pointer transition-all active:scale-95 <?= $currentStatus === 'alpa' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-700 border-gray-300' ?>"
                                        data-siswa-id="<?= $sid ?>" data-status="alpa"
                                        onclick="selectStatus('<?= $sid ?>', 'alpa')">
                                    <i class="fas fa-times-circle text-lg mb-1"></i>
                                    <span class="text-xs font-semibold">Alpa</span>
                                </button>
                            </div>

                            <!-- Keterangan Field -->
                            <div class="mt-3">
                                <textarea name="siswa[<?= $sid ?>][keterangan]"
                                          rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Keterangan (opsional)"><?= esc($currentKeterangan) ?></textarea>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 sticky bottom-20 z-10">
                <button type="submit"
                        class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-4 px-6 rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>
            </div>

            <!-- Back Link -->
            <div class="mt-4">
                <a href="<?= base_url('guru/absensi-pkl') ?>"
                   class="w-full block text-center bg-white border-2 border-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-2xl hover:bg-gray-50 transition-all">
                    Kembali ke Daftar Absensi PKL
                </a>
            </div>
        </form>
    </div>
</div>

<style>
/* Status Button Styles */
.status-btn {
    border-color: #e5e7eb;
    color: #6b7280;
    background-color: #ffffff;
}

.status-btn.active-hadir {
    border-color: #10b981;
    background-color: #10b981;
    color: #ffffff;
}

.status-btn.active-izin {
    border-color: #3b82f6;
    background-color: #3b82f6;
    color: #ffffff;
}

.status-btn.active-sakit {
    border-color: #eab308;
    background-color: #eab308;
    color: #ffffff;
}

.status-btn.active-alpa {
    border-color: #ef4444;
    background-color: #ef4444;
    color: #ffffff;
}

.status-btn:hover {
    border-color: #9ca3af;
}
</style>

<script>
function selectStatus(siswaId, status) {
    const hiddenInput = document.querySelector(`.status-input[data-siswa-id="${siswaId}"]`);
    if (hiddenInput) {
        hiddenInput.value = status;
        hiddenInput.setAttribute('data-manually-set', 'true');
    }

    const buttons = document.querySelectorAll(`.status-btn[data-siswa-id="${siswaId}"]`);
    buttons.forEach(btn => {
        const btnStatus = btn.getAttribute('data-status');
        btn.classList.remove('active-hadir', 'active-izin', 'active-sakit', 'active-alpa');
        if (btnStatus === status) {
            btn.classList.add(`active-${status}`);
        }
    });

    updateProgress();
}

function setAllStatus(status) {
    document.querySelectorAll('.status-input').forEach(input => {
        const siswaId = input.getAttribute('data-siswa-id');
        selectStatus(siswaId, status);
    });

    const labels = { hadir: 'Hadir', izin: 'Izin', sakit: 'Sakit', alpa: 'Alpa' };
    showToast(`Semua siswa di-set ${labels[status]}`);
}

function updateProgress() {
    const inputs = document.querySelectorAll('.status-input');
    let filled = 0;
    inputs.forEach(i => { if (i.getAttribute('data-manually-set') === 'true') filled++; });
    const counter = document.getElementById('mobile-progress-counter');
    if (counter) {
        counter.textContent = `${filled} / ${inputs.length} Siswa Diubah`;
    }
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}
</script>

<?= $this->endSection() ?>
