<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style id="print-dynamic-style">
        @page {
            size: A4 landscape;
            margin: 10mm 12mm;
        }
    </style>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000;
            background: #f1f5f9;
            padding: 0;
            margin: 0;
        }

        /* Top Toolbar Controls */
        .print-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #1e293b;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 13px;
        }

        .toolbar-left, .toolbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toolbar-divider {
            width: 1px;
            height: 24px;
            background: #475569;
        }

        .tool-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tool-group label {
            font-size: 12px;
            color: #cbd5e1;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-group-toggle {
            display: inline-flex;
            background: #334155;
            border-radius: 8px;
            padding: 2px;
        }

        .btn-toggle {
            background: transparent;
            color: #cbd5e1;
            border: none;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-toggle:hover {
            color: #fff;
        }

        .btn-toggle.active {
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 2px 6px rgba(79, 70, 229, 0.4);
        }

        .form-select-sm {
            background: #334155;
            color: #fff;
            border: 1px solid #475569;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            outline: none;
        }

        .toggle-checkbox-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            color: #e2e8f0;
            font-size: 12px;
            user-select: none;
        }

        .toggle-checkbox-label input {
            cursor: pointer;
            accent-color: #4f46e5;
        }

        .btn-tool {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
            transition: all 0.15s;
        }

        .btn-back {
            background: #475569;
            color: #fff;
        }

        .btn-back:hover {
            background: #64748b;
        }

        .btn-print-main {
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 3px 10px rgba(79, 70, 229, 0.4);
        }

        .btn-print-main:hover {
            background: #4338ca;
        }

        /* Sheet / Paper Presentation */
        .sheet-container {
            padding: 80px 20px 40px 20px;
            display: flex;
            justify-content: center;
        }

        .page-sheet {
            background: #fff;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            padding: 15mm 15mm;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .page-sheet.landscape {
            width: 297mm;
            min-height: 210mm;
        }

        .page-sheet.portrait {
            width: 210mm;
            min-height: 297mm;
        }

        /* Header Kop Surat */
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 3px double #000;
            padding-bottom: 5px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 10px;
        }

        .logo {
            width: 55px;
            height: 55px;
            flex-shrink: 0;
        }

        .logo img {
            width: 100%;
            height: auto;
            object-fit: contain;
        }

        .header-text {
            text-align: center;
            flex: 1;
        }

        .header-text h3 {
            font-size: 11pt;
            font-weight: bold;
            line-height: 1.15;
            text-transform: uppercase;
        }

        .header-text h2 {
            font-size: 13pt;
            font-weight: bold;
            line-height: 1.2;
            margin: 1px 0;
        }

        .header-text p {
            font-size: 8.5pt;
            line-height: 1.2;
        }

        /* Document Title */
        .document-title {
            text-align: center;
            margin: 8px 0 12px 0;
        }

        .document-title h2 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }

        .document-title p {
            font-size: 9.5pt;
            color: #222;
            margin-top: 2px;
        }

        /* Layout Grid for Landscape vs Portrait */
        .journal-layout {
            display: flex;
            gap: 15px;
        }

        .landscape .journal-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .portrait .journal-layout {
            display: block;
        }

        .portrait .journal-col {
            width: 100%;
            margin-bottom: 10px;
        }

        /* Meta / Info Section */
        .section-box {
            margin-bottom: 10px;
        }

        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            background: #f1f5f9;
            padding: 3px 8px;
            border-left: 3px solid #334155;
            margin-bottom: 5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 9.5pt;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .info-table .label {
            width: 140px;
            font-weight: bold;
        }

        .info-table .colon {
            width: 12px;
            text-align: center;
        }

        /* Content Box */
        .content-box {
            border: 1px solid #000;
            padding: 8px 10px;
            min-height: 55px;
            font-size: 9.5pt;
            line-height: 1.4;
            margin-bottom: 10px;
            background: #fafafa;
        }

        /* Photo Box */
        .photo-wrapper {
            text-align: center;
            margin: 6px 0 10px 0;
            border: 1px dashed #cbd5e1;
            padding: 6px;
            border-radius: 4px;
            background: #f8fafc;
        }

        .photo-img {
            max-width: 100%;
            max-height: 160px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #94a3b8;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .hide-photo-box #photo-section-box {
            display: none !important;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 48%;
            font-size: 9.5pt;
        }

        .signature-box .title {
            margin-bottom: 50px;
            line-height: 1.25;
        }

        .signature-box .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-box .nip {
            margin-top: 2px;
            font-size: 9pt;
        }

        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .sheet-container {
                padding: 0 !important;
                display: block !important;
            }
            .page-sheet {
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
            .content-box {
                background: transparent !important;
            }
            .section-title {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .photo-wrapper {
                background: transparent !important;
                border: 1px solid #ccc !important;
            }
        }
    </style>
</head>
<body>
    <!-- Top Interactive Toolbar -->
    <div class="print-toolbar no-print">
        <div class="toolbar-left">
            <a href="<?= base_url('guru/jurnal-piket/detail/' . $jurnal['id']) ?>" class="btn-tool btn-back">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div class="toolbar-divider"></div>
            
            <!-- Orientation Toggle -->
            <div class="tool-group">
                <label><i class="fas fa-compass"></i> Orientasi:</label>
                <div class="btn-group-toggle">
                    <button type="button" id="btn-landscape" class="btn-toggle active" onclick="setOrientation('landscape')">
                        <i class="fas fa-image"></i> Landscape
                    </button>
                    <button type="button" id="btn-portrait" class="btn-toggle" onclick="setOrientation('portrait')">
                        <i class="fas fa-file-alt"></i> Portrait
                    </button>
                </div>
            </div>

            <!-- Paper Size Selector -->
            <div class="tool-group">
                <label><i class="fas fa-scroll"></i> Kertas:</label>
                <select id="paper-size-select" class="form-select-sm" onchange="setPaperSize(this.value)">
                    <option value="A4" selected>A4 (210 x 297 mm)</option>
                    <option value="legal">F4 / Folio (215 x 330 mm)</option>
                    <option value="letter">Letter</option>
                </select>
            </div>

            <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                <div class="toolbar-divider"></div>
                <!-- Toggle Foto -->
                <div class="tool-group">
                    <label class="toggle-checkbox-label">
                        <input type="checkbox" id="toggle-photo" checked onchange="togglePhotos(this.checked)">
                        <span>Tampilkan Foto</span>
                    </label>
                </div>
            <?php endif; ?>
        </div>

        <div class="toolbar-right">
            <button onclick="window.print()" class="btn-tool btn-print-main">
                <i class="fas fa-print"></i> Cetak Lembar Jurnal (Ctrl+P)
            </button>
        </div>
    </div>

    <div class="sheet-container">
        <div id="page-sheet" class="page-sheet landscape">
            <!-- Header Kop Surat -->
            <div class="header">
                <div class="header-content">
                    <div class="logo">
                        <img src="<?= base_url('assets/images/provinsi.png') ?>" alt="Logo Provinsi" onerror="this.style.display='none'">
                    </div>
                    <div class="header-text">
                        <h3>PEMERINTAH PROVINSI SULAWESI SELATAN</h3>
                        <h3>DINAS PENDIDIKAN</h3>
                        <h3>CABANG DINAS PENDIDIKAN WILAYAH III</h3>
                        <h2><?= esc(function_exists('get_nama_sekolah') ? get_nama_sekolah() : 'UPT SMKN 8 BONE') ?></h2>
                        <p><em>Alamat: Jln. Poros Bone – Sengkang Welado Kec. Ajangale Kode Pos 92755</em></p>
                        <p><em>Email: smkn8bone@gmail.com &bull; Website: smkn8bone.sch.id</em></p>
                    </div>
                    <div class="logo">
                        <img src="<?= base_url('assets/images/sekolah.png') ?>" alt="Logo Sekolah" onerror="this.style.display='none'">
                    </div>
                </div>
            </div>

            <!-- Document Title -->
            <div class="document-title">
                <h2>LEMBAR LAPORAN JURNAL PIKET HARIAN</h2>
                <p>Tahun Ajaran: <?= esc($tahunAjaran) ?> &bull; Semester: <?= esc(ucfirst($jurnal['semester'] ?? '')) ?></p>
            </div>

            <!-- Journal Layout Structure (2 columns in Landscape, 1 column in Portrait) -->
            <div class="journal-layout" id="journal-layout-container">
                <!-- Column 1 -->
                <div class="journal-col">
                    <!-- Identitas Pelaksanaan -->
                    <div class="section-box">
                        <div class="section-title">I. IDENTITAS GURU & PELAKSANAAN PIKET</div>
                        <table class="info-table">
                            <tr>
                                <td class="label">Nama Guru Piket</td>
                                <td class="colon">:</td>
                                <td><strong><?= esc($guru['nama_lengkap']) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="label">NIP</td>
                                <td class="colon">:</td>
                                <td><?= esc($guru['nip'] ?: '-') ?></td>
                            </tr>
                            <tr>
                                <td class="label">Hari / Tanggal Piket</td>
                                <td class="colon">:</td>
                                <td><strong><?= esc(date_to_indo($jurnal['tanggal'], true)) ?></strong></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Rincian Panduan Tugas -->
                    <?php if (!empty($jurnal['rincian_tugas'])): ?>
                        <div class="section-box">
                            <div class="section-title">II. RINCIAN PANDUAN TUGAS PIKET</div>
                            <div class="content-box">
                                <?= nl2br(esc($jurnal['rincian_tugas'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Uraian Pelaksanaan Kegiatan -->
                    <div class="section-box">
                        <div class="section-title"><?= !empty($jurnal['rincian_tugas']) ? 'III.' : 'II.' ?> URAIAN PELAKSANAAN KEGIATAN PIKET</div>
                        <div class="content-box">
                            <?= nl2br(esc($jurnal['deskripsi'])) ?>
                        </div>
                    </div>
                </div>

                <!-- Column 2 -->
                <div class="journal-col">
                    <!-- Catatan / Kejadian Khusus -->
                    <div class="section-box">
                        <div class="section-title"><?= !empty($jurnal['rincian_tugas']) ? 'IV.' : 'III.' ?> CATATAN KEJADIAN / SITUASI KHUSUS</div>
                        <div class="content-box">
                            <?= !empty($jurnal['catatan']) ? nl2br(esc($jurnal['catatan'])) : '<em>Tidak ada catatan khusus / situasi gerbang dan sekolah berjalan aman dan tertib.</em>' ?>
                        </div>
                    </div>

                    <!-- Foto Dokumentasi -->
                    <?php if (!empty($jurnal['foto_dokumentasi'])): ?>
                        <?php 
                        $fotos = explode(',', $jurnal['foto_dokumentasi']);
                        $validFotos = [];
                        foreach ($fotos as $f) {
                            $f = trim($f);
                            if (file_exists(WRITEPATH . 'uploads/jurnal_piket/' . $f)) {
                                $validFotos[] = $f;
                            }
                        }
                        if (!empty($validFotos)):
                        ?>
                            <div class="section-box" id="photo-section-box">
                                <div class="section-title"><?= !empty($jurnal['rincian_tugas']) ? 'V.' : 'IV.' ?> FOTO DOKUMENTASI KEGIATAN</div>
                                <div style="display: flex; flex-wrap: wrap; gap: 15px; justify-content: center; margin-top: 10px;">
                                    <?php foreach ($validFotos as $vf): ?>
                                        <div class="photo-wrapper" style="flex: 1 1 200px; max-width: 240px; border: 1px solid #ddd; padding: 4px; border-radius: 8px;">
                                            <img src="<?= base_url('files/jurnal-piket/' . $vf) ?>" alt="Foto Dokumentasi Piket" class="photo-img" style="width: 100%; height: 160px; object-fit: cover; border-radius: 6px;">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Signature Section -->
                    <div class="signature-section">
                        <div class="signature-box">
                            <div class="title">
                                Mengetahui,<br>
                                Kepala <?= esc(function_exists('get_nama_sekolah') ? get_nama_sekolah() : 'UPT SMKN 8 Bone') ?>
                            </div>
                            <div class="name"><?= function_exists('get_kepala_sekolah_nama') && get_kepala_sekolah_nama() ? esc(get_kepala_sekolah_nama()) : '_______________________' ?></div>
                            <div class="nip">NIP. <?= function_exists('get_kepala_sekolah_nip') && get_kepala_sekolah_nip() ? esc(get_kepala_sekolah_nip()) : '........................................' ?></div>
                        </div>

                        <div class="signature-box">
                            <div class="title">
                                Welado, <?= format_tanggal_indo($jurnal['tanggal']) ?><br>
                                Guru Piket Pelaksana
                            </div>
                            <div class="name"><?= esc($guru['nama_lengkap']) ?></div>
                            <div class="nip">NIP. <?= esc($guru['nip'] ?: '........................................') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let currentOrientation = 'landscape';
    let currentPaperSize = 'A4';

    function updatePageStyles() {
        const dynamicStyle = document.getElementById('print-dynamic-style');
        dynamicStyle.innerHTML = `@page { size: ${currentPaperSize} ${currentOrientation}; margin: 10mm 12mm; }`;
        
        const sheet = document.getElementById('page-sheet');
        sheet.className = `page-sheet ${currentOrientation}`;
    }

    function setOrientation(orientation) {
        currentOrientation = orientation;
        document.getElementById('btn-landscape').classList.toggle('active', orientation === 'landscape');
        document.getElementById('btn-portrait').classList.toggle('active', orientation === 'portrait');
        updatePageStyles();
    }

    function setPaperSize(size) {
        currentPaperSize = size;
        updatePageStyles();
    }

    function togglePhotos(show) {
        const sheet = document.getElementById('page-sheet');
        if (show) {
            sheet.classList.remove('hide-photo-box');
        } else {
            sheet.classList.add('hide-photo-box');
        }
    }
    </script>
</body>
</html>
