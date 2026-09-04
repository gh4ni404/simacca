<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Absensi Shalat - <?= esc($guru['nama_lengkap']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 4px double #000;
            padding-bottom: 5px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 3px;
            padding: 0 15px;
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

        .header-text h3 {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .header-text h2 {
            font-size: 13pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .header-text p {
            font-size: 8.5pt;
            margin: 0.5px 0;
        }

        /* Title Section */
        .title {
            text-align: center;
            margin: 15px 0;
        }

        .title h3 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .title p {
            font-size: 10pt;
            font-style: italic;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .info-table td:first-child {
            width: 140px;
            font-weight: bold;
        }

        .info-table td:nth-child(2) {
            width: 10px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-inside: auto;
        }

        .data-table thead {
            background-color: #e0e0e0;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: center;
            vertical-align: middle;
        }

        .data-table th {
            font-weight: bold;
            font-size: 9.5pt;
        }

        .data-table td {
            font-size: 9pt;
        }

        .data-table td.left {
            text-align: left;
        }

        .section-heading {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-box p {
            margin: 3px 0;
            font-size: 10pt;
        }

        .signature-space {
            height: 50px;
        }

        .signature-name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 180px;
            padding-bottom: 2px;
        }

        /* Print Controls */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #1d4ed8;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Print Button -->
    <button onclick="window.print()" class="print-button no-print">
        <i class="fas fa-print"></i> Cetak Laporan
    </button>

    <div class="container">
        <!-- Kop Surat -->
        <div class="header">
            <div class="header-content">
                <div class="logo">
                    <img src="<?= base_url('assets/images/provinsi.png') ?>" alt="Logo Provinsi" height="60px" onError="this.style.display='none'">
                </div>
                <div class="header-text">
                    <h3>PEMERINTAH PROPINSI SULAWESI SELATAN</h3>
                    <h3>DINAS PENDIDIKAN</h3>
                    <h2><?= esc(function_exists('get_nama_sekolah') ? get_nama_sekolah() : 'UPT SMKN 8 BONE') ?></h2>
                    <p><em>Alamat : Jln. Poros Bone – Sengkang Welado Kec. Ajangale Kode Pos 92755</em></p>
                    <p><em>Email : smkn8bone@gmail.com</em></p>
                </div>
                <div class="logo">
                    <img src="<?= base_url('assets/images/sekolah.png') ?>" alt="Logo Sekolah" height="60px" onError="this.style.display='none'">
                </div>
            </div>
        </div>

        <!-- Judul -->
        <div class="title">
            <h3>LAPORAN REKAPITULASI ABSENSI SHALAT</h3>
            <p>Periode: <?= date('d F Y', strtotime($from)) ?> s/d <?= date('d F Y', strtotime($to)) ?></p>
        </div>

        <!-- Meta Info -->
        <div class="info-table">
            <table>
                <tr>
                    <td>Nama Guru</td>
                    <td>:</td>
                    <td><?= esc($guru['nama_lengkap']) ?> (NIP: <?= esc($guru['nip'] ?: '-') ?>)</td>
                </tr>
                <?php if ($namaKelasSelected): ?>
                <tr>
                    <td>Filter Kelas</td>
                    <td>:</td>
                    <td><?= esc($namaKelasSelected) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td>Kategori Laporan</td>
                    <td>:</td>
                    <td>
                        <?php 
                        if ($type === 'personal') echo 'Presensi Shalat Saya (Kehadiran Pribadi)';
                        elseif ($type === 'piket_siswa') echo 'Laporan Piket - Kehadiran Siswa';
                        elseif ($type === 'piket_guru') echo 'Laporan Piket - Kehadiran Guru Lain';
                        else echo 'Laporan Piket Shalat Lengkap';
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Tanggal Cetak</td>
                    <td>:</td>
                    <td><?= date('d F Y, H:i') ?> WITA</td>
                </tr>
            </table>
        </div>

        <!-- Section 1: Presensi Pribadi -->
        <?php if (in_array($type, ['personal', 'semua'])): ?>
            <div class="section-heading"><i class="fas fa-user-check"></i> Riwayat Kehadiran Shalat Pribadi</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">No</th>
                        <th style="width: 30%;">Waktu Sesi</th>
                        <th style="width: 42%;">Guru Piket Penanggung Jawab</th>
                        <th style="width: 20%;">Waktu Scan / Absen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rekapPersonal)): ?>
                        <?php foreach ($rekapPersonal as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['waktu_sesi'])) ?></td>
                            <td class="left"><?= esc($row['nama_guru_piket'] ?: 'Guru Piket') ?></td>
                            <td><?= date('H:i:s', strtotime($row['waktu_absen'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #666;">Belum ada record presensi shalat pribadi pada periode ini.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Section 2: Rekap Piket - Siswa -->
        <?php if (in_array($type, ['piket_siswa', 'piket_semua', 'semua'])): ?>
            <div class="section-heading"><i class="fas fa-user-graduate"></i> Rekapan Hasil Piket Shalat - Kehadiran Siswa</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th style="width: 16%;">NIS</th>
                        <th style="width: 34%;">Nama Siswa</th>
                        <th style="width: 14%;">Kelas</th>
                        <th style="width: 15%;">Waktu Sesi</th>
                        <th style="width: 15%;">Waktu Absen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rekapPiketSiswa)): ?>
                        <?php foreach ($rekapPiketSiswa as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['nis'] ?: '-') ?></td>
                            <td class="left"><?= esc($row['nama_lengkap']) ?></td>
                            <td><?= esc($row['unit'] ?: '-') ?></td>
                            <td><?= date('d/m H:i', strtotime($row['waktu_sesi'])) ?></td>
                            <td><?= date('H:i:s', strtotime($row['waktu_absen'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: #666;">Belum ada data siswa yang hadir pada sesi piket Anda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Section 3: Rekap Piket - Guru Lain -->
        <?php if (in_array($type, ['piket_guru', 'piket_semua', 'semua'])): ?>
            <div class="section-heading"><i class="fas fa-chalkboard-teacher"></i> Rekapan Hasil Piket Shalat - Kehadiran Guru Lain</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 6%;">No</th>
                        <th style="width: 20%;">NIP</th>
                        <th style="width: 44%;">Nama Guru</th>
                        <th style="width: 15%;">Waktu Sesi</th>
                        <th style="width: 15%;">Waktu Absen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rekapPiketGuru)): ?>
                        <?php foreach ($rekapPiketGuru as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($row['identifier'] ?: '-') ?></td>
                            <td class="left"><?= esc($row['nama_lengkap']) ?></td>
                            <td><?= date('d/m H:i', strtotime($row['waktu_sesi'])) ?></td>
                            <td><?= date('H:i:s', strtotime($row['waktu_absen'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #666;">Belum ada data guru lain yang hadir pada sesi piket Anda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>Kepala <?= esc(function_exists('get_nama_sekolah') ? get_nama_sekolah() : 'UPT SMKN 8 Bone') ?></strong></p>
                <div class="signature-space"></div>
                <p class="signature-name"><?= function_exists('get_kepala_sekolah_nama') && get_kepala_sekolah_nama() ? esc(get_kepala_sekolah_nama()) : '(_______________________)' ?></p>
                <p>NIP: <?= function_exists('get_kepala_sekolah_nip') && get_kepala_sekolah_nip() ? esc(get_kepala_sekolah_nip()) : '___________________' ?></p>
            </div>
            <div class="signature-box">
                <p>Ajangale, <?= date('d F Y') ?></p>
                <p><strong>Guru / Guru Piket</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name"><?= esc($guru['nama_lengkap']) ?></p>
                <p>NIP: <?= esc($guru['nip'] ?: '___________________') ?></p>
            </div>
        </div>
    </div>

    <script>
        <?php if ($auto_print ?? false): ?>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 500);
        };
        <?php endif; ?>
    </script>
</body>
</html>
