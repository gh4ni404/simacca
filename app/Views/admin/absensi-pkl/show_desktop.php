<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
<div class="min-h-screen">
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">Detail Absensi PKL</span>
                </h1>
                <p class="text-gray-600 mt-1 text-sm">
                    <i class="fas fa-calendar mr-1"></i> <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?>
                    &mdash; <?= esc($absensi['nama_perusahaan'] ?? '') ?>
                </p>
            </div>
        </div>
    </div>

    <?= render_flash_message() ?>

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <?php
        $statCards = [
            ['label' => 'Total', 'value' => $statistics['total'], 'icon' => 'users', 'color' => 'blue'],
            ['label' => 'Hadir', 'value' => $statistics['hadir'], 'icon' => 'user-check', 'color' => 'green'],
            ['label' => 'Izin', 'value' => $statistics['izin'], 'icon' => 'file-alt', 'color' => 'blue'],
            ['label' => 'Sakit', 'value' => $statistics['sakit'], 'icon' => 'medkit', 'color' => 'yellow'],
            ['label' => 'Alpa', 'value' => $statistics['alpa'], 'icon' => 'user-times', 'color' => 'red'],
        ];
        ?>
        <?php foreach ($statCards as $sc): ?>
        <div class="bg-white rounded-xl shadow-lg p-5 border-t-4 border-<?= $sc['color'] ?>-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500"><?= $sc['label'] ?></p>
                    <p class="text-2xl font-bold text-gray-800"><?= $sc['value'] ?></p>
                </div>
                <div class="p-3 bg-<?= $sc['color'] ?>-100 rounded-full">
                    <i class="fas fa-<?= $sc['icon'] ?> text-<?= $sc['color'] ?>-600"></i>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Detail Info -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i> Informasi Absensi</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500">Tanggal</p>
                <p class="font-semibold text-gray-900"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Pembimbing</p>
                <p class="font-semibold text-gray-900"><?= esc($absensi['nama_pembimbing'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Tempat PKL</p>
                <p class="font-semibold text-gray-900"><?= esc($absensi['nama_perusahaan'] ?? '-') ?></p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Persentase Kehadiran</p>
                <p class="font-semibold <?= ($statistics['persen_kehadiran'] ?? 0) >= 80 ? 'text-green-700' : (($statistics['persen_kehadiran'] ?? 0) >= 60 ? 'text-yellow-700' : 'text-red-700') ?>"><?= $statistics['persen_kehadiran'] ?? 0 ?>%</p>
            </div>
        </div>
        <?php if (!empty($absensi['keterangan_umum'])): ?>
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-500 mb-1">Keterangan Umum</p>
            <p class="text-sm text-gray-700"><?= esc($absensi['keterangan_umum']) ?></p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Detail Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-list mr-3"></i> Daftar Kehadiran Siswa
                </h2>
                <button type="button" onclick="bulkSetWaktuAbsen()"
                        class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-lg transition-all backdrop-blur-sm">
                    <i class="fas fa-clock mr-2"></i> Set Jam Absensi (08:00 - 16:00)
                </button>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($details)): ?>
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-users-slash text-4xl text-gray-400"></i>
                </div>
                <p class="text-gray-500">Tidak ada data kehadiran siswa</p>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                            <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Jam Pulang</th>
                            <th class="px-4 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $statusClasses = [
                            'hadir'  => 'bg-green-100 text-green-800',
                            'izin'   => 'bg-blue-100 text-blue-800',
                            'sakit'  => 'bg-yellow-100 text-yellow-800',
                            'alpa'   => 'bg-red-100 text-red-800',
                            'libur'  => 'bg-purple-100 text-purple-800',
                        ];
                        $no = 1;
                        foreach ($details as $d):
                            $waktuAbsenVal = '';
                            if (!empty($d['waktu_absen'])) {
                                $waktuAbsenVal = date('H:i', strtotime($d['waktu_absen']));
                            }
                            $waktuPulangVal = '';
                            if (!empty($d['waktu_pulang'])) {
                                $waktuPulangVal = date('H:i', strtotime($d['waktu_pulang']));
                            }
                        ?>
                        <tr class="hover:bg-gray-50" data-detail-id="<?= $d['id'] ?>">
                            <td class="px-4 py-4 text-sm text-gray-500"><?= $no++ ?></td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900"><?= esc($d['nis'] ?? '-') ?></td>
                            <td class="px-4 py-4 text-sm font-medium text-gray-900"><?= esc($d['nama_siswa']) ?></td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $statusClasses[$d['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= ucfirst($d['status']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <?php if ($d['status'] === 'hadir'): ?>
                                <input type="time" id="waktu_absen_<?= $d['id'] ?>" value="<?= $waktuAbsenVal ?>"
                                       class="px-2 py-1.5 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all time-input">
                                <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <?php if ($d['status'] === 'hadir'): ?>
                                <input type="time" id="waktu_pulang_<?= $d['id'] ?>" value="<?= $waktuPulangVal ?>"
                                       class="px-2 py-1.5 border-2 border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all time-input">
                                <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <?php if ($d['status'] === 'hadir'): ?>
                                <button type="button" onclick="saveWaktuAbsen(<?= $d['id'] ?>, <?= $absensi['id'] ?>)"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition-all shadow-sm save-btn"
                                        data-detail-id="<?= $d['id'] ?>">
                                    <i class="fas fa-save mr-1"></i> Simpan
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-8">
        <a href="<?= base_url('admin/absensi-pkl'); ?>"
           class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Monitoring
        </a>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function bulkSetWaktuAbsen() {
    const timeInputs = document.querySelectorAll('.time-input');
    const hadirRows = document.querySelectorAll('tr[data-detail-id]');
    let count = 0;
    hadirRows.forEach(row => {
        const absenInput = row.querySelector('[id^="waktu_absen_"]');
        if (absenInput) count++;
    });

    if (count === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Data',
            text: 'Tidak ada siswa dengan status hadir',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }

    Swal.fire({
        title: 'Set Jam Absensi?',
        html: `Apa kamu yakin ingin mengisi jam masuk <b>08:00</b> dan jam pulang <b>16:00</b> untuk <b>${count}</b> siswa?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Simpan!',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-lg font-bold',
            htmlContainer: 'text-sm'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            bulkSaveWaktuAbsen();
        }
    });
}

function bulkSaveWaktuAbsen() {
    Swal.fire({
        title: 'Menyimpan...',
        html: '<i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>',
        showConfirmButton: false,
        allowOutsideClick: false,
        customClass: { popup: 'rounded-2xl' }
    });

    const rows = document.querySelectorAll('tr[data-detail-id]');
    const promises = [];
    const absensiPklId = <?= $absensi['id'] ?>;

    rows.forEach(row => {
        const detailId = row.getAttribute('data-detail-id');
        const absenInput = document.getElementById('waktu_absen_' + detailId);
        const pulangInput = document.getElementById('waktu_pulang_' + detailId);

        if (!absenInput || !pulangInput) return;

        absenInput.value = '08:00';
        pulangInput.value = '16:00';

        const formData = new FormData();
        formData.append('detail_id', detailId);
        formData.append('absensi_pkl_id', absensiPklId);
        formData.append('waktu_absen', '08:00');
        formData.append('waktu_pulang', '16:00');
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        const promise = fetch('<?= base_url('admin/absensi-pkl/update-waktu') ?>', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(response => response.json());

        promises.push(promise);
    });

    Promise.all(promises).then(results => {
        const successCount = results.filter(r => r.success).length;
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            html: `Jam absensi untuk <b>${successCount}</b> siswa berhasil disimpan`,
            confirmButtonColor: '#22C55E',
            customClass: { popup: 'rounded-2xl' }
        }).then(() => {
            location.reload();
        });
    }).catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menyimpan data',
            confirmButtonColor: '#EF4444'
        });
    });
}

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
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

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
            // Highlight row briefly
            const row = document.querySelector(`[data-detail-id="${detailId}"]`);
            if (row) {
                row.classList.add('bg-green-50');
                setTimeout(() => row.classList.remove('bg-green-50'), 1500);
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
        saveBtn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
    });
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    toast.className = `fixed top-4 right-4 z-50 ${bgColor} text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-2`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span class="text-sm font-medium">${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.border-t-4');
    statCards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.3s ease ${index * 50}ms both`;
    });
});
</script>
<?= $this->endSection() ?>
