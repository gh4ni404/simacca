<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-alt text-primary"></i> Detail Absensi
            </h1>
            <p class="text-muted mb-0"><?= date('l, d F Y', strtotime($absensi['tanggal'])) ?></p>
        </div>
        <div>
            <a href="<?= base_url('guru/absensi-guru/history') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Info Card -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> Informasi Absensi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Status Kehadiran</h6>
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
                            <h4><span class="badge bg-<?= $class ?>"><?= ucfirst($absensi['status']) ?></span></h4>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Tanggal</h6>
                            <p class="h5"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="text-success mb-3">
                                        <i class="fas fa-sign-in-alt"></i> Check-In
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <strong>Waktu:</strong>
                                        <p class="h4 text-success">
                                            <?= $absensi['jam_masuk'] ? date('H:i:s', strtotime($absensi['jam_masuk'])) : '-' ?>
                                        </p>
                                    </div>

                                    <?php if ($absensi['keterangan_masuk']): ?>
                                        <div class="mb-3">
                                            <strong>Keterangan:</strong>
                                            <p><?= esc($absensi['keterangan_masuk']) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($absensi['latitude_masuk'] && $absensi['longitude_masuk']): ?>
                                        <div class="mb-3">
                                            <strong>Lokasi:</strong>
                                            <p class="mb-1">
                                                <small>Lat: <?= $absensi['latitude_masuk'] ?></small><br>
                                                <small>Long: <?= $absensi['longitude_masuk'] ?></small>
                                            </p>
                                            <a href="https://www.google.com/maps?q=<?= $absensi['latitude_masuk'] ?>,<?= $absensi['longitude_masuk'] ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-map-marker-alt"></i> Lihat di Map
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="card border-<?= $absensi['jam_keluar'] ? 'info' : 'secondary' ?>">
                                <div class="card-body">
                                    <h6 class="text-<?= $absensi['jam_keluar'] ? 'info' : 'muted' ?> mb-3">
                                        <i class="fas fa-sign-out-alt"></i> Check-Out
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <strong>Waktu:</strong>
                                        <?php if ($absensi['jam_keluar']): ?>
                                            <p class="h4 text-info">
                                                <?= date('H:i:s', strtotime($absensi['jam_keluar'])) ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-muted">Belum check-out</p>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($absensi['keterangan_keluar']): ?>
                                        <div class="mb-3">
                                            <strong>Keterangan:</strong>
                                            <p><?= esc($absensi['keterangan_keluar']) ?></p>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($absensi['latitude_keluar'] && $absensi['longitude_keluar']): ?>
                                        <div class="mb-3">
                                            <strong>Lokasi:</strong>
                                            <p class="mb-1">
                                                <small>Lat: <?= $absensi['latitude_keluar'] ?></small><br>
                                                <small>Long: <?= $absensi['longitude_keluar'] ?></small>
                                            </p>
                                            <a href="https://www.google.com/maps?q=<?= $absensi['latitude_keluar'] ?>,<?= $absensi['longitude_keluar'] ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-map-marker-alt"></i> Lihat di Map
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($absensi['jam_masuk'] && $absensi['jam_keluar']): ?>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-muted mb-2">Durasi Kerja</h6>
                                <?php
                                $masuk = strtotime($absensi['jam_masuk']);
                                $keluar = strtotime($absensi['jam_keluar']);
                                $diff = $keluar - $masuk;
                                $hours = floor($diff / 3600);
                                $minutes = floor(($diff % 3600) / 60);
                                ?>
                                <p class="h4 text-primary">
                                    <i class="fas fa-clock"></i> <?= $hours ?> jam <?= $minutes ?> menit
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Photo Cards -->
        <div class="col-lg-4 mb-4">
            <!-- Check-In Photo -->
            <?php if ($absensi['foto_masuk']): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-camera"></i> Foto Check-In
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= base_url('writable/' . $absensi['foto_masuk']) ?>" 
                             class="img-fluid rounded" 
                             alt="Foto Check-In"
                             style="max-height: 300px; cursor: pointer;"
                             onclick="showImage('<?= base_url('writable/' . $absensi['foto_masuk']) ?>')">
                        <p class="mt-2 mb-0">
                            <small class="text-muted">
                                <?= $absensi['jam_masuk'] ? date('H:i:s', strtotime($absensi['jam_masuk'])) : '' ?>
                            </small>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Check-Out Photo -->
            <?php if ($absensi['foto_keluar']): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-camera"></i> Foto Check-Out
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= base_url('writable/' . $absensi['foto_keluar']) ?>" 
                             class="img-fluid rounded" 
                             alt="Foto Check-Out"
                             style="max-height: 300px; cursor: pointer;"
                             onclick="showImage('<?= base_url('writable/' . $absensi['foto_keluar']) ?>')">
                        <p class="mt-2 mb-0">
                            <small class="text-muted">
                                <?= $absensi['jam_keluar'] ? date('H:i:s', strtotime($absensi['jam_keluar'])) : '' ?>
                            </small>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Preview Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid" alt="Preview">
            </div>
        </div>
    </div>
</div>

<script>
function showImage(url) {
    document.getElementById('previewImage').src = url;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}
</script>

<?= $this->endSection() ?>
