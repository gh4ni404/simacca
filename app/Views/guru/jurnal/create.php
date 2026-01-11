<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="<?= base_url('guru/jurnal') ?>" class="mr-4 text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle mr-2 text-indigo-600"></i>
                    Tambah Jurnal KBM
                </h1>
                <p class="text-gray-600 mt-1">Lengkapi informasi jurnal kegiatan belajar mengajar</p>
            </div>
        </div>
        <nav class="text-sm text-gray-600">
            <a href="<?= base_url('guru/dashboard') ?>" class="hover:text-indigo-600">
                <i class="fas fa-home mr-1"></i>Dashboard
            </a>
            <span class="mx-2">/</span>
            <a href="<?= base_url('guru/jurnal') ?>" class="hover:text-indigo-600">Jurnal KBM</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800 font-medium">Tambah</span>
        </nav>
    </div>

    <!-- Info Absensi Card -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
            Informasi Absensi Terkait
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
                <div class="flex items-center text-sm">
                    <i class="fas fa-calendar-alt w-6 text-blue-600"></i>
                    <span class="text-gray-600">Tanggal:</span>
                    <span class="ml-2 font-semibold text-gray-800"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-book w-6 text-blue-600"></i>
                    <span class="text-gray-600">Mata Pelajaran:</span>
                    <span class="ml-2 font-semibold text-gray-800"><?= esc($absensi['nama_mapel']) ?></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-users w-6 text-blue-600"></i>
                    <span class="text-gray-600">Kelas:</span>
                    <span class="ml-2 font-semibold text-gray-800"><?= esc($absensi['nama_kelas']) ?></span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center text-sm">
                    <i class="fas fa-list-ol w-6 text-blue-600"></i>
                    <span class="text-gray-600">Pertemuan Ke:</span>
                    <span class="ml-2 font-semibold text-gray-800"><?= $absensi['pertemuan_ke'] ?></span>
                </div>
                <div class="flex items-start text-sm">
                    <i class="fas fa-book-open w-6 text-blue-600 mt-1"></i>
                    <div class="flex-1">
                        <span class="text-gray-600">Materi:</span>
                        <p class="font-semibold text-gray-800"><?= esc($absensi['materi_pembelajaran']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-t-lg">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Form Jurnal KBM
            </h2>
        </div>
        <div class="p-6">
            <form id="formJurnal">
                <?= csrf_field() ?>
                <input type="hidden" name="absensi_id" value="<?= $absensi['id'] ?>">

                <!-- Tujuan Pembelajaran -->
                <div class="mb-6">
                    <label for="tujuan_pembelajaran" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-bullseye mr-2 text-indigo-500"></i>
                        Tujuan Pembelajaran <span class="text-red-500">*</span>
                    </label>
                    <textarea id="tujuan_pembelajaran" 
                              name="tujuan_pembelajaran" 
                              rows="4" 
                              required
                              placeholder="Deskripsikan tujuan pembelajaran yang ingin dicapai pada pertemuan ini..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"></textarea>
                    <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                </div>

                <!-- Kegiatan Pembelajaran -->
                <div class="mb-6">
                    <label for="kegiatan_pembelajaran" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tasks mr-2 text-green-500"></i>
                        Kegiatan Pembelajaran <span class="text-red-500">*</span>
                    </label>
                    <textarea id="kegiatan_pembelajaran" 
                              name="kegiatan_pembelajaran" 
                              rows="4" 
                              required
                              placeholder="Jelaskan kegiatan pembelajaran yang dilakukan (pembuka, inti, penutup)..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
                    <div class="invalid-feedback text-red-500 text-sm mt-1"></div>
                </div>

                <!-- Media Ajar -->
                <div class="mb-6">
                    <label for="media_ajar" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-desktop mr-2 text-blue-500"></i>
                        Media Ajar
                    </label>
                    <textarea id="media_ajar" 
                              name="media_ajar" 
                              rows="3"
                              placeholder="Contoh: Papan tulis, LCD Proyektor, Google Classroom, Video Pembelajaran, dll."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Sebutkan media atau alat bantu yang digunakan dalam pembelajaran
                    </p>
                </div>

                <!-- Penilaian -->
                <div class="mb-6">
                    <label for="penilaian" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clipboard-check mr-2 text-purple-500"></i>
                        Penilaian
                    </label>
                    <textarea id="penilaian" 
                              name="penilaian" 
                              rows="3"
                              placeholder="Contoh: Quiz, Tugas Kelompok, Observasi, Presentasi, dll."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Jelaskan metode penilaian yang digunakan
                    </p>
                </div>

                <!-- Catatan Khusus -->
                <div class="mb-6">
                    <label for="catatan_khusus" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2 text-amber-500"></i>
                        Catatan Khusus
                    </label>
                    <textarea id="catatan_khusus" 
                              name="catatan_khusus" 
                              rows="3"
                              placeholder="Catatan tambahan atau kendala yang dihadapi dalam pembelajaran hari ini..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Opsional - Tambahkan catatan penting lainnya
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-4 border-t border-gray-200">
                    <a href="<?= base_url('guru/jurnal') ?>" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    <button type="submit" id="btnSimpan" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Info -->
    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-amber-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-amber-800">
                <p class="font-semibold mb-1">Tips Mengisi Jurnal KBM:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Tuliskan tujuan pembelajaran dengan jelas dan spesifik</li>
                    <li>Deskripsikan kegiatan pembelajaran secara kronologis</li>
                    <li>Sebutkan media pembelajaran yang benar-benar digunakan</li>
                    <li>Catat kendala atau hal menarik yang terjadi selama pembelajaran</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('formJurnal').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnSimpan = document.getElementById('btnSimpan');
    btnSimpan.disabled = true;
    btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('guru/jurnal/simpan') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            window.location.href = '<?= base_url('guru/jurnal') ?>';
        } else {
            alert(data.message);
            if (data.errors) {
                // Show validation errors
                Object.keys(data.errors).forEach(key => {
                    const input = document.getElementById(key);
                    if (input) {
                        input.classList.add('is-invalid');
                        input.nextElementSibling.textContent = data.errors[key];
                    }
                });
            }
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Jurnal';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
        btnSimpan.disabled = false;
        btnSimpan.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Jurnal';
    });
});

// Remove invalid class on input
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});
</script>

<?= $this->endSection() ?>
