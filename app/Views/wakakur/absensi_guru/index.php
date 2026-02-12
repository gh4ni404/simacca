<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-check text-primary"></i> Monitoring Absensi Guru
            </h1>
            <p class="text-muted mb-0">Real-time monitoring kehadiran guru hari ini</p>
        </div>
        <div>
            <a href="<?= base_url('wakakur/absensi-guru/laporan') ?>" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Summary Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Guru</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['total_guru'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sudah Check-In</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['sudah_checkin'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-in-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Belum Check-In</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['belum_checkin'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sudah Check-Out</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $summary['sudah_checkout'] ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sign-out-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card text-center border-success">
                <div class="card-body py-2">
                    <h6 class="text-success mb-0">Hadir</h6>
                    <h4 class="mb-0"><?= $summary['hadir'] ?? 0 ?></h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card text-center border-warning">
                <div class="card-body py-2">
                    <h6 class="text-warning mb-0">Terlambat</h6>
                    <h4 class="mb-0"><?= $summary['terlambat'] ?? 0 ?></h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card text-center border-info">
                <div class="card-body py-2">
                    <h6 class="text-info mb-0">Izin</h6>
                    <h4 class="mb-0"><?= $summary['izin'] ?? 0 ?></h4>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card text-center border-primary">
                <div class="card-body py-2">
                    <h6 class="text-primary mb-0">Sakit</h6>
                    <h4 class="mb-0"><?= $summary['sakit'] ?? 0 ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filter Data
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= base_url('wakakur/absensi-guru') ?>">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= $filters['tanggal'] ?? '' ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Guru</label>
                        <select name="guru_id" class="form-control">
                            <option value="">Semua Guru</option>
                            <?php foreach ($guruList as $guru): ?>
                                <option value="<?= $guru['id'] ?>" <?= ($filters['guru_id'] ?? '') == $guru['id'] ? 'selected' : '' ?>>
                                    <?= esc($guru['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="hadir" <?= ($filters['status'] ?? '') == 'hadir' ? 'selected' : '' ?>>Hadir</option>
                            <option value="terlambat" <?= ($filters['status'] ?? '') == 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                            <option value="izin" <?= ($filters['status'] ?? '') == 'izin' ? 'selected' : '' ?>>Izin</option>
                            <option value="sakit" <?= ($filters['status'] ?? '') == 'sakit' ? 'selected' : '' ?>>Sakit</option>
                            <option value="alpha" <?= ($filters['status'] ?? '') == 'alpha' ? 'selected' : '' ?>>Alpha</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="<?= base_url('wakakur/absensi-guru') ?>" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Absensi List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Absensi Guru
            </h6>
            <span class="badge bg-info">Total: <?= count($absensiList) ?> record(s)</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Jam Keluar</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($absensiList)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Belum ada data absensi
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach ($absensiList as $absensi): 
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= esc($absensi['nama_guru']) ?></td>
                                    <td><?= esc($absensi['nip'] ?? '-') ?></td>
                                    <td class="text-center"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></td>
                                    <td class="text-center">
                                        <?php if ($absensi['jam_masuk']): ?>
                                            <span class="badge bg-success"><?= date('H:i', strtotime($absensi['jam_masuk'])) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($absensi['jam_keluar']): ?>
                                            <span class="badge bg-info"><?= date('H:i', strtotime($absensi['jam_keluar'])) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badgeClass = [
                                            'hadir' => 'success',
                                            'terlambat' => 'warning',
                                            'izin' => 'info',
                                            'sakit' => 'primary',
                                            'alpha' => 'danger'
                                        ];
                                        $class = $badgeClass[$absensi['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $class ?>"><?= ucfirst($absensi['status']) ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('wakakur/absensi-guru/detail/' . $absensi['guru_id']) ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-warning btn-update-status" 
                                                data-id="<?= $absensi['id'] ?>"
                                                data-status="<?= $absensi['status'] ?>"
                                                title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($pager && count($absensiList) > 0): ?>
                <div class="mt-3">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="absensi_id" name="absensi_id">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="hadir">Hadir</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpha">Alpha</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveStatus">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update Status Modal
    const updateStatusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    
    document.querySelectorAll('.btn-update-status').forEach(btn => {
        btn.addEventListener('click', function() {
            const absensiId = this.dataset.id;
            const currentStatus = this.dataset.status;
            
            document.getElementById('absensi_id').value = absensiId;
            document.getElementById('status').value = currentStatus;
            document.getElementById('keterangan').value = '';
            
            updateStatusModal.show();
        });
    });
    
    // Save Status
    document.getElementById('btnSaveStatus').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('updateStatusForm'));
        
        fetch('<?= base_url('wakakur/absensi-guru/update-status') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status berhasil diupdate');
                location.reload();
            } else {
                alert('Gagal update status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    });
});
</script>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>

<?= $this->endSection() ?>
