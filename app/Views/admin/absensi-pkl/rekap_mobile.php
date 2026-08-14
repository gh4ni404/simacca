<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Mobile Header -->
    <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-6 shadow-lg">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('admin/absensi-pkl') ?>" class="text-white mr-3">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-white">Rekap Absensi Pembimbing</h1>
                <p class="text-purple-100 text-xs mt-1">
                    <?= esc($details['nama_pembimbing'] ?? '') ?> &mdash; <?= esc($details['nama_perusahaan'] ?? '') ?>
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
                ['label' => 'Total Hari', 'value' => $statistics['total_hari'] ?? 0, 'color' => 'blue'],
                ['label' => 'Hadir', 'value' => $statistics['hadir'] ?? 0, 'color' => 'green'],
                ['label' => 'Izin', 'value' => $statistics['izin'] ?? 0, 'color' => 'blue'],
                ['label' => 'Sakit', 'value' => $statistics['sakit'] ?? 0, 'color' => 'yellow'],
                ['label' => 'Alpa', 'value' => $statistics['alpa'] ?? 0, 'color' => 'red'],
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
        <?php $persen = $statistics['persen_kehadiran'] ?? 0; ?>
        <div class="bg-white rounded-xl shadow-md p-4 mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-chart-line text-emerald-500 mr-2"></i> Persentase Kehadiran
                </span>
                <strong class="text-lg <?= $persen >= 80 ? 'text-green-600' : ($persen >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $persen ?>%</strong>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-2 rounded-full transition-all duration-300"
                     style="width: <?= $persen ?>%"></div>
            </div>
        </div>

        <!-- Info Pembimbing -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informasi Pembimbing
                </h2>
            </div>
            <div class="p-4 space-y-3">
                <div class="flex items-start">
                    <div class="p-2 bg-purple-100 rounded-lg mr-3">
                        <i class="fas fa-user-tie text-purple-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Nama Pembimbing</p>
                        <p class="text-sm font-bold text-gray-800"><?= esc($details['nama_pembimbing'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <i class="fas fa-building text-green-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Tempat PKL</p>
                        <p class="text-sm font-bold text-gray-800"><?= esc($details['nama_perusahaan'] ?? '-') ?></p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                        <i class="fas fa-users text-blue-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Total Siswa Bimbingan</p>
                        <p class="text-sm font-bold text-gray-800"><?= $details['total_siswa'] ?? 0 ?> siswa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Absensi List -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-purple-500 to-blue-600 px-4 py-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-white font-bold text-sm flex items-center">
                        <i class="fas fa-list mr-2"></i>
                        Daftar Absensi (<?= count($absensi) ?>)
                    </h2>
                    <button type="button" onclick="bulkSetWaktuAbsen()"
                            class="inline-flex items-center px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg transition-all">
                        <i class="fas fa-clock mr-1"></i> Set Jam
                    </button>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($absensi)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-clipboard-list text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada data absensi</p>
                </div>
                <?php else: ?>
                    <?php $no = 1; foreach ($absensi as $item): ?>
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <i class="fas fa-calendar-day text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($item['tanggal'])) ?></p>
                                    <p class="text-xs text-gray-500"><?= date('l', strtotime($item['tanggal'])) ?></p>
                                </div>
                            </div>
                            <?php
                            $persen = $item['persen_kehadiran'] ?? 0;
                            $badgeColor = $persen >= 80 ? 'green' : ($persen >= 60 ? 'yellow' : 'red');
                            ?>
                            <span class="px-2 py-1 bg-<?= $badgeColor ?>-100 text-<?= $badgeColor ?>-800 text-xs font-bold rounded-full">
                                <?= $persen ?>%
                            </span>
                        </div>
                        <div class="grid grid-cols-5 gap-2 mb-3 mt-2">
                            <div class="text-center p-1 bg-green-50 rounded-lg">
                                <p class="text-xs text-gray-500">Hadir</p>
                                <p class="text-sm font-bold text-green-600"><?= $item['hadir_count'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-blue-50 rounded-lg">
                                <p class="text-xs text-gray-500">Izin</p>
                                <p class="text-sm font-bold text-blue-600"><?= $item['izin_count'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-yellow-50 rounded-lg">
                                <p class="text-xs text-gray-500">Sakit</p>
                                <p class="text-sm font-bold text-yellow-600"><?= $item['sakit_count'] ?? 0 ?></p>
                            </div>
                            <div class="text-center p-1 bg-red-50 rounded-lg">
                                <p class="text-xs text-gray-500">Alpa</p>
                                <p class="text-sm font-bold text-red-600"><?= $item['alpa_count'] ?? 0 ?></p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-xs text-gray-600">
                                <span class="font-semibold"><?= $item['total_siswa'] ?? 0 ?> siswa</span>
                            </div>
                            <a href="<?= base_url('admin/absensi-pkl/detail/' . $item['id']) ?>"
                               class="flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg active:scale-95 transition-all shadow-sm">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
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
const PEMBIMBING_PKL_ID = <?= $absensi[0]['pembimbing_pkl_id'] ?? 0 ?>;
const TOTAL_ABSENSI = <?= count($absensi) ?>;
const TOTAL_SISWA = <?= $details['total_siswa'] ?? 0 ?>;

function bulkSetWaktuAbsen() {
    if (TOTAL_ABSENSI === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Data',
            text: 'Tidak ada data absensi untuk pembimbing ini',
            confirmButtonColor: '#3B82F6'
        });
        return;
    }

    Swal.fire({
        title: 'Set Jam Absensi?',
        html: `Apa kamu yakin ingin mengisi jam masuk <b>08:00</b> dan jam pulang <b>16:00</b> untuk semua siswa hadir di <b><?= esc($details['nama_pembimbing'] ?? '') ?></b>?<br><br><small class="text-gray-500">Total: <?= count($absensi) ?> hari, <?= $details['total_siswa'] ?? 0 ?> siswa</small>`,
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

    const formData = new FormData();
    formData.append('pembimbing_pkl_id', PEMBIMBING_PKL_ID);
    formData.append('waktu_absen', '08:00');
    formData.append('waktu_pulang', '16:00');
    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch('<?= base_url('admin/absensi-pkl/bulk-update-waktu') ?>', {
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
                html: data.message,
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
    const cards = document.querySelectorAll('.bg-white.rounded-xl, .bg-white.rounded-2xl');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.3s ease ${index * 40}ms both`;
    });
});
</script>
<?= $this->endSection() ?>
