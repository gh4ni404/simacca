<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50/30 p-4 md:p-6 lg:p-8">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Isi Jurnal Piket Guru</h1>
                <p class="text-sm text-gray-600 mt-1">Lengkapi rincian kegiatan dan dokumentasi piket Anda hari ini</p>
            </div>
            <a href="<?= base_url('guru/jurnal-piket') ?>" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                <i class="fas fa-arrow-left mr-1.5"></i> Kembali
            </a>
        </div>

        <?= view('components/alerts') ?>

        <form action="<?= base_url('guru/jurnal-piket/simpan') ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <?= csrf_field() ?>

            <div class="p-6 space-y-6">
                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tanggal Piket <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal" value="<?= esc($tanggal ?? date('Y-m-d')) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>

                <!-- Rincian Tugas Guidelines -->
                <div>
                    <label for="rincian_tugas" class="block text-sm font-semibold text-gray-700 mb-2">
                        Rincian / Panduan Tugas Piket
                    </label>
                    <textarea id="rincian_tugas" name="rincian_tugas" rows="5" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-gray-50/70 text-gray-700"><?= esc($rincianTugas ?? '') ?></textarea>
                    <p class="text-xs text-gray-500 mt-1.5">Rincian tugas ini diisi secara otomatis dari panduan tugas piket Anda. Anda dapat menyesuaikannya jika ada perubahan tugas.</p>
                </div>

                <!-- Deskripsi Kegiatan -->
                <div>
                    <label for="deskripsi" class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi Laporan / Uraian Kegiatan Piket <span class="text-red-500">*</span>
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required placeholder="Tuliskan uraian singkat mengenai kegiatan piket yang telah dilaksanakan hari ini..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"><?= old('deskripsi') ?></textarea>
                </div>

                <!-- Catatan / Kejadian Khusus -->
                <div>
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan Kejadian / Kejadian Khusus (Opsional)
                    </label>
                    <textarea id="catatan" name="catatan" rows="3" placeholder="Contoh: Siswa terlambat 3 orang sudah dicatat, situasi gerbang kondusif..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"><?= old('catatan') ?></textarea>
                </div>

                <!-- Foto Dokumentasi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Dokumentasi Piket (Opsional)
                    </label>
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center hover:border-indigo-400 transition bg-gray-50/50">
                        <input type="file" id="foto_dokumentasi" name="foto_dokumentasi" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <label for="foto_dokumentasi" class="cursor-pointer block">
                            <div id="upload_placeholder">
                                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-700">Klik untuk mengunggah foto dokumentasi</p>
                                <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG (Maks 5MB)</p>
                            </div>
                            <div id="image_preview_container" class="hidden">
                                <img id="image_preview" src="#" alt="Preview Foto" class="max-h-64 mx-auto rounded-xl shadow-md border border-gray-200 object-cover">
                                <p class="text-xs text-indigo-600 font-semibold mt-3">Klik untuk mengganti foto</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="p-6 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="<?= base_url('guru/jurnal-piket') ?>" class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-500/20 transition">
                    <i class="fas fa-save mr-2"></i> Simpan Jurnal Piket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const placeholder = document.getElementById('upload_placeholder');
    const previewContainer = document.getElementById('image_preview_container');
    const preview = document.getElementById('image_preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            placeholder.classList.add('hidden');
            previewContainer.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?= $this->endSection() ?>
