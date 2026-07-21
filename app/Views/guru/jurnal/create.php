<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
    .capture-button { transition: all 0.3s ease; }
    .capture-button:hover { transform: scale(1.05); }
    .image-preview { position: relative; border-radius: 1rem; overflow: hidden; }
    .image-preview img { width: 100%; height: auto; display: block; }
    .remove-image { position: absolute; top: 0.5rem; right: 0.5rem; background: rgba(239,68,68,0.9); color: white; border-radius: 50%; width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; }
    .remove-image:hover { background: rgba(220,38,38,1); transform: scale(1.1); }
    #video-container { position: relative; border-radius: 1rem; overflow: hidden; background: #000; }
    #video { width: 100%; height: auto; display: block; }
    .camera-controls { position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); display: flex; gap: 1rem; z-index: 10; }
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-4 md:p-6 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 animate-fade-in-up">
            <div class="flex items-center mb-4">
                <?= button_link('secondary', '', 'arrow-left', base_url('guru/jurnal'), ['class' => 'mr-4 p-2 rounded-lg shadow-sm']) ?>
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
        </div>

        <!-- Form Card -->
        <form id="jurnalForm" method="post" action="<?= base_url('guru/jurnal/simpan') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="absensi_id" value="<?= esc($absensi['id']) ?>">

            <div class="bg-white rounded-2xl shadow-xl p-6 md:p-8 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-edit text-indigo-600 mr-3"></i>
                    Isi Jurnal Pembelajaran
                </h2>

                <!-- Materi Pembelajaran -->
                <?= form_textarea('kegiatan_pembelajaran', 'Materi Pembelajaran', old('kegiatan_pembelajaran'), [
                    'rows' => 6,
                    'required' => true,
                    'placeholder' => "Jelaskan materi yang diajarkan hari ini...\nContoh: Materi Pythagoras - siswa belajar rumus a² + b² = c² dan penerapannya dalam kehidupan sehari-hari"
                ]) ?>

                <!-- Foto Dokumentasi Section -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-camera text-indigo-600 mr-2"></i>
                        Foto Dokumentasi Pembelajaran
                    </label>

                    <!-- Camera/Upload Buttons -->
                    <div class="flex flex-wrap gap-3 mb-4">
                        <button type="button" id="captureBtn" class="capture-button inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 shadow-lg font-semibold text-sm transition-colors duration-200">
                            <i class="fas fa-camera mr-2"></i>
                            Ambil Foto
                        </button>
                        <button type="button" id="uploadBtn" class="capture-button inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 shadow-lg font-semibold text-sm transition-colors duration-200">
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
                                <button type="button" id="snapBtn" class="px-6 py-3 bg-white text-gray-800 rounded-xl shadow-lg hover:bg-gray-100 transition-all font-semibold text-sm">
                                    <i class="fas fa-circle text-red-600 mr-2"></i>
                                    Ambil Foto
                                </button>
                                <button type="button" id="closeCameraBtn" class="px-6 py-3 bg-red-600 text-white rounded-xl shadow-lg hover:bg-red-700 transition-all font-semibold text-sm">
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
                        <p id="compressionInfo" class="text-sm text-gray-600 mt-2 text-center">
                            <i class="fas fa-check-circle text-green-600 mr-1"></i>
                            Foto siap diupload
                        </p>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Opsional - Dokumentasi aktivitas pembelajaran (max 1MB, dikompres otomatis)
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t-2 border-gray-100">
                    <?= button_link('secondary', 'Batal', 'times', base_url('guru/jurnal')) ?>
                    <?= button('primary', 'Simpan Jurnal', 'save', ['type' => 'submit', 'class' => 'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-lg']) ?>
                </div>
            </div>
        </form>
    </div>
</div>

<?= view('components/upload_script') ?>
<script>
    let stream = null;
    let capturedImageBlob = null;

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
    const compressionInfo = document.getElementById('compressionInfo');

    captureBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } }
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

    snapBtn.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob((blob) => {
            const file = new File([blob], 'camera_photo.jpg', { type: 'image/jpeg', lastModified: Date.now() });
            compressImage(file, (compressed) => {
                capturedImageBlob = compressed;
                const url = URL.createObjectURL(compressed);
                previewImg.src = url;
                imagePreview.classList.remove('hidden');
                const origSize = (blob.size / 1024).toFixed(1);
                const compSize = (compressed.size / 1024).toFixed(1);
                compressionInfo.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-1"></i> Foto siap diupload (' + compSize + ' KB' + (compressed.size < blob.size ? ' | dikompres dari ' + origSize + ' KB' : '') + ')';
                stopCamera();
                cameraView.classList.add('hidden');
                captureBtn.disabled = false;
                uploadBtn.disabled = false;
            });
        }, 'image/jpeg');
    });

    closeCameraBtn.addEventListener('click', () => {
        stopCamera();
        cameraView.classList.add('hidden');
        captureBtn.disabled = false;
        uploadBtn.disabled = false;
    });

    uploadBtn.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) { alert('File harus berupa gambar'); fileInput.value = ''; return; }
            compressImage(file, (compressed) => {
                const dt = new DataTransfer();
                dt.items.add(compressed);
                fileInput.files = dt.files;
                const url = URL.createObjectURL(compressed);
                previewImg.src = url;
                imagePreview.classList.remove('hidden');
                const origSize = (file.size / 1024).toFixed(1);
                const compSize = (compressed.size / 1024).toFixed(1);
                compressionInfo.innerHTML = '<i class="fas fa-check-circle text-green-600 mr-1"></i> Foto siap diupload (' + compSize + ' KB' + (compressed.size < file.size ? ' | dikompres dari ' + origSize + ' KB' : '') + ')';
                capturedImageBlob = null;
            });
        }
    });

    removeImage.addEventListener('click', () => {
        previewImg.src = '';
        imagePreview.classList.add('hidden');
        fileInput.value = '';
        capturedImageBlob = null;
    });

    function stopCamera() {
        if (stream) { stream.getTracks().forEach(track => track.stop()); stream = null; }
    }

    document.getElementById('jurnalForm').addEventListener('submit', async (e) => {
        if (capturedImageBlob) {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.delete('foto_dokumentasi');
            formData.append('foto_dokumentasi', capturedImageBlob, 'captured_photo.jpg');
            try {
                const response = await fetch(e.target.action, { method: 'POST', body: formData });
                if (response.ok) { window.location.href = '<?= base_url('guru/jurnal') ?>'; }
                else { alert('Terjadi kesalahan saat menyimpan jurnal'); }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan jurnal');
            }
        }
    });

    window.addEventListener('beforeunload', stopCamera);
</script>

<?= $this->endSection() ?>
