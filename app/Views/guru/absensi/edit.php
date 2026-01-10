<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? 'Edit Absensi' ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('guru/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('guru/absensi') ?>">Absensi</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('guru/absensi/detail/' . $absensi['id']) ?>">Detail</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Absensi Info Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-info-circle me-1"></i>
            Informasi Absensi
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Tanggal:</strong><br>
                    <?= date('d F Y', strtotime($absensi['tanggal'])) ?>
                </div>
                <div class="col-md-3">
                    <strong>Mata Pelajaran:</strong><br>
                    <?= $absensi['nama_mapel'] ?>
                </div>
                <div class="col-md-3">
                    <strong>Kelas:</strong><br>
                    <?= $absensi['nama_kelas'] ?>
                </div>
                <div class="col-md-3">
                    <strong>Hari:</strong><br>
                    <?= $absensi['hari'] ?? '-' ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="<?= base_url('guru/absensi/update/' . $absensi['id']) ?>" method="POST" id="formEditAbsensi">
        <?= csrf_field() ?>

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-edit me-1"></i>
                Edit Data Absensi
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pertemuan_ke" class="form-label">Pertemuan Ke <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="pertemuan_ke" name="pertemuan_ke" 
                               value="<?= old('pertemuan_ke', $absensi['pertemuan_ke']) ?>" required min="1">
                    </div>
                    <div class="col-md-6">
                        <label for="materi_pembelajaran" class="form-label">Materi Pembelajaran</label>
                        <input type="text" class="form-control" id="materi_pembelajaran" name="materi_pembelajaran" 
                               value="<?= old('materi_pembelajaran', $absensi['materi_pembelajaran']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Siswa -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-users me-1"></i>
                    Daftar Kehadiran Siswa
                </span>
                <div>
                    <button type="button" class="btn btn-sm btn-success" onclick="setAllStatus('hadir')">
                        <i class="fas fa-check me-1"></i> Semua Hadir
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($siswaList)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Tidak ada siswa dalam kelas ini.
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th width="100">NIS</th>
                                <th>Nama Siswa</th>
                                <th width="150">Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Create array of existing absensi details
                            $existingDetails = [];
                            foreach ($absensiDetails as $detail) {
                                $existingDetails[$detail['siswa_id']] = $detail;
                            }
                            
                            foreach ($siswaList as $siswa): 
                                $detail = $existingDetails[$siswa['id']] ?? null;
                                $currentStatus = $detail ? $detail['status'] : 'hadir';
                                $currentKeterangan = $detail ? $detail['keterangan'] : '';
                                
                                // Check if student has approved izin
                                $hasIzin = false;
                                foreach ($approvedIzin as $izin) {
                                    if ($izin['siswa_id'] == $siswa['id']) {
                                        $hasIzin = true;
                                        break;
                                    }
                                }
                            ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= $siswa['nis'] ?></td>
                                <td>
                                    <?= $siswa['nama_lengkap'] ?>
                                    <?php if ($hasIzin): ?>
                                    <span class="badge bg-info ms-2">
                                        <i class="fas fa-envelope me-1"></i> Ada Izin
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select class="form-select form-select-sm status-select" 
                                            name="siswa[<?= $siswa['id'] ?>][status]" 
                                            data-siswa-id="<?= $siswa['id'] ?>"
                                            required>
                                        <option value="hadir" <?= $currentStatus == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                                        <option value="izin" <?= $currentStatus == 'izin' ? 'selected' : '' ?>>Izin</option>
                                        <option value="sakit" <?= $currentStatus == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                                        <option value="alpa" <?= $currentStatus == 'alpa' ? 'selected' : '' ?>>Alpa</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" 
                                           class="form-control form-control-sm keterangan-input" 
                                           name="siswa[<?= $siswa['id'] ?>][keterangan]" 
                                           id="keterangan_<?= $siswa['id'] ?>"
                                           value="<?= $currentKeterangan ?>"
                                           placeholder="Keterangan (opsional)">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('guru/absensi/detail/' . $absensi['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Set all students status
function setAllStatus(status) {
    const selects = document.querySelectorAll('.status-select');
    selects.forEach(select => {
        select.value = status;
    });
}

// Auto focus keterangan when status is not hadir
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        const siswaId = this.dataset.siswaId;
        const keteranganInput = document.getElementById('keterangan_' + siswaId);
        
        if (this.value !== 'hadir' && keteranganInput) {
            keteranganInput.focus();
        }
    });
});

// Form validation
document.getElementById('formEditAbsensi').addEventListener('submit', function(e) {
    const btnSubmit = document.getElementById('btnSubmit');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
});
</script>

<?= $this->endSection() ?>
