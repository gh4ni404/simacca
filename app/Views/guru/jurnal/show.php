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

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    .jurnal-card {
        transition: all 0.3s ease;
    }

    .jurnal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex !important;
    }

    .modal-content {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.5);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .close-modal {
        position: absolute;
        top: 20px;
        right: 40px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10000;
        transition: 0.3s;
    }

    .close-modal:hover {
        color: #ff6b6b;
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center mb-4">
                <a href="<?= base_url('guru/jurnal') ?>" 
                   class="mr-4 p-2 rounded-lg bg-white text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                        <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            <i class="fas fa-list-alt mr-3"></i>
                            Daftar Pertemuan
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-2"><?= esc($kelas['nama_kelas']) ?> - <?= esc($kelas['nama_mapel']) ?></p>
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
                <span class="text-gray-800 font-medium">Daftar Pertemuan</span>
            </nav>
        </div>

        <!-- Kelas Info Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 mb-8 shadow-xl text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex items-center mb-4 md:mb-0">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-xl mr-4">
                        <i class="fas fa-chalkboard text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold"><?= esc($kelas['nama_kelas']) ?></h2>
                        <p class="text-indigo-100 mt-1"><?= esc($kelas['nama_mapel']) ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 backdrop-blur-sm px-6 py-4 rounded-xl text-center">
                        <p class="text-sm opacity-90">Total Pertemuan</p>
                        <p class="text-4xl font-bold"><?= count($jurnalList) ?></p>
                    </div>
                    <a href="<?= base_url('guru/jurnal/print/' . $kelas['id']) ?>" 
                       target="_blank"
                       class="flex items-center gap-2 bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-xl font-semibold shadow-lg transition-all hover:scale-105">
                        <i class="fas fa-print text-xl"></i>
                        <span>Cetak Semua</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Jurnal List -->
        <div class="grid grid-cols-1 gap-6">
            <?php foreach ($jurnalList as $index => $jurnal): ?>
            <div class="jurnal-card bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-lg mr-4">
                                <p class="text-xs">Pertemuan</p>
                                <p class="text-2xl font-bold"><?= $jurnal['pertemuan_ke'] ?></p>
                            </div>
                            <div class="text-white">
                                <p class="text-lg font-bold"><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></p>
                                <p class="text-xs opacity-75"><?= date('l', strtotime($jurnal['tanggal'])) ?></p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="<?= base_url('guru/jurnal/edit/' . $jurnal['id']) ?>" 
                               class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-all font-semibold shadow-md"
                               title="Edit Jurnal">
                                <i class="fas fa-edit mr-2"></i>
                                <span class="hidden md:inline">Edit</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 <?= !empty($jurnal['foto_dokumentasi']) ? 'md:grid-cols-2' : '' ?> gap-6">
                        <!-- Kegiatan Pembelajaran -->
                        <div class="<?= !empty($jurnal['foto_dokumentasi']) ? '' : 'md:col-span-2' ?>">
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-100 p-3 rounded-lg mr-3">
                                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Kegiatan Pembelajaran</h3>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl p-5 border-2 border-blue-200">
                                <p class="text-gray-800 leading-relaxed whitespace-pre-wrap"><?= esc($jurnal['kegiatan_pembelajaran']) ?></p>
                            </div>
                        </div>

                        <!-- Foto Dokumentasi -->
                        <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="bg-purple-100 p-3 rounded-lg mr-3">
                                    <i class="fas fa-camera text-purple-600 text-xl"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800">Foto Dokumentasi</h3>
                            </div>
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border-2 border-purple-200">
                                <img src="<?= base_url('files/jurnal/' . esc($jurnal['foto_dokumentasi'])) ?>" 
                                     alt="Foto Dokumentasi Pertemuan <?= $jurnal['pertemuan_ke'] ?>" 
                                     class="foto-preview w-full h-40 object-cover rounded-lg cursor-pointer hover:opacity-90 hover:scale-105 transition-all shadow-md"
                                     data-image="<?= base_url('files/jurnal/' . esc($jurnal['foto_dokumentasi'])) ?>">
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    <i class="fas fa-search-plus mr-1"></i>
                                    Klik untuk memperbesar
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Timestamp -->
                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 text-xs text-gray-500">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-plus mr-2"></i>
                            Dibuat: <?= date('d/m/Y H:i', strtotime($jurnal['created_at'])) ?>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-book-open mr-2"></i>
                            <?= esc($jurnal['materi_pembelajaran']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="<?= base_url('guru/jurnal') ?>" 
               class="inline-flex items-center px-8 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all font-semibold shadow-md">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Daftar Kelas
            </a>
        </div>
    </div>
</div>

<!-- Modal untuk foto -->
<div id="imageModal" class="modal">
    <span class="close-modal">&times;</span>
    <img class="modal-content" id="modalImage" alt="Preview">
</div>

<script>
// Add animation on scroll
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.jurnal-card');
    
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Setup modal untuk semua foto
    setupImageModal();
});

function setupImageModal() {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const allImages = document.querySelectorAll('.foto-preview');
    
    console.log('Setup modal - Found images:', allImages.length);
    
    // Add click event to all images
    allImages.forEach(function(img) {
        img.addEventListener('click', function() {
            const imageSrc = this.getAttribute('data-image');
            console.log('Image clicked:', imageSrc);
            
            if (modal && modalImg && imageSrc) {
                modalImg.src = imageSrc;
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                console.log('Modal opened');
            }
        });
    });
    
    // Close button
    const closeBtn = document.querySelector('.close-modal');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeModalFunc();
        });
    }
    
    // Click on background to close
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModalFunc();
            }
        });
    }
    
    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModalFunc();
        }
    });
}

function closeModalFunc() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
        console.log('Modal closed');
    }
}
</script>

<?= $this->endSection() ?>
