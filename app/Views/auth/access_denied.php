<?= $this->extend('templates/auth_layout') ?>

<?= $this->section('title') ?>
Akses Ditolak
<?= $this->endSection() ?>

<?= $this->section('header') ?>
<div class="flex justify-center">
    <i class="fas fa-ban text-5xl text-red-600"></i>
</div>
<h2 class="mt-6 text-3xl font-extrabold text-gray-900">
    Akses Ditolak
</h2>
<p class="mt-2 text-sm text-gray-600">
    Anda tidak memiliki izin untuk mengakses halaman ini
</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="text-center py-8">
    <p class="text-gray-600 mb-6">
        Halaman yang Anda coba akses memerlukan hak akses khusus. 
        Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
    </p>
    
    <div class="space-y-3">
        <a href="<?= base_url('/'); ?>" 
           class="block w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold transition-colors">
            <i class="fas fa-home mr-2"></i>
            Kembali ke Beranda
        </a>
        
        <a href="<?= base_url('logout'); ?>" 
           class="block w-full py-3 px-4 border-2 border-gray-300 hover:bg-gray-100 text-gray-700 rounded-lg font-semibold transition-colors">
            <i class="fas fa-sign-out-alt mr-2"></i>
            Logout
        </a>
    </div>
</div>
<?= $this->endSection() ?>

<!-- Keep the old code below this line as backup (will be removed after testing) -->
<!-- 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Akses Ditolak'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="h-full bg-gray-100">
    <div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 text-center">
            <div>
                <i class="fas fa-ban text-6xl text-red-500"></i>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900"><?= $title ?? 'Test'; ?></h2>
                <p class="mt-2 text-sm text-gray-600">
                    Anda tidak memiliki izin untuk mengakses halaman ini
                </p>
            </div>
            <?= view('components/alerts') ?>
            <div class="space-y-4">
                <div>
                    <a href="<?= base_url('dashboard'); ?>"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
                    </a>
                </div>
                <div>
                    <a href="<?= base_url('logout'); ?>"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
            <div class="text-sm text-gray-500">
                <p>Role Anda: <span class="font-semibold"><?= get_role_name(); ?></span></p>
                <p class="mt-1">Jika Anda merasa ini adalah kesalahan, silahkan hubungi administrator.</p>
            </div>
        </div>
    </div>
</body>

</html>