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
                        <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            <i class="fas fa-plus-circle mr-3"></i>
                            Tambah Jurnal KBM
                        </span>
                    </h1>
                    <p class="text-gray-600 mt-2">Catat materi pembelajaran dan dokumentasi kegiatan</p>
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
                        <span class="text-lg font-bold text-gray-800"><?= date('d/m/Y', strtotime($absensi['tanggal'])) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-purple-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-book text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Mata Pelajaran</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($absensi['nama_mapel']) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-green-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Kelas</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($absensi['nama_kelas']) ?></span>
                    </div>
                </div>

                <div class="flex items-center bg-white/60 backdrop-blur-sm rounded-xl p-4">
                    <div class="bg-orange-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-hashtag text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <span class="text-sm text-gray-600 block">Pertemuan Ke</span>
                        <span class="text-lg font-bold text-gray-800"><?= esc($absensi['pertemuan_ke']) ?></span>
                    </div>
                </div>
            </div>

            <?php if (!empty($absensi['materi_pembelajaran'])): ?>
            <div class="mt-4 bg-white/60 backdrop-blur-sm rounded-xl p-4">
                <span class="text-sm text-gray-600 block mb-2">
                    <i class="fas fa-clipboard-list mr-2"></i>Materi dari Absensi
                </span>
                <p class="text-gray-800"><?= esc($absensi['materi_pembelajaran']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Form Card -->
        <form id="jurnalForm" method="post" action="<?= base_url('guru/jurnal/store') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="absensi_id" value="<?= esc($absensi['id']) ?>">

            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-edit text-indigo-600 mr-3"></i>
                    Isi Jurnal Pembelajaran
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
                        placeholder="Jelaskan materi yang diajarkan hari ini...&#10;Contoh: Materi Pythagoras - siswa belajar rumus a² + b² = c² dan penerapannya dalam kehidupan sehari-hari"
                        required><?= old('kegiatan_pembelajaran') ?></textarea>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Jelaskan materi, kegiatan, dan hal penting yang terjadi selama pembelajaran
                    </p>
                </div>

                <!-- Foto Dokumentasi Section -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-camera text-indigo-600 mr-2"></i>
                        Foto Dokumentasi Pembelajaran
                    </label>

                    <!-- Camera/Upload Buttons -->
                    <div class="flex flex-wrap gap-3 mb-4">
                        <button type="button" id="captureBtn" class="capture-button flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 shadow-lg">
                            <i class="fas fa-camera mr-2"></i>
                            Ambil Foto
                        </button>
                        <button type="button" id="uploadBtn" class="capture-button flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 shadow-lg">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Foto
                        </button>
                    </div>

                    <input type="file" id="fileInput" name="foto_dokumentasi" accept="image/*" class="hidden">

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

                    <!-- Image Preview -->
                    <div id="imagePreview" class="hidden">
                        <div class="image-preview bg-gray-100 p-4 rounded-xl">
                            <img id="previewImg" src="" alt="Preview">
                            <div class="remove-image" id="removeImage" title="Hapus foto">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 text-center">
                            <i class="fas fa-check-circle text-green-600 mr-1"></i>
                            Foto siap diupload
                        </p>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Opsional - Dokumentasi aktivitas pembelajaran (max 5MB)
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t-2 border-gray-100">
                    <a href="<?= base_url('guru/jurnal') ?>" 
                       class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-200 font-medium">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg font-medium">
                        <i class="fas fa-save mr-2"></i>Simpan Jurnal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let stream = null;
    let capturedImageBlob = null;

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

    // Open Camera
    captureBtn.addEventListener('click', async () => {
        try {
            // Request camera permission and stream
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { 
                    facingMode: 'environment',  // Use back camera on mobile
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
        // Set canvas dimensions to match video
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        
        // Draw video frame to canvas
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // Convert to blob
        canvas.toBlob((blob) => {
            capturedImageBlob = blob;
            
            // Show preview
            const url = URL.createObjectURL(blob);
            previewImg.src = url;
            imagePreview.classList.remove('hidden');
            
            // Hide camera
            stopCamera();
            cameraView.classList.add('hidden');
            
            // Re-enable buttons
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
            // Validate file size (5MB)
            if (file.size > 5242880) {
                alert('Ukuran file terlalu besar. Maksimal 5MB');
                fileInput.value = '';
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar');
                fileInput.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
                capturedImageBlob = null; // Clear captured image
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove Image
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
            
            // Create FormData
            const formData = new FormData(e.target);
            
            // Replace file input with captured blob
            formData.delete('foto_dokumentasi');
            formData.append('foto_dokumentasi', capturedImageBlob, 'captured_photo.jpg');
            
            // Submit form
            try {
                const response = await fetch(e.target.action, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    window.location.href = '<?= base_url('guru/jurnal') ?>';
                } else {
                    alert('Terjadi kesalahan saat menyimpan jurnal');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan jurnal');
            }
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', stopCamera);
</script>

<?= $this->endSection() ?>
