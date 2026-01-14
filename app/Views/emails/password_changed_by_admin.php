<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;">Password Anda Telah Diubah oleh Admin 🔐</h2>

<p>Halo, <strong><?= esc($fullName) ?></strong>!</p>

<p>Kami ingin memberitahukan bahwa password akun SIMACCA Anda telah diubah oleh administrator sistem.</p>

<div class="info-box">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 5px 0; font-weight: 600; width: 180px;">Username</td>
            <td style="padding: 5px 0;">: <?= esc($username) ?></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Password Baru</td>
            <td style="padding: 5px 0;">: <code style="background: #f8f9fa; padding: 4px 12px; border-radius: 4px; font-size: 16px; font-weight: bold; color: #e74c3c;"><?= esc($newPassword) ?></code></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Waktu Perubahan</td>
            <td style="padding: 5px 0;">: <?= esc($changeTime) ?> WIB</td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Diubah oleh</td>
            <td style="padding: 5px 0;">: Administrator</td>
        </tr>
    </table>
</div>

<div style="text-align: center; margin: 25px 0;">
    <a href="<?= base_url('login') ?>" class="button">Login Sekarang</a>
</div>

<div class="alert-box">
    <strong>⚠️ Penting untuk Keamanan Akun:</strong>
    <ul style="margin: 10px 0 0; padding-left: 20px;">
        <li>Catat password baru Anda di tempat yang aman</li>
        <li>Segera ganti password setelah login pertama kali</li>
        <li>Gunakan password yang kuat dan mudah diingat</li>
        <li>Jangan bagikan password kepada siapapun</li>
    </ul>
</div>

<hr>

<h3 style="color: #333;">Cara Login dengan Password Baru:</h3>
<ol style="line-height: 1.8;">
    <li>Buka halaman login SIMACCA: <a href="<?= base_url('login') ?>"><?= base_url('login') ?></a></li>
    <li>Masukkan username: <strong><?= esc($username) ?></strong></li>
    <li>Masukkan password baru yang tercantum di atas</li>
    <li>Klik "Login"</li>
    <li>Setelah berhasil login, sebaiknya segera ganti password Anda</li>
</ol>

<hr>

<h3 style="color: #333;">Tips Mengganti Password:</h3>
<ul style="line-height: 1.8;">
    <li>🔐 Gunakan kombinasi huruf besar, huruf kecil, dan angka</li>
    <li>📏 Minimal 6 karakter (lebih panjang lebih baik)</li>
    <li>🚫 Jangan gunakan tanggal lahir atau nama yang mudah ditebak</li>
    <li>💡 Contoh password kuat: <code>BudiGuru2024!</code></li>
</ul>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Jika Anda tidak meminta perubahan password ini atau mengalami masalah login, segera hubungi administrator sistem atau guru TI di sekolah.
</p>

<?= $this->endSection() ?>
