<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<!-- Custom Styles for Animation -->
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
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }

    .textarea-enhanced:focus {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-card {
        transition: all 0.3s ease;
    }

    .form-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto">
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
                            <i class="fas fa-plus-circle mr-3"></i>
                            Tambah Jurnal KBM
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-2">Lengkapi informasi jurnal kegiatan belajar mengajar dengan detail</p>
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
                <span class="text-gray-800 font-medium">Tambah Baru</span>
            </nav>
        </div>

        <!-- Info Absensi Card -->
        <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 border-2 border-blue-200 rounded-2xl p-6 mb-8 shadow-lg animate-slide-in">
            <div class="flex items-center mb-5">
                <div class="bg-blue-600 text-white p-3 rounded-xl mr-4">
                    <i class="fas fa-info-circle text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Informasi Absensi Terkait</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4 hover:bg-white/80 transition-all">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 block">Tanggal</span>
                            <span class="text-lg font-bold text-gray-800"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></span>
                        </div>
                    </div>
                    
                    <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4 hover:bg-white/80 transition-all">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-book text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 block">Mata Pelajaran</span>
                            <span class="text-lg font-bold text-gray-800"><?= esc($absensi['nama_mapel']) ?></span>
                        </div>
                    </div>
                    
                    <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4 hover:bg-white/80 transition-all">
                        <div class="bg-purple-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 block">Kelas</span>
                            <span class="text-lg font-bold text-gray-800"><?= esc($absensi['nama_kelas']) ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column -->
                <div class="space-y-4">
                    <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4 hover:bg-white/80 transition-all">
                        <div class="bg-amber-100 p-3 rounded-lg mr-4">
                            <i class="fas fa-list-ol text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="text-sm text-gray-600 block">Pertemuan Ke</span>
                            <span class="text-lg font-bold text-gray-800"><?= $absensi['pertemuan_ke'] ?></span>
                        </div>
                    </div>
                    
                    <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 hover:bg-white/80 transition-all">
                        <div class="flex items-start">
                            <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-book-open text-indigo-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <span class="text-sm text-gray-600 block mb-1">Materi Pembelajaran</span>
                                <p class="text-base font-semibold text-gray-800 leading-relaxed"><?= esc($absensi['materi_pembelajaran']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden form-card mb-8">
            <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-6">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-edit mr-3"></i>
                    Form Jurnal KBM
                </h2>
                <p class="text-indigo-100 mt-1">Isi form berikut dengan lengkap dan detail</p>
            </div>
            
            <div class="p-8">
                <form id="formJurnal" class="space-y-8">
                    <?= csrf_field() ?>
                    <input type="hidden" name="absensi_id" value="<?= $absensi['id'] ?>">

                    <!-- Tujuan Pembelajaran -->
                    <div class="form-group">
                        <label for="tujuan_pembelajaran" class="block text-sm font-bold text-gray-700 mb-3">
                            <span class="flex items-center">
                                <i class="fas fa-bullseye text-indigo-600 text-lg mr-2"></i>
                                Tujuan Pembelajaran 
                                <span class="text-red-500 ml-1">*</span>
                            </span>
                        </label>
                        <textarea id="tujuan_pembelajaran" 
                                  name="tujuan_pembelajaran" 
                                  rows="5" 
                                  required
                                  placeholder="Contoh: Siswa dapat memahami konsep dasar matematika dan menerapkannya dalam soal cerita..."
                                  class="textarea-enhanced w-full px-5 py-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 resize-none"></textarea>
                        <div class="error-message text-red-500 text-sm mt-2 hidden"></div>
                    </div>

                    <!-- Kegiatan Pembelajaran -->
                    <div class="form-group">
                        <label for="kegiatan_pembelajaran" class="block text-sm font-bold text-gray-700 mb-3">
                            <span class="flex items-center">
                                <i class="fas fa-tasks text-green-600 text-lg mr-2"></i>
                                Kegiatan Pembelajaran 
                                <span class="text-red-500 ml-1">*</span>
                            </span>
                        </label>
                        <textarea id="kegiatan_pembelajaran" 
                                  name="kegiatan_pembelajaran" 
                                  rows="5" 
                                  required
                                  placeholder="Jelaskan secara detail:&#10;• Kegiatan Pembuka (10 menit)&#10;• Kegiatan Inti (60 menit)&#10;• Kegiatan Penutup (20 menit)"
                                  class="textarea-enhanced w-full px-5 py-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-green-500 focus:border-green-500 transition-all duration-200 resize-none"></textarea>
                        <div class="error-message text-red-500 text-sm mt-2 hidden"></div>
                    </div>

                    <!-- Media Ajar -->
                    <div class="form-group">
                        <label for="media_ajar" class="block text-sm font-bold text-gray-700 mb-3">
                            <span class="flex items-center">
                                <i class="fas fa-desktop text-blue-600 text-lg mr-2"></i>
                                Media Ajar
                            </span>
                        </label>
                        <textarea id="media_ajar" 
                                  name="media_ajar" 
                                  rows="4"
                                  placeholder="Sebutkan media yang digunakan, contoh:&#10;• Papan tulis dan spidol warna&#10;• LCD Proyektor dan presentasi PowerPoint&#10;• Video pembelajaran dari YouTube&#10;• Google Classroom untuk tugas online"
                                  class="textarea-enhanced w-full px-5 py-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"></textarea>
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                            Sebutkan semua media atau alat bantu yang digunakan dalam pembelajaran
                        </p>
                    </div>

                    <!-- Penilaian -->
                    <div class="form-group">
                        <label for="penilaian" class="block text-sm font-bold text-gray-700 mb-3">
                            <span class="flex items-center">
                                <i class="fas fa-clipboard-check text-purple-600 text-lg mr-2"></i>
                                Penilaian
                            </span>
                        </label>
                        <textarea id="penilaian" 
                                  name="penilaian" 
                                  rows="4"
                                  placeholder="Jelaskan metode penilaian, contoh:&#10;• Quiz (10 soal pilihan ganda)&#10;• Tugas kelompok (diskusi dan presentasi)&#10;• Observasi keaktifan siswa&#10;• Penilaian sikap dan partisipasi"
                                  class="textarea-enhanced w-full px-5 py-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 resize-none"></textarea>
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-purple-500"></i>
                            Jelaskan metode dan instrumen penilaian yang digunakan
                        </p>
                    </div>

                    <!-- Catatan Khusus -->
                    <div class="form-group">
                        <label for="catatan_khusus" class="block text-sm font-bold text-gray-700 mb-3">
                            <span class="flex items-center">
                                <i class="fas fa-sticky-note text-amber-600 text-lg mr-2"></i>
                                Catatan Khusus
                            </span>
                        </label>
                        <textarea id="catatan_khusus" 
                                  name="catatan_khusus" 
                                  rows="4"
                                  placeholder="Catatan tambahan seperti:&#10;• Kendala atau masalah yang dihadapi&#10;• Hal menarik atau prestasi siswa&#10;• Saran untuk pembelajaran berikutnya&#10;• Tindak lanjut yang perlu dilakukan"
                                  class="textarea-enhanced w-full px-5 py-4 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 resize-none"></textarea>
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-amber-500"></i>
                            Opsional - Tambahkan catatan penting atau refleksi pembelajaran
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t-2 border-gray-200">
                        <a href="<?= base_url('guru/jurnal') ?>" 
                           class="flex-1 px-8 py-4 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-semibold text-center shadow-md hover:shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <button type="submit" 
                                id="btnSimpan" 
                                class="flex-1 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 font-semibold shadow-md hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Jurnal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Info -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-2xl p-6 shadow-lg">
            <div class="flex items-start">
                <div class="bg-amber-400 text-white p-3 rounded-xl mr-4">
                    <i class="fas fa-lightbulb text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-amber-900 mb-3">💡 Tips Mengisi Jurnal KBM</h3>
                    <ul class="space-y-2 text-amber-900">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-amber-600 mr-3 mt-1"></i>
                            <span>Tuliskan tujuan pembelajaran dengan <strong>jelas dan spesifik</strong>, mengacu pada indikator pencapaian kompetensi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-amber-600 mr-3 mt-1"></i>
                            <span>Deskripsikan kegiatan pembelajaran secara <strong>kronologis</strong> (pembuka, inti, penutup)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-amber-600 mr-3 mt-1"></i>
                            <span>Sebutkan <strong>media pembelajaran</strong> yang benar-benar digunakan, bukan yang direncanakan saja</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-amber-600 mr-3 mt-1"></i>
                            <span>Catat <strong>kendala</strong>, <strong>prestasi siswa</strong>, atau hal menarik yang terjadi selama pembelajaran</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-amber-600 mr-3 mt-1"></i>
                            <span>Gunakan jurnal sebagai <strong>bahan refleksi</strong> untuk meningkatkan kualitas pembelajaran berikutnya</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('formJurnal').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    
    document.querySelectorAll('textarea').forEach(el => {
        el.classList.remove('border-red-500');
    });
    
    const btnSimpan = document.getElementById('btnSimpan');
    const originalContent = btnSimpan.innerHTML;
    btnSimpan.disabled = true;
    btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
    btnSimpan.classList.add('opacity-75', 'cursor-not-allowed');
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('guru/jurnal/simpan') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Success animation
            btnSimpan.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Berhasil Disimpan!';
            btnSimpan.classList.add('bg-green-600');
            
            setTimeout(() => {
                window.location.href = '<?= base_url('guru/jurnal') ?>';
            }, 1000);
        } else {
            // Show error message
            if (data.errors) {
                Object.keys(data.errors).forEach(key => {
                    const textarea = document.getElementById(key);
                    if (textarea) {
                        textarea.classList.add('border-red-500');
                        const errorDiv = textarea.nextElementSibling;
                        if (errorDiv && errorDiv.classList.contains('error-message')) {
                            errorDiv.textContent = data.errors[key];
                            errorDiv.classList.remove('hidden');
                        }
                    }
                });
            }
            
            // Show alert
            alert(data.message || 'Terjadi kesalahan saat menyimpan data');
            
            // Reset button
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = originalContent;
            btnSimpan.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
        btnSimpan.disabled = false;
        btnSimpan.innerHTML = originalContent;
        btnSimpan.classList.remove('opacity-75', 'cursor-not-allowed');
    });
});

// Remove error styling on input
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.classList.remove('border-red-500');
        const errorDiv = this.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('error-message')) {
            errorDiv.classList.add('hidden');
        }
    });
});

// Character counter (optional enhancement)
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        if (length > 0) {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        }
    });
});
</script>

<?= $this->endSection() ?>
