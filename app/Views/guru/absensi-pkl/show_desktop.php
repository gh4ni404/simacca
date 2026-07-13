<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
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

    <!-- Info Cards -->
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
                <p class="font-semibold text-green-700"><?= $statistics['persen_kehadiran'] ?>%</p>
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
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-list mr-3"></i> Daftar Kehadiran Siswa
            </h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $statusClasses = [
                            'hadir'  => 'bg-green-100 text-green-800',
                            'izin'   => 'bg-blue-100 text-blue-800',
                            'sakit'  => 'bg-yellow-100 text-yellow-800',
                            'alpa'   => 'bg-red-100 text-red-800',
                            'dispen' => 'bg-purple-100 text-purple-800',
                        ];
                        $no = 1;
                        foreach ($details as $d):
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $no++ ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($d['nis'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= esc($d['nama_siswa']) ?></td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full <?= $statusClasses[$d['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                                    <?= ucfirst($d['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600"><?= esc($d['keterangan'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mt-8">
        <a href="<?= base_url('guru/absensi-pkl'); ?>"
           class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
        <div class="flex gap-3">
            <a href="<?= base_url('guru/absensi-pkl/edit/' . $absensi['id']); ?>"
               class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-xl shadow-lg transition-all">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <button onclick="confirmDelete(<?= $absensi['id'] ?>)"
                    class="inline-flex items-center px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl shadow-lg transition-all">
                <i class="fas fa-trash mr-2"></i> Hapus
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus absensi ini?')) {
        window.location.href = '<?= base_url('guru/absensi-pkl/hapus/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
