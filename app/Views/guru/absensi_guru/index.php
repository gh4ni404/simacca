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
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
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
                                        <p class="h3 mb-2"><?= date('H:i', strtotime($todayAbsensi['check_in'])) ?></p>
                                        <span class="badge bg-<?= $todayAbsensi['status'] == 'hadir' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($todayAbsensi['status']) ?>
                                        </span>
                                        <?php if ($todayAbsensi['foto_check_in']): ?>
                                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                                    onclick="showImage('<?= base_url('writable/' . $todayAbsensi['foto_check_in']) ?>')">
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
                                            <p class="h3 mb-2"><?= date('H:i', strtotime($todayAbsensi['check_out'])) ?></p>
                                            <span class="badge bg-info">Selesai</span>
                                            <?php if ($todayAbsensi['foto_check_out']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" 
                                                        onclick="showImage('<?= base_url('writable/' . $todayAbsensi['foto_check_out']) ?>')">
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
                                                <?= $history['check_in'] ? date('H:i', strtotime($history['check_in'])) : '-' ?>
                                            </td>
                                            <td class="text-center">
                                                <?= $history['check_out'] ? date('H:i', strtotime($history['check_out'])) : '-' ?>
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
                    <p class="mb-2"><strong>Waktu Check-In:</strong> 06:00 - 10:00</p>
                    <p class="mb-2"><strong>Batas Tepat Waktu:</strong> 07:15</p>
                    <p class="mb-2"><strong>Batas Akhir Hadir:</strong> 10:00</p>
                    <p class="mb-2"><strong>Jam Kerja Minimum:</strong> 8 jam (480 menit)</p>
                    <hr>
                    <small class="text-muted">
                        <i class="fas fa-lightbulb"></i> <strong>Tips:</strong> Check-in sebelum 07:15 untuk status hadir tepat waktu. 
                        Setelah 07:15 akan tercatat terlambat. Check-in setelah 10:00 akan tercatat alpha.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-In Modal -->
<div class="modal fade hidden" id="checkInModal" tabindex="-1" role="dialog" data-modal-overlay="checkInModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-sign-in-alt"></i> Check-In</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeModal('checkInModal'); cameraCheckIn.reset();"></button>
            </div>
            <form id="checkInForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Camera Section -->
                    <div class="mb-3">
                        <label class="form-label">Foto Selfie <span class="text-danger">*</span></label>
                        
                        <!-- Camera/Upload Toggle -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="photoMethodCheckIn" id="cameraCheckIn" checked>
                            <label class="btn btn-outline-primary py-2 py-sm-1" for="cameraCheckIn">
                                <i class="fas fa-camera"></i> <span class="d-none d-sm-inline">Ambil</span> Foto
                            </label>
                            
                            <input type="radio" class="btn-check" name="photoMethodCheckIn" id="uploadCheckIn">
                            <label class="btn btn-outline-primary py-2 py-sm-1" for="uploadCheckIn">
                                <i class="fas fa-upload"></i> Upload<span class="d-none d-sm-inline"> File</span>
                            </label>
                        </div>

                        <!-- Camera Interface -->
                        <div id="cameraContainerCheckIn" class="camera-container">
                            <div class="position-relative bg-dark rounded" style="min-height: 300px;">
                                <video id="videoCheckIn" class="w-100 rounded" autoplay playsinline style="display: none;"></video>
                                <canvas id="canvasCheckIn" class="w-100 rounded" style="display: none;"></canvas>
                                <div id="cameraPlaceholderCheckIn" class="d-flex align-items-center justify-content-center h-100 text-white" style="min-height: 300px;">
                                    <div class="text-center">
                                        <i class="fas fa-camera fa-3x mb-3"></i>
                                        <p>Klik tombol untuk mengaktifkan kamera</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 d-grid gap-2 d-sm-flex">
                                <button type="button" class="btn btn-success flex-sm-fill" id="startCameraCheckIn">
                                    <i class="fas fa-video"></i> <span class="d-none d-sm-inline">Aktifkan</span> Kamera
                                </button>
                                <button type="button" class="btn btn-primary flex-sm-fill" id="captureCheckIn" style="display: none;">
                                    <i class="fas fa-camera"></i> Ambil Foto
                                </button>
                                <button type="button" class="btn btn-warning flex-sm-fill" id="retakeCheckIn" style="display: none;">
                                    <i class="fas fa-redo"></i> Ulangi
                                </button>
                            </div>
                        </div>

                        <!-- File Upload Interface -->
                        <div id="uploadContainerCheckIn" class="upload-container" style="display: none;">
                            <input type="file" class="form-control" id="fotoCheckIn" name="foto" accept="image/*" capture="user">
                            <small class="text-muted">Upload foto selfie untuk check-in</small>
                        </div>

                        <input type="hidden" name="foto_base64" id="fotoBase64CheckIn">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan_masuk" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="latitude" id="latitudeCheckIn">
                    <input type="hidden" name="longitude" id="longitudeCheckIn">
                </div>
                <div class="modal-footer flex-column flex-sm-row">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto mb-2 mb-sm-0" onclick="closeModal('checkInModal'); cameraCheckIn.reset();">Batal</button>
                    <button type="submit" class="btn btn-primary w-100 w-sm-auto" id="btnSubmitCheckIn">
                        <i class="fas fa-save"></i> Submit Check-In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Check-Out Modal -->
<div class="modal fade hidden" id="checkOutModal" tabindex="-1" role="dialog" data-modal-overlay="checkOutModal">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-sign-out-alt"></i> Check-Out</h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeModal('checkOutModal'); cameraCheckOut.reset();"></button>
            </div>
            <form id="checkOutForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Camera Section -->
                    <div class="mb-3">
                        <label class="form-label">Foto Selfie (Opsional)</label>
                        
                        <!-- Camera/Upload Toggle -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="photoMethodCheckOut" id="cameraCheckOut" checked>
                            <label class="btn btn-outline-info py-2 py-sm-1" for="cameraCheckOut">
                                <i class="fas fa-camera"></i> <span class="d-none d-sm-inline">Ambil</span> Foto
                            </label>
                            
                            <input type="radio" class="btn-check" name="photoMethodCheckOut" id="uploadCheckOut">
                            <label class="btn btn-outline-info py-2 py-sm-1" for="uploadCheckOut">
                                <i class="fas fa-upload"></i> Upload<span class="d-none d-sm-inline"> File</span>
                            </label>
                        </div>

                        <!-- Camera Interface -->
                        <div id="cameraContainerCheckOut" class="camera-container">
                            <div class="position-relative bg-dark rounded" style="min-height: 300px;">
                                <video id="videoCheckOut" class="w-100 rounded" autoplay playsinline style="display: none;"></video>
                                <canvas id="canvasCheckOut" class="w-100 rounded" style="display: none;"></canvas>
                                <div id="cameraPlaceholderCheckOut" class="d-flex align-items-center justify-content-center h-100 text-white" style="min-height: 300px;">
                                    <div class="text-center">
                                        <i class="fas fa-camera fa-3x mb-3"></i>
                                        <p>Klik tombol untuk mengaktifkan kamera</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 d-grid gap-2 d-sm-flex">
                                <button type="button" class="btn btn-success flex-sm-fill" id="startCameraCheckOut">
                                    <i class="fas fa-video"></i> <span class="d-none d-sm-inline">Aktifkan</span> Kamera
                                </button>
                                <button type="button" class="btn btn-info flex-sm-fill" id="captureCheckOut" style="display: none;">
                                    <i class="fas fa-camera"></i> Ambil Foto
                                </button>
                                <button type="button" class="btn btn-warning flex-sm-fill" id="retakeCheckOut" style="display: none;">
                                    <i class="fas fa-redo"></i> Ulangi
                                </button>
                            </div>
                        </div>

                        <!-- File Upload Interface -->
                        <div id="uploadContainerCheckOut" class="upload-container" style="display: none;">
                            <input type="file" class="form-control" id="fotoCheckOut" name="foto" accept="image/*" capture="user">
                            <small class="text-muted">Upload foto untuk check-out (opsional)</small>
                        </div>

                        <input type="hidden" name="foto_base64" id="fotoBase64CheckOut">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan_keluar" rows="3"></textarea>
                    </div>
                    <input type="hidden" name="latitude" id="latitudeCheckOut">
                    <input type="hidden" name="longitude" id="longitudeCheckOut">
                </div>
                <div class="modal-footer flex-column flex-sm-row">
                    <button type="button" class="btn btn-secondary w-100 w-sm-auto mb-2 mb-sm-0" onclick="closeModal('checkOutModal'); cameraCheckOut.reset();">Batal</button>
                    <button type="submit" class="btn btn-info w-100 w-sm-auto" id="btnSubmitCheckOut">
                        <i class="fas fa-save"></i> Submit Check-Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade hidden" id="imageModal" tabindex="-1" role="dialog" data-modal-overlay="imageModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h5 class="modal-title">Foto Absensi</h5>
                <button type="button" class="btn-close" onclick="closeModal('imageModal');"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid" alt="Foto Absensi">
            </div>
        </div>
    </div>
</div>

<script>
// Camera handling class
class CameraHandler {
    constructor(prefix) {
        this.prefix = prefix;
        this.video = document.getElementById(`video${prefix}`);
        this.canvas = document.getElementById(`canvas${prefix}`);
        this.placeholder = document.getElementById(`cameraPlaceholder${prefix}`);
        this.startBtn = document.getElementById(`startCamera${prefix}`);
        this.captureBtn = document.getElementById(`capture${prefix}`);
        this.retakeBtn = document.getElementById(`retake${prefix}`);
        this.base64Input = document.getElementById(`fotoBase64${prefix}`);
        this.stream = null;
        this.photoTaken = false;
        
        this.initEventListeners();
    }
    
    initEventListeners() {
        this.startBtn?.addEventListener('click', () => this.startCamera());
        this.captureBtn?.addEventListener('click', () => this.capturePhoto());
        this.retakeBtn?.addEventListener('click', () => this.retakePhoto());
    }
    
    async startCamera() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                } 
            });
            
            this.video.srcObject = this.stream;
            this.video.style.display = 'block';
            this.placeholder.style.display = 'none';
            this.startBtn.style.display = 'none';
            this.captureBtn.style.display = 'block';
            
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin kamera.');
        }
    }
    
    capturePhoto() {
        // Set canvas size to match video
        this.canvas.width = this.video.videoWidth;
        this.canvas.height = this.video.videoHeight;
        
        // Draw video frame to canvas
        const context = this.canvas.getContext('2d');
        context.drawImage(this.video, 0, 0);
        
        // Get base64 image
        const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
        this.base64Input.value = imageData;
        
        // Stop camera stream
        this.stopCamera();
        
        // Show captured image
        this.canvas.style.display = 'block';
        this.video.style.display = 'none';
        this.captureBtn.style.display = 'none';
        this.retakeBtn.style.display = 'block';
        
        this.photoTaken = true;
    }
    
    retakePhoto() {
        this.canvas.style.display = 'none';
        this.base64Input.value = '';
        this.photoTaken = false;
        this.startCamera();
    }
    
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
    }
    
    reset() {
        this.stopCamera();
        this.video.style.display = 'none';
        this.canvas.style.display = 'none';
        this.placeholder.style.display = 'flex';
        this.startBtn.style.display = 'block';
        this.captureBtn.style.display = 'none';
        this.retakeBtn.style.display = 'none';
        this.base64Input.value = '';
        this.photoTaken = false;
    }
    
    hasPhoto() {
        return this.photoTaken || this.base64Input.value !== '';
    }
}

