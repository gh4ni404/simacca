<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? 'Jadwal Mengajar' ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('guru/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Jadwal Mengajar</li>
    </ol>

    <!-- Jadwal Hari Ini -->
    <?php if (!empty($jadwalHariIni)): ?>
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-calendar-day me-1"></i>
            Jadwal Hari Ini - <?= date('l, d F Y') ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Semester</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwalHariIni as $j): ?>
                        <tr>
                            <td><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></td>
                            <td><?= $j['nama_mapel'] ?></td>
                            <td><?= $j['nama_kelas'] ?></td>
                            <td><?= $j['semester'] ?> <?= $j['tahun_ajaran'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-1"></i>
        Tidak ada jadwal mengajar hari ini.
    </div>
    <?php endif; ?>

    <!-- Jadwal Mingguan -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-week me-1"></i>
            Jadwal Mengajar Mingguan
        </div>
        <div class="card-body">
            <?php if (empty($jadwal)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Belum ada jadwal mengajar yang terdaftar.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Hari</th>
                            <th style="width: 150px;">Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Semester/TA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                        foreach ($hariList as $hari): 
                            if (!empty($jadwalByHari[$hari])):
                                $rowspan = count($jadwalByHari[$hari]);
                                $first = true;
                                foreach ($jadwalByHari[$hari] as $j):
                        ?>
                        <tr>
                            <?php if ($first): ?>
                            <td rowspan="<?= $rowspan ?>" class="align-middle"><strong><?= $hari ?></strong></td>
                            <?php $first = false; endif; ?>
                            <td><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></td>
                            <td><?= $j['nama_mapel'] ?></td>
                            <td><?= $j['nama_kelas'] ?></td>
                            <td><?= $j['semester'] ?> / <?= $j['tahun_ajaran'] ?></td>
                        </tr>
                        <?php 
                                endforeach;
                            endif;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Total Jadwal -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Total Jadwal</div>
                            <div class="h3 mb-0"><?= count($jadwal) ?></div>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
