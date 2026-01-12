<?= $this->extend('templates/auth_layout') ?>

<?= $this->section('title') ?>
Lupa Password
<?= $this->endSection() ?>

<?= $this->section('header') ?>
<div class="flex justify-center">
    <i class="fas fa-key text-5xl text-indigo-600"></i>
</div>
<h2 class="mt-6 text-3xl font-extrabold text-gray-900">
    Lupa Password?
</h2>
<p class="mt-2 text-sm text-gray-600">
    Masukkan email Anda untuk mereset password
</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= base_url('forgot-password/process'); ?>" method="POST" class="space-y-6">
    <?= csrf_field(); ?>

    <!-- Email Input -->
    <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
            Email
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
            </div>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= old('email'); ?>"
                placeholder="email@example.com"
                required
                class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
        </div>
        <?php if (isset($validation) && $validation->hasError('email')): ?>
            <p class="mt-1 text-sm text-red-600">
                <i class="fas fa-exclamation-circle mr-1"></i><?= $validation->getError('email') ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Submit Button -->
    <div>
        <button 
            type="submit"
            class="w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
        >
            <i class="fas fa-paper-plane mr-2"></i>
            Kirim Link Reset
        </button>
    </div>

    <!-- Back to Login -->
    <div class="text-center">
        <a href="<?= base_url('login'); ?>" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Login
        </a>
    </div>
</form>
<?= $this->endSection() ?>