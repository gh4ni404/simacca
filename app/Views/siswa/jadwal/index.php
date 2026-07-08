<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                    Jadwal Pelajaran
                </h1>
                <p class="text-gray-600 mt-1">Jadwal pelajaran kelas <?= esc($siswa['nama_kelas']); ?></p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-user-graduate mr-1"></i>
                    <?= esc($siswa['nama_lengkap']); ?>
                    <span class="mx-2">•</span>
                    <i class="fas fa-id-card mr-1"></i>
                    <?= esc($siswa['nis']); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Day Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex overflow-x-auto -mb-px">
                <?php 
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                foreach ($days as $day): 
                    $isToday = ($day === $hariIni);
                    $hasSchedule = !empty($jadwalByDay[$day]);
                ?>
                <a href="#<?= strtolower($day); ?>" 
                   class="day-tab whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm <?= $isToday ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>"
                   data-day="<?= strtolower($day); ?>">
                    <i class="fas fa-calendar-day mr-2"></i>
                    <?= $day; ?>
                    <?php if ($isToday): ?>
                        <span class="ml-2 bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">Hari Ini</span>
                    <?php endif; ?>
                    <?php if ($hasSchedule): ?>
                        <span class="ml-2 bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded-full">
                            <?= count($jadwalByDay[$day]); ?>
                        </span>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </div>

    <!-- Schedule Content -->
    <div class="schedule-content">
        <?php foreach ($days as $day): ?>
        <div id="<?= strtolower($day); ?>" class="day-content <?= $day !== $hariIni ? 'hidden' : ''; ?>">
            <?php if (empty($jadwalByDay[$day])): ?>
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                    <i class="fas fa-calendar-times text-6xl mb-4"></i>
                    <p class="text-lg mb-2">Tidak ada jadwal untuk hari <?= $day; ?></p>
                    <p class="text-sm">Selamat berlibur! 🎉</p>
                </div>
            <?php else: ?>
                <!-- Timeline View -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-t-lg">
                        <h2 class="text-lg font-semibold">
                            <i class="fas fa-calendar-day mr-2"></i>
                            Jadwal Hari <?= $day; ?>
                        </h2>
                        <p class="text-sm opacity-80 mt-1">
                            Total <?= count($jadwalByDay[$day]); ?> mata pelajaran
                        </p>
                    </div>
                    <div class="p-6">
                        <div class="relative">
                            <!-- Timeline Line -->
                            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-300"></div>
                            
                            <!-- Schedule Items -->
                            <div class="space-y-6">
                                <?php foreach ($jadwalByDay[$day] as $index => $jadwal): ?>
                                <div class="relative flex items-start group">
                                    <!-- Timeline Dot -->
                                    <div class="absolute left-8 w-4 h-4 rounded-full bg-blue-500 border-4 border-white shadow-md transform -translate-x-1/2 group-hover:scale-125 transition-transform"></div>
                                    
                                    <!-- Time -->
                                    <div class="flex-shrink-0 w-32 pt-1">
                                        <div class="text-right pr-8">
                                            <div class="text-lg font-bold text-blue-600">
                                                <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Content Card -->
                                    <div class="flex-1 ml-8">
                                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 shadow hover:shadow-lg transition-all group-hover:scale-105">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-600 text-white">
                                                            <i class="fas fa-book mr-1"></i>
                                                            <?= esc($jadwal['kode_mapel']); ?>
                                                        </span>
                                                        <span class="ml-2 text-xs text-gray-500">
                                                            Pertemuan ke-<?= $index + 1; ?>
                                                        </span>
                                                    </div>
                                                    <h3 class="text-lg font-bold text-gray-800 mb-2">
                                                        <?= esc($jadwal['nama_mapel']); ?>
                                                    </h3>
                                                    <div class="space-y-1 text-sm text-gray-600">
                                                        <div class="flex items-center">
                                                            <i class="fas fa-user-tie w-5 text-blue-500"></i>
                                                            <span class="ml-2"><?= esc($jadwal['nama_guru']); ?></span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <i class="fas fa-clock w-5 text-green-500"></i>
                                                            <span class="ml-2">
                                                                <?php 
                                                                $start = strtotime($jadwal['jam_mulai']);
                                                                $end = strtotime($jadwal['jam_selesai']);
                                                                $duration = ($end - $start) / 60;
                                                                echo $duration . ' menit';
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ml-4">
                                                    <div class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <i class="fas fa-graduation-cap text-2xl text-blue-600"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                                <i class="fas fa-book-open text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Pelajaran</p>
                                <p class="text-2xl font-bold"><?= count($jadwalByDay[$day]); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                                <i class="fas fa-hourglass-start text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jam Mulai</p>
                                <p class="text-2xl font-bold">
                                    <?= !empty($jadwalByDay[$day]) ? date('H:i', strtotime($jadwalByDay[$day][0]['jam_mulai'])) : '-'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-3">
                                <i class="fas fa-hourglass-end text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jam Selesai</p>
                                <p class="text-2xl font-bold">
                                    <?= !empty($jadwalByDay[$day]) ? date('H:i', strtotime($jadwalByDay[$day][count($jadwalByDay[$day]) - 1]['jam_selesai'])) : '-'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-600 text-xl mr-3 mt-1"></i>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1">Informasi:</p>
                <ul class="list-disc list-inside space-y-1 ml-2">
                    <li>Jadwal dapat berubah sewaktu-waktu, harap selalu cek update terbaru</li>
                    <li>Pastikan datang tepat waktu sesuai jam yang tertera</li>
                    <li>Jika ada perubahan jadwal mendadak, akan diinformasikan oleh guru/wali kelas</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.day-tab');
    const contents = document.querySelectorAll('.day-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const targetDay = this.getAttribute('data-day');
            
            // Remove active class from all tabs
            tabs.forEach(t => {
                t.classList.remove('border-blue-500', 'text-blue-600');
                t.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600');
            
            // Hide all contents
            contents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Show target content
            document.getElementById(targetDay).classList.remove('hidden');
        });
    });
});
</script>
<?= $this->endSection() ?>
