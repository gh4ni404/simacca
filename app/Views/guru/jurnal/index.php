<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    .table-row-hover { transition: all 0.2s ease; }
    .table-row-hover:hover { background-color: #f8fafc; transform: translateX(4px); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 flex items-center">
                        <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            <i class="fas fa-book-open mr-3"></i>
                            Jurnal Kegiatan Belajar Mengajar
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-2">Kelola jurnal pembelajaran per kelas dengan mudah</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <nav class="text-sm text-gray-600 bg-white px-4 py-3 rounded-lg shadow-sm">
                        <a href="<?= base_url('guru/dashboard') ?>" class="hover:text-indigo-600 transition-colors">
                            <i class="fas fa-home mr-1"></i>Dashboard
                        </a>
                        <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
                        <span class="text-gray-800 font-medium">Jurnal KBM</span>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?= view('components/alerts') ?>

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-filter mr-3"></i>
                    Filter Jurnal
                </h2>
                <p class="text-indigo-100 mt-1">Cari jurnal berdasarkan periode tanggal</p>
            </div>
            <div class="p-6">
                <form method="GET" action="<?= base_url('guru/jurnal') ?>" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <?= form_input('start_date', 'Tanggal Mulai', $startDate ?? '', ['type' => 'date']) ?>
                    </div>
                    <div class="flex-1">
                        <?= form_input('end_date', 'Tanggal Akhir', $endDate ?? '', ['type' => 'date']) ?>
                    </div>
                    <div class="flex items-end gap-3">
                        <?= button('primary', 'Filter', 'search', ['type' => 'submit', 'class' => 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md hover:shadow-xl transform hover:-translate-y-0.5']) ?>
                        <?= button_link('secondary', 'Reset', 'redo', base_url('guru/jurnal'), ['class' => 'shadow-md']) ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Jurnal List per Kelas -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-list mr-3"></i>
                            Daftar Jurnal KBM per Kelas
                        </h2>
                        <p class="text-indigo-100 mt-1">Jurnal pembelajaran dikelompokkan berdasarkan kelas</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                        <p class="text-sm opacity-90">Total Kelas</p>
                        <p class="text-3xl font-bold"><?= count($kelasList ?? []) ?></p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <?php if (empty($kelasList)): ?>
                    <?= empty_state('book-open', 'Belum Ada Jurnal KBM', 'Jurnal KBM akan otomatis tersedia setelah Anda melakukan absensi siswa. Silakan buat absensi terlebih dahulu.', 'Ke Halaman Absensi', base_url('guru/absensi')) ?>
                <?php else: ?>
                    <!-- Table Desktop -->
                    <div class="hidden md:block overflow-x-auto">
                        <?= table_start() ?>
                            <?= table_header(['No', 'Mata Pelajaran', 'Kelas', 'Total Pertemuan', 'Aksi']) ?>
                            <?php $no = 1; foreach ($kelasList as $kelas): ?>
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 p-3 rounded-lg mr-3">
                                            <i class="fas fa-book text-green-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900"><?= esc($kelas['nama_mapel']) ?></div>
                                            <div class="text-xs text-gray-500">Mata Pelajaran</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                            <i class="fas fa-users text-purple-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900"><?= esc($kelas['nama_kelas']) ?></div>
                                            <div class="text-xs text-gray-500">Kelas</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center justify-center bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border-2 border-blue-200 px-4 py-2 rounded-xl">
                                        <i class="fas fa-clipboard-list mr-2"></i>
                                        <span class="text-lg font-bold"><?= $kelas['total_pertemuan'] ?></span>
                                        <span class="text-xs ml-1">pertemuan</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?= button_link('info', 'Lihat Pertemuan', 'eye', base_url('guru/jurnal/show/' . $kelas['kelas_id']), ['class' => 'bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 shadow-md hover:shadow-lg transform hover:-translate-y-0.5']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?= table_end() ?>
                    </div>

                    <!-- Cards Mobile -->
                    <div class="md:hidden space-y-4">
                        <?php $no = 1; foreach ($kelasList as $kelas): ?>
                        <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border-2 border-gray-200 p-5 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between mb-4">
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full">#{<?= $no++ ?>}</span>
                                <span class="bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 text-xs font-bold px-3 py-2 rounded-lg border border-blue-200">
                                    <i class="fas fa-clipboard-list mr-1"></i>
                                    <?= $kelas['total_pertemuan'] ?> pertemuan
                                </span>
                            </div>

                            <div class="space-y-3 mb-4">
                                <div class="flex items-center">
                                    <div class="bg-green-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-book text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Mata Pelajaran</p>
                                        <p class="text-sm font-bold text-gray-900"><?= esc($kelas['nama_mapel']) ?></p>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                        <i class="fas fa-users text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Kelas</p>
                                        <p class="text-sm font-bold text-gray-900"><?= esc($kelas['nama_kelas']) ?></p>
                                    </div>
                                </div>
                            </div>

                            <?= button_link('info', 'Lihat Pertemuan', 'eye', base_url('guru/jurnal/show/' . $kelas['kelas_id']), ['class' => 'w-full justify-center bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 shadow-md']) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="mt-8">
            <?= info_card('info-circle', 'Informasi Penting', '
                <ul class="space-y-2 text-sm">
                    <li class="flex items-start"><i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i><span>Jurnal KBM dibuat <strong>otomatis setelah melakukan absensi siswa</strong></span></li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i><span>Klik <strong>"Lihat Pertemuan"</strong> untuk melihat semua jurnal per kelas</span></li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i><span>Gunakan <strong>filter tanggal</strong> untuk mencari jurnal pada periode tertentu</span></li>
                    <li class="flex items-start"><i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i><span>Jurnal dikelompokkan berdasarkan <strong>kelas dan mata pelajaran</strong></span></li>
                </ul>
            ', 'blue') ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.table-row-hover');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });
});
</script>

<?= $this->endSection() ?>
