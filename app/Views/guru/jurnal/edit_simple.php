<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out;
    }

    .capture-button {
        transition: all 0.3s ease;
    }

    .capture-button:hover {
        transform: scale(1.05);
    }

    .image-preview {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
    }

    .image-preview img {
        width: 100%;
        height: auto;
        display: block;
    }

    .remove-image {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border-radius: 50%;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .remove-image:hover {
        background: rgba(220, 38, 38, 1);
        transform: scale(1.1);
    }

    #video-container {
        position: relative;
        border-radius: 1rem;
        overflow: hidden;
        background: #000;
    }

    #video {
        width: 100%;
        height: auto;
        display: block;
    }

    .camera-controls {
        position: absolute;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 1rem;
        z-index: 10;
    }

    .current-photo {
        position: relative;
        border: 2px solid #E5E7EB;
    }

    .replace-badge {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        background: rgba(59, 130, 246, 0.9);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center mb-4">
                <a href="<?= base_url('guru/jurnal') ?>" 
                   class="mr-4 p-2 rounded-lg bg-white text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800">
                        <span class="bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">
                            <i class="fas fa-edit mr-3"></i>
                            Edit Jurnal KBM
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-2">Perbarui materi pembelajaran dan dokumentasi kegiatan</p>
                </div>
            </div>
        </div>

        <!-- Info Absensi Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6 mb-8 shadow-lg">
            <div class="flex items-center mb-4">
                <div class="bg-blue-600 text-white p-3 rounded-xl mr-4">
                    <i class="fas fa-info-circle text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Informasi Pembelajaran</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Tanggal</span>
                        <span class="text-lg font-bold text-gray-800"><?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-purple-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-book text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Mata Pelajaran</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($jurnal['nama_mapel']) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-green-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Kelas</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($jurnal['nama_kelas']) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-orange-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-hashtag text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Pertemuan Ke</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($jurnal['pertemuan_ke']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <form id="jurnalForm" method="post" action="<?= base_url('guru/jurnal/update/' . $jurnal['id']) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">

            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-edit text-amber-600 mr-3"></i>
                    Edit Isi Jurnal
                </h2>

                <!-- Materi Pembelajaran -->
                <div class="mb-6">
                    <label for="kegiatan_pembelajaran" class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-book-open text-indigo-600 mr-2"></i>
                        Materi Pembelajaran <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="kegiatan_pembelajaran" 
                        name="kegiatan_pembelajaran" 
                        rows="6" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition-all duration-200 resize-none"
                        placeholder="Jelaskan materi yang diajarkan..."
                        required><?= esc($jurnal['kegiatan_pembelajaran']) ?></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Jelaskan materi, kegiatan, dan hal penting yang terjadi selama pembelajaran
                    </p>
                </div>

                <!-- Catatan Khusus (Optional) -->
                <div class="mb-6">
                    <label for="catatan_khusus" class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-sticky-note text-amber-600 mr-2"></i>
                        Catatan Khusus (Opsional)
                    </label>
                    <textarea 
                        id="catatan_khusus" 
                        name="catatan_khusus" 
                        rows="3" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-amber-200 focus:border-amber-500 transition-all duration-200 resize-none"
                        placeholder="Catatan tambahan jika ada..."><?= $jurnal['catatan_khusus'] !== '-' ? esc($jurnal['catatan_khusus']) : '' ?></textarea>
                </div>

                <!-- Foto Dokumentasi Section -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-camera text-indigo-600 mr-2"></i>
                        Foto Dokumentasi Pembelajaran
                    </label>

                    <!-- Current Photo Display -->
                    <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                    <div id="currentPhoto" class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-image text-blue-600 mr-1"></i>
                            Foto saat ini:
                        </p>
                        <div class="current-photo image-preview bg-gray-100 p-4 rounded-xl">
                            <img src="<?= base_url('files/jurnal/' . esc($jurnal['foto_dokumentasi'])) ?>" 
                                 alt="Foto Current"
                                 id="currentPhotoImg">
                            <div class="remove-image" id="removeCurrentPhoto" title="Hapus foto">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Camera/Upload Buttons -->
                    <div class="flex flex-wrap gap-3 mb-4">
                        <button type="button" id="captureBtn" class="capture-button flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 shadow-lg">
                            <i class="fas fa-camera mr-2"></i>
                            <?= !empty($jurnal['foto_dokumentasi']) ? 'Ganti dengan Foto Baru' : 'Ambil Foto' ?>
                        </button>
                        <button type="button" id="uploadBtn" class="capture-button flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 shadow-lg">
                            <i class="fas fa-upload mr-2"></i>
                            <?= !empty($jurnal['foto_dokumentasi']) ? 'Ganti dengan Upload' : 'Upload Foto' ?>
                        </button>
                    </div>

                    <input type="file" id="fileInput" name="foto_dokumentasi" accept="image/*" class="hidden">
                    <input type="hidden" name="remove_foto" id="removeFotoInput" value="0">
                    <input type="hidden" name="existing_foto" value="<?= esc($jurnal['foto_dokumentasi'] ?? '') ?>">

                    <!-- Camera View (Hidden by default) -->
                    <div id="cameraView" class="hidden mb-4">
                        <div id="video-container" class="bg-gray-900">
                            <video id="video" autoplay playsinline></video>
                            <div class="camera-controls">
                                <button type="button" id="snapBtn" class="px-6 py-3 bg-white text-gray-800 rounded-xl shadow-lg hover:bg-gray-100 transition-all">
                                    <i class="fas fa-circle text-red-600 mr-2"></i>
                                    Ambil Foto
                                </button>
                                <button type="button" id="closeCameraBtn" class="px-6 py-3 bg-red-600 text-white rounded-xl shadow-lg hover:bg-red-700 transition-all">
                                    <i class="fas fa-times mr-2"></i>
                                    Tutup
                                </button>
                            </div>
                        </div>
                        <canvas id="canvas" class="hidden"></canvas>
                    </div>

                    <!-- New Image Preview -->
                    <div id="imagePreview" class="hidden">
                        <p class="text-sm text-green-600 mb-2 font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>
                            Foto baru dipilih (akan menggantikan foto lama):
                        </p>
                        <div class="image-preview bg-gray-100 p-4 rounded-xl">
                            <div class="replace-badge">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Foto Baru
                            </div>
                            <img id="previewImg" src="" alt="Preview">
                            <div class="remove-image" id="removeImage" title="Batalkan">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        <?= !empty($jurnal['foto_dokumentasi']) ? 'Kosongkan jika tidak ingin mengubah foto' : 'Opsional - Dokumentasi aktivitas pembelajaran (max 5MB)' ?>
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t-2 border-gray-100">
                    <a href="<?= base_url('guru/jurnal') ?>" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-xl hover:from-amber-700 hover:to-orange-700 transition-all duration-200 shadow-lg font-medium">
                        <i class="fas fa-save mr-2"></i>Update Jurnal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let stream = null;
    let capturedImageBlob = null;
    let hasExistingPhoto = <?= !empty($jurnal['foto_dokumentasi']) ? 'true' : 'false' ?>;

    // Elements
    const captureBtn = document.getElementById('captureBtn');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInput = document.getElementById('fileInput');
    const cameraView = document.getElementById('cameraView');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const snapBtn = document.getElementById('snapBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImage = document.getElementById('removeImage');
    const currentPhoto = document.getElementById('currentPhoto');
    const removeCurrentPhoto = document.getElementById('removeCurrentPhoto');
    const removeFotoInput = document.getElementById('removeFotoInput');

    // Remove current photo
    if (removeCurrentPhoto) {
        removeCurrentPhoto.addEventListener('click', () => {
            if (confirm('Yakin ingin menghapus foto dokumentasi?')) {
                currentPhoto.style.display = 'none';
                removeFotoInput.value = '1';
                hasExistingPhoto = false;
            }
        });
    }

    // Open Camera
    captureBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',
                    width: { ideal: 1920 },
                    height: { ideal: 1080 }
                } 
            });
            
            video.srcObject = stream;
            cameraView.classList.remove('hidden');
            captureBtn.disabled = true;
            uploadBtn.disabled = true;
        } catch (error) {
            console.error('Error accessing camera:', error);
            alert('Tidak dapat mengakses kamera. Pastikan Anda memberikan izin akses kamera.');
        }
    });

    // Capture Photo
    snapBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        canvas.toBlob((blob) => {
            capturedImageBlob = blob;
            
            const url = URL.createObjectURL(blob);
            previewImg.src = url;
            imagePreview.classList.remove('hidden');
            
            stopCamera();
            cameraView.classList.add('hidden');
            
            captureBtn.disabled = false;
            uploadBtn.disabled = false;
        }, 'image/jpeg', 0.85);
    });

    // Close Camera
    closeCameraBtn.addEventListener('click', () => {
        stopCamera();
        cameraView.classList.add('hidden');
        captureBtn.disabled = false;
        uploadBtn.disabled = false;
    });

    // Upload Button
    uploadBtn.addEventListener('click', () => {
        fileInput.click();
    });

    // File Input Change
    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5242880) {
                alert('Ukuran file terlalu besar. Maksimal 5MB');
                fileInput.value = '';
                return;
            }

            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar');
                fileInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
                capturedImageBlob = null;
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove New Image
    removeImage.addEventListener('click', () => {
        previewImg.src = '';
        imagePreview.classList.add('hidden');
        fileInput.value = '';
        capturedImageBlob = null;
    });

    // Stop camera stream
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
    }

    // Form Submit - handle captured image
    document.getElementById('jurnalForm').addEventListener('submit', async (e) => {
        if (capturedImageBlob) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
            
            const formData = new FormData(e.target);
            
            // Remove old file input and add captured blob
            formData.delete('foto_dokumentasi');
            formData.append('foto_dokumentasi', capturedImageBlob, 'captured_photo.jpg');
            
            // Ensure _method and CSRF token are included
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }
            
            try {
                const response = await fetch(e.target.action, {
                    method: 'POST',  // Still POST, but with _method=PUT for Laravel/CI4 spoofing
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                // Check response
                if (response.ok || response.redirected) {
                    // Success - redirect to jurnal list
                    window.location.href = '<?= base_url('guru/jurnal') ?>';
                } else {
                    // Try to get error message
                    const text = await response.text();
                    console.error('Server response:', text);
                    
                    // Restore button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                    
                    alert('Terjadi kesalahan saat menyimpan jurnal. Silakan coba lagi.');
                }
            } catch (error) {
                console.error('Error:', error);
                
                // Restore button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                alert('Terjadi kesalahan jaringan. Pastikan koneksi internet Anda stabil dan coba lagi.');
            }
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', stopCamera);
</script>

<?= $this->endSection() ?>
