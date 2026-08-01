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
<form id="loginForm" action="<?= base_url('login/process'); ?>" method="POST" class="space-y-6">
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
                    class="pl-10 pr-10 w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword()">
                    <i id="eyeIcon" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                </div>
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
            id="loginBtn"
            class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors"
        >
            <span id="loginText"><i class="fas fa-sign-in-alt mr-2"></i>Login</span>
        </button>
    </div>
</form>
<?= $this->endSection() ?>

<?= $this->section('footer') ?>
<!-- Optional: Add demo credentials or other footer content -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const loginForm = document.getElementById('loginForm');
const loginBtn = document.getElementById('loginBtn');
const loginText = document.getElementById('loginText');

loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (!username || !password) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Username dan password harus diisi!',
            timer: 2000,
            showConfirmButton: false,
        });
        return;
    }

    setLoadingState(true);

    try {
        const formData = new URLSearchParams(new FormData(loginForm));

        const response = await fetch('<?= base_url('login/process'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData.toString(),
        });

        const data = await response.json();

        if (data.csrf_token) {
            const csrfInput = loginForm.querySelector('input[name="csrf_test_name"]');
            if (csrfInput) csrfInput.value = data.csrf_token;
        }

        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Selamat datang, ' + data.username + '!',
                timer: 2000,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
            });

            setTimeout(() => {
                window.location.href = data.redirect_url;
            }, 2000);
        } else {
            setLoadingState(false);
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal',
                text: data.message || 'Username atau password salah',
                timer: 2500,
                showConfirmButton: false,
            });
        }
    } catch (error) {
        setLoadingState(false);
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.',
            timer: 2500,
            showConfirmButton: false,
        });
    }
});

function togglePassword() {
    const password = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (password.type === 'password') {
        password.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

function setLoadingState(loading) {
    if (loading) {
        loginBtn.disabled = true;
        loginBtn.classList.add('opacity-75', 'cursor-not-allowed');
        loginBtn.classList.remove('hover:bg-indigo-700');
        loginText.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
    } else {
        loginBtn.disabled = false;
        loginBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        loginBtn.classList.add('hover:bg-indigo-700');
        loginText.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Login';
    }
}
</script>
<?= $this->endSection() ?>
