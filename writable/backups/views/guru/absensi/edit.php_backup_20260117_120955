<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">
    <!-- Header -->
    <div class="mb-6 md:mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2 md:p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                <i class="fas fa-edit text-white text-xl md:text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                    <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Edit Absensi
                    </span>
                </h1>
                <p class="text-gray-600 text-xs md:text-sm mt-1">
                    <i class="fas fa-info-circle mr-2 text-blue-500 text-xs md:text-sm"></i>
                    <span class="text-xs md:text-sm">Perbarui data absensi siswa</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?= view('components/alerts') ?>

    <!-- Absensi Info Card -->
    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border-2 border-blue-300 rounded-xl md:rounded-2xl shadow-md md:shadow-lg p-4 md:p-6 mb-6 md:mb-8">
        <div class="flex items-center mb-4">
            <div class="p-2 bg-blue-500 rounded-lg mr-3">
                <i class="fas fa-info-circle text-white"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-800">Informasi Absensi</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <div class="flex items-center bg-white rounded-lg p-3 md:p-4 shadow-sm">
                <div class="p-1.5 md:p-2 bg-blue-100 rounded-lg mr-2 md:mr-3">
                    <i class="fas fa-calendar-day text-blue-600 text-xs md:text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Tanggal</p>
                    <p class="text-xs md:text-sm font-bold text-gray-800"><?= date('d F Y', strtotime($absensi['tanggal'])) ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-3 md:p-4 shadow-sm">
                <div class="p-1.5 md:p-2 bg-green-100 rounded-lg mr-2 md:mr-3">
                    <i class="fas fa-book text-green-600 text-xs md:text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Mapel</p>
                    <p class="text-xs md:text-sm font-bold text-gray-800"><?= $absensi['nama_mapel'] ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-3 md:p-4 shadow-sm">
                <div class="p-1.5 md:p-2 bg-purple-100 rounded-lg mr-2 md:mr-3">
                    <i class="fas fa-school text-purple-600 text-xs md:text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Kelas</p>
                    <p class="text-xs md:text-sm font-bold text-gray-800"><?= $absensi['nama_kelas'] ?></p>
                </div>
            </div>
            <div class="flex items-center bg-white rounded-lg p-3 md:p-4 shadow-sm">
                <div class="p-1.5 md:p-2 bg-indigo-100 rounded-lg mr-2 md:mr-3">
                    <i class="fas fa-calendar-week text-indigo-600 text-xs md:text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Hari</p>
                    <p class="text-xs md:text-sm font-bold text-gray-800"><?= $absensi['hari'] ?? '-' ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="<?= base_url('guru/absensi/update/' . $absensi['id']) ?>" method="POST" id="formEditAbsensi">
        <?= csrf_field() ?>

        <!-- Card: Edit Data Absensi -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg md:shadow-xl overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-400 to-purple-500 px-4 md:px-6 py-3 md:py-4">
                <div class="flex items-center">
                    <i class="fas fa-edit text-white text-base md:text-lg mr-2 md:mr-3"></i>
                    <h2 class="text-base md:text-lg font-bold text-white">Edit Data Absensi</h2>
                </div>
            </div>
            <div class="p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Pertemuan Ke -->
                    <div>
                        <label for="pertemuan_ke" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-hashtag mr-2 text-indigo-500"></i>
                            Pertemuan Ke
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="number"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            id="pertemuan_ke"
                            name="pertemuan_ke"
                            value="<?= old('pertemuan_ke', $absensi['pertemuan_ke']) ?>"
                            required
                            min="1">
                    </div>

                    <!-- Tanggal (Editable) -->
                    <div>
                        <label for="tanggal" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            Tanggal
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="date"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all"
                            id="tanggal"
                            name="tanggal"
                            value="<?= old('tanggal', $absensi['tanggal']) ?>"
                            required>
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tanggal dapat diubah sesuai kebutuhan
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Daftar Kehadiran Siswa -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg md:shadow-xl overflow-hidden mb-6">
            <div class="px-4 md:px-6 py-3 md:py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex items-center">
                        <div class="p-1.5 md:p-2 bg-blue-500 rounded-lg mr-2 md:mr-3">
                            <i class="fas fa-users text-white text-sm md:text-base"></i>
                        </div>
                        <h2 class="text-base md:text-lg font-bold text-gray-800">Daftar Kehadiran Siswa</h2>
                    </div>
                    <div class="hidden md:flex gap-2 flex-wrap">
                        <button type="button"
                            onclick="setAllStatus('hadir')"
                            class="inline-flex items-center px-3 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                            <i class="fas fa-check-circle mr-1"></i> Semua Hadir
                        </button>
                        <button type="button"
                            onclick="setAllStatus('izin')"
                            class="inline-flex items-center px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                            <i class="fas fa-file-alt mr-1"></i> Semua Izin
                        </button>
                        <button type="button"
                            onclick="setAllStatus('sakit')"
                            class="inline-flex items-center px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                            <i class="fas fa-medkit mr-1"></i> Semua Sakit
                        </button>
                        <button type="button"
                            onclick="setAllStatus('alpa')"
                            class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-105">
                            <i class="fas fa-times-circle mr-1"></i> Semua Alpha
                        </button>
                    </div>
                </div>
            </div>

            <!-- Progress Indicator & Quick Actions (Mobile Only) -->
            <div class="md:hidden px-4 py-3 bg-gray-50 border-b border-gray-200">
                <div class="bg-gray-900 text-white px-3 py-2 rounded-full text-center shadow-md mb-3">
                    <span id="mobile-progress-counter" class="font-semibold text-xs">
                        <?php 
                        $filledCount = count($absensiDetails);
                        $totalCount = count($siswaList);
                        echo "$filledCount / $totalCount Siswa Terisi";
                        ?>
                    </span>
                </div>
                <!-- Mobile Quick Actions -->
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" 
                            onclick="setAllStatus('hadir')"
                            class="flex flex-col items-center justify-center p-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-sm transition-all text-xs">
                        <i class="fas fa-check-circle mb-1"></i>
                        <span>Hadir</span>
                    </button>
                    <button type="button" 
                            onclick="setAllStatus('izin')"
                            class="flex flex-col items-center justify-center p-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg shadow-sm transition-all text-xs">
                        <i class="fas fa-file-alt mb-1"></i>
                        <span>Izin</span>
                    </button>
                    <button type="button" 
                            onclick="setAllStatus('sakit')"
                            class="flex flex-col items-center justify-center p-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-sm transition-all text-xs">
                        <i class="fas fa-medkit mb-1"></i>
                        <span>Sakit</span>
                    </button>
                    <button type="button" 
                            onclick="setAllStatus('alpa')"
                            class="flex flex-col items-center justify-center p-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg shadow-sm transition-all text-xs">
                        <i class="fas fa-times-circle mb-1"></i>
                        <span>Alpha</span>
                    </button>
                </div>
            </div>

            <div class="p-6">
                <?php if (empty($siswaList)): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                            <p class="text-yellow-800 font-medium">Tidak ada siswa dalam kelas ini.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Desktop View: Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-100 to-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NO</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NAMA SISWA</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                        STATUS
                                        <span class="ml-2 text-xs font-normal text-gray-500">(Klik tombol)</span>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                // Create array of existing absensi details
                                $existingDetails = [];
                                foreach ($absensiDetails as $detail) {
                                    $existingDetails[$detail['siswa_id']] = $detail;
                                }

                                foreach ($siswaList as $siswa):
                                    $detail = $existingDetails[$siswa['id']] ?? null;
                                    $currentStatus = $detail ? $detail['status'] : 'Hadir';
                                    $currentKeterangan = $detail ? $detail['keterangan'] : '';
                                ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4 text-center text-sm font-medium text-gray-900"><?= $no++ ?></td>
                                        <td class="px-4 py-4 text-sm text-gray-700 font-medium"><?= $siswa['nis'] ?></td>
                                        <td class="px-4 py-4 text-sm text-gray-900 font-medium"><?= $siswa['nama_lengkap'] ?></td>
                                        <td class="px-4 py-4">
                                            <!-- Hidden input to store the selected status -->
                                            <input type="hidden" name="siswa[<?= $siswa['id'] ?>][status]" value="<?= strtolower($currentStatus) ?>" class="status-input" data-siswa-id="<?= $siswa['id'] ?>">
                                            
                                            <!-- Status Button Group -->
                                            <div class="grid grid-cols-4 gap-2" data-siswa-id="<?= $siswa['id'] ?>">
                                                <?php foreach (['hadir', 'izin', 'sakit', 'alpa'] as $statusValue): 
                                                    $isSelected = (strtolower($currentStatus) == $statusValue);
                                                    
                                                    // Set button style based on status - matching reference image
                                                    // Display labels (capitalized for UI)
                                                    $displayLabel = ucfirst($statusValue);
                                                    if ($statusValue == 'alpa') {
                                                        $displayLabel = 'Alpha';
                                                    }
                                                    
                                                    if ($statusValue == 'hadir') {
                                                        $btnClass = $isSelected ? 'bg-green-500 text-white border-green-500' : 'bg-white text-green-600 border-green-400 hover:bg-green-50';
                                                        $icon = 'fa-check-circle';
                                                    } elseif ($statusValue == 'izin') {
                                                        $btnClass = $isSelected ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-blue-600 border-blue-400 hover:bg-blue-50';
                                                        $icon = 'fa-clipboard-list';
                                                    } elseif ($statusValue == 'sakit') {
                                                        $btnClass = $isSelected ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-orange-600 border-orange-400 hover:bg-orange-50';
                                                        $icon = 'fa-briefcase-medical';
                                                    } else { // alpa
                                                        $btnClass = $isSelected ? 'bg-red-500 text-white border-red-500' : 'bg-white text-red-600 border-red-400 hover:bg-red-50';
                                                        $icon = 'fa-times-circle';
                                                    }
                                                ?>
                                                <button type="button" 
                                                        class="status-btn px-3 py-2.5 border-2 rounded-lg font-semibold text-sm transition-all <?= $btnClass ?> flex items-center justify-center gap-1.5 min-w-[90px]"
                                                        data-siswa-id="<?= $siswa['id'] ?>"
                                                        data-status="<?= $statusValue ?>"
                                                        onclick="selectStatus(<?= $siswa['id'] ?>, '<?= $statusValue ?>')">
                                                    <i class="fas <?= $icon ?> text-base"></i>
                                                    <span><?= $displayLabel ?></span>
                                                </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <input type="text"
                                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all keterangan-input"
                                                name="siswa[<?= $siswa['id'] ?>][keterangan]"
                                                id="keterangan_<?= $siswa['id'] ?>"
                                                value="<?= $currentKeterangan ?>"
                                                placeholder="Keterangan (opsional)">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile View: Cards -->
                    <div class="md:hidden space-y-4">
                        <?php
                        // Recreate the same data structure for mobile view
                        $existingDetails = [];
                        foreach ($absensiDetails as $detail) {
                            $existingDetails[$detail['siswa_id']] = $detail;
                        }

                        foreach ($siswaList as $siswa):
                            $detail = $existingDetails[$siswa['id']] ?? null;
                            $currentStatus = $detail ? $detail['status'] : 'Hadir';
                            $currentKeterangan = $detail ? $detail['keterangan'] : '';
                            
                            // Check if student has approved izin
                            $hasIzin = false;
                            foreach ($approvedIzin as $izin) {
                                if ($izin['siswa_id'] == $siswa['id']) {
                                    $hasIzin = true;
                                    break;
                                }
                            }
                        ?>
                        <div class="student-card bg-white rounded-2xl shadow-md p-4 border-2 <?= $detail ? 'border-gray-300' : 'border-transparent' ?> transition-all" data-student-id="<?= $siswa['id'] ?>">
                            <!-- Student Info -->
                            <div class="flex items-center gap-3 mb-3">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg">
                                        <?= strtoupper(substr($siswa['nama_lengkap'], 0, 1)) ?>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white items-center justify-center <?= $detail ? 'flex' : 'hidden' ?> student-check-<?= $siswa['id'] ?>">
                                        <i class="fas fa-check text-white text-xs"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-base text-gray-900"><?= $siswa['nama_lengkap'] ?></h3>
                                        <?php if ($detail): ?>
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                                Tersimpan
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-gray-600">NIS: <?= $siswa['nis'] ?></p>
                                    <?php if ($hasIzin): ?>
                                    <p class="text-xs text-blue-600 mt-0.5">
                                        <i class="fas fa-info-circle mr-1"></i>Izin disetujui
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Hidden Input -->
                            <input type="hidden" name="siswa[<?= $siswa['id'] ?>][status]" value="<?= strtolower($currentStatus) ?>" class="status-input" data-siswa-id="<?= $siswa['id'] ?>">

                            <!-- Status Buttons -->
                            <div class="grid grid-cols-4 gap-2 mb-3">
                                <?php 
                                $mobileStatusOptions = [
                                    'hadir' => ['label' => 'Hadir', 'icon' => 'fa-check-circle', 'activeColor' => 'bg-green-500 text-white border-green-500', 'inactiveColor' => 'bg-white text-gray-700 border-gray-300'],
                                    'izin' => ['label' => 'Izin', 'icon' => 'fa-clipboard-list', 'activeColor' => 'bg-blue-500 text-white border-blue-500', 'inactiveColor' => 'bg-white text-gray-700 border-gray-300'],
                                    'sakit' => ['label' => 'Sakit', 'icon' => 'fa-briefcase-medical', 'activeColor' => 'bg-orange-500 text-white border-orange-500', 'inactiveColor' => 'bg-white text-gray-700 border-gray-300'],
                                    'alpa' => ['label' => 'Alpha', 'icon' => 'fa-times-circle', 'activeColor' => 'bg-red-500 text-white border-red-500', 'inactiveColor' => 'bg-white text-gray-700 border-gray-300']
                                ];
                                
                                foreach ($mobileStatusOptions as $value => $option):
                                    $isSelected = (strtolower($currentStatus) == $value);
                                    $buttonClass = $isSelected ? $option['activeColor'] : $option['inactiveColor'];
                                ?>
                                <button type="button" 
                                        class="status-btn flex flex-col items-center justify-center p-3 border-2 rounded-xl transition-all active:scale-95 <?= $buttonClass ?> relative"
                                        data-siswa-id="<?= $siswa['id'] ?>"
                                        data-status="<?= $value ?>"
                                        onclick="selectStatus(<?= $siswa['id'] ?>, '<?= $value ?>')">
                                    <i class="fas <?= $option['icon'] ?> text-xl mb-1"></i>
                                    <span class="text-xs font-semibold"><?= $option['label'] ?></span>
                                    <?php if ($isSelected): ?>
                                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-white rounded-full flex items-center justify-center border-2 border-current">
                                            <i class="fas fa-check text-xs"></i>
                                        </div>
                                    <?php endif; ?>
                                </button>
                                <?php endforeach; ?>
                            </div>

                            <!-- Notes Field -->
                            <textarea name="siswa[<?= $siswa['id'] ?>][keterangan]"
                                      class="w-full px-3 py-2 bg-gray-50 border-2 border-gray-200 rounded-xl resize-none focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm"
                                      rows="2"
                                      placeholder="Keterangan (opsional)"><?= $currentKeterangan ?></textarea>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-lg md:shadow-xl p-4 md:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 md:gap-4 pt-6 md:pt-8 border-t-2 border-gray-200">
                <a href="<?= base_url('guru/absensi') ?>"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <button type="submit"
                        name="next_action"
                        value="list"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5"
                        id="btnSubmit">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                    <button type="submit"
                        name="next_action"
                        value="jurnal"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-book mr-2"></i> Lanjut isi Jurnal
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Function to handle status button selection
    function selectStatus(siswaId, status) {
        console.log('selectStatus called:', siswaId, status);
        
        // Update hidden input value
        const hiddenInputs = document.querySelectorAll(`.status-input[data-siswa-id="${siswaId}"]`);
        if (hiddenInputs.length > 0) {
            hiddenInputs.forEach(input => {
                input.value = status;
                // Mark as manually set
                input.setAttribute('data-manually-set', 'true');
            });
            console.log('Hidden inputs updated:', hiddenInputs.length);
        } else {
            console.error('Hidden input not found for siswa ID:', siswaId);
            return;
        }

        // Define button styles for each status (desktop) - use lowercase keys to match database
        const desktopButtonStyles = {
            'hadir': {
                active: ['bg-green-500', 'text-white', 'border-green-500'],
                inactive: ['bg-white', 'text-green-600', 'border-green-400', 'hover:bg-green-50']
            },
            'izin': {
                active: ['bg-blue-500', 'text-white', 'border-blue-500'],
                inactive: ['bg-white', 'text-blue-600', 'border-blue-400', 'hover:bg-blue-50']
            },
            'sakit': {
                active: ['bg-orange-500', 'text-white', 'border-orange-500'],
                inactive: ['bg-white', 'text-orange-600', 'border-orange-400', 'hover:bg-orange-50']
            },
            'alpa': {
                active: ['bg-red-500', 'text-white', 'border-red-500'],
                inactive: ['bg-white', 'text-red-600', 'border-red-400', 'hover:bg-red-50']
            }
        };

        // Define button styles for mobile
        const mobileButtonStyles = {
            'hadir': {
                active: ['bg-green-500', 'text-white', 'border-green-500'],
                inactive: ['bg-white', 'text-gray-700', 'border-gray-300']
            },
            'izin': {
                active: ['bg-blue-500', 'text-white', 'border-blue-500'],
                inactive: ['bg-white', 'text-gray-700', 'border-gray-300']
            },
            'sakit': {
                active: ['bg-orange-500', 'text-white', 'border-orange-500'],
                inactive: ['bg-white', 'text-gray-700', 'border-gray-300']
            },
            'alpa': {
                active: ['bg-red-500', 'text-white', 'border-red-500'],
                inactive: ['bg-white', 'text-gray-700', 'border-gray-300']
            }
        };

        // All possible color classes to remove
        const allColorClasses = [
            'bg-green-500', 'bg-blue-500', 'bg-yellow-500', 'bg-orange-500', 'bg-red-500',
            'bg-white', 'bg-green-50', 'bg-blue-50', 'bg-yellow-50', 'bg-orange-50', 'bg-red-50',
            'text-white', 'text-green-600', 'text-green-700', 'text-blue-600', 'text-blue-700', 
            'text-yellow-600', 'text-yellow-700', 'text-orange-600', 'text-orange-700', 
            'text-red-600', 'text-red-700', 'text-gray-700',
            'border-green-500', 'border-green-600', 'border-green-400', 'border-green-300',
            'border-blue-500', 'border-blue-600', 'border-blue-400', 'border-blue-300',
            'border-yellow-500', 'border-yellow-600', 'border-yellow-400', 'border-yellow-300',
            'border-orange-500', 'border-orange-600', 'border-orange-400', 'border-orange-300',
            'border-red-500', 'border-red-600', 'border-red-400', 'border-red-300', 
            'border-gray-300',
            'shadow-md', 'hover:bg-green-50', 'hover:bg-blue-50', 'hover:bg-yellow-50', 'hover:bg-orange-50', 'hover:bg-red-50'
        ];

        // Get all status buttons for this student
        const allButtons = document.querySelectorAll(`.status-btn[data-siswa-id="${siswaId}"]`);
        console.log('Total buttons found:', allButtons.length);
        
        if (allButtons.length === 0) {
            console.error('No buttons found for siswa ID:', siswaId);
            return;
        }

        allButtons.forEach(btn => {
            const btnStatus = btn.getAttribute('data-status');
            
            // Determine if this is a mobile button (has flex-col class)
            const isMobile = btn.classList.contains('flex-col');
            const styleSet = isMobile ? mobileButtonStyles : desktopButtonStyles;
            const style = styleSet[btnStatus];
            
            if (!style) {
                console.warn('No style found for status:', btnStatus);
                return;
            }
            
            // Remove all color classes
            btn.classList.remove(...allColorClasses);
            
            // Apply appropriate style
            if (btnStatus === status) {
                // Active button
                btn.classList.add(...style.active);
            } else {
                // Inactive button
                btn.classList.add(...style.inactive);
            }
        });

        // Update progress counter if exists
        updateProgressCounters();

        // Show check mark on mobile card
        const checkMark = document.querySelector(`.student-check-${siswaId}`);
        if (checkMark) {
            checkMark.classList.remove('hidden');
            checkMark.classList.add('flex');
        }

        // Add visual feedback for mobile card
        const mobileCard = document.querySelector(`.student-card[data-student-id="${siswaId}"]`);
        if (mobileCard) {
            mobileCard.classList.add('border-green-500', 'bg-green-50');
            setTimeout(() => {
                mobileCard.classList.remove('bg-green-50');
            }, 300);
        }
        
        console.log('selectStatus completed for:', siswaId, status);
    }

    // Function to update progress counters
    function updateProgressCounters() {
        const hiddenInputs = document.querySelectorAll('.status-input');
        let filledCount = 0;
        
        // Count students that have been manually changed (marked with data attribute)
        hiddenInputs.forEach(input => {
            const isManuallySet = input.getAttribute('data-manually-set') === 'true';
            if (isManuallySet) {
                filledCount++;
            }
        });

        const totalCount = hiddenInputs.length;
        
        // Update mobile progress counter
        const mobileCounter = document.getElementById('mobile-progress-counter');
        if (mobileCounter) {
            mobileCounter.textContent = `${filledCount} / ${totalCount} Siswa Diubah`;
        }
        
        // Update desktop progress display if it exists
        const progressDisplay = document.getElementById('edit-progress-counter');
        if (progressDisplay) {
            progressDisplay.textContent = `${filledCount} / ${totalCount} Siswa Diubah`;
        }
    }
    
    // Function to count initially filled students
    function getInitialFilledCount() {
        const hiddenInputs = document.querySelectorAll('.status-input');
        let count = 0;
        hiddenInputs.forEach(input => {
            if (input.value && input.value !== '') {
                count++;
            }
        });
        return count;
    }

    // Function to set all students to the same status
    function setAllStatus(status) {
        const hiddenInputs = document.querySelectorAll('.status-input');
        
        // Convert to lowercase for consistency with database
        const statusLower = status.toLowerCase();
        const statusToSend = (statusLower === 'alpha') ? 'alpa' : statusLower;
        
        hiddenInputs.forEach(input => {
            const siswaId = input.getAttribute('data-siswa-id');
            selectStatus(siswaId, statusToSend);
        });

        // Show feedback
        const statusLabels = {
            'hadir': 'Hadir',
            'izin': 'Izin',
            'sakit': 'Sakit',
            'alpa': 'Alpha',
            'Hadir': 'Hadir',
            'Izin': 'Izin',
            'Sakit': 'Sakit',
            'Alpha': 'Alpha'
        };

        // Create temporary notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>Semua siswa di-set <strong>${statusLabels[status]}</strong></span>
            </div>
        `;
        document.body.appendChild(notification);

        // Remove notification after 2 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    // Form validation and submission handler
    document.getElementById('formEditAbsensi').addEventListener('submit', function(e) {
        // Validate that we have data to submit
        const hiddenInputs = document.querySelectorAll('.status-input');
        let hasData = false;
        
        hiddenInputs.forEach(input => {
            if (input.value && input.value !== '') {
                hasData = true;
            }
        });
        
        if (!hasData) {
            e.preventDefault();
            alert('Mohon isi setidaknya satu status kehadiran siswa!');
            return false;
        }
        
        // Debug: Log data yang akan dikirim
        console.log('Form submitting with data:');
        const formData = new FormData(this);
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        // Get the button that was clicked
        const clickedButton = e.submitter || document.activeElement;
        
        // Disable the clicked button only after a short delay to ensure form submits
        setTimeout(function() {
            if (clickedButton && clickedButton.tagName === 'BUTTON') {
                clickedButton.disabled = true;
                const originalHTML = clickedButton.innerHTML;
                clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
                
                // Re-enable after 5 seconds in case of error
                setTimeout(function() {
                    clickedButton.disabled = false;
                    clickedButton.innerHTML = originalHTML;
                }, 5000);
            }
        }, 100);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Show initial count of filled students from database
        const initialCount = getInitialFilledCount();
        console.log('Initial filled count:', initialCount);
        
        // Don't mark as manually set initially - counter will show changes only
        // But we can show a summary of existing data
        if (initialCount > 0) {
            const totalCount = document.querySelectorAll('.status-input').length;
            
            // Add info badge showing existing data
            const header = document.querySelector('.bg-gradient-to-r.from-gray-50');
            if (header && window.innerWidth >= 768) {
                const infoBadge = document.createElement('div');
                infoBadge.className = 'inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold';
                infoBadge.innerHTML = `<i class="fas fa-database mr-1.5"></i>${initialCount}/${totalCount} Data Tersimpan`;
                
                const headerDiv = header.querySelector('.flex.flex-col');
                if (headerDiv) {
                    headerDiv.appendChild(infoBadge);
                }
            }
        }
        
        updateProgressCounters();
    });
</script>

<?= $this->endSection() ?>