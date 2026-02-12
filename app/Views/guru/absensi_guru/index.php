<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-fingerprint text-primary"></i> Absensi Guru
        </h1>
        <p class="text-muted mb-0">Check-in dan check-out kehadiran harian</p>
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

    <div class="row">
        <!-- Left Column: Check-in/Check-out -->
        <div class="col-lg-8 mb-4">
            <!-- Today's Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-calendar-day"></i> Status Hari Ini - <?= date('d F Y') ?>
                    </h6>
                </div>
                <div class="card-body">
                    <?php if ($hasCheckedIn && $todayAbsensi): ?>
                        <!-- Already Checked In -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <h6 class="text-success"><i class="fas fa-sign-in-alt"></i> Check-In</h6>
                                        <p class="h3 mb-2"><?= date('H:i', strtotime($todayAbsensi['jam_masuk'])) ?></p>
                                        <span class="badge bg-<?= $todayAbsensi['status'] == 'hadir' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($todayAbsensi['status']) ?>
                                        </span>
                                        <?php if ($todayAbsensi['foto_masuk']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                                    onclick="showImage('<?= base_url('writable/' . $todayAbsensi['foto_masuk']) ?>')">
                                                <i class="fas fa-image"></i> Lihat Foto
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card border-<?= $hasCheckedOut ? 'info' : 'warning' ?>">
                                    <div class="card-body">
                                        <h6 class="text-<?= $hasCheckedOut ? 'info' : 'warning' ?>">
                                            <i class="fas fa-sign-out-alt"></i> Check-Out
                                        </h6>
                                        <?php if ($hasCheckedOut): ?>
                                            <p class="h3 mb-2"><?= date('H:i', strtotime($todayAbsensi['jam_keluar'])) ?></p>
                                            <span class="badge bg-info">Selesai</span>
                                            <?php if ($todayAbsensi['foto_keluar']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                                        onclick="showImage('<?= base_url('writable/' . $todayAbsensi['foto_keluar']) ?>')">
                                                    <i class="fas fa-image"></i> Lihat Foto
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <p class="text-muted mb-2">Belum check-out</p>
                                            <button type="button" class="btn btn-info" id="btnCheckOut">
                                                <i class="fas fa-sign-out-alt"></i> Check-Out Sekarang
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($todayAbsensi['keterangan_masuk']): ?>
                            <div class="alert alert-info mt-3">
                                <strong>Keterangan Check-In:</strong> <?= esc($todayAbsensi['keterangan_masuk']) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($hasCheckedOut && $todayAbsensi['keterangan_keluar']): ?>
                            <div class="alert alert-info mt-3">
                                <strong>Keterangan Check-Out:</strong> <?= esc($todayAbsensi['keterangan_keluar']) ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Not Checked In Yet -->
                        <div class="text-center py-5">
                            <i class="fas fa-clock fa-5x text-muted mb-4"></i>
                            <h4>Anda belum melakukan check-in hari ini</h4>
                            <p class="text-muted mb-4">Silakan lakukan check-in untuk mencatat kehadiran Anda</p>
                            <button type="button" class="btn btn-primary btn-lg" id="btnCheckIn">
                                <i class="fas fa-sign-in-alt"></i> Check-In Sekarang
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-history"></i> Riwayat Terakhir
                        </h6>
                        <a href="<?= base_url('guru/absensi-guru/history') ?>" class="btn btn-sm btn-outline-primary">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-center">Check-In</th>
                                    <th class="text-center">Check-Out</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentHistory)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada riwayat</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentHistory as $history): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($history['tanggal'])) ?></td>
                                            <td class="text-center">
                                                <?= $history['jam_masuk'] ? date('H:i', strtotime($history['jam_masuk'])) : '-' ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $history['jam_keluar'] ? date('H:i', strtotime($history['jam_keluar'])) : '-' ?>
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
                                                $class = $badgeClass[$history['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge bg-<?= $class ?>"><?= ucfirst($history['status']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Statistics -->
        <div class="col-lg-4 mb-4">
            <!-- Monthly Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-pie"></i> Statistik Bulan Ini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-success"><i class="fas fa-check-circle"></i> Hadir</span>
                            <strong><?= $monthlyStats['total_hadir'] ?? 0 ?></strong>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Terlambat</span>
                            <strong><?= $monthlyStats['total_terlambat'] ?? 0 ?></strong>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-warning" style="width: 80%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-info"><i class="fas fa-file-alt"></i> Izin</span>
                            <strong><?= $monthlyStats['total_izin'] ?? 0 ?></strong>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-info" style="width: 60%"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-primary"><i class="fas fa-medkit"></i> Sakit</span>
                            <strong><?= $monthlyStats['total_sakit'] ?? 0 ?></strong>
                        </div>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-primary" style="width: 40%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> Informasi
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Waktu Check-In:</strong> 06:00 - 07:00</p>
                    <p class="mb-2"><strong>Batas Terlambat:</strong> 07:00</p>
                    <p class="mb-2"><strong>Waktu Kerja:</strong> 07:00 - 16:00</p>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-lightbulb"></i> Check-in sebelum pukul 07:00 untuk status hadir tepat waktu
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-In Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-sign-in-alt"></i> Check-In</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkInForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Foto <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="foto" id="fotoCheckIn" accept="image/*" capture="user" required>
                        <small class="text-muted">Upload foto selfie untuk check-in</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan_masuk" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="latitude" id="latitudeCheckIn">
                    <input type="hidden" name="longitude" id="longitudeCheckIn">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitCheckIn">
                        <i class="fas fa-save"></i> Submit Check-In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Check-Out Modal -->
<div class="modal fade" id="checkOutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-sign-out-alt"></i> Check-Out</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="checkOutForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Foto (Opsional)</label>
                        <input type="file" class="form-control" name="foto" id="fotoCheckOut" accept="image/*" capture="user">
                        <small class="text-muted">Upload foto untuk check-out (opsional)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan_keluar" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="latitude" id="latitudeCheckOut">
                    <input type="hidden" name="longitude" id="longitudeCheckOut">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info" id="btnSubmitCheckOut">
                        <i class="fas fa-save"></i> Submit Check-Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Foto Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid" alt="Foto Absensi">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkInModal = new bootstrap.Modal(document.getElementById('checkInModal'));
    const checkOutModal = new bootstrap.Modal(document.getElementById('checkOutModal'));
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));

    // Check-In Button
    const btnCheckIn = document.getElementById('btnCheckIn');
    if (btnCheckIn) {
        btnCheckIn.addEventListener('click', function() {
            getLocation('checkIn');
            checkInModal.show();
        });
    }

    // Check-Out Button
    const btnCheckOut = document.getElementById('btnCheckOut');
    if (btnCheckOut) {
        btnCheckOut.addEventListener('click', function() {
            getLocation('checkOut');
            checkOutModal.show();
        });
    }

    // Check-In Form Submit
    document.getElementById('checkInForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        submitAbsensi('check-in', formData);
    });

    // Check-Out Form Submit
    document.getElementById('checkOutForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        submitAbsensi('check-out', formData);
    });

    // Get Geolocation
    function getLocation(type) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    if (type === 'checkIn') {
                        document.getElementById('latitudeCheckIn').value = position.coords.latitude;
                        document.getElementById('longitudeCheckIn').value = position.coords.longitude;
                    } else {
                        document.getElementById('latitudeCheckOut').value = position.coords.latitude;
                        document.getElementById('longitudeCheckOut').value = position.coords.longitude;
                    }
                },
                function(error) {
                    console.log('Geolocation error:', error);
                }
            );
        }
    }

    // Submit Absensi
    function submitAbsensi(type, formData) {
        const url = type === 'check-in' ? '<?= base_url('guru/absensi-guru/check-in') ?>' : '<?= base_url('guru/absensi-guru/check-out') ?>';
        const btn = type === 'check-in' ? document.getElementById('btnSubmitCheckIn') : document.getElementById('btnSubmitCheckOut');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
                btn.disabled = false;
                btn.innerHTML = type === 'check-in' ? '<i class="fas fa-save"></i> Submit Check-In' : '<i class="fas fa-save"></i> Submit Check-Out';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat submit');
            btn.disabled = false;
            btn.innerHTML = type === 'check-in' ? '<i class="fas fa-save"></i> Submit Check-In' : '<i class="fas fa-save"></i> Submit Check-Out';
        });
    }
});

// Show Image
function showImage(url) {
    document.getElementById('previewImage').src = url;
    const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
    imageModal.show();
}
</script>

<?= $this->endSection() ?>
