<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <?= button_link('secondary', '', 'arrow-left', base_url('guru/izin-guru'), ['class' => 'mr-4']) ?>
            <div>
                <h1 class="text-2xl font-bold text-gray-900"><?= $pageTitle ?></h1>
                <p class="mt-1 text-sm text-gray-500"><?= $pageDescription ?></p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600">
                    <h2 class="text-lg font-semibold text-white">Form Pengajuan Izin</h2>
                </div>

                <form action="<?= base_url('guru/izin-guru/store') ?>" method="POST" enctype="multipart/form-data" class="p-6">
                    <?= csrf_field() ?>

                    <?= form_select('jenis_izin', 'Jenis Izin', [
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'cuti' => 'Cuti',
                        'dinas_luar' => 'Dinas Luar',
                        'lainnya' => 'Lainnya'
                    ], old('jenis_izin'), ['required' => true]) ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <?= form_input('tanggal_mulai', 'Tanggal Mulai', old('tanggal_mulai', date('Y-m-d')), ['type' => 'date', 'required' => true]) ?>
                        <?= form_input('tanggal_selesai', 'Tanggal Selesai', old('tanggal_selesai', date('Y-m-d')), ['type' => 'date', 'required' => true]) ?>
                    </div>

                    <!-- Durasi Info -->
                    <div id="durasi-info" class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span class="text-sm text-blue-800">
                                Durasi izin: <strong id="durasi-text">-</strong>
                            </span>
                        </div>
                    </div>

                    <?= form_textarea('alasan', 'Alasan/Keterangan', old('alasan'), [
                        'rows' => 4,
                        'required' => true,
                        'placeholder' => 'Jelaskan alasan pengajuan izin (minimal 10 karakter)'
                    ]) ?>
                    <p class="mt-1 text-xs text-gray-500">
                        <span id="char-count">0</span>/500 karakter
                    </p>

                    <!-- Upload Berkas -->
                    <div class="mb-6">
                        <label for="berkas" class="block text-sm font-medium text-gray-700 mb-2">
                            Berkas Pendukung <span class="text-gray-500">(Opsional)</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="berkas"
                                           class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Upload file</span>
                                        <input id="berkas" name="berkas" type="file" class="sr-only"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               onchange="displayFileName(this)">
                                    </label>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 2MB (gambar dikompres otomatis)</p>
                                <p id="file-name" class="text-sm text-gray-700 font-medium mt-2"></p>
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Upload surat keterangan dokter untuk sakit, atau dokumen pendukung lainnya
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4 pt-4 border-t border-gray-200">
                        <?= button_link('secondary', 'Batal', 'times', base_url('guru/izin-guru'), ['class' => 'flex-1 justify-center']) ?>
                        <?= button('primary', 'Kirim Pengajuan', 'paper-plane', ['type' => 'submit', 'class' => 'flex-1 justify-center']) ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Panduan Pengajuan
                </h3>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">1</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Pilih Jenis Izin</h4>
                            <p class="text-sm text-gray-600">Pilih jenis izin yang sesuai dengan kebutuhan Anda</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">2</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Tentukan Periode</h4>
                            <p class="text-sm text-gray-600">Isi tanggal mulai dan selesai izin</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">3</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Jelaskan Alasan</h4>
                            <p class="text-sm text-gray-600">Berikan penjelasan yang jelas dan lengkap</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 text-xs font-bold">4</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Upload Dokumen</h4>
                            <p class="text-sm text-gray-600">Lampirkan surat keterangan jika diperlukan</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-semibold text-yellow-900 text-sm mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Catatan Penting
                    </h4>
                    <ul class="text-xs text-yellow-800 space-y-1">
                        <li>&bull; Izin sakit >2 hari wajib surat dokter</li>
                        <li>&bull; Ajukan minimal H-1 untuk izin terencana</li>
                        <li>&bull; Pengajuan akan diproses maksimal 1x24 jam</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('components/upload_script') ?>
<script>
const alasanInput = document.getElementById('alasan');
const charCount = document.getElementById('char-count');

alasanInput?.addEventListener('input', function() {
    const count = this.value.length;
    charCount.textContent = count;
    if (count < 10) {
        charCount.classList.add('text-red-600');
        charCount.classList.remove('text-gray-500');
    } else {
        charCount.classList.remove('text-red-600');
        charCount.classList.add('text-gray-500');
    }
});

function displayFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDisplay = document.getElementById('file-name');
    if (fileName) {
        const file = input.files[0];
        if (file.type.startsWith('image/')) {
            compressImage(file, function(compressedFile) {
                const dt = new DataTransfer();
                dt.items.add(compressedFile);
                input.files = dt.files;
                const compSize = (compressedFile.size / 1024 / 1024).toFixed(2);
                fileNameDisplay.textContent = `File: ${fileName} (${compSize} MB, dikompres)`;
            });
        } else {
            fileNameDisplay.textContent = `File: ${fileName}`;
        }
    } else {
        fileNameDisplay.textContent = '';
    }
}

const tanggalMulai = document.getElementById('tanggal_mulai');
const tanggalSelesai = document.getElementById('tanggal_selesai');
const durasiInfo = document.getElementById('durasi-info');
const durasiText = document.getElementById('durasi-text');

function calculateDuration() {
    const start = new Date(tanggalMulai.value);
    const end = new Date(tanggalSelesai.value);
    if (start && end && end >= start) {
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        durasiText.textContent = `${diffDays} hari`;
        durasiInfo.classList.remove('hidden');
    } else {
        durasiInfo.classList.add('hidden');
    }
}

tanggalMulai?.addEventListener('change', calculateDuration);
tanggalSelesai?.addEventListener('change', calculateDuration);

if (tanggalMulai?.value && tanggalSelesai?.value) { calculateDuration(); }

const today = new Date().toISOString().split('T')[0];
tanggalMulai?.setAttribute('min', today);
tanggalSelesai?.setAttribute('min', today);

tanggalMulai?.addEventListener('change', function() {
    tanggalSelesai?.setAttribute('min', this.value);
});
</script>

<?= $this->endSection() ?>
