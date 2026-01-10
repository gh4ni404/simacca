<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? 'Detail Absensi' ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('guru/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('guru/absensi') ?>">Absensi</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Absensi Info Card -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-info-circle me-1"></i>
                    Informasi Absensi
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Tanggal</th>
                                    <td>: <?= date('d F Y', strtotime($absensi['tanggal'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Hari</th>
                                    <td>: <?= $absensi['hari'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Pertemuan Ke</th>
                                    <td>: <?= $absensi['pertemuan_ke'] ?></td>
                                </tr>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <td>: <?= $absensi['nama_mapel'] ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Kelas</th>
                                    <td>: <?= $absensi['nama_kelas'] ?></td>
                                </tr>
                                <tr>
                                    <th>Guru</th>
                                    <td>: <?= $absensi['nama_guru'] ?></td>
                                </tr>
                                <tr>
                                    <th>Materi</th>
                                    <td>: <?= $absensi['materi_pembelajaran'] ?? '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>: <?= date('d/m/Y H:i', strtotime($absensi['created_at'])) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-chart-pie me-1"></i>
                    Statistik Kehadiran
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span><i class="fas fa-check-circle text-success"></i> Hadir</span>
                            <strong><?= $statistics['hadir'] ?></strong>
                        </div>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-success" style="width: <?= $statistics['percentage'] ?>%">
                                <?= $statistics['percentage'] ?>%
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-envelope text-info"></i> Izin</span>
                            <strong><?= $statistics['izin'] ?></strong>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-hospital text-warning"></i> Sakit</span>
                            <strong><?= $statistics['sakit'] ?></strong>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span><i class="fas fa-times-circle text-danger"></i> Alpa</span>
                            <strong><?= $statistics['alpa'] ?></strong>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <span><strong>Total Siswa</strong></span>
                        <strong><?= count($absensiDetails) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-users me-1"></i>
                Daftar Kehadiran Siswa
            </span>
            <div>
                <a href="<?= base_url('guru/absensi/print/' . $absensi['id']) ?>" class="btn btn-sm btn-success" target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak
                </a>
                <?php if ($isEditable): ?>
                <a href="<?= base_url('guru/absensi/edit/' . $absensi['id']) ?>" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $absensi['id'] ?>)">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
                <?php else: ?>
                <span class="badge bg-secondary">
                    <i class="fas fa-lock me-1"></i> Tidak dapat diedit (>24 jam)
                </span>
                <?php endif; ?>
                <a href="<?= base_url('guru/absensi') ?>" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($absensiDetails)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Tidak ada data kehadiran siswa.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th width="100">NIS</th>
                            <th>Nama Siswa</th>
                            <th width="100">Status</th>
                            <th>Keterangan</th>
                            <th width="150">Waktu Absen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach ($absensiDetails as $detail): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $detail['nis'] ?></td>
                            <td><?= $detail['nama_lengkap'] ?></td>
                            <td class="text-center">
                                <?php
                                $badgeClass = '';
                                switch($detail['status']) {
                                    case 'hadir':
                                        $badgeClass = 'bg-success';
                                        $icon = 'fa-check-circle';
                                        break;
                                    case 'izin':
                                        $badgeClass = 'bg-info';
                                        $icon = 'fa-envelope';
                                        break;
                                    case 'sakit':
                                        $badgeClass = 'bg-warning';
                                        $icon = 'fa-hospital';
                                        break;
                                    case 'alpa':
                                        $badgeClass = 'bg-danger';
                                        $icon = 'fa-times-circle';
                                        break;
                                    default:
                                        $badgeClass = 'bg-secondary';
                                        $icon = 'fa-question-circle';
                                }
                                ?>
                                <span class="badge <?= $badgeClass ?>">
                                    <i class="fas <?= $icon ?> me-1"></i>
                                    <?= ucfirst($detail['status']) ?>
                                </span>
                            </td>
                            <td><?= $detail['keterangan'] ?? '-' ?></td>
                            <td><?= date('H:i', strtotime($detail['waktu_absen'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-book me-2"></i>Jurnal KBM</h5>
                    <p class="card-text">Lengkapi jurnal pembelajaran untuk absensi ini</p>
                    <a href="<?= base_url('guru/jurnal/tambah/' . $absensi['id']) ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Buat Jurnal
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-list me-2"></i>Riwayat Absensi</h5>
                    <p class="card-text">Lihat riwayat absensi kelas ini</p>
                    <a href="<?= base_url('guru/absensi?kelas_id=' . ($absensi['kelas_id'] ?? '')) ?>" class="btn btn-secondary">
                        <i class="fas fa-history me-1"></i> Lihat Riwayat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<form action="<?= base_url('guru/absensi/delete/' . $absensi['id']) ?>" method="POST" id="formDelete">
    <?= csrf_field() ?>
</form>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data absensi ini?\n\nSemua data kehadiran siswa juga akan dihapus!')) {
        document.getElementById('formDelete').submit();
    }
}

// Auto print if print parameter exists
<?php if ($this->request->getGet('print') == 'true'): ?>
window.onload = function() {
    window.print();
}
<?php endif; ?>
</script>

<?= $this->endSection() ?>
