<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? 'Edit Jurnal KBM' ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('guru/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('guru/jurnal') ?>">Jurnal KBM</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>

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
                    <span class="ml-2 font-semibold text-gray-800"><?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-book w-6 text-blue-600"></i>
                    <span class="text-gray-600">Mata Pelajaran:</span>
                    <span class="ml-2 font-semibold text-gray-800"><?= esc($jurnal['nama_mapel']) ?></span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-users w-6 text-blue-600"></i>
                    <span class="text-gray-600">Kelas:</span>
                    <span class="ml-2 font-semibold text-gray-800"><?= esc($jurnal['nama_kelas']) ?></span>
                </div>
            </div>
            <div class="space-y-2">
                <div class="flex items-center text-sm">
                    <i class="fas fa-list-ol w-6 text-blue-600"></i>
                    <span class="text-gray-600">Pertemuan Ke:</span>
                    <span class="ml-2 font-semibold text-gray-800"><?= $jurnal['pertemuan_ke'] ?></span>
                </div>
                <div class="flex items-start text-sm">
                    <i class="fas fa-book-open w-6 text-blue-600 mt-1"></i>
                    <div class="flex-1">
                        <span class="text-gray-600">Materi:</span>
                        <p class="font-semibold text-gray-800"><?= esc($jurnal['materi_pembelajaran']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-t-lg">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Form Jurnal KBM
            </h2>
        </div>
        <div class="p-6">
            <form id="formJurnal">
                <?= csrf_field() ?>
                <div class="mb-6">
                    <label for="tujuan_pembelajaran" class="form-label">Tujuan Pembelajaran <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="tujuan_pembelajaran" name="tujuan_pembelajaran" rows="4" required><?= $jurnal['tujuan_pembelajaran'] ?></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="kegiatan_pembelajaran" class="form-label">Kegiatan Pembelajaran <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="kegiatan_pembelajaran" name="kegiatan_pembelajaran" rows="4" required><?= $jurnal['kegiatan_pembelajaran'] ?></textarea>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-3">
                    <label for="media_ajar" class="form-label">Media Ajar</label>
                    <textarea class="form-control" id="media_ajar" name="media_ajar" rows="3"><?= $jurnal['media_ajar'] ?? '' ?></textarea>
                    <small class="text-muted">Contoh: Papan tulis, LCD Proyektor, Google Classroom, dll.</small>
                </div>

                <div class="mb-3">
                    <label for="penilaian" class="form-label">Penilaian</label>
                    <textarea class="form-control" id="penilaian" name="penilaian" rows="3"><?= $jurnal['penilaian'] ?? '' ?></textarea>
                    <small class="text-muted">Contoh: Quiz, Tugas, Observasi, dll.</small>
                </div>

                <div class="mb-3">
                    <label for="catatan_khusus" class="form-label">Catatan Khusus</label>
                    <textarea class="form-control" id="catatan_khusus" name="catatan_khusus" rows="3"><?= $jurnal['catatan_khusus'] ?? '' ?></textarea>
                    <small class="text-muted">Catatan tambahan tentang pembelajaran hari ini.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('guru/jurnal') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnUpdate">
                        <i class="fas fa-save me-1"></i> Update Jurnal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('formJurnal').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnUpdate = document.getElementById('btnUpdate');
    btnUpdate.disabled = true;
    btnUpdate.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('guru/jurnal/update/' . $jurnal['id']) ?>', {
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
            btnUpdate.disabled = false;
            btnUpdate.innerHTML = '<i class="fas fa-save me-1"></i> Update Jurnal';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan data');
        btnUpdate.disabled = false;
        btnUpdate.innerHTML = '<i class="fas fa-save me-1"></i> Update Jurnal';
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
