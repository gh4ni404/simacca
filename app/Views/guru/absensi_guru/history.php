<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-history text-primary"></i> Riwayat Absensi
            </h1>
            <p class="text-muted mb-0">Histori kehadiran Anda</p>
        </div>
        <div>
            <a href="<?= base_url('guru/absensi-guru') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $monthlyStats['total_hadir'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Terlambat</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $monthlyStats['total_terlambat'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $monthlyStats['total_izin'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sakit</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $monthlyStats['total_sakit'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body py-3">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpha</div>
                    <div class="h5 mb-0 font-weight-bold"><?= $monthlyStats['total_alpha'] ?? 0 ?></div>
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
            <form method="GET" action="<?= base_url('guru/absensi-guru/history') ?>">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>" <?= ($filters['bulan'] ?? '') == $i ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
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
                            <a href="<?= base_url('guru/absensi-guru/history') ?>" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- History Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar Absensi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Tanggal</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Jam Keluar</th>
                            <th class="text-center">Durasi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($absensiList)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Belum ada data absensi
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach ($absensiList as $absensi): 
                                // Calculate duration
                                $durasi = '-';
                                if ($absensi['check_in'] && $absensi['check_out']) {
                                    $masuk = strtotime($absensi['check_in']);
                                    $keluar = strtotime($absensi['check_out']);
                                    $diff = $keluar - $masuk;
                                    $hours = floor($diff / 3600);
                                    $minutes = floor(($diff % 3600) / 60);
                                    $durasi = sprintf('%d jam %d menit', $hours, $minutes);
                                }
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($absensi['tanggal'])) ?>
                                        <br>
                                        <small class="text-muted"><?= date('l', strtotime($absensi['tanggal'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($absensi['check_in']): ?>
                                            <span class="badge bg-success"><?= date('H:i', strtotime($absensi['check_in'])) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($absensi['check_out']): ?>
                                            <span class="badge bg-info"><?= date('H:i', strtotime($absensi['check_out'])) ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Belum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <small><?= $durasi ?></small>
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
                                        <a href="<?= base_url('guru/absensi-guru/show/' . $absensi['id']) ?>" 
                                           class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
