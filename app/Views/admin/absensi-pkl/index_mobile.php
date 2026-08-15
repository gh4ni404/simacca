<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-4 mb-4 rounded-b-lg mx-0 shadow-md">
        <h1 class="text-xl font-bold mb-1">
            <i class="fas fa-clipboard-check mr-2"></i> Monitoring Absensi PKL
        </h1>
        <p class="text-sm opacity-90">Pantau kehadiran siswa PKL seluruh pembimbing</p>
    </div>

    <div class="px-4">
        <?= render_flash_message() ?>

        <!-- Filter Section - Collapsible -->
        <div class="bg-white rounded-xl shadow-md mb-4 overflow-hidden">
            <div class="flex items-center justify-between p-4 bg-gray-50 cursor-pointer"
                onclick="toggleFilter()" id="filterHeader">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-500 rounded-lg mr-3">
                        <i class="fas fa-filter text-white text-sm"></i>
                    </div>
                    <h2 class="text-base font-semibold text-gray-800">Filter Data</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-600 transition-transform duration-300" id="filterToggleIcon"></i>
            </div>

            <form method="get" class="hidden p-4 pt-0" id="filterForm">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-user-tie mr-2 text-purple-500"></i>
                            Pembimbing
                        </label>
                        <select name="pembimbing_id" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Pembimbing</option>
                            <?php foreach ($pembimbingOptions as $id => $nama): ?>
                            <option value="<?= $id ?>" <?= ($filters['pembimbing_id'] ?? '') == $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Dari Tanggal
                        </label>
                        <input type="date" name="date_from" value="<?= esc($filters['date_from'] ?? '') ?>"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Sampai Tanggal
                        </label>
                        <input type="date" name="date_to" value="<?= esc($filters['date_to'] ?? '') ?>"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="<?= base_url('admin/absensi-pkl'); ?>"
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold rounded-lg active:scale-95 transition-all">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Quick Action: Set Jam Absensi -->
        <div class="bg-gradient-to-r from-cyan-500 to-blue-500 rounded-xl shadow-md p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-sm">Set Jam Absensi</h3>
                        <p class="text-cyan-100 text-xs">Atur jam masuk & pulang per pembimbing</p>
                    </div>
                </div>
                <button type="button" onclick="bulkSetWaktuAbsen()"
                        class="inline-flex items-center px-4 py-2 bg-white text-blue-600 hover:bg-blue-50 text-xs font-bold rounded-lg transition-all shadow-md flex-shrink-0 active:scale-95">
                    <i class="fas fa-clock mr-1"></i> Atur
                </button>
            </div>
        </div>

        <!-- Stats Compact -->
        <?php
        $totalKehadiran = $globalStats['total'] ?? 0;
        $totalHadir = $globalStats['hadir'] ?? 0;
        $totalIzin = $globalStats['izin'] ?? 0;
        $totalSakit = $globalStats['sakit'] ?? 0;
        $totalAlpa = $globalStats['alpa'] ?? 0;
        $persenGlobal = $globalStats['persen_kehadiran'] ?? 0;
        ?>
        <div class="bg-white rounded-xl shadow-md p-4 mb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-gray-800 flex items-center">
                    <i class="fas fa-chart-bar text-purple-500 mr-2"></i> Statistik Global
                </h3>
                <span class="text-lg font-bold <?= $persenGlobal >= 80 ? 'text-green-600' : ($persenGlobal >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persenGlobal ?>%</span>
            </div>
            <div class="grid grid-cols-5 gap-2 mb-3">
                <div class="text-center p-2 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-sm font-bold text-gray-800"><?= $totalKehadiran ?></p>
                </div>
                <div class="text-center p-2 bg-green-50 rounded-lg">
                    <p class="text-xs text-gray-500">Hadir</p>
                    <p class="text-sm font-bold text-green-600"><?= $totalHadir ?></p>
                </div>
                <div class="text-center p-2 bg-blue-50 rounded-lg">
                    <p class="text-xs text-gray-500">Izin</p>
                    <p class="text-sm font-bold text-blue-600"><?= $totalIzin ?></p>
                </div>
                <div class="text-center p-2 bg-yellow-50 rounded-lg">
                    <p class="text-xs text-gray-500">Sakit</p>
                    <p class="text-sm font-bold text-yellow-600"><?= $totalSakit ?></p>
                </div>
                <div class="text-center p-2 bg-red-50 rounded-lg">
                    <p class="text-xs text-gray-500">Alpa</p>
                    <p class="text-sm font-bold text-red-600"><?= $totalAlpa ?></p>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                     style="width: <?= $persenGlobal ?>%"></div>
            </div>
        </div>

        <!-- Rekap per Pembimbing -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-user-tie mr-2"></i>
                    Rekap per Pembimbing (<?= count($rekapPembimbing) ?>)
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($rekapPembimbing)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-user-tie text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada data pembimbing</p>
                </div>
                <?php else: ?>
                    <?php foreach ($rekapPembimbing as $rp): ?>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-blue-500 flex items-center justify-center mr-3">
                                    <span class="text-white font-bold text-sm"><?= substr($rp['nama_pembimbing'], 0, 1) ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= esc($rp['nama_pembimbing']) ?></p>
                                    <p class="text-xs text-gray-500"><?= esc($rp['nama_perusahaan'] ?? '-') ?></p>
                                </div>
                            </div>
                            <?php
                            $persen = $rp['persen_kehadiran'] ?? 0;
                            $badgeColor = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                            ?>
                            <span class="px-2 py-1 bg-<?= $badgeColor ?>-100 text-<?= $badgeColor ?>-800 text-xs font-bold rounded-full">
                                <?= $persen ?>%
                            </span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 mb-3 mt-2">
                            <div class="text-center p-1 bg-green-50 rounded-lg">
                                <p class="text-xs text-gray-500">Hadir</p>
                                <p class="text-sm font-bold text-green-600"><?= $rp['hadir'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-blue-50 rounded-lg">
                                <p class="text-xs text-gray-500">Izin</p>
                                <p class="text-sm font-bold text-blue-600"><?= $rp['izin'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-yellow-50 rounded-lg">
                                <p class="text-xs text-gray-500">Sakit</p>
                                <p class="text-sm font-bold text-yellow-600"><?= $rp['sakit'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-red-50 rounded-lg">
                                <p class="text-xs text-gray-500">Alpa</p>
                                <p class="text-sm font-bold text-red-600"><?= $rp['alpa'] ?? 0 ?></p>
                            </div>
                        </div>
                        <a href="<?= base_url('admin/absensi-pkl/rekap/' . $rp['pembimbing_pkl_id']) ?>"
                           class="flex items-center justify-center w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl active:scale-98 transition-all shadow-sm">
                            <i class="fas fa-eye mr-2"></i> Lihat Detail
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const TOTAL_ABSENSI = <?= count($rekapPembimbing) ?>;
const DEFAULT_JAM_MASUK = '<?= get_absensi_pkl_jam_masuk() ?>';
const DEFAULT_JAM_PULANG = '<?= get_absensi_pkl_jam_pulang() ?>';
const PEMBIMBING_OPTIONS = <?= json_encode($pembimbingOptions) ?>;

function toggleFilter() {
    const form = document.getElementById('filterForm');
    const icon = document.getElementById('filterToggleIcon');
    form.classList.toggle('hidden');
    icon.style.transform = form.classList.contains('hidden') ? '' : 'rotate(180deg)';
}

function bulkSetWaktuAbsen() {
    if (TOTAL_ABSENSI === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Data',
            text: 'Tidak ada data pembimbing',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }

    // Build pembimbing select options (skip empty key)
    let optionsHtml = '<option value="">-- Pilih Pembimbing --</option>';
    for (const [id, label] of Object.entries(PEMBIMBING_OPTIONS)) {
        if (id === '') continue;
        optionsHtml += `<option value="${id}">${label}</option>`;
    }

    // Step 1: Pilih Pembimbing
    Swal.fire({
        title: 'Set Jam Absensi',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-600 mb-4">Pilih pembimbing yang ingin diatur jam absensinya:</p>
                <label class="block text-xs font-medium text-gray-700 mb-1 text-left">Pembimbing</label>
                <select id="swal-pembimbing" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-left">
                    ${optionsHtml}
                </select>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="fas fa-arrow-right mr-1"></i> Selanjutnya',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-lg font-bold',
            htmlContainer: 'text-sm'
        },
        preConfirm: () => {
            const pembimbingId = document.getElementById('swal-pembimbing').value;
            if (!pembimbingId) {
                Swal.showValidationMessage('Pembimbing harus dipilih');
                return false;
            }
            return { pembimbingId, pembimbingLabel: document.getElementById('swal-pembimbing').options[document.getElementById('swal-pembimbing').selectedIndex].text };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            showSetJamAbsensi(result.value.pembimbingId, result.value.pembimbingLabel);
        }
    });
}

function showSetJamAbsensi(pembimbingId, pembimbingLabel) {
    // Step 2: Loading lalu tampilkan jam tersimpan
    Swal.fire({
        title: 'Memuat data jam...',
        html: '<i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>',
        showConfirmButton: false,
        allowOutsideClick: false,
        customClass: { popup: 'rounded-2xl' }
    });

    const formData = new FormData();
    formData.append('pembimbing_pkl_id', pembimbingId);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= base_url('admin/absensi-pkl/get-times-by-pembimbing') ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal memuat data', confirmButtonColor: '#EF4444' });
            return;
        }

        let timesHtml = '';
        if (data.times && data.times.length > 0) {
            timesHtml = `
                <div class="text-left mb-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Jam yang Tersimpan</p>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        ${data.times.map(t => `
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock text-blue-500 text-xs"></i>
                                    <span class="text-sm font-bold text-gray-800">${t.jam_masuk || '-'} &mdash; ${t.jam_pulang || '-'}</span>
                                </div>
                                <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">${t.jumlah_siswa} siswa</span>
                            </div>
                        `).join('')}
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Total <b class="text-gray-600">${data.total_hadir}</b> siswa hadir</p>
                </div>
            `;
        } else {
            timesHtml = `
                <div class="text-left mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-700"><i class="fas fa-info-circle mr-1"></i> Belum ada jam tersimpan untuk pembimbing ini</p>
                </div>
            `;
        }

        // Step 2: Tampilkan jam tersimpan + tombol set jam baru
        Swal.fire({
            title: 'Set Jam Absensi',
            html: `
                <div class="text-left">
                    <p class="text-sm text-gray-600 mb-3">Pembimbing: <b>${pembimbingLabel}</b></p>
                    ${timesHtml}
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#22C55E',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fas fa-edit mr-1"></i> Set Jam Baru',
            cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
            customClass: {
                popup: 'rounded-2xl',
                title: 'text-lg font-bold',
                htmlContainer: 'text-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                showFormSetJam(pembimbingId, pembimbingLabel);
            }
        });
    })
    .catch(() => {
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Gagal memuat data jam', confirmButtonColor: '#EF4444' });
    });
}

function showFormSetJam(pembimbingId, pembimbingLabel) {
    // Step 3: Form set jam masuk & pulang
    Swal.fire({
        title: 'Set Jam Absensi',
        html: `
            <div class="text-left">
                <p class="text-sm text-gray-600 mb-4">Atur jam masuk dan jam pulang untuk <b>${pembimbingLabel}</b>:</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jam Masuk</label>
                        <input type="time" id="swal-jam-masuk" value="${DEFAULT_JAM_MASUK}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Jam Pulang</label>
                        <input type="time" id="swal-jam-pulang" value="${DEFAULT_JAM_PULANG}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#22C55E',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Simpan!',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        customClass: {
            popup: 'rounded-2xl',
            title: 'text-lg font-bold',
            htmlContainer: 'text-sm'
        },
        preConfirm: () => {
            const jamMasuk = document.getElementById('swal-jam-masuk').value;
            const jamPulang = document.getElementById('swal-jam-pulang').value;
            if (!jamMasuk || !jamPulang) {
                Swal.showValidationMessage('Jam masuk dan jam pulang harus diisi');
                return false;
            }
            return { jamMasuk, jamPulang };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            bulkSaveWaktuAbsen(pembimbingId, result.value.jamMasuk, result.value.jamPulang);
        }
    });
}

function bulkSaveWaktuAbsen(pembimbingId, jamMasuk, jamPulang) {
    Swal.fire({
        title: 'Menyimpan...',
        html: '<i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>',
        showConfirmButton: false,
        allowOutsideClick: false,
        customClass: { popup: 'rounded-2xl' }
    });

    const formData = new FormData();
    formData.append('pembimbing_pkl_id', pembimbingId);
    formData.append('waktu_absen', jamMasuk);
    formData.append('waktu_pulang', jamPulang);
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= base_url('admin/absensi-pkl/bulk-update-waktu-by-pembimbing') ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                html: `${data.message}<br><small class="text-gray-500">Jam: ${jamMasuk} - ${jamPulang}</small>`,
                confirmButtonColor: '#22C55E',
                customClass: { popup: 'rounded-2xl' }
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: data.message || 'Terjadi kesalahan',
                confirmButtonColor: '#EF4444'
            });
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: 'Terjadi kesalahan saat menyimpan data',
            confirmButtonColor: '#EF4444'
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.bg-white.rounded-xl');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.3s ease ${index * 40}ms both`;
    });
});
</script>
<?= $this->endSection() ?>
