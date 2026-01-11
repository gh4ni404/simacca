<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Jurnal KBM - <?= esc($jurnal['nama_guru']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 10px;
        }

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

        /* Title */
        .document-title {
            text-align: center;
            margin: 15px 0 10px 0;
        }

        .document-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
        }

        .document-title h3 {
            font-size: 12pt;
            font-weight: bold;
        }

        /* Student Info */
        .student-info {
            margin: 10px 0;
            padding-left: 40px;
        }

        .student-info table {
            border: none;
            margin-bottom: 0;
        }

        .student-info td {
            padding: 2px 8px;
            border: none;
        }

        .student-info .label {
            width: 150px;
            font-weight: normal;
        }

        .student-info .colon {
            width: 15px;
        }

        .student-info .value {
            font-weight: normal;
        }

        /* Table */
        .jurnal-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .jurnal-table th,
        .jurnal-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }

        .jurnal-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .jurnal-table .col-no {
            width: 4%;
            text-align: center;
        }

        .jurnal-table .col-tanggal {
            width: 12%;
        }

        .jurnal-table .col-materi {
            width: 22%;
        }

        .jurnal-table .col-kegiatan {
            width: 22%;
        }

        .jurnal-table .col-foto {
            width: 18%;
            text-align: center;
        }

        .jurnal-table .col-catatan {
            width: 22%;
        }

        .jurnal-table td.center {
            text-align: center;
        }

        .dokumentasi-img {
            max-width: 100%;
            height: auto;
            max-height: 120px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
        }

        .signature-box {
            text-align: center;
            width: 45%;
        }

        .signature-box p {
            margin-bottom: 60px;
            line-height: 1.3;
        }

        .signature-box .name {
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 180px;
            padding-bottom: 2px;
        }

        .signature-box .nip {
            margin-top: 3px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14pt;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #2563eb;
        }

        .print-button i {
            margin-right: 8px;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .print-button {
                display: none;
            }

            .container {
                padding: 0;
            }

            @page {
                margin: 15mm;
            }
        }

        .location-date {
            text-align: right;
            margin: 15px 40px 20px 0;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <button class="print-button" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak Dokumen
    </button>

    <div class="container">
        <!-- Header dengan Logo -->
        <div class="header">
            <div class="header-content">
                <div class="logo">
                    <img src="<?= base_url('/assets/images/sekolah.png') ?>" alt="Logo Sekolah" height="64px"/>
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
                    <img src="<?= base_url('/assets/images/provinsi.png') ?>" alt="Logo Provinsi" height="64px">
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="document-title">
            <h2>JURNAL KEGIATAN BELAJAR MENGAJAR</h2>
            <h3>SMKN 8 BONE</h3>
        </div>

        <!-- Student/Teacher Info -->
        <div class="student-info">
            <table>
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['nama_guru']) ?></td>
                </tr>
                <tr>
                    <td class="label">Mata Pelajaran</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['nama_mapel']) ?></td>
                </tr>
                <tr>
                    <td class="label">Kelas</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['nama_kelas']) ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="colon">:</td>
                    <td class="value"><?= date('d F Y', strtotime($jurnal['tanggal'])) ?></td>
                </tr>
                <tr>
                    <td class="label">Pertemuan Ke</td>
                    <td class="colon">:</td>
                    <td class="value"><?= esc($jurnal['pertemuan_ke']) ?></td>
                </tr>
            </table>
        </div>

        <!-- Jurnal Table -->
        <table class="jurnal-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-tanggal">Tanggal</th>
                    <th class="col-kegiatan">Materi Pembelajaran</th>
                    <th class="col-foto">Foto</th>
                    <th class="col-catatan">Catatan Khusus</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td><?= date('d/m/Y', strtotime($jurnal['tanggal'])) ?><br>
                        <small style="color: #666;">Jam: <?= !empty($jurnal['jam_mulai']) ? date('H:i', strtotime($jurnal['jam_mulai'])) : '-' ?> - <?= !empty($jurnal['jam_selesai']) ? date('H:i', strtotime($jurnal['jam_selesai'])) : '-' ?></small>
                    </td>
                    <td><?= nl2br(esc($jurnal['kegiatan_pembelajaran'])) ?></td>
                    <td class="center">
                        <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                            <img src="<?= base_url('files/jurnal/' . $jurnal['foto_dokumentasi']) ?>" 
                                 alt="Foto Kegiatan" 
                                 class="dokumentasi-img">
                        <?php else: ?>
                            <em style="color: #999; font-size: 9pt;">Tidak ada foto</em>
                        <?php endif; ?>
                    </td>
                    <td><?= !empty($jurnal['catatan_khusus']) ? nl2br(esc($jurnal['catatan_khusus'])) : '<em style="color: #999;">-</em>' ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Location and Date -->
        <div class="location-date">
            <p>Bone, <?= date('d F Y') ?></p>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui,<br>Kepala Sekolah</p>
                <p class="name">H. Muh. Amin, S.Pd</p>
                <p class="nip">NIP. ......................................</p>
            </div>
            <div class="signature-box">
                <p>Guru Mata Pelajaran</p>
                <p class="name"><?= esc($jurnal['nama_guru']) ?></p>
                <p class="nip">NIP. <?= esc($jurnal['nip']) ?></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Sistem KBM SMKN 8 BONE | Dicetak: <?= date('Y-m-d h:i:s A') ?></p>
        </div>
    </div>

    <script>
        // Auto print when loaded (optional)
        window.onload = function() {
            <?php if ($request->getGet('auto') == 'true'): ?>
            window.print();
            <?php endif; ?>
        }
    </script>
</body>
</html>
