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
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    .animate-slide-in-right {
        animation: slideInRight 0.4s ease-out;
    }

    .content-card {
        transition: all 0.3s ease;
    }

    .content-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }

    .print-button:hover {
        transform: scale(1.05);
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-area {
            margin: 0;
            padding: 20px;
        }
        
        body {
            background: white !important;
        }
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up no-print">
            <div class="flex items-center mb-4">
                <a href="<?= base_url('guru/jurnal') ?>" 
                   class="mr-4 p-2 rounded-lg bg-white text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            <i class="fas fa-eye mr-3"></i>
                            Preview Jurnal KBM
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-2">Detail lengkap jurnal kegiatan belajar mengajar</p>
                </div>
            </div>
            
            <!-- Breadcrumb -->
            <nav class="flex items-center text-sm text-gray-600 bg-white px-4 py-3 rounded-lg shadow-sm">
                <a href="<?= base_url('guru/dashboard') ?>" class="hover:text-indigo-600 transition-colors">
                    <i class="fas fa-home mr-1"></i>Dashboard
                </a>
                <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
                <a href="<?= base_url('guru/jurnal') ?>" class="hover:text-indigo-600 transition-colors">Jurnal KBM</a>
                <i class="fas fa-chevron-right mx-3 text-gray-400 text-xs"></i>
                <span class="text-gray-800 font-medium">Preview</span>
            </nav>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mb-8 no-print animate-slide-in-right">
            <button onclick="window.print()" 
                    class="print-button flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 text-white rounded-xl hover:from-blue-700 hover:to-cyan-700 transition-all font-semibold shadow-lg">
                <i class="fas fa-print mr-2 text-lg"></i>
                Cetak Jurnal
            </button>
            <a href="<?= base_url('guru/jurnal/edit/' . $jurnal['id']) ?>" 
               class="flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-xl hover:from-amber-700 hover:to-orange-700 transition-all font-semibold shadow-lg">
                <i class="fas fa-edit mr-2 text-lg"></i>
                Edit Jurnal
            </a>
            <a href="<?= base_url('guru/jurnal') ?>" 
               class="flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-semibold shadow-md">
                <i class="fas fa-list mr-2"></i>
                Kembali ke Daftar
            </a>
        </div>

        <!-- Print Area -->
        <div class="print-area">
            <!-- Header Info Card -->
            <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 rounded-2xl p-8 mb-8 shadow-xl text-white">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">JURNAL KEGIATAN BELAJAR MENGAJAR</h2>
                        <p class="text-indigo-100">Dokumentasi Proses Pembelajaran</p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm px-6 py-4 rounded-xl text-center">
                        <p class="text-sm opacity-90 mb-1">ID Jurnal</p>
                        <p class="text-2xl font-bold">#<?= str_pad($jurnal['id'], 4, '0', STR_PAD_LEFT) ?></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm opacity-90 mb-1">Tanggal Pembelajaran</p>
                        <p class="text-lg font-bold"><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></p>
                        <p class="text-xs opacity-75"><?= date('l', strtotime($jurnal['tanggal'])) ?></p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm opacity-90 mb-1">Pertemuan Ke</p>
                        <p class="text-3xl font-bold"><?= $jurnal['pertemuan_ke'] ?></p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <p class="text-sm opacity-90 mb-1">Waktu Dibuat</p>
                        <p class="text-sm font-semibold"><?= date('d/m/Y H:i', strtotime($jurnal['created_at'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Informasi Pembelajaran -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-6">
                    <h3 class="text-xl font-bold text-white flex items-center">
                        <i class="fas fa-info-circle mr-3"></i>
                        Informasi Pembelajaran
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-blue-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Nama Guru</p>
                                    <p class="text-lg font-bold text-gray-900"><?= esc($jurnal['nama_guru']) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-green-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fas fa-book text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Mata Pelajaran</p>
                                    <p class="text-lg font-bold text-gray-900"><?= esc($jurnal['nama_mapel']) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <div class="bg-purple-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fas fa-users text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Kelas</p>
                                    <p class="text-lg font-bold text-gray-900"><?= esc($jurnal['nama_kelas']) ?></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="bg-amber-100 p-3 rounded-xl mr-4 flex-shrink-0">
                                    <i class="fas fa-book-open text-amber-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Materi Pembelajaran</p>
                                    <p class="text-base font-semibold text-gray-900"><?= esc($jurnal['materi_pembelajaran']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tujuan Pembelajaran -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 content-card">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-5">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-bullseye mr-3"></i>
                        Tujuan Pembelajaran
                    </h3>
                </div>
                <div class="p-8">
                    <div class="prose max-w-none">
                        <p class="text-gray-800 leading-relaxed text-justify whitespace-pre-wrap"><?= esc($jurnal['tujuan_pembelajaran']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Kegiatan Pembelajaran -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 content-card">
                <div class="bg-gradient-to-r from-green-500 to-teal-600 p-5">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-tasks mr-3"></i>
                        Kegiatan Pembelajaran
                    </h3>
                </div>
                <div class="p-8">
                    <div class="prose max-w-none">
                        <p class="text-gray-800 leading-relaxed text-justify whitespace-pre-wrap"><?= esc($jurnal['kegiatan_pembelajaran']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Media Ajar & Penilaian -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Media Ajar -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden content-card">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-desktop mr-3"></i>
                            Media Ajar
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($jurnal['media_ajar'])): ?>
                            <div class="prose max-w-none">
                                <p class="text-gray-800 leading-relaxed whitespace-pre-wrap"><?= esc($jurnal['media_ajar']) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-sm">Tidak ada data media ajar</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Penilaian -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden content-card">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-5">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="fas fa-clipboard-check mr-3"></i>
                            Penilaian
                        </h3>
                    </div>
                    <div class="p-6">
                        <?php if (!empty($jurnal['penilaian'])): ?>
                            <div class="prose max-w-none">
                                <p class="text-gray-800 leading-relaxed whitespace-pre-wrap"><?= esc($jurnal['penilaian']) ?></p>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p class="text-sm">Tidak ada data penilaian</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Catatan Khusus -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8 content-card">
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-5">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-sticky-note mr-3"></i>
                        Catatan Khusus
                    </h3>
                </div>
                <div class="p-8">
                    <?php if (!empty($jurnal['catatan_khusus'])): ?>
                        <div class="prose max-w-none">
                            <p class="text-gray-800 leading-relaxed text-justify whitespace-pre-wrap"><?= esc($jurnal['catatan_khusus']) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p class="text-sm">Tidak ada catatan khusus</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="bg-gradient-to-r from-gray-100 to-gray-200 rounded-2xl p-6 border-2 border-gray-300">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <div class="flex items-center">
                        <i class="fas fa-calendar-check text-gray-500 mr-2"></i>
                        <span>Dibuat pada: <strong><?= date('d F Y, H:i', strtotime($jurnal['created_at'])) ?> WIB</strong></span>
                    </div>
                    <?php if (isset($jurnal['updated_at']) && $jurnal['updated_at']): ?>
                    <div class="flex items-center">
                        <i class="fas fa-edit text-gray-500 mr-2"></i>
                        <span>Terakhir diupdate: <strong><?= date('d F Y, H:i', strtotime($jurnal['updated_at'])) ?> WIB</strong></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions (No Print) -->
        <div class="mt-8 bg-blue-50 border-2 border-blue-300 rounded-2xl p-6 no-print">
            <div class="flex items-start">
                <div class="bg-blue-500 text-white p-3 rounded-xl mr-4">
                    <i class="fas fa-lightbulb text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-blue-900 mb-3">💡 Tips</h3>
                    <ul class="space-y-2 text-blue-900 text-sm">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                            <span>Gunakan tombol <strong>Cetak</strong> untuk menyimpan atau mencetak jurnal dalam format PDF</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                            <span>Klik <strong>Edit Jurnal</strong> untuk mengubah atau melengkapi informasi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-0.5"></i>
                            <span>Preview ini dapat digunakan sebagai dokumentasi pembelajaran</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add animation on scroll
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.content-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.5s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
            }
        });
    }, {
        threshold: 0.1
    });

    cards.forEach(card => {
        observer.observe(card);
    });
});
</script>

<?= $this->endSection() ?>
