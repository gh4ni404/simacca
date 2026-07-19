<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tidak Tersedia - SIMACCA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        @media print {
            body { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-error-container" data-print-error="true" style="display:none;"></div>
    <div class="max-w-md mx-auto text-center p-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="w-16 h-16 mx-auto mb-5 rounded-full bg-amber-100 flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-2xl text-amber-500"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-800 mb-2"><?= esc($title ?? 'Data Tidak Tersedia') ?></h2>
            <p class="text-sm text-gray-500 leading-relaxed mb-6"><?= esc($message ?? 'Belum ada catatan kegiatan yang dapat dicetak.') ?></p>
            <?php if (!empty($details)): ?>
            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Detail:</p>
                <ul class="text-sm text-gray-600 space-y-1.5">
                    <?php foreach ($details as $detail): ?>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-gray-400 text-xs mt-1 flex-shrink-0"></i>
                        <span><?= esc($detail) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <p class="text-xs text-gray-400">Silakan kembali ke halaman jurnal PKL.</p>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
