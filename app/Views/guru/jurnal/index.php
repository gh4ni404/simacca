<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<!-- Custom Styles -->
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
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    .animate-slide-in-left {
        animation: slideInLeft 0.3s ease-out;
    }

    .table-row-hover {
        transition: all 0.2s ease;
    }

    .table-row-hover:hover {
        background-color: #f8fafc;
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stats-badge {
        transition: all 0.3s ease;
    }

    .stats-badge:hover {
        transform: scale(1.05);
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 flex items-center">
                        <span class="py-2 bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            <i class="fas fa-book-open mr-3"></i>
                            Jurnal Kegiatan Belajar Mengajar
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-2">Kelola dan pantau jurnal KBM Anda dengan mudah</p>
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

        <!-- Success/Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-lg shadow-md animate-slide-in-left">
            <div class="flex items-center">
                <div class="bg-green-500 text-white p-2 rounded-lg mr-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold">Berhasil!</p>
                    <p class="text-sm"><?= session()->getFlashdata('success') ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-lg shadow-md animate-slide-in-left">
            <div class="flex items-center">
                <div class="bg-red-500 text-white p-2 rounded-lg mr-4">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold">Terjadi Kesalahan!</p>
                    <p class="text-sm"><?= session()->getFlashdata('error') ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

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
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt text-indigo-600 mr-2"></i>
                            Tanggal Mulai
                        </label>
                        <input type="date" 
                               name="start_date" 
                               value="<?= $startDate ?? '' ?>"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-check text-purple-600 mr-2"></i>
                            Tanggal Akhir
                        </label>
                        <input type="date" 
                               name="end_date" 
                               value="<?= $endDate ?? '' ?>"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-500 focus:border-purple-500 transition-all">
                    </div>
                    <div class="flex items-end gap-3">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all font-semibold shadow-md hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                        <a href="<?= base_url('guru/jurnal') ?>" class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-semibold shadow-md">
                            <i class="fas fa-redo mr-2"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Jurnal List -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-list mr-3"></i>
                            Daftar Jurnal KBM
                        </h2>
                        <p class="text-indigo-100 mt-1">Semua jurnal pembelajaran yang telah dibuat</p>
                    </div>
                    <div class="stats-badge bg-white/20 backdrop-blur-sm text-white px-6 py-3 rounded-xl">
                        <p class="text-sm opacity-90">Total Jurnal</p>
                        <p class="text-3xl font-bold"><?= count($jurnal ?? []) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <?php if (empty($jurnal)): ?>
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 mb-6">
                        <i class="fas fa-book-open text-5xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Belum Ada Jurnal KBM</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">Jurnal KBM akan otomatis tersedia setelah Anda melakukan absensi siswa. Silakan buat absensi terlebih dahulu.</p>
                    <a href="<?= base_url('guru/absensi') ?>" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-clipboard-list mr-3 text-xl"></i>
                        Ke Halaman Absensi
                    </a>
                </div>
                <?php else: ?>
                <!-- Table Desktop -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mata Pelajaran</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kelas</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Materi Pembelajaran</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Foto</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $no = 1; foreach ($jurnal as $j): ?>
                            <tr class="table-row-hover">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900"><?= $no++ ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                            <i class="fas fa-calendar-alt text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></div>
                                            <div class="text-xs text-gray-500"><?= date('l', strtotime($j['tanggal'])) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                                            <i class="fas fa-book text-green-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900"><?= esc($j['nama_mapel']) ?></div>
                                            <div class="text-xs text-gray-500">Pertemuan ke-<?= $j['pertemuan_ke'] ?? '-' ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200">
                                        <i class="fas fa-users mr-1"></i>
                                        <?= esc($j['nama_kelas']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 max-w-md line-clamp-2">
                                        <?= esc(substr($j['kegiatan_pembelajaran'], 0, 100)) ?><?= strlen($j['kegiatan_pembelajaran']) > 100 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php if (!empty($j['foto_dokumentasi'])): ?>
                                        <img src="<?= base_url('files/jurnal/' . esc($j['foto_dokumentasi'])) ?>" 
                                             alt="Foto Dokumentasi" 
                                             class="w-16 h-16 object-cover rounded-lg mx-auto cursor-pointer hover:scale-110 transition-transform"
                                             onclick="showImageModal('<?= base_url('files/jurnal/' . esc($j['foto_dokumentasi'])) ?>')">
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">
                                            <i class="fas fa-image"></i><br>Tidak ada foto
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= base_url('guru/jurnal/show/' . $j['id']) ?>" 
                                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg hover:from-blue-600 hover:to-cyan-600 transition-all font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                           title="Preview Jurnal">
                                            <i class="fas fa-eye mr-2"></i>
                                            Preview
                                        </a>
                                        <a href="<?= base_url('guru/jurnal/edit/' . $j['id']) ?>" 
                                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg hover:from-amber-600 hover:to-orange-600 transition-all font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                           title="Edit Jurnal">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Cards Mobile -->
                <div class="md:hidden space-y-4">
                    <?php $no = 1; foreach ($jurnal as $j): ?>
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-md border-2 border-gray-200 p-5 hover:shadow-lg transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full">#{<?= $no++ ?>}</span>
                            <div class="flex gap-2">
                                <a href="<?= base_url('guru/jurnal/show/' . $j['id']) ?>" 
                                   class="px-3 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-lg text-sm font-semibold shadow-md"
                                   title="Preview">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= base_url('guru/jurnal/edit/' . $j['id']) ?>" 
                                   class="px-3 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-lg text-sm font-semibold shadow-md"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-calendar-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Tanggal</p>
                                    <p class="text-sm font-bold text-gray-900"><?= date('d/m/Y', strtotime($j['tanggal'])) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-book text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Mata Pelajaran</p>
                                    <p class="text-sm font-bold text-gray-900"><?= esc($j['nama_mapel']) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                                    <i class="fas fa-users text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Kelas</p>
                                    <p class="text-sm font-bold text-gray-900"><?= esc($j['nama_kelas']) ?></p>
                                </div>
                            </div>
                            
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-1">Tujuan Pembelajaran:</p>
                                <p class="text-sm text-gray-700">
                                    <?= esc(substr($j['tujuan_pembelajaran'], 0, 100)) ?><?= strlen($j['tujuan_pembelajaran']) > 100 ? '...' : '' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Info Footer -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-2xl p-6 shadow-lg">
            <div class="flex items-start">
                <div class="bg-blue-500 text-white p-3 rounded-xl mr-4">
                    <i class="fas fa-info-circle text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-blue-900 mb-3">📋 Informasi Penting</h3>
                    <ul class="space-y-2 text-blue-900 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                            <span>Jurnal KBM dibuat <strong>otomatis setelah melakukan absensi siswa</strong></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                            <span>Gunakan <strong>filter tanggal</strong> untuk mencari jurnal pada periode tertentu</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                            <span>Pastikan melengkapi semua informasi jurnal dengan <strong>detail dan akurat</strong></span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                            <span>Jurnal dapat diedit <strong>kapan saja</strong> untuk memperbaiki atau menambahkan informasi</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add fade-in animation for table rows
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

    // Image Modal
    function showImageModal(imageUrl) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4';
        modal.onclick = () => modal.remove();
        
        const img = document.createElement('img');
        img.src = imageUrl;
        img.className = 'max-w-full max-h-full rounded-lg shadow-2xl';
        img.onclick = (e) => e.stopPropagation();
        
        modal.appendChild(img);
        document.body.appendChild(modal);
    }
</script>

<?= $this->endSection() ?>
