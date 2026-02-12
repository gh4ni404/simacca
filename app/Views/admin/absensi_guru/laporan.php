<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-chart-bar text-primary"></i> Laporan Absensi Guru
            </h1>
            <p class="text-muted mb-0">Laporan historis kehadiran guru</p>
        </div>
        <div>
            <a href="<?= base_url('admin/absensi-guru') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <button type="button" class="btn btn-success" id="btnExport">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Record</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $stats['total_records'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $stats['total_hadir'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Terlambat</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $stats['total_terlambat'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $stats['total_izin'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sakit</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $stats['total_sakit'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $stats['total_alpha'] ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filter Laporan
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="<?= base_url('admin/absensi-guru/laporan') ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>" <?= ($filters['bulan'] ?? '') == $i ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label>Tahun</label>
                        <select name="tahun" class="form-control">
                            <?php 
                            $currentYear = date('Y');
                            for ($y = $currentYear; $y >= $currentYear - 5; $y--): 
                            ?>
                                <option value="<?= $y ?>" <?= ($filters['tahun'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Guru</label>
                        <select name="guru_id" class="form-control">
                            <option value="">Semua Guru</option>
                            <?php foreach ($guruList as $guru): ?>
                                <option value="<?= $guru['id'] ?>" <?= ($filters['guru_id'] ?? '') == $guru['id'] ? 'selected' : '' ?>>
                                    <?= esc($guru['nama_lengkap']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
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
                            <a href="<?= base_url('admin/absensi-guru/laporan') ?>" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table"></i> Data Absensi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Jam Keluar</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($absensiList)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Tidak ada data untuk ditampilkan
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach ($absensiList as $absensi): 
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></td>
                                    <td><?= esc($absensi['nama_guru']) ?></td>
                                    <td><?= esc($absensi['nip'] ?? '-') ?></td>
                                    <td class="text-center">
                                        <?= $absensi['check_in'] ? date('H:i', strtotime($absensi['check_in'])) : '-' ?>
                                    </td>
                                    <td class="text-center">
                                        <?= $absensi['check_out'] ? date('H:i', strtotime($absensi['check_out'])) : '-' ?>
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
                                    <td><?= esc($absensi['keterangan_masuk'] ?? '-') ?></td>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Export to Excel
    document.getElementById('btnExport').addEventListener('click', function() {
        const form = document.getElementById('filterForm');
        const params = new URLSearchParams(new FormData(form));
        window.location.href = '<?= base_url('admin/absensi-guru/export-excel') ?>?' + params.toString();
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
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
</style>

<?= $this->endSection() ?>
