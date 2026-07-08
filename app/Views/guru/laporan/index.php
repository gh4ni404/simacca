<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? 'Laporan Absensi' ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('guru/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Laporan Absensi</li>
    </ol>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Laporan
        </div>
        <div class="card-body">
            <form method="GET" action="<?= base_url('guru/laporan') ?>">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelasList as $id => $nama): ?>
                            <option value="<?= $id ?>" <?= ($kelasId == $id) ? 'selected' : '' ?>><?= $nama ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="<?= $startDate ?? '' ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" value="<?= $endDate ?? '' ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($laporan !== null): ?>
    
    <!-- Rekap Statistik -->
    <?php if ($rekap): ?>
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hadir</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rekap['total_hadir'] ?> 
                                <small>(<?= $rekap['persentase_hadir'] ?? 0 ?>%)</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sakit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rekap['total_sakit'] ?> 
                                <small>(<?= $rekap['persentase_sakit'] ?? 0 ?>%)</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hospital fa-2x text-warning"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Izin</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rekap['total_izin'] ?> 
                                <small>(<?= $rekap['persentase_izin'] ?? 0 ?>%)</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Alpa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $rekap['total_alpa'] ?> 
                                <small>(<?= $rekap['persentase_alpa'] ?? 0 ?>%)</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Tabel Laporan -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-table me-1"></i>
                Laporan Kehadiran Siswa
            </span>
            <button onclick="window.print()" class="btn btn-sm btn-success">
                <i class="fas fa-print me-1"></i> Cetak
            </button>
        </div>
        <div class="card-body">
            <?php if (empty($laporan)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-1"></i>
                Tidak ada data absensi untuk periode yang dipilih.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Alpa</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1; 
                        foreach ($laporan as $item): 
                            $persentase = $item['total'] > 0 ? round(($item['hadir'] / $item['total']) * 100, 1) : 0;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $item['siswa']['nis'] ?></td>
                            <td><?= $item['siswa']['nama_lengkap'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-success"><?= $item['hadir'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning"><?= $item['sakit'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info"><?= $item['izin'] ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger"><?= $item['alpa'] ?></span>
                            </td>
                            <td class="text-center"><strong><?= $item['total'] ?></strong></td>
                            <td class="text-center">
                                <strong><?= $persentase ?>%</strong>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if ($rekap): ?>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="3" class="text-end">Total:</th>
                            <th class="text-center"><?= $rekap['total_hadir'] ?></th>
                            <th class="text-center"><?= $rekap['total_sakit'] ?></th>
                            <th class="text-center"><?= $rekap['total_izin'] ?></th>
                            <th class="text-center"><?= $rekap['total_alpa'] ?></th>
                            <th class="text-center"><?= $rekap['total_siswa'] * $rekap['total_pertemuan'] ?></th>
                            <th class="text-center"><?= $rekap['persentase_hadir'] ?? 0 ?>%</th>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>

            <!-- Info Periode -->
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Laporan periode: <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?>
                    (Total <?= $rekap['total_pertemuan'] ?? 0 ?> pertemuan)
                </small>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php endif; ?>
</div>

<style>
@media print {
    .breadcrumb, .btn, .card-header button, form, .sidebar, nav {
        display: none !important;
    }
    .card {
        border: none !important;
    }
}
</style>

<?= $this->endSection() ?>
