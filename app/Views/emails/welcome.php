<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;">Selamat Datang di SIMACCA! 🎉</h2>

<p>Halo, <strong><?= esc($username) ?></strong>!</p>

<p>Akun SIMACCA Anda telah berhasil dibuat. Berikut adalah detail akun Anda:</p>

<div class="info-box">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 5px 0; font-weight: 600; width: 150px;">Username</td>
            <td style="padding: 5px 0;">: <?= esc($username) ?></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Password Sementara</td>
            <td style="padding: 5px 0;">: <code style="background: #f8f9fa; padding: 2px 8px; border-radius: 3px;"><?= esc($temporaryPassword) ?></code></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Role</td>
            <td style="padding: 5px 0;">: <?= ucfirst(str_replace('_', ' ', esc($role))) ?></td>
        </tr>
    </table>
</div>

<p>Untuk login ke sistem, klik tombol di bawah ini:</p>

<div style="text-align: center; margin: 25px 0;">
    <a href="<?= esc($loginUrl) ?>" class="button">Login Sekarang</a>
</div>

<div class="alert-box">
    <strong>🔐 Keamanan Akun:</strong>
    <ul style="margin: 10px 0 0; padding-left: 20px;">
        <li>Segera ganti password Anda setelah login pertama kali</li>
        <li>Jangan bagikan password Anda kepada siapapun</li>
        <li>Gunakan password yang kuat dan unik</li>
    </ul>
</div>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Jika Anda mengalami kesulitan untuk login, silakan hubungi administrator sistem.
</p>

<?= $this->endSection() ?>
