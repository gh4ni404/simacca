<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? 'Edit Jurnal KBM' ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('guru/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('guru/jurnal') ?>">Jurnal KBM</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Form Edit Jurnal KBM
        </div>
        <div class="card-body">
            <!-- Info Absensi -->
            <div class="alert alert-info mb-4">
                <h5 class="alert-heading"><i class="fas fa-info-circle me-1"></i> Informasi Absensi</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?></p>
                        <p class="mb-1"><strong>Mata Pelajaran:</strong> <?= $jurnal['nama_mapel'] ?></p>
                        <p class="mb-1"><strong>Kelas:</strong> <?= $jurnal['nama_kelas'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Pertemuan Ke:</strong> <?= $jurnal['pertemuan_ke'] ?></p>
                        <p class="mb-1"><strong>Materi:</strong> <?= $jurnal['materi_pembelajaran'] ?></p>
                    </div>
                </div>
            </div>

            <form id="formJurnal">
                <?= csrf_field() ?>
                <div class="mb-3">
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
