<?= $this->extend(get_device_layout()) ?> 

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 text-lg">
                    <i class="fas fa-hands-helping"></i>
                </span>
                Jurnal Guru Wali
            </h1>
            <p class="text-xs md:text-sm text-gray-500 mt-1">
                Pendampingan dan pencatatan bimbingan personal siswa binaan hingga tamat sekolah
            </p>
        </div>
        <div>
            <a href="<?= base_url('guru/jurnal-wali/cetak' . (!empty($filters['siswa_id']) ? '?siswa_id=' . (int)$filters['siswa_id'] : '')) ?>" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-semibold text-xs rounded-xl border border-gray-300 shadow-sm transition-all">
                <i class="fas fa-print mr-2 text-blue-600"></i> Preview & Cetak PDF
            </a>
        </div>
    </div>

    <!-- Flash Notifications -->
    <?= view('components/alerts') ?>

    <!-- Top Row: 2 Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Panel Kiri: Siswa Bimbingan (4 Cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl p-5 shadow-sm border border-gray-100 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Siswa bimbingan</h2>
                    <p class="text-xs text-gray-400">Total <?= count($siswaBinaan) ?> siswa binaan</p>
                </div>
                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold text-xs rounded-full border border-blue-100">
                    <?= count($siswaBinaan) ?> Siswa
                </span>
            </div>

            <!-- Search Filter -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <i class="fas fa-search text-xs"></i>
                </span>
                <input type="text" id="searchMentee" onkeyup="filterMentees(this.value)" placeholder="Cari nama siswa atau NIS..." class="w-full pl-9 pr-3 text-xs rounded-xl border border-gray-200 bg-gray-50/70 focus:bg-white focus:border-blue-500 py-2 transition-all">
            </div>

            <!-- List of Mentees -->
            <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1" id="menteeList">
                <?php if (empty($siswaBinaan)): ?>
                    <div class="text-center py-10 text-gray-400 bg-slate-50/50 rounded-xl p-4 border border-dashed border-gray-200">
                        <i class="fas fa-user-friends text-3xl mb-2 text-gray-300 block"></i>
                        <p class="text-xs font-semibold text-gray-600">Belum ada siswa binaan</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Admin belum menugaskan siswa ke Anda sebagai Guru Wali.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($siswaBinaan as $mb): ?>
                        <div class="mentee-item p-3 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50/30 transition-all cursor-pointer flex items-center justify-between gap-3 group" 
                             onclick="selectStudent(<?= $mb['siswa_id'] ?>)">
                            <div class="flex items-center space-x-3 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    <?= strtoupper(substr($mb['nama_siswa'], 0, 2)) ?>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-gray-900 truncate group-hover:text-blue-600 mentee-name">
                                        <?= esc($mb['nama_siswa']) ?>
                                    </p>
                                    <p class="text-[11px] text-gray-500 truncate mt-0.5 mentee-info">
                                        <?= esc($mb['nama_kelas'] ?? 'Kelas -') ?> • NIS <?= esc($mb['nis']) ?>
                                    </p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-500 text-xs transition-transform"></i>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Panel Kanan: Catat Kegiatan Bimbingan (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-gray-100 space-y-5">
            <div class="pb-3 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Catat kegiatan bimbingan</h2>
                <p class="text-xs text-gray-400">Dokumentasikan sesi bimbingan individual siswa secara berkala</p>
            </div>

            <form action="<?= base_url('guru/jurnal-wali/simpan') ?>" method="POST" enctype="multipart/form-data" class="space-y-4" id="createForm">
                <?= csrf_field() ?>

                <!-- Row 1: Tanggal & Siswa -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Tanggal <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal" required value="<?= date('Y-m-d') ?>" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 font-medium text-gray-800 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Siswa <span class="text-rose-500">*</span>
                        </label>
                        <select name="siswa_id" id="formSiswaId" required class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 px-3.5 py-2.5 font-medium text-gray-800 shadow-sm">
                            <option value="">Pilih siswa</option>
                            <?php foreach ($siswaBinaan as $mb): ?>
                                <option value="<?= $mb['siswa_id'] ?>">
                                    <?= esc($mb['nama_siswa']) ?> (<?= esc($mb['nama_kelas'] ?? '-') ?> • <?= esc($mb['nis']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Row 2: Jenis Bimbingan & Catatan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Jenis Bimbingan <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_bimbingan" required class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 px-3.5 py-2.5 font-medium text-gray-800 shadow-sm">
                            <option value="Akademik" selected>Akademik</option>
                            <option value="Kedisiplinan">Kedisiplinan & Tata Tertib</option>
                            <option value="Pribadi">Pribadi</option>
                            <option value="Sosial">Sosial & Pertemanan</option>
                            <option value="Karir">Karir & Minat Bakat</option>
                            <option value="Kehadiran">Kehadiran & Absensi</option>
                            <option value="Prestasi">Pengembangan Prestasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Catatan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="catatan" id="formCatatan" rows="3" required placeholder="Catatan hasil diskusi, observasi, atau kendala siswa..." class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 px-3.5 py-2.5 shadow-sm resize-none"></textarea>
                    </div>
                </div>

                <!-- Row 3: Tindak Lanjut -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Tindak Lanjut
                    </label>
                    <textarea name="tindak_lanjut" rows="2" placeholder="Rencana aksi, arahan, atau solusi yang disepakati bersama siswa..." class="w-full text-xs rounded-xl border border-gray-200 bg-white focus:border-blue-500 px-3.5 py-2.5 shadow-sm resize-none"></textarea>
                </div>

                <!-- Row 4: Dokumentasi Foto (Kamera & File Picker dengan Kompresi Otomatis) -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Dokumentasi Foto <span class="text-gray-400 font-normal lowercase">(opsional)</span></span>
                        <span class="text-[11px] text-gray-400 font-normal">Max 2MB • Dikompres otomatis</span>
                    </label>
                    
                    <div class="border border-dashed border-gray-300 rounded-2xl p-4 bg-gray-50/50 hover:border-blue-400 transition-colors" id="createDropzone">
                        <input type="file" name="foto_dokumentasi" id="createFotoInput" accept="image/*" class="hidden" onchange="handleCreatePhotoSelect(this)">
                        
                        <!-- Camera Live View Box -->
                        <div id="createCameraContainer" class="hidden mb-3 p-3 bg-gray-900 rounded-xl space-y-3">
                            <div class="relative overflow-hidden rounded-lg bg-black">
                                <video id="createVideo" autoplay playsinline class="w-full h-52 md:h-64 object-cover"></video>
                                <span class="absolute top-2 left-2 px-2 py-0.5 bg-rose-600/90 text-white text-[10px] font-bold rounded-md flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> KAMERA AKTIF
                                </span>
                            </div>
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" onclick="takeCreateSnapshot()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition flex items-center gap-2">
                                    <i class="fas fa-circle text-rose-400"></i> Ambil Foto
                                </button>
                                <button type="button" onclick="switchCreateCamera()" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold rounded-xl transition flex items-center gap-1.5">
                                    <i class="fas fa-sync-alt"></i> Putar
                                </button>
                                <button type="button" onclick="stopCreateCamera()" class="px-3 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-semibold rounded-xl transition flex items-center gap-1.5">
                                    <i class="fas fa-times"></i> Tutup
                                </button>
                            </div>
                            <canvas id="createCanvas" class="hidden"></canvas>
                        </div>

                        <!-- Upload & Camera Choice Placeholder -->
                        <div id="createUploadPlaceholder" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base flex-shrink-0">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-gray-800 truncate">Ambil atau Unggah Foto</p>
                                    <p class="text-[11px] text-gray-400 truncate">Foto sesi bimbingan / konsultasi dengan siswa</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button type="button" onclick="startCreateCamera()" class="px-3 py-1.5 text-xs font-semibold bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl shadow-sm transition flex items-center gap-1.5">
                                    <i class="fas fa-camera"></i> Kamera
                                </button>
                                <button type="button" onclick="document.getElementById('createFotoInput').click()" class="px-3 py-1.5 text-xs font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl shadow-sm transition flex items-center gap-1.5">
                                    <i class="fas fa-upload text-blue-600"></i> Pilih File
                                </button>
                            </div>
                        </div>

                        <!-- Preview Container with compression info -->
                        <div id="createPreviewContainer" class="hidden flex items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 min-w-0">
                                <img id="createPreviewImg" src="" alt="Preview" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-sm flex-shrink-0">
                                <div class="min-w-0">
                                    <p id="createFileName" class="text-xs font-bold text-gray-800 truncate"></p>
                                    <p id="createFileSize" class="text-[11px] text-emerald-600 font-medium"></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <button type="button" onclick="startCreateCamera()" class="px-2.5 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 rounded-xl border border-blue-200">
                                    Foto Ulang
                                </button>
                                <button type="button" onclick="removeCreatePhoto()" class="px-2.5 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl border border-rose-200">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-600/20 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan Jurnal Bimbingan
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Bottom Row: Riwayat Bimbingan Siswa -->
    <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm border border-gray-100 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pb-3 border-b border-gray-100">
            <div>
                <h2 class="text-base font-bold text-gray-900">Riwayat bimbingan siswa</h2>
                <p class="text-xs text-gray-400">Histori seluruh sesi bimbingan personal yang telah Anda catat</p>
            </div>

            <!-- Filter Controls -->
            <form method="GET" action="<?= base_url('guru/jurnal-wali') ?>" class="flex flex-wrap items-center gap-2">
                <select name="siswa_id" onchange="this.form.submit()" class="text-xs rounded-xl border border-gray-200 bg-slate-50 px-3 py-1.5">
                    <option value="">Semua Siswa</option>
                    <?php foreach ($siswaBinaan as $mb): ?>
                        <option value="<?= $mb['siswa_id'] ?>" <?= (!empty($filters['siswa_id']) && $filters['siswa_id'] == $mb['siswa_id']) ? 'selected' : '' ?>>
                            <?= esc($mb['nama_siswa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="jenis_bimbingan" onchange="this.form.submit()" class="text-xs rounded-xl border border-gray-200 bg-slate-50 px-3 py-1.5">
                    <option value="">Semua Jenis</option>
                    <?php 
                    $jenisOptions = ['Akademik', 'Kedisiplinan', 'Pribadi', 'Sosial', 'Karir', 'Kehadiran', 'Prestasi', 'Lainnya'];
                    foreach ($jenisOptions as $opt): 
                    ?>
                        <option value="<?= $opt ?>" <?= (!empty($filters['jenis_bimbingan']) && $filters['jenis_bimbingan'] == $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($filters['siswa_id']) || !empty($filters['jenis_bimbingan'])): ?>
                    <a href="<?= base_url('guru/jurnal-wali') ?>" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-semibold rounded-xl">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table Riwayat -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-xs">
                <thead>
                    <tr class="bg-slate-50 text-gray-500 font-bold uppercase tracking-wider text-left">
                        <th class="px-4 py-3 rounded-l-xl">Tanggal</th>
                        <th class="px-4 py-3">Siswa</th>
                        <th class="px-4 py-3 text-center">Jenis</th>
                        <th class="px-4 py-3">Catatan</th>
                        <th class="px-4 py-3">Tindak Lanjut</th>
                        <th class="px-4 py-3 text-center">Dokumentasi</th>
                        <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-gray-800">
                    <?php if (empty($jurnalList)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-gray-400">
                                <i class="fas fa-book-open text-3xl mb-2 text-gray-300 block"></i>
                                <p class="font-semibold text-gray-600">Belum ada riwayat bimbingan.</p>
                                <p class="text-gray-400 text-xs mt-0.5">Gunakan formulir di atas untuk mulai mencatat bimbingan siswa.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($jurnalList as $j): ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3.5 font-mono text-gray-600 whitespace-nowrap">
                                    <?= date('Y-m-d', strtotime($j['tanggal'])) ?>
                                </td>
                                <td class="px-4 py-3.5 font-bold text-gray-900 whitespace-nowrap">
                                    <p><?= esc($j['nama_siswa']) ?></p>
                                    <p class="text-[10px] text-gray-400 font-normal"><?= esc($j['nama_kelas'] ?? '-') ?> • <?= esc($j['nis']) ?></p>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        <?= esc($j['jenis_bimbingan']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs text-gray-700">
                                    <p class="line-clamp-2"><?= esc($j['catatan']) ?></p>
                                </td>
                                <td class="px-4 py-3.5 max-w-xs text-gray-600">
                                    <p class="line-clamp-2"><?= esc($j['tindak_lanjut'] ?: '-') ?></p>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <?php if (!empty($j['foto_dokumentasi'])): ?>
                                        <button type="button" 
                                                onclick="openPhotoModal('<?= base_url('files/jurnal-wali/' . esc($j['foto_dokumentasi'])) ?>', '<?= esc(addslashes($j['nama_siswa'])) ?> • <?= date('d/m/Y', strtotime($j['tanggal'])) ?>')" 
                                                class="inline-flex items-center gap-1.5 p-1 rounded-xl hover:bg-blue-50 transition border border-transparent hover:border-blue-200 group"
                                                title="Lihat foto dokumentasi">
                                            <img src="<?= base_url('files/jurnal-wali/' . esc($j['foto_dokumentasi'])) ?>" 
                                                 alt="Dokumentasi" 
                                                 class="w-10 h-10 rounded-lg object-cover border border-gray-200 shadow-sm group-hover:scale-105 transition-transform"
                                                 loading="lazy">
                                        </button>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-xs font-semibold">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-1.5">
                                        <button type="button" 
                                                onclick="openEditModal(<?= (int)$j['id'] ?>, '<?= esc(addslashes($j['tanggal'])) ?>', <?= (int)$j['siswa_id'] ?>, '<?= esc(addslashes($j['jenis_bimbingan'])) ?>', '<?= esc(addslashes(preg_replace('/\r|\n/', ' ', $j['catatan']))) ?>', '<?= esc(addslashes(preg_replace('/\r|\n/', ' ', $j['tindak_lanjut'] ?? ''))) ?>', '<?= !empty($j['foto_dokumentasi']) ? base_url('files/jurnal-wali/' . esc($j['foto_dokumentasi'])) : '' ?>')" 
                                                class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg"
                                                title="Edit Jurnal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?= base_url('guru/jurnal-wali/hapus/' . $j['id']) ?>" method="POST" class="inline" onsubmit="return confirm('Hapus catatan bimbingan ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="px-2.5 py-1 text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 font-bold rounded-lg border border-rose-200" title="Hapus Jurnal">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Jurnal -->
<div id="editModal" class="fixed inset-0 z-50 bg-black/50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Edit Jurnal Bimbingan
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="editTanggal" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Siswa</label>
                    <select name="siswa_id" id="editSiswaId" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                        <?php foreach ($siswaBinaan as $mb): ?>
                            <option value="<?= $mb['siswa_id'] ?>"><?= esc($mb['nama_siswa']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jenis Bimbingan</label>
                <select name="jenis_bimbingan" id="editJenis" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5">
                    <?php 
                    $jenisOptions = ['Akademik', 'Kedisiplinan', 'Pribadi', 'Sosial', 'Karir', 'Kehadiran', 'Prestasi', 'Lainnya'];
                    foreach ($jenisOptions as $opt): 
                    ?>
                        <option value="<?= $opt ?>"><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan</label>
                <textarea name="catatan" id="editCatatan" rows="3" required class="w-full text-xs rounded-xl border border-gray-200 p-2.5"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tindak Lanjut</label>
                <textarea name="tindak_lanjut" id="editTindakLanjut" rows="2" class="w-full text-xs rounded-xl border border-gray-200 p-2.5"></textarea>
            </div>

            <!-- Dokumentasi Foto (Edit) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1 flex items-center justify-between">
                    <span>Dokumentasi Foto</span>
                    <span class="text-[10px] text-gray-400 font-normal">Max 2MB • Dikompres otomatis</span>
                </label>

                <!-- Existing Photo Preview -->
                <div id="editExistingPhotoContainer" class="hidden mb-2.5 p-3 rounded-xl border border-gray-200 bg-gray-50 flex items-center justify-between gap-3">
                    <div class="flex items-center space-x-3 min-w-0">
                        <img id="editExistingPhotoImg" src="" alt="Foto saat ini" class="w-12 h-12 rounded-lg object-cover border border-gray-200 cursor-pointer shadow-sm flex-shrink-0" onclick="openPhotoModal(this.src, 'Foto Dokumentasi')">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-700 truncate">Foto Tersimpan</p>
                            <label class="flex items-center gap-1.5 text-[11px] text-rose-600 cursor-pointer mt-0.5 font-medium">
                                <input type="checkbox" name="hapus_foto" id="editHapusFoto" value="1" onchange="toggleDeletePhoto(this.checked)" class="rounded text-rose-600 focus:ring-rose-500 text-xs">
                                <span>Hapus foto saat ini</span>
                            </label>
                        </div>
                    </div>
                    <button type="button" onclick="openPhotoModal(document.getElementById('editExistingPhotoImg').src, 'Foto Dokumentasi')" class="text-xs text-blue-600 hover:underline flex-shrink-0 font-medium">
                        <i class="fas fa-expand mr-1"></i> Lihat
                    </button>
                </div>

                <!-- Camera Live View Box for Edit Modal -->
                <div id="editCameraContainer" class="hidden mb-2.5 p-3 bg-gray-900 rounded-xl space-y-3">
                    <div class="relative overflow-hidden rounded-lg bg-black">
                        <video id="editVideo" autoplay playsinline class="w-full h-44 object-cover"></video>
                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-rose-600/90 text-white text-[10px] font-bold rounded-md flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> KAMERA AKTIF
                        </span>
                    </div>
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" onclick="takeEditSnapshot()" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow transition flex items-center gap-1.5">
                            <i class="fas fa-circle text-rose-400"></i> Ambil Foto
                        </button>
                        <button type="button" onclick="switchEditCamera()" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-200 text-xs font-semibold rounded-xl transition flex items-center gap-1">
                            <i class="fas fa-sync-alt"></i> Putar
                        </button>
                        <button type="button" onclick="stopEditCamera()" class="px-3 py-1.5 bg-gray-700 hover:bg-gray-600 text-gray-200 text-xs font-semibold rounded-xl transition flex items-center gap-1">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                    <canvas id="editCanvas" class="hidden"></canvas>
                </div>

                <!-- Upload New / Replacement Photo Controls -->
                <div class="border border-dashed border-gray-300 rounded-xl p-3 bg-gray-50/50 hover:border-blue-400 transition-colors">
                    <input type="file" name="foto_dokumentasi" id="editFotoInput" accept="image/*" class="hidden" onchange="handleEditPhotoSelect(this)">
                    
                    <div id="editUploadPlaceholder" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="flex items-center space-x-2 min-w-0">
                            <i class="fas fa-camera text-gray-400 text-sm flex-shrink-0"></i>
                            <span class="text-xs text-gray-600 truncate" id="editUploadLabel">Unggah / ganti foto</span>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <button type="button" onclick="startEditCamera()" class="px-2.5 py-1 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm">
                                <i class="fas fa-camera mr-1"></i> Kamera
                            </button>
                            <button type="button" onclick="document.getElementById('editFotoInput').click()" class="px-2.5 py-1 text-xs font-semibold bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg shadow-sm">
                                <i class="fas fa-upload mr-1 text-blue-600"></i> Pilih File
                            </button>
                        </div>
                    </div>

                    <div id="editPreviewContainer" class="hidden flex items-center justify-between gap-2">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <img id="editPreviewImg" src="" alt="Preview Baru" class="w-10 h-10 rounded-lg object-cover border border-gray-200 flex-shrink-0">
                            <div class="min-w-0">
                                <p id="editFileName" class="text-xs font-bold text-gray-800 truncate"></p>
                                <p id="editFileSize" class="text-[10px] text-emerald-600 font-medium"></p>
                            </div>
                        </div>
                        <button type="button" onclick="removeEditPhoto()" class="px-2 py-1 text-[11px] font-semibold text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 flex-shrink-0">
                            Batal
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Lightbox Preview Foto -->
<div id="photoModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4" onclick="closePhotoModal(event)">
    <div class="relative max-w-3xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl space-y-3 p-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
            <h3 id="photoModalTitle" class="text-sm font-bold text-gray-900 flex items-center gap-2 truncate">
                <i class="fas fa-image text-blue-600"></i> Dokumentasi Kegiatan Bimbingan
            </h3>
            <button type="button" onclick="closePhotoModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex justify-center items-center max-h-[70vh] bg-slate-900/5 rounded-xl overflow-hidden p-2">
            <img id="photoModalImg" src="" alt="Dokumentasi Full" class="max-w-full max-h-[65vh] object-contain rounded-lg shadow">
        </div>
        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
            <a id="photoModalDownload" href="" target="_blank" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                <i class="fas fa-external-link-alt"></i> Buka Gambar Penuh
            </a>
            <button type="button" onclick="closePhotoModal()" class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                Tutup
            </button>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= view('components/upload_script') ?>

<?= $this->section('scripts') ?>
<script>
let createStream = null;
let editStream = null;
let createFacingMode = 'environment';
let editFacingMode = 'environment';

function selectStudent(id) {
    document.getElementById('formSiswaId').value = id;
    document.getElementById('formCatatan').focus();
}

function filterMentees(query) {
    const q = (query || '').toLowerCase();
    document.querySelectorAll('.mentee-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(q) ? 'flex' : 'none';
    });
}

// -------------------------------------------------------------
// CAMERA HANDLING: CREATE FORM
// -------------------------------------------------------------
async function startCreateCamera() {
    try {
        stopCreateCamera();
        const constraints = {
            video: {
                facingMode: createFacingMode,
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            }
        };
        createStream = await navigator.mediaDevices.getUserMedia(constraints);
        const videoEl = document.getElementById('createVideo');
        videoEl.srcObject = createStream;
        document.getElementById('createCameraContainer').classList.remove('hidden');
        document.getElementById('createUploadPlaceholder').classList.add('hidden');
    } catch (err) {
        console.error('Error starting camera:', err);
        alert('Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin kamera pada peramban/browser.');
    }
}

function stopCreateCamera() {
    if (createStream) {
        createStream.getTracks().forEach(track => track.stop());
        createStream = null;
    }
    const container = document.getElementById('createCameraContainer');
    if (container) container.classList.add('hidden');
    const placeholder = document.getElementById('createUploadPlaceholder');
    const preview = document.getElementById('createPreviewContainer');
    if (placeholder && (!preview || preview.classList.contains('hidden'))) {
        placeholder.classList.remove('hidden');
    }
}

function switchCreateCamera() {
    createFacingMode = createFacingMode === 'environment' ? 'user' : 'environment';
    startCreateCamera();
}

function takeCreateSnapshot() {
    const video = document.getElementById('createVideo');
    const canvas = document.getElementById('createCanvas');
    if (!video || !canvas || video.videoWidth === 0) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(function(blob) {
        if (!blob) return;
        const originalFile = new File([blob], 'kamera_dokumentasi_' + Date.now() + '.jpg', {
            type: 'image/jpeg',
            lastModified: Date.now()
        });

        // Apply compression utility
        compressImage(originalFile, function(compressedFile) {
            const input = document.getElementById('createFotoInput');
            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            input.files = dt.files;

            const origKb = (originalFile.size / 1024).toFixed(1);
            const compKb = (compressedFile.size / 1024).toFixed(1);

            document.getElementById('createPreviewImg').src = URL.createObjectURL(compressedFile);
            document.getElementById('createFileName').textContent = compressedFile.name;
            document.getElementById('createFileSize').textContent = compKb + ' KB' + (compressedFile.size < originalFile.size ? ' (dikompres dari ' + origKb + ' KB)' : '');

            stopCreateCamera();
            document.getElementById('createUploadPlaceholder').classList.add('hidden');
            document.getElementById('createPreviewContainer').classList.remove('hidden');
        });
    }, 'image/jpeg', 0.9);
}

// -------------------------------------------------------------
// FILE PICKER HANDLING: CREATE FORM
// -------------------------------------------------------------
function handleCreatePhotoSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file foto melebihi batas 2MB. Sistem akan mengompres foto secara otomatis.');
        }

        compressImage(file, function(compressedFile) {
            if (compressedFile.size > 2 * 1024 * 1024) {
                alert('Foto masih melebihi 2MB setelah kompresi. Silakan gunakan foto lain.');
                input.value = '';
                return;
            }

            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            input.files = dt.files;

            const origKb = (file.size / 1024).toFixed(1);
            const compKb = (compressedFile.size / 1024).toFixed(1);

            document.getElementById('createPreviewImg').src = URL.createObjectURL(compressedFile);
            document.getElementById('createFileName').textContent = file.name;
            document.getElementById('createFileSize').textContent = compKb + ' KB' + (compressedFile.size < file.size ? ' (dikompres dari ' + origKb + ' KB)' : '');

            stopCreateCamera();
            document.getElementById('createUploadPlaceholder').classList.add('hidden');
            document.getElementById('createPreviewContainer').classList.remove('hidden');
        });
    }
}

function removeCreatePhoto() {
    stopCreateCamera();
    const input = document.getElementById('createFotoInput');
    input.value = '';
    document.getElementById('createPreviewImg').src = '';
    document.getElementById('createPreviewContainer').classList.add('hidden');
    document.getElementById('createUploadPlaceholder').classList.remove('hidden');
}

// -------------------------------------------------------------
// CAMERA HANDLING: EDIT FORM
// -------------------------------------------------------------
async function startEditCamera() {
    try {
        stopEditCamera();
        const constraints = {
            video: {
                facingMode: editFacingMode,
                width: { ideal: 1920 },
                height: { ideal: 1080 }
            }
        };
        editStream = await navigator.mediaDevices.getUserMedia(constraints);
        const videoEl = document.getElementById('editVideo');
        videoEl.srcObject = editStream;
        document.getElementById('editCameraContainer').classList.remove('hidden');
        document.getElementById('editUploadPlaceholder').classList.add('hidden');
    } catch (err) {
        console.error('Error starting camera:', err);
        alert('Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin kamera pada peramban/browser.');
    }
}

function stopEditCamera() {
    if (editStream) {
        editStream.getTracks().forEach(track => track.stop());
        editStream = null;
    }
    const container = document.getElementById('editCameraContainer');
    if (container) container.classList.add('hidden');
    const placeholder = document.getElementById('editUploadPlaceholder');
    const preview = document.getElementById('editPreviewContainer');
    if (placeholder && (!preview || preview.classList.contains('hidden'))) {
        placeholder.classList.remove('hidden');
    }
}

function switchEditCamera() {
    editFacingMode = editFacingMode === 'environment' ? 'user' : 'environment';
    startEditCamera();
}

function takeEditSnapshot() {
    const video = document.getElementById('editVideo');
    const canvas = document.getElementById('editCanvas');
    if (!video || !canvas || video.videoWidth === 0) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(function(blob) {
        if (!blob) return;
        const originalFile = new File([blob], 'kamera_edit_' + Date.now() + '.jpg', {
            type: 'image/jpeg',
            lastModified: Date.now()
        });

        // Apply compression utility
        compressImage(originalFile, function(compressedFile) {
            const input = document.getElementById('editFotoInput');
            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            input.files = dt.files;

            const origKb = (originalFile.size / 1024).toFixed(1);
            const compKb = (compressedFile.size / 1024).toFixed(1);

            document.getElementById('editPreviewImg').src = URL.createObjectURL(compressedFile);
            document.getElementById('editFileName').textContent = compressedFile.name;
            document.getElementById('editFileSize').textContent = compKb + ' KB' + (compressedFile.size < originalFile.size ? ' (dikompres dari ' + origKb + ' KB)' : '');

            const deleteCb = document.getElementById('editHapusFoto');
            if (deleteCb) {
                deleteCb.checked = false;
                toggleDeletePhoto(false);
            }

            stopEditCamera();
            document.getElementById('editUploadPlaceholder').classList.add('hidden');
            document.getElementById('editPreviewContainer').classList.remove('hidden');
        });
    }, 'image/jpeg', 0.9);
}

// -------------------------------------------------------------
// FILE PICKER HANDLING: EDIT FORM
// -------------------------------------------------------------
function handleEditPhotoSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file foto melebihi batas 2MB. Sistem akan mengompres foto secara otomatis.');
        }

        compressImage(file, function(compressedFile) {
            if (compressedFile.size > 2 * 1024 * 1024) {
                alert('Foto masih melebihi 2MB setelah kompresi. Silakan gunakan foto lain.');
                input.value = '';
                return;
            }

            const dt = new DataTransfer();
            dt.items.add(compressedFile);
            input.files = dt.files;

            const origKb = (file.size / 1024).toFixed(1);
            const compKb = (compressedFile.size / 1024).toFixed(1);

            document.getElementById('editPreviewImg').src = URL.createObjectURL(compressedFile);
            document.getElementById('editFileName').textContent = file.name;
            document.getElementById('editFileSize').textContent = compKb + ' KB' + (compressedFile.size < file.size ? ' (dikompres dari ' + origKb + ' KB)' : '');

            const deleteCb = document.getElementById('editHapusFoto');
            if (deleteCb) {
                deleteCb.checked = false;
                toggleDeletePhoto(false);
            }

            stopEditCamera();
            document.getElementById('editUploadPlaceholder').classList.add('hidden');
            document.getElementById('editPreviewContainer').classList.remove('hidden');
        });
    }
}

function removeEditPhoto() {
    stopEditCamera();
    const input = document.getElementById('editFotoInput');
    input.value = '';
    document.getElementById('editPreviewImg').src = '';
    document.getElementById('editPreviewContainer').classList.add('hidden');
    document.getElementById('editUploadPlaceholder').classList.remove('hidden');
}

function toggleDeletePhoto(checked) {
    const img = document.getElementById('editExistingPhotoImg');
    if (img) {
        if (checked) {
            img.classList.add('opacity-40', 'grayscale');
        } else {
            img.classList.remove('opacity-40', 'grayscale');
        }
    }
}

function openEditModal(id, tanggal, siswaId, jenis, catatan, tindakLanjut, fotoDokumentasi) {
    stopCreateCamera();
    stopEditCamera();

    document.getElementById('editForm').action = '<?= base_url('guru/jurnal-wali/update') ?>/' + id;
    document.getElementById('editTanggal').value = tanggal;
    document.getElementById('editSiswaId').value = siswaId;
    document.getElementById('editJenis').value = jenis;
    document.getElementById('editCatatan').value = catatan;
    document.getElementById('editTindakLanjut').value = tindakLanjut;

    removeEditPhoto();
    const deleteCb = document.getElementById('editHapusFoto');
    if (deleteCb) {
        deleteCb.checked = false;
        toggleDeletePhoto(false);
    }

    const existingContainer = document.getElementById('editExistingPhotoContainer');
    const existingImg = document.getElementById('editExistingPhotoImg');
    const uploadLabel = document.getElementById('editUploadLabel');

    if (fotoDokumentasi && fotoDokumentasi.trim() !== '') {
        existingImg.src = fotoDokumentasi;
        existingContainer.classList.remove('hidden');
        if (uploadLabel) uploadLabel.textContent = 'Ganti dengan foto baru';
    } else {
        existingImg.src = '';
        existingContainer.classList.add('hidden');
        if (uploadLabel) uploadLabel.textContent = 'Unggah foto dokumentasi';
    }

    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    stopEditCamera();
    document.getElementById('editModal').classList.add('hidden');
}

// Lightbox Modal functions
function openPhotoModal(src, title) {
    document.getElementById('photoModalImg').src = src;
    document.getElementById('photoModalDownload').href = src;
    document.getElementById('photoModalTitle').innerHTML = '<i class="fas fa-image text-blue-600 mr-2"></i>' + (title || 'Dokumentasi Bimbingan');
    document.getElementById('photoModal').classList.remove('hidden');
}

function closePhotoModal(event) {
    if (!event || event.target.id === 'photoModal' || (event.target.closest && event.target.closest('button'))) {
        document.getElementById('photoModal').classList.add('hidden');
        document.getElementById('photoModalImg').src = '';
    }
}

// Stop camera streams when user leaves or hides page
window.addEventListener('beforeunload', () => {
    stopCreateCamera();
    stopEditCamera();
});
</script>
<?= $this->endSection() ?>
