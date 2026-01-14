<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;">Password Anda Berhasil Diubah 🔐</h2>

<p>Halo, <strong><?= esc($fullName) ?></strong>!</p>

<p>Kami ingin mengonfirmasi bahwa password akun SIMACCA Anda telah berhasil diubah.</p>

<div class="info-box">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 5px 0; font-weight: 600; width: 180px;">Username</td>
            <td style="padding: 5px 0;">: <?= esc($username) ?></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Password Baru Anda</td>
            <td style="padding: 5px 0;">: <code style="background: #f8f9fa; padding: 4px 12px; border-radius: 4px; font-size: 16px; font-weight: bold; color: #e74c3c;"><?= esc($newPassword) ?></code></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Waktu Perubahan</td>
            <td style="padding: 5px 0;">: <?= esc($changeTime) ?> WIB</td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">IP Address</td>
            <td style="padding: 5px 0;">: <?= esc($ipAddress) ?></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Diubah oleh</td>
            <td style="padding: 5px 0;">: Anda sendiri</td>
        </tr>
    </table>
</div>

<div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; border-radius: 4px;">
    <strong style="color: #155724;">✓ Password berhasil diubah!</strong>
    <p style="margin: 10px 0 0; color: #155724;">
        Password baru Anda sudah aktif dan dapat digunakan untuk login berikutnya.
    </p>
</div>

<div class="alert-box">
    <strong>⚠️ Tidak melakukan perubahan ini?</strong>
    <p style="margin: 10px 0 0;">
        Jika Anda tidak mengubah password, ada kemungkinan akun Anda telah diakses oleh orang lain. 
        Segera hubungi administrator di <strong>admin@smkn8bone.sch.id</strong> untuk mengamankan akun Anda.
    </p>
</div>

<hr>

<h3 style="color: #333;">Tips Menjaga Keamanan Akun:</h3>
<ul style="line-height: 1.8;">
    <li>🔐 Jangan bagikan password kepada siapapun</li>
    <li>🔄 Ganti password secara berkala (setiap 3-6 bulan)</li>
    <li>💪 Gunakan password yang kuat dan unik</li>
    <li>🚫 Jangan gunakan password yang sama untuk akun lain</li>
    <li>👁️ Selalu logout setelah selesai menggunakan SIMACCA</li>
    <li>📱 Jangan login di perangkat umum/komputer bersama</li>
</ul>

<hr>

<h3 style="color: #333;">Informasi Login:</h3>
<p>Anda dapat login ke SIMACCA menggunakan:</p>
<ul style="line-height: 1.8;">
    <li><strong>URL:</strong> <a href="<?= base_url('login') ?>"><?= base_url('login') ?></a></li>
    <li><strong>Username:</strong> <?= esc($username) ?></li>
    <li><strong>Password:</strong> <code style="background: #f8f9fa; padding: 2px 8px; border-radius: 3px; font-weight: bold; color: #e74c3c;"><?= esc($newPassword) ?></code></li>
</ul>

<div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 4px;">
    <strong style="color: #856404;">📝 Catat Password Anda:</strong>
    <p style="margin: 10px 0 0; color: #856404;">
        Simpan password <strong><?= esc($newPassword) ?></strong> di tempat yang aman. 
        Jika lupa, Anda perlu menggunakan fitur "Lupa Password?" atau hubungi administrator.
    </p>
</div>

<div style="text-align: center; margin: 25px 0;">
    <a href="<?= base_url('login') ?>" class="button">Login ke SIMACCA</a>
</div>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Email ini dikirim secara otomatis sebagai konfirmasi perubahan password. 
    Jika Anda mengalami masalah atau mencurigai aktivitas yang tidak sah pada akun Anda, 
    segera hubungi administrator sistem.
</p>

<?= $this->endSection() ?>
