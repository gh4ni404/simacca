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

        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #000;
            padding: 15mm;
        }

        @page {
            size: A4 portrait;
            margin: 10mm 8mm;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            .page-break {
                page-break-after: always;
            }
        }

        /* Header */
        /* .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        } */

        /* Header dengan Logo */
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 4px double #000;
            padding-bottom: 5px;
            position: relative;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 3px;
            padding: 0 15px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 9pt;
        }

        .header .period-box {
            display: inline-block;
            border: 2px solid #000;
            padding: 5px 15px;
            margin-top: 8px;
            font-size: 9pt;
        }

        .logo {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
        }

        .header-text {
            text-align: center;
            flex: 1;
            padding: 0 5px;
        }

        .header-text h1 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 1px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .header-text h2 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 1px;
            line-height: 1.1;
        }

        .header-text h3 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 2px;
            line-height: 1.1;
        }

        .header-text p {
            font-size: 8.5pt;
            margin: 0.5px 0;
            line-height: 1.2;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 20px;
        }

        .info-section table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-section td {
            padding: 3px 0;
        }

        .info-section td:first-child {
            width: 150px;
            font-weight: bold;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 8pt;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: left;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
            line-height: 1.2;
        }

        .data-table .center {
            text-align: center;
        }

        .data-table .right {
            text-align: right;
        }

        .data-table .left {
            text-align: left;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Highlight row merah untuk yang belum mengisi */
        .data-table .belum-isi {
            background-color: #ffebee !important;
        }

        .data-table .belum-isi td {
            color: #c62828;
        }

        .data-table .badge-belum {
            background-color: #ef5350;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .data-table .foto-cell {
            text-align: center;
            padding: 2px;
        }

        .data-table .foto-cell img {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .data-table .kegiatan-cell {
            font-size: 7pt;
            max-width: 100px;
            word-wrap: break-word;
        }

        /* Date group header */
        .date-group-header {
            background-color: #1a237e;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 6px;
            font-size: 9pt;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10pt;
            font-weight: bold;
        }

        .status-hadir {
            background-color: #d4edda;
            color: #155724;
        }

        .status-sakit {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-izin {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-alpa {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Summary Section */
        .summary-section {
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .summary-box {
            border: 2px solid #000;
            padding: 8px;
            background-color: #f9f9f9;
        }

        .summary-box h3 {
            font-size: 10pt;
            margin-bottom: 5px;
            text-align: center;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            text-align: center;
        }

        .summary-item {
            padding: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
        }

        .summary-item .label {
            font-size: 8pt;
            color: #666;
            margin-bottom: 2px;
        }

        .summary-item .value {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 20px;
            display: table;
            width: 100%;
            font-size: 8pt;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-box p {
            margin-bottom: 3px;
        }

        .signature-box .name {
            margin-top: 40px;
            font-weight: bold;
            text-decoration: underline;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14pt;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #4338ca;
        }

        /* Utility */
        .text-small {
            font-size: 10pt;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mb-10 {
            margin-bottom: 10px;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <!-- Print Button -->
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Cetak Laporan
    </button>

    <!-- Header dengan Logo -->
    <div class="header">
        <div class="header-content">
            <div class="logo">
                <img src="<?= base_url('assets/images/sekolah.png') ?>" alt="Logo Sekolah" height="64px" />
            </div>
            <div class="header-text">
                <h3>PEMERINTAH PROPINSI SULAWESI SELATAN</h1>
                    <h3>DINAS PENDIDIKAN</h2>
                        <h3>CABANG DINAS PENDIDIKAN WILAYAH III</h3>
                        <h2>UPT SMKN 8 BONE</h2>
                        <p><em>Alamat : Jln. Poros Bone – Sengkang Welado Kec. Ajangale Kode Pos 92755</em></p>
                        <p><em>Email : smkn8bone@gmail.com</em></p>
            </div>
            <div class="logo">
                <img src="<?= base_url('assets/images/provinsi.png') ?>" alt="Logo Provinsi" height="64px">
            </div>
        </div>
    </div>

    <!-- Header -->
    <div style="text-align: center;">
        <h1>Laporan Absensi Pembelajaran</h1>
        <?php
        // 1. Buat alat pengaturnya (formatter)
        $formatter = new IntlDateFormatter(
            'id_ID',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'Asia/Makassar',
            IntlDateFormatter::GREGORIAN,
            'EEEE, d MMMM y'
        );

        // 2. Gunakan alat tersebut untuk mencetak tanggal
        ?>
        <strong>Tanggal:</strong> <?= $formatter->format(strtotime($tanggal)); ?>
        <?php if ($kelasId): ?>
            <br><strong>Kelas:</strong> <?= esc($kelasList[$kelasId] ?? '-'); ?>
        <?php /**else:*/ ?>
            <!-- <br><strong>Semua Kelas</strong> -->
        <?php endif; ?>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-box">
            <h3>Ringkasan Kehadiran & Pengisian</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="label">Hadir</div>
                    <div class="value" style="color: #10b981;"><?= $totalStats['hadir']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Sakit</div>
                    <div class="value" style="color: #f59e0b;"><?= $totalStats['sakit']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Izin</div>
                    <div class="value" style="color: #3b82f6;"><?= $totalStats['izin']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Alpa</div>
                    <div class="value" style="color: #ef4444;"><?= $totalStats['alpa']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Total Absensi</div>
                    <div class="value"><?= $totalStats['total']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Sudah Isi</div>
                    <div class="value" style="color: #10b981;"><?= $totalStats['jadwal_sudah_isi']; ?></div>
                </div>
                <div class="summary-item">
                    <div class="label">Belum Isi</div>
                    <div class="value" style="color: #ef4444;"><?= $totalStats['jadwal_belum_isi']; ?></div>
                </div>
            </div>
            <p class="mt-20 text-small" style="text-align: center;">
                <strong>Kehadiran: <?= $totalStats['percentage']; ?>% | Pengisian: <?= $totalStats['percentage_isi']; ?>% (<?= $totalStats['jadwal_sudah_isi']; ?>/<?= $totalStats['total_jadwal']; ?> jadwal)</strong>
            </p>
        </div>
    </div>

    <!-- Info -->
    <div class="info-section" style="font-size: 8pt; margin-bottom: 10px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;"><strong>Total Jadwal:</strong> <?= $totalStats['total_jadwal']; ?> | <strong>Hari Efektif:</strong> <?= count($laporanPerHari); ?></td>
                <td style="width: 50%; text-align: right;"><strong>Tanggal Cetak:</strong> <?= date('d/m/Y H:i'); ?> WIB</td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 55px;">Kelas</th>
                <th style="width: 80px;">Jam<br>Mengajar</th>
                <th style="width: 95px;">Nama Guru<br>Mapel</th>
                <th style="width: 85px;">Mata<br>Pelajaran</th>
                <th style="width: 110px;">Kegiatan<br>Pembelajaran</th>
                <!-- <th style="width: 85px;">Nama<br>Wali Kelas</th> -->
                <!-- <th style="width: 45px;">Nama<br>Wali Kelas</th> -->
                <!-- <th style="width: 15px;">Nama<br>Wali Kelas</th> -->
                <!-- <th style="width: 0px;">Nama<br>Wali Kelas</th> -->
                <th style="width: 28px;">Hadir</th>
                <th style="width: 28px;">Sakit</th>
                <th style="width: 28px;">Izin</th>
                <th style="width: 28px;">Alpa</th>
                <th style="width: 100px;">Foto</th>
                <th style="width: 85px;">Guru Piket<br>Pengganti</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($laporanPerHari)): ?>
                <?php $globalNo = 1; ?>
                <?php foreach ($laporanPerHari as $hariData): ?>
                    <!-- Date Group Header -->
                    <tr>
                        <td colspan="13" class="date-group-header">
                            <?= $hariData['hari']; ?>, <?= date('d F Y', strtotime($hariData['tanggal'])); ?>
                        </td>
                    </tr>

                    <?php foreach ($hariData['jadwal_list'] as $jadwal): ?>
                        <?php
                        $belumIsi = empty($jadwal['absensi_id']);
                        $rowClass = $belumIsi ? 'belum-isi' : '';
                        ?>
                        <tr class="<?= $rowClass; ?>">
                            <td class="center"><?= $globalNo++; ?></td>
                            <td class="center"><?= esc($jadwal['nama_kelas']); ?></td>
                            <td class="center"><?= substr($jadwal['jam_mulai'], 0, 5); ?> - <?= substr($jadwal['jam_selesai'], 0, 5); ?></td>
                            <td><?= esc($jadwal['nama_guru']); ?></td>
                            <td><?= esc($jadwal['nama_mapel']); ?></td>
                            <!-- hapus tag php nya jika ingin diaktifkan kembali -->
                            <?php /**<td><?= esc($jadwal['nama_wali_kelas'] ?? '-'); ?></td> */ ?>

                            <?php if ($belumIsi): ?>
                                <td class="kegiatan-cell center">-</td>
                                <td class="center" colspan="4">
                                    <span class="badge-belum">BELUM ISI</span>
                                </td>
                                <td class="foto-cell center">-</td>
                                <td class="center">-</td>
                            <?php else: ?>
                                <td class="kegiatan-cell left"><?= esc($jadwal['kegiatan_pembelajaran'] ?: '-'); ?></td>
                                <td class="center"><?= $jadwal['jumlah_hadir']; ?></td>
                                <td class="center"><?= $jadwal['jumlah_sakit']; ?></td>
                                <td class="center"><?= $jadwal['jumlah_izin']; ?></td>
                                <td class="center"><?= $jadwal['jumlah_alpa']; ?></td>
                                <td class="foto-cell">
                                    <?php if (!empty($jadwal['foto_dokumentasi'])): ?>
                                        <img src="<?= base_url('files/jurnal/' . $jadwal['foto_dokumentasi']); ?>" alt="Foto">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($jadwal['nama_guru_pengganti'] ?: '-'); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <!-- Total Row -->
                <tr style="background-color: #e5e7eb; font-weight: bold;">
                    <td colspan="6" class="right" style="padding-right: 5px;">TOTAL</td>
                    <td class="center"><?= $totalStats['hadir']; ?></td>
                    <td class="center"><?= $totalStats['sakit']; ?></td>
                    <td class="center"><?= $totalStats['izin']; ?></td>
                    <td class="center"><?= $totalStats['alpa']; ?></td>
                    <td colspan="3" class="center"><?= $totalStats['jadwal_sudah_isi']; ?>/<?= $totalStats['total_jadwal']; ?> terisi</td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="13" class="center" style="padding: 20px; color: #999;">
                        Tidak ada jadwal dalam periode ini
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Keterangan -->
    <div class="info-section" style="font-size: 8pt; margin-top: 8px;">
        <p><strong>Keterangan:</strong></p>
        <table style="margin-left: 15px; margin-top: 3px; font-size: 8pt;">
            <tr>
                <td style="width: 25px;">H</td>
                <td style="width: 8px;">:</td>
                <td style="width: 100px;">Hadir</td>
            </tr>
            <tr>
                <td style="width: 25px;">S</td>
                <td style="width: 8px;">:</td>
                <td style="width: 100px;">Sakit</td>
            </tr>
            <tr>
                <td style="width: 25px;">I</td>
                <td style="width: 8px;">:</td>
                <td style="width: 100px;">Izin</td>
            </tr>
            <tr>
                <td style="width: 25px;">A</td>
                <td style="width: 8px;">:</td>
                <td>Alpa (Tanpa Keterangan)</td>
            </tr>
        </table>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <p>Mengetahui,<br>Kepala Sekolah</p>
            <div class="name">
                (H. Muh. Amin, S.Pd)
            </div>
            <p style="margin-top: 3px; font-size: 7pt;">NIP. ...................................</p>
        </div>
        <div class="signature-box">
            <p><?= date('d F Y'); ?><br>Administrator</p>
            <div class="name">
                (.....................................)
            </div>
            <p style="margin-top: 3px; font-size: 7pt;">NIP. ...................................</p>
        </div>
    </div>

    <script>
        // Optional: Auto print when loaded (uncomment if needed)
        // window.onload = function() {
        //     window.print();
        // }
    </script>
</body>

</html>