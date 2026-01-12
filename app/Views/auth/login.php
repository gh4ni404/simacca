<?= $this->extend('templates/auth_layout') ?>

<?= $this->section('title') ?>
Login
<?= $this->endSection() ?>

<?= $this->section('header') ?>
<div class="flex justify-center">
    <i class="fas fa-graduation-cap text-5xl text-indigo-600"></i>
</div>
<h2 class="mt-6 text-3xl font-extrabold text-gray-900">
    Login SIMACCA
</h2>
<p class="mt-2 text-sm text-gray-600">
    Silahkan login untuk melanjutkan
</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= base_url('login/process'); ?>" method="POST" class="space-y-6">
    <?= csrf_field(); ?>
    
    <div class="space-y-4">
        <!-- Username -->
        <div>
            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                Username
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                </div>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?= old('username'); ?>"
                    placeholder="Masukkan username"
                    required
                    class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            <?php if (isset($validation) && $validation->hasError('username')): ?>
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i><?= $validation->getError('username') ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password"
                    required
                    class="pl-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            <?php if (isset($validation) && $validation->hasError('password')): ?>
                <p class="mt-1 text-sm text-red-600">
                    <i class="fas fa-exclamation-circle mr-1"></i><?= $validation->getError('password') ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="flex items-center justify-between">
        <div class="flex items-center">
            <input 
                type="checkbox" 
                id="remember-me" 
                name="remember-me"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            >
            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                Ingat Saya
            </label>
        </div>
        <div class="text-sm">
            <a href="<?= base_url('forgot-password'); ?>" class="font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                Lupa Password?
            </a>
        </div>
    </div>

    <!-- Submit Button -->
    <div>
        <button 
            type="submit"
            class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
        >
            <i class="fas fa-sign-in-alt mr-2"></i>
            Login
        </button>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('footer') ?>
<!-- Optional: Add demo credentials or other footer content -->
<?= $this->endSection() ?>