<?= $this->extend('templates/mobile_layout') ?>

<?= $this->section('pageTitle') ?>Detail Absensi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50 pb-20">
    <!-- Mobile Header -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-6 shadow-lg">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('guru/absensi') ?>" class="text-white mr-3">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-xl font-bold text-white">Detail Absensi</h1>
                <p class="text-blue-100 text-xs mt-1">Informasi data absensi</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <div class="px-4 pt-4">
        <?= view('components/alerts') ?>
    </div>

    <!-- Absensi Info Card -->
    <div class="px-4">
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
                        <i class="fas fa-calendar-week text-purple-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Hari & Pertemuan</p>
                        <p class="text-sm font-bold text-gray-800"><?= $absensi['hari'] ?? '-' ?> - Pertemuan Ke-<?= $absensi['pertemuan_ke'] ?></p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <i class="fas fa-book text-green-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Mata Pelajaran</p>
                        <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_mapel'] ?></p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="p-2 bg-orange-100 rounded-lg mr-3">
                        <i class="fas fa-school text-orange-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Kelas</p>
                        <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_kelas'] ?></p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="p-2 bg-teal-100 rounded-lg mr-3">
                        <i class="fas fa-user-tie text-teal-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Guru</p>
                        <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_guru'] ?></p>
                    </div>
                </div>

                <div class="flex items-start">
                    <div class="p-2 bg-red-100 rounded-lg mr-3">
                        <i class="fas fa-clock text-red-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Jam Pelajaran</p>
                        <p class="text-sm font-bold text-gray-800"><?= $absensi['jam_mulai'] ?> - <?= $absensi['jam_selesai'] ?></p>
                    </div>
                </div>

                <?php if (isset($absensi['guru_pengganti_nama'])): ?>
                <div class="flex items-start">
                    <div class="p-2 bg-pink-100 rounded-lg mr-3">
                        <i class="fas fa-user-clock text-pink-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">Guru Pengganti</p>
                        <p class="text-sm font-bold text-gray-800"><?= $absensi['guru_pengganti_nama'] ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-chart-pie mr-2"></i>
                    Statistik Kehadiran
                </h2>
            </div>
            <div class="p-4">
                <!-- Total Students -->
                <div class="mb-4 p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl text-center">
                    <p class="text-xs text-gray-600 font-medium mb-1">Total Siswa</p>
                    <p class="text-3xl font-bold text-blue-600"><?= count($absensiDetails) ?></p>
                </div>

                <!-- Hadir -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Hadir
                        </span>
                        <strong class="text-base text-gray-800"><?= $statistics['hadir'] ?></strong>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300" style="width: <?= $statistics['percentage'] ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 text-right font-bold"><?= $statistics['percentage'] ?>%</p>
                </div>

                <!-- Other Stats -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                        <span class="text-xs font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-envelope text-blue-500 mr-2"></i> Izin
                        </span>
                        <strong class="text-base text-blue-700"><?= $statistics['izin'] ?></strong>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                        <span class="text-xs font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-hospital text-yellow-500 mr-2"></i> Sakit
                        </span>
                        <strong class="text-base text-yellow-700"><?= $statistics['sakit'] ?></strong>
                    </div>

                    <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                        <span class="text-xs font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-times-circle text-red-500 mr-2"></i> Alpa
                        </span>
                        <strong class="text-base text-red-700"><?= $statistics['alpa'] ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <a href="<?= base_url('guru/jurnal/tambah/' . $absensi['id']) ?>" 
                class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-4 text-center">
                <i class="fas fa-book text-white text-2xl mb-2"></i>
                <p class="text-white text-xs font-bold">Buat Jurnal</p>
            </a>
            <a href="<?= base_url('guru/absensi' . (isset($absensi['kelas_id']) ? '?kelas_id=' . $absensi['kelas_id'] : '')) ?>" 
                class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-4 text-center">
                <i class="fas fa-history text-white text-2xl mb-2"></i>
                <p class="text-white text-xs font-bold">Riwayat</p>
            </a>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 gap-2 mb-4">
            <a href="<?= base_url('guru/absensi/print/' . $absensi['id']) ?>" 
                class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-medium rounded-xl shadow-md" 
                target="_blank">
                <i class="fas fa-print mr-2"></i> Cetak Absensi
            </a>
            <?php if ($isEditable): ?>
            <a href="<?= base_url('guru/absensi/edit/' . $absensi['id']) ?>" 
                class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-medium rounded-xl shadow-md">
                <i class="fas fa-edit mr-2"></i> Edit Absensi
            </a>
            <button type="button" 
                onclick="confirmDelete(<?= $absensi['id'] ?>)"
                class="flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white font-medium rounded-xl shadow-md">
                <i class="fas fa-trash mr-2"></i> Hapus Absensi
            </button>
            <?php else: ?>
            <div class="flex items-center justify-center px-4 py-3 bg-gray-200 text-gray-600 font-medium rounded-xl">
                <i class="fas fa-lock mr-2"></i> Tidak Dapat Diedit
            </div>
            <?php endif; ?>
        </div>

        <!-- Students List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-4 py-3">
                <h2 class="text-white font-bold text-sm flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Daftar Kehadiran (<?= count($absensiDetails) ?>)
                </h2>
            </div>
            <div class="divide-y divide-gray-200">
                <?php if (empty($absensiDetails)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-users-slash text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 text-sm">Belum ada data kehadiran siswa</p>
                </div>
                <?php else: ?>
                    <?php 
                    $no = 1;
                    foreach ($absensiDetails as $detail): 
                    ?>
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-3">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                    <span class="text-white font-bold text-sm"><?= substr($detail['nama_lengkap'], 0, 1) ?></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 truncate"><?= $detail['nama_lengkap'] ?></p>
                                        <p class="text-xs text-gray-500">NIS: <?= $detail['nis'] ?></p>
                                    </div>
                                    <span class="ml-2 text-xs text-gray-500 flex items-center flex-shrink-0">
                                        <i class="far fa-clock mr-1"></i>
                                        <?= date('H:i', strtotime($detail['waktu_absen'])) ?>
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <?php
                                    $badgeClass = '';
                                    $icon = '';
                                    switch($detail['status']) {
                                        case 'hadir':
                                            $badgeClass = 'bg-green-100 text-green-800 border-green-300';
                                            $icon = 'fa-check-circle';
                                            break;
                                        case 'izin':
                                            $badgeClass = 'bg-blue-100 text-blue-800 border-blue-300';
                                            $icon = 'fa-envelope';
                                            break;
                                        case 'sakit':
                                            $badgeClass = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                                            $icon = 'fa-hospital';
                                            break;
                                        case 'alpa':
                                            $badgeClass = 'bg-red-100 text-red-800 border-red-300';
                                            $icon = 'fa-times-circle';
                                            break;
                                        default:
                                            $badgeClass = 'bg-gray-100 text-gray-800 border-gray-300';
                                            $icon = 'fa-question-circle';
                                    }
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border-2 <?= $badgeClass ?>">
                                        <i class="fas <?= $icon ?> mr-1"></i>
                                        <?= ucfirst($detail['status']) ?>
                                    </span>
                                </div>
                                <?php if (!empty($detail['keterangan'])): ?>
                                <div class="mt-2">
                                    <p class="text-xs text-gray-600 italic"><?= $detail['keterangan'] ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form action="<?= base_url('guru/absensi/delete/' . $absensi['id']) ?>" method="POST" id="formDelete">
    <?= csrf_field() ?>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data absensi ini?\n\nSemua data kehadiran siswa juga akan dihapus!')) {
        document.getElementById('formDelete').submit();
    }
}
</script>
<?= $this->endSection() ?>
