<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .print-button {
        transition: all 0.3s ease;
    }

    .print-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }

    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center flex-1">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white p-3 rounded-xl mr-4 shadow-lg">
                        <i class="fas fa-chart-bar text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">
                            <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                Laporan Absensi
                            </span>
                        </h1>
                        <p class="text-gray-600 mt-1">Rekap kehadiran siswa per kelas dan periode</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-8 animate-fade-in-up" style="animation-delay: 0.1s;">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-filter text-indigo-600 mr-3"></i>
                Filter Laporan
            </h2>

            <form method="get" action="<?= base_url('guru/laporan') ?>" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Kelas -->
                    <div>
                        <label for="kelas_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-school text-indigo-600 mr-2"></i>
                            Kelas
                        </label>
                        <select id="kelas_id" name="kelas_id" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition-all"
                                required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelasList as $id => $nama): ?>
                                <option value="<?= $id ?>" <?= $kelasId == $id ? 'selected' : '' ?>>
                                    <?= esc($nama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-green-600 mr-2"></i>
                            Tanggal Mulai
                        </label>
                        <input type="date" id="start_date" name="start_date" 
                               value="<?= $startDate ?>"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition-all"
                               required>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar-check text-red-600 mr-2"></i>
                            Tanggal Selesai
                        </label>
                        <input type="date" id="end_date" name="end_date" 
                               value="<?= $endDate ?>"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-red-200 focus:border-red-500 transition-all"
                               required>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-3 justify-end pt-4 border-t-2 border-gray-100">
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg font-medium">
                        <i class="fas fa-search mr-2"></i>
                        Tampilkan Laporan
                    </button>
                    
                    <?php if ($laporan): ?>
                    <a href="<?= base_url('guru/laporan/print?kelas_id=' . $kelasId . '&start_date=' . $startDate . '&end_date=' . $endDate) ?>" 
                       target="_blank"
                       class="print-button px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg font-medium">
                        <i class="fas fa-print mr-2"></i>
                        Print Laporan
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if ($laporan): ?>
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-fade-in-up" style="animation-delay: 0.2s;">
            <!-- Total Siswa -->
            <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?= $rekap['total_siswa'] ?></span>
                </div>
                <h3 class="text-sm font-semibold opacity-90">Total Siswa</h3>
                <p class="text-xs opacity-75 mt-1">Dalam periode ini</p>
            </div>

            <!-- Hadir -->
            <div class="stat-card bg-gradient-to-br from-green-500 to-green-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?= $rekap['total_hadir'] ?></span>
                </div>
                <h3 class="text-sm font-semibold opacity-90">Total Hadir</h3>
                <p class="text-xs opacity-75 mt-1"><?= $rekap['persentase_hadir'] ?? 0 ?>% kehadiran</p>
            </div>

            <!-- Sakit -->
            <div class="stat-card bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-bed text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?= $rekap['total_sakit'] ?></span>
                </div>
                <h3 class="text-sm font-semibold opacity-90">Total Sakit</h3>
                <p class="text-xs opacity-75 mt-1"><?= $rekap['persentase_sakit'] ?? 0 ?>% dari total</p>
            </div>

            <!-- Alpa/Izin -->
            <div class="stat-card bg-gradient-to-br from-red-500 to-red-600 text-white rounded-2xl p-6 shadow-lg">
                <div class="flex items-center justify-between mb-2">
                    <div class="bg-white/20 p-3 rounded-xl">
                        <i class="fas fa-times-circle text-2xl"></i>
                    </div>
                    <span class="text-3xl font-bold"><?= $rekap['total_alpa'] + $rekap['total_izin'] ?></span>
                </div>
                <h3 class="text-sm font-semibold opacity-90">Alpa & Izin</h3>
                <p class="text-xs opacity-75 mt-1"><?= round(($rekap['persentase_alpa'] ?? 0) + ($rekap['persentase_izin'] ?? 0), 2) ?>% dari total</p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="p-6 border-b-2 border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-table text-indigo-600 mr-3"></i>
                    Detail Absensi Siswa
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Periode: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?>
                    (<?= $rekap['total_pertemuan'] ?> pertemuan)
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider bg-green-50">
                                <i class="fas fa-check-circle text-green-600 mr-1"></i>Hadir
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider bg-yellow-50">
                                <i class="fas fa-bed text-yellow-600 mr-1"></i>Sakit
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider bg-blue-50">
                                <i class="fas fa-hand-paper text-blue-600 mr-1"></i>Izin
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider bg-red-50">
                                <i class="fas fa-times-circle text-red-600 mr-1"></i>Alpa
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fas fa-percentage text-indigo-600 mr-1"></i>Kehadiran
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $no = 1; foreach ($laporan as $row): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $no++ ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= esc($row['siswa']['nis']) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?= esc($row['siswa']['nama_lengkap']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <?= $row['hadir'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <?= $row['sakit'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= $row['izin'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    <?= $row['alpa'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php 
                                $persentase = $row['total'] > 0 ? round(($row['hadir'] / $row['total']) * 100, 1) : 0;
                                $colorClass = $persentase >= 80 ? 'text-green-600' : ($persentase >= 60 ? 'text-yellow-600' : 'text-red-600');
                                ?>
                                <span class="text-sm font-bold <?= $colorClass ?>">
                                    <?= $persentase ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-gradient-to-r from-gray-100 to-gray-200">
                        <tr class="font-bold">
                            <td colspan="3" class="px-6 py-4 text-sm text-gray-900">
                                <i class="fas fa-calculator text-indigo-600 mr-2"></i>
                                TOTAL
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-green-200 text-green-900">
                                    <?= $rekap['total_hadir'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-yellow-200 text-yellow-900">
                                    <?= $rekap['total_sakit'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-200 text-blue-900">
                                    <?= $rekap['total_izin'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-red-200 text-red-900">
                                    <?= $rekap['total_alpa'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold text-indigo-600">
                                    <?= $rekap['persentase_hadir'] ?? 0 ?>%
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?php else: ?>
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="bg-gradient-to-br from-indigo-100 to-purple-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-chart-bar text-indigo-600 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Data</h3>
            <p class="text-gray-600 mb-6">
                Silakan pilih kelas dan periode tanggal untuk menampilkan laporan absensi.
            </p>
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-xl">
                <i class="fas fa-info-circle mr-2"></i>
                Gunakan filter di atas untuk memulai
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