// Initialize camera handlers
const cameraCheckIn = new CameraHandler('CheckIn');
const cameraCheckOut = new CameraHandler('CheckOut');

// Toggle between camera and file upload
document.getElementById('cameraCheckIn')?.addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('cameraContainerCheckIn').style.display = 'block';
        document.getElementById('uploadContainerCheckIn').style.display = 'none';
    }
});

document.getElementById('uploadCheckIn')?.addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('cameraContainerCheckIn').style.display = 'none';
        document.getElementById('uploadContainerCheckIn').style.display = 'block';
        cameraCheckIn.reset();
    }
});

document.getElementById('cameraCheckOut')?.addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('cameraContainerCheckOut').style.display = 'block';
        document.getElementById('uploadContainerCheckOut').style.display = 'none';
    }
});

document.getElementById('uploadCheckOut')?.addEventListener('change', function() {
    if (this.checked) {
        document.getElementById('cameraContainerCheckOut').style.display = 'none';
        document.getElementById('uploadContainerCheckOut').style.display = 'block';
        cameraCheckOut.reset();
    }
});

// Clean up camera on modal close - handled by close button events
// No Bootstrap modal events needed

document.addEventListener('DOMContentLoaded', function() {
    // Modals are now handled by the modal_scripts() helper
    // No need to initialize Bootstrap modals

    // Check-In Button
    const btnCheckIn = document.getElementById('btnCheckIn');
    if (btnCheckIn) {
        btnCheckIn.addEventListener('click', function() {
            getLocation('checkIn');
            openModal('checkInModal');
            setTimeout(() => cameraCheckIn.start(), 100);
        });
    }

    // Check-Out Button
    const btnCheckOut = document.getElementById('btnCheckOut');
    if (btnCheckOut) {
        btnCheckOut.addEventListener('click', function() {
            getLocation('checkOut');
            openModal('checkOutModal');
            setTimeout(() => cameraCheckOut.start(), 100);
        });
    }

    // Check-In Form Submit
    document.getElementById('checkInForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate photo
        const usingCamera = document.getElementById('cameraCheckIn').checked;
        const usingUpload = document.getElementById('uploadCheckIn').checked;
        
        if (usingCamera && !cameraCheckIn.hasPhoto()) {
            alert('Silakan ambil foto selfie terlebih dahulu');
            return;
        }
        
        if (usingUpload && !document.getElementById('fotoCheckIn').files.length) {
            alert('Silakan pilih file foto terlebih dahulu');
            return;
        }
        
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
    // Get CSRF token from meta tag or cookie
    function getCsrfToken() {
        return '<?= csrf_hash() ?>';
    }

    function submitAbsensi(type, formData) {
        const url = type === 'check-in' ? '<?= base_url('guru/absensi-guru/check-in') ?>' : '<?= base_url('guru/absensi-guru/check-out') ?>';
        const btn = type === 'check-in' ? document.getElementById('btnSubmitCheckIn') : document.getElementById('btnSubmitCheckOut');
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken()
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
    openModal('imageModal');
}
</script>

<style>
    /* Modal Styles for Tailwind Integration */
    .modal.fade {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
    }
    
    .modal.hidden {
        display: none !important;
    }
    
    .modal-dialog {
        max-width: 90%;
        margin: 1.75rem auto;
    }
    
    .modal-lg {
        max-width: 800px;
    }
    
    .modal-content {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    
    /* Existing Styles */
/* Mobile Responsive Improvements */
@media (max-width: 575.98px) {
    /* Reduce camera container height on mobile */
    #cameraPlaceholderCheckIn,
    #cameraPlaceholderCheckOut {
        min-height: 200px !important;
    }
    
    /* Full width buttons on mobile */
    .modal-footer button {
        font-size: 0.95rem;
        padding: 0.6rem 1rem;
    }
    
    /* Larger touch targets */
    .btn-group label {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
    
    /* Stack camera buttons vertically on small screens */
    .d-grid.gap-2 {
        row-gap: 0.5rem;
    }
    
    /* Better spacing for camera buttons */
    .d-grid button {
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
    }
    
    /* Adjust video and canvas for mobile */
    #videoCheckIn, #canvasCheckIn,
    #videoCheckOut, #canvasCheckOut {
        max-height: 300px;
        object-fit: cover;
    }
    
    /* Modal title adjustment */
    .modal-title {
        font-size: 1.1rem;
    }
    
    /* Form labels */
    .form-label {
        font-size: 0.95rem;
        margin-bottom: 0.4rem;
    }
    
    /* Textarea adjustment */
    textarea.form-control {
        font-size: 0.9rem;
    }
}

@media (min-width: 576px) {
    /* Desktop gap between buttons */
    .d-sm-flex {
        gap: 0.5rem;
    }
}

/* Camera container improvements */
.camera-container {
    position: relative;
}

.camera-container video,
.camera-container canvas {
    border-radius: 0.375rem;
    background: #000;
}

/* Touch-friendly button sizing */
.btn-group label {
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-group label:active {
    transform: scale(0.98);
}

/* Modal fullscreen improvements */
@media (max-width: 575.98px) {
    .modal-fullscreen-sm-down .modal-body {
        padding: 1rem;
    }
    
    .modal-fullscreen-sm-down .modal-header {
        padding: 0.75rem 1rem;
    }
    
    .modal-fullscreen-sm-down .modal-footer {
        padding: 0.75rem 1rem;
    }
}

/* Spinner animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spinner.fa-spin {
    animation: spin 1s linear infinite;
}
</style>

<?= $this->endSection() ?>
