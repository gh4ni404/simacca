<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Mobile Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-6 shadow-lg">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('admin/absensi-pkl') ?>" class="text-white mr-3">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-white">Detail Absensi PKL</h1>
                <p class="text-blue-100 text-xs mt-1">
                    <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?> &mdash; <?= esc($absensi['nama_perusahaan'] ?? '') ?>
                </p>
            </div>
        </div>
    </div>

    <div class="px-4 pt-4">
        <?= render_flash_message() ?>

        <!-- Stats Grid -->
        <div class="grid grid-cols-3 gap-3 mb-4">
            <?php
            $statCards = [
                ['label' => 'Total', 'value' => $statistics['total'], 'color' => 'blue'],
                ['label' => 'Hadir', 'value' => $statistics['hadir'], 'color' => 'green'],
                ['label' => 'Izin', 'value' => $statistics['izin'], 'color' => 'blue'],
                ['label' => 'Sakit', 'value' => $statistics['sakit'], 'color' => 'yellow'],
                ['label' => 'Alpa', 'value' => $statistics['alpa'], 'color' => 'red'],
            ];
            ?>
            <?php foreach ($statCards as $sc): ?>
            <div class="bg-white rounded-xl shadow-md p-3 text-center border-t-2 border-<?= $sc['color'] ?>-500">
                <p class="text-xs text-gray-500"><?= $sc['label'] ?></p>
                <p class="text-xl font-bold text-<?= $sc['color'] ?>-600"><?= $sc['value'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Kehadiran Progress -->
        <div class="bg-white rounded-xl shadow-md p-4 mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-chart-line text-emerald-500 mr-2"></i> Persentase Kehadiran
                </span>
                <strong class="text-lg <?= ($statistics['persen_kehadiran'] ?? 0) >= 80 ? 'text-green-600' : (($statistics['persen_kehadiran'] ?? 0) >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $statistics['persen_kehadiran'] ?? 0 ?>%</strong>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                     style="width: <?= $statistics['persen_kehadiran'] ?? 0 ?>%"></div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informasi Absensi
                </h2>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <i class="fas fa-calendar-day text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Tanggal</p>
                        <p class="text-sm font-bold text-gray-800"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                        <i class="fas fa-user-tie text-purple-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Pembimbing</p>
                        <p class="text-sm font-bold text-gray-800"><?= esc($absensi['nama_pembimbing'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <i class="fas fa-building text-green-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Tempat PKL</p>
                        <p class="text-sm font-bold text-gray-800"><?= esc($absensi['nama_perusahaan'] ?? '-') ?></p>
                    </div>
                </div>
                <?php if (!empty($absensi['keterangan_umum'])): ?>
                <div class="flex items-start">
                    <div class="p-2 bg-gray-100 rounded-lg mr-3">
                        <i class="fas fa-sticky-note text-gray-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Keterangan Umum</p>
                        <p class="text-sm text-gray-700 italic"><?= esc($absensi['keterangan_umum']) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Students List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Daftar Kehadiran (<?= count($details) ?>)
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($details)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-users-slash text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Tidak ada data kehadiran siswa</p>
                </div>
                <?php else: ?>
                    <?php
                    $statusClasses = [
                        'hadir'  => ['bg-green-100 text-green-800 border-green-300', 'fa-check-circle'],
                        'izin'   => ['bg-blue-100 text-blue-800 border-blue-300', 'fa-file-alt'],
                        'sakit'  => ['bg-yellow-100 text-yellow-800 border-yellow-300', 'fa-medkit'],
                        'alpa'   => ['bg-red-100 text-red-800 border-red-300', 'fa-user-times'],
                    ];
                    $no = 1;
                    foreach ($details as $d):
                        $cls = $statusClasses[$d['status']] ?? ['bg-gray-100 text-gray-800 border-gray-300', 'fa-question-circle'];
                        $waktuAbsenVal = '';
                        if (!empty($d['waktu_absen'])) {
                            $waktuAbsenVal = date('H:i', strtotime($d['waktu_absen']));
                        }
                        $waktuPulangVal = '';
                        if (!empty($d['waktu_pulang'])) {
                            $waktuPulangVal = date('H:i', strtotime($d['waktu_pulang']));
                        }
                    ?>
                    <div class="p-4" data-detail-id="<?= $d['id'] ?>">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm"><?= substr($d['nama_siswa'], 0, 1) ?></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 truncate"><?= esc($d['nama_siswa']) ?></p>
                                        <p class="text-xs text-gray-500">NIS: <?= esc($d['nis'] ?? '-') ?></p>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border-2 <?= $cls[0] ?>">
                                        <i class="fas <?= $cls[1] ?> mr-1"></i>
                                        <?= ucfirst($d['status']) ?>
                                    </span>
                                </div>
                                <?php if ($d['status'] === 'hadir'): ?>
                                <div class="grid grid-cols-2 gap-2 mt-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Jam Masuk</label>
                                        <input type="time" id="waktu_absen_<?= $d['id'] ?>" value="<?= $waktuAbsenVal ?>"
                                               class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1">Jam Pulang</label>
                                        <input type="time" id="waktu_pulang_<?= $d['id'] ?>" value="<?= $waktuPulangVal ?>"
                                               class="w-full px-2 py-1.5 border-2 border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                                    </div>
                                </div>
                                <button type="button" onclick="saveWaktuAbsen(<?= $d['id'] ?>, <?= $absensi['id'] ?>)"
                                        class="mt-2 w-full flex items-center justify-center gap-1.5 py-2 px-3 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm save-btn"
                                        data-detail-id="<?= $d['id'] ?>">
                                    <i class="fas fa-save"></i> Simpan Waktu
                                </button>
                                <?php endif; ?>
                                <?php if (!empty($d['keterangan'])): ?>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-600 italic"><?= esc($d['keterangan']) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-4 mb-4">
            <a href="<?= base_url('admin/absensi-pkl') ?>"
               class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl active:bg-gray-100 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Monitoring
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function saveWaktuAbsen(detailId, absensiPklId) {
    const waktuAbsenInput = document.getElementById('waktu_absen_' + detailId);
    const waktuPulangInput = document.getElementById('waktu_pulang_' + detailId);
    const saveBtn = document.querySelector(`.save-btn[data-detail-id="${detailId}"]`);

    if (!waktuAbsenInput || !waktuPulangInput) return;

    const waktuAbsen = waktuAbsenInput.value;
    const waktuPulang = waktuPulangInput.value;

    if (!waktuAbsen && !waktuPulang) {
        showToast('Jam masuk dan jam pulang tidak boleh kosong', 'error');
        return;
    }

    // Disable button & show loading
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData();
    formData.append('detail_id', detailId);
    formData.append('absensi_pkl_id', absensiPklId);
    formData.append('waktu_absen', waktuAbsen);
    formData.append('waktu_pulang', waktuPulang);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= base_url('admin/absensi-pkl/update-waktu') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Waktu absensi berhasil diperbarui', 'success');
            // Highlight card briefly
            const card = document.querySelector(`[data-detail-id="${detailId}"]`);
            if (card) {
                card.classList.add('bg-green-50');
                setTimeout(() => card.classList.remove('bg-green-50'), 1500);
            }
        } else {
            showToast(data.message || 'Gagal menyimpan', 'error');
        }
    })
    .catch(() => {
        // Fallback: submit via form
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('admin/absensi-pkl/update-waktu') ?>';

        const fields = {
            'detail_id': detailId,
            'absensi_pkl_id': absensiPklId,
            'waktu_absen': waktuAbsen,
            'waktu_pulang': waktuPulang
        };

        for (const [key, value] of Object.entries(fields)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Simpan Waktu';
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 z-50 ${bgColor} text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-2`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span class="text-sm font-medium">${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.rounded-2xl');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 60);
    });
});
</script>
<?= $this->endSection() ?>
