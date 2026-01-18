<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm">
            <li><a href="<?= base_url('guru/dashboard') ?>" class="text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-home mr-1"></i>Dashboard</a></li>
            <li class="text-gray-400">/</li>
            <li><a href="<?= base_url('guru/absensi') ?>" class="text-blue-600 hover:text-blue-800 font-medium">Absensi</a></li>
            <li class="text-gray-400">/</li>
            <li class="text-gray-600 font-semibold">Detail</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-clipboard-check text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Detail Absensi
                    </span>
                </h1>
                <p class="text-gray-600 text-sm mt-1">Informasi lengkap data absensi siswa</p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Absensi Info Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Absensi
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <i class="fas fa-calendar-day text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Tanggal</p>
                                    <p class="text-sm font-bold text-gray-800"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                    <i class="fas fa-calendar-week text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Hari</p>
                                    <p class="text-sm font-bold text-gray-800"><?= $absensi['hari'] ?? '-' ?></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="p-2 bg-indigo-100 rounded-lg mr-3">
                                    <i class="fas fa-hashtag text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Pertemuan Ke</p>
                                    <p class="text-sm font-bold text-gray-800"><?= $absensi['pertemuan_ke'] ?></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="p-2 bg-green-100 rounded-lg mr-3">
                                    <i class="fas fa-book text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Mata Pelajaran</p>
                                    <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_mapel'] ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="p-2 bg-orange-100 rounded-lg mr-3">
                                    <i class="fas fa-school text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Kelas</p>
                                    <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_kelas'] ?></p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="p-2 bg-teal-100 rounded-lg mr-3">
                                    <i class="fas fa-user-tie text-teal-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Guru</p>
                                    <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_guru'] ?></p>
                                </div>
                            </div>
                            <?php if (!empty($absensi['guru_pengganti_id'])): ?>
                            <div class="flex items-start">
                                <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                    <i class="fas fa-user-plus text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Guru Pengganti</p>
                                    <p class="text-sm font-bold text-gray-800"><?= $absensi['nama_guru_pengganti'] ?? '-' ?></p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 mt-1">
                                        <i class="fas fa-exchange-alt mr-1"></i>
                                        Piket Pengganti
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="flex items-start">
                                <div class="p-2 bg-gray-100 rounded-lg mr-3">
                                    <i class="fas fa-clock text-gray-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-medium">Waktu Input</p>
                                    <p class="text-sm font-bold text-gray-800"><?= date('d/m/Y H:i', strtotime($absensi['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h2 class="text-white font-bold text-lg flex items-center">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Statistik Kehadiran
                    </h2>
                </div>
                <div class="p-6">
                    <!-- Hadir -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i> Hadir
                            </span>
                            <strong class="text-lg text-gray-800"><?= $statistics['hadir'] ?></strong>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-300" style="width: <?= $statistics['percentage'] ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 text-right font-bold"><?= $statistics['percentage'] ?>%</p>
                    </div>

                    <!-- Other Stats -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-envelope text-blue-500 mr-2"></i> Izin
                            </span>
                            <strong class="text-lg text-blue-700"><?= $statistics['izin'] ?></strong>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-hospital text-yellow-500 mr-2"></i> Sakit
                            </span>
                            <strong class="text-lg text-yellow-700"><?= $statistics['sakit'] ?></strong>
                        </div>

                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg">
                            <span class="text-sm font-semibold text-gray-700 flex items-center">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i> Alpa
                            </span>
                            <strong class="text-lg text-red-700"><?= $statistics['alpa'] ?></strong>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t-2 border-gray-200">
                        <div class="flex justify-between items-center p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg">
                            <span class="font-bold text-gray-800">Total Siswa</span>
                            <strong class="text-2xl text-blue-600"><?= count($absensiDetails) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-blue-500 rounded-xl mr-3">
                    <i class="fas fa-book text-white text-xl"></i>
                </div>
                <h5 class="text-lg font-bold text-gray-800">Jurnal KBM</h5>
            </div>
            <p class="text-gray-600 mb-4">Lengkapi jurnal pembelajaran untuk absensi ini</p>
            <a href="<?= base_url('guru/jurnal/tambah/' . $absensi['id']) ?>" 
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-plus mr-2"></i> Buat Jurnal
            </a>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center mb-4">
                <div class="p-3 bg-purple-500 rounded-xl mr-3">
                    <i class="fas fa-list text-white text-xl"></i>
                </div>
                <h5 class="text-lg font-bold text-gray-800">Riwayat Absensi</h5>
            </div>
            <p class="text-gray-600 mb-4">Lihat riwayat absensi kelas ini</p>
            <a href="<?= base_url('guru/absensi' . (isset($absensi['kelas_id']) ? '?kelas_id=' . $absensi['kelas_id'] : '')) ?>" 
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-history mr-2"></i> Lihat Riwayat
            </a>
        </div>
    </div>
    
    <!-- Action Buttons & Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="fas fa-users mr-2 text-blue-500"></i>
                    Daftar Kehadiran Siswa
                </h2>
                <div class="flex flex-wrap gap-2">
                    <a href="<?= base_url('guru/absensi/print/' . $absensi['id']) ?>" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5" 
                        target="_blank">
                        <i class="fas fa-print mr-2"></i> Cetak
                    </a>
                    <?php if ($isEditable): ?>
                    <a href="<?= base_url('guru/absensi/edit/' . $absensi['id']) ?>" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </a>
                    <button type="button" 
                        onclick="confirmDelete(<?= $absensi['id'] ?>)"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-trash mr-2"></i> Hapus
                    </button>
                    <?php else: ?>
                    <span class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-600 font-medium rounded-lg">
                        <i class="fas fa-lock mr-2"></i> Tidak dapat diedit (>24 jam)
                    </span>
                    <?php endif; ?>
                    <a href="<?= base_url('guru/absensi') ?>" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-medium rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($absensiDetails)): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                    <p class="text-yellow-800 font-medium">Tidak ada data kehadiran siswa.</p>
                </div>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="dataTable">
                    <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Waktu Absen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($absensiDetails as $detail): ?>
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-semibold text-gray-700"><?= $no++ ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900"><?= $detail['nis'] ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900"><?= $detail['nama_lengkap'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
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
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold border-2 <?= $badgeClass ?>">
                                    <i class="fas <?= $icon ?> mr-1.5"></i>
                                    <?= ucfirst($detail['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700"><?= $detail['keterangan'] ?? '-' ?></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center">
                                    <i class="far fa-clock text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900"><?= date('H:i', strtotime($detail['waktu_absen'])) ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<form action="<?= base_url('guru/absensi/delete/' . $absensi['id']) ?>" method="POST" id="formDelete">
    <?= csrf_field() ?>
</form>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data absensi ini?\n\nSemua data kehadiran siswa juga akan dihapus!')) {
        document.getElementById('formDelete').submit();
    }
}

// Auto print if print parameter exists
<?php 
$request = \Config\Services::request();
if ($request->getGet('print') == 'true'): 
?>
window.onload = function() {
    window.print();
}
<?php endif; ?>
</script>

<?= $this->endSection() ?>
