<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 1.5cm 1cm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .print-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000;
        }

        .print-header h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .print-header h3 {
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .info-box {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            display: inline-block;
            padding: 5px 30px;
            margin-top: 10px;
        }

        .info-box p {
            font-size: 9pt;
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 8pt;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px 4px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background-color: #e5e7eb;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
        }

        .text-center {
            text-align: center;
        }

        .text-nowrap {
            white-space: nowrap;
        }

        .bg-red {
            background-color: #fee;
        }

        .text-red {
            color: #dc2626;
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }

        .badge-danger {
            background-color: #dc2626;
            color: white;
        }

        .badge-success {
            background-color: #10b981;
            color: white;
        }

        .badge-warning {
            background-color: #f59e0b;
            color: white;
        }

        .badge-info {
            background-color: #3b82f6;
            color: white;
        }

        .badge-purple {
            background-color: #8b5cf6;
            color: white;
        }

        .print-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
        }

        .signature-box {
            width: 45%;
        }

        .signature-box p {
            margin: 3px 0;
            font-size: 9pt;
        }

        .signature-box .name {
            margin-top: 60px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            text-align: center;
        }

        .signature-box .nip {
            font-size: 8pt;
            margin-top: 2px;
        }

        img {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
        }

        .truncate {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .wrap-text {
            word-wrap: break-word;
            word-break: break-word;
        }
    </style>
</head>
<body>
    <!-- Print Header -->
    <div class="print-header">
        <h2>LAPORAN ABSENSI PEMBELAJARAN</h2>
        <h3>SISTEM INFORMASI AKADEMIK</h3>
        <div class="info-box">
            <p>Tanggal: <?= date('d/m/Y', strtotime($tanggal)); ?></p>
            <?php if ($kelasId): ?>
                <p>Kelas: <?= esc($kelasList[$kelasId] ?? '-'); ?></p>
            <?php else: ?>
                <p>Semua Kelas</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 8%;">Kelas</th>
                <th style="width: 7%;">Jam</th>
                <th style="width: 15%;">Guru Mapel</th>
                <th style="width: 12%;">Mata Pelajaran</th>
                <th style="width: 18%;">Kegiatan Pembelajaran</th>
                <th style="width: 4%;">H</th>
                <th style="width: 4%;">S</th>
                <th style="width: 4%;">I</th>
                <th style="width: 4%;">A</th>
                <th style="width: 8%;">Foto</th>
                <th style="width: 12%;">Pengganti</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($laporanPerHari)): ?>
                <?php $globalNo = 1; ?>
                <?php foreach ($laporanPerHari as $hariData): ?>
                    <?php foreach ($hariData['jadwal_list'] as $jadwal): ?>
                        <?php 
                        $belumIsi = empty($jadwal['absensi_id']);
                        ?>
                        <tr <?= $belumIsi ? 'class="bg-red"' : ''; ?>>
                            <td class="text-center <?= $belumIsi ? 'text-red' : ''; ?>"><?= $globalNo++; ?></td>
                            <td class="<?= $belumIsi ? 'text-red' : ''; ?>">
                                <?= esc($jadwal['nama_kelas']); ?>
                            </td>
                            <td class="text-center text-nowrap <?= $belumIsi ? 'text-red' : ''; ?>">
                                <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?>
                            </td>
                            <td class="<?= $belumIsi ? 'text-red' : ''; ?>">
                                <?= esc($jadwal['nama_guru']); ?>
                            </td>
                            <td class="<?= $belumIsi ? 'text-red' : ''; ?>">
                                <?= esc($jadwal['nama_mapel']); ?>
                            </td>

                            <?php if ($belumIsi): ?>
                                <td class="text-center text-red">-</td>
                                <td colspan="4" class="text-center">
                                    <span class="badge badge-danger">BELUM ISI</span>
                                </td>
                                <td class="text-center text-red">-</td>
                                <td class="text-center text-red">-</td>
                            <?php else: ?>
                                <td class="wrap-text">
                                    <?php if (!empty($jadwal['kegiatan_pembelajaran'])): ?>
                                        <?= esc(strlen($jadwal['kegiatan_pembelajaran']) > 80 ? substr($jadwal['kegiatan_pembelajaran'], 0, 80) . '...' : $jadwal['kegiatan_pembelajaran']); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success"><?= (int)$jadwal['jumlah_hadir']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning"><?= (int)$jadwal['jumlah_sakit']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info"><?= (int)$jadwal['jumlah_izin']; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-danger"><?= (int)$jadwal['jumlah_alpa']; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($jadwal['foto_dokumentasi'])): ?>
                                        <img src="<?= base_url('files/jurnal/' . esc($jadwal['foto_dokumentasi'])) ?>" alt="Foto">
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 8pt;">Tidak ada foto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($jadwal['nama_guru_pengganti'])): ?>
                                        <span class="badge badge-purple"><?= esc($jadwal['nama_guru_pengganti']); ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12" class="text-center" style="padding: 20px;">
                        Belum ada jadwal untuk tanggal ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Print Footer -->
    <div class="print-footer">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p style="font-weight: bold;">Kepala Sekolah</p>
            <p class="name">_____________________</p>
            <p class="nip">NIP. __________________</p>
        </div>
        <div class="signature-box" style="text-align: right;">
            <p>Dicetak tanggal: <?= date('d/m/Y H:i'); ?></p>
            <p style="font-weight: bold;">Wakil Kepala Kurikulum</p>
            <p class="name"><?= esc($guru['nama_lengkap']); ?></p>
            <p class="nip">NIP. <?= esc($guru['nip'] ?? '__________________'); ?></p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
