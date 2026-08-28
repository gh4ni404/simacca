<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;">Email Anda Telah Diubah 📧</h2>

<p>Halo, <strong><?= esc($fullName) ?></strong>!</p>

<p>Kami ingin memberitahukan bahwa email akun SIMACCA Anda telah berhasil diubah.</p>

<div class="info-box">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 5px 0; font-weight: 600; width: 150px;">Waktu Perubahan</td>
            <td style="padding: 5px 0;">: <?= esc($changeTime) ?> WITA</td>
        </tr>
        <?php if (isset($oldEmail)): ?>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Email Lama</td>
            <td style="padding: 5px 0;">: <?= esc($oldEmail) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">Email Baru</td>
            <td style="padding: 5px 0;">: <?= esc($newEmail) ?></td>
        </tr>
        <tr>
            <td style="padding: 5px 0; font-weight: 600;">IP Address</td>
            <td style="padding: 5px 0;">: <?= esc($ipAddress) ?></td>
        </tr>
    </table>
</div>

<?php if ($isOldEmail): ?>
<div class="alert-box">
    <strong>⚠️ Penting:</strong>
    <p style="margin: 10px 0 0;">
        Email ini dikirim ke alamat email lama Anda sebagai pemberitahuan keamanan. 
        Jika Anda tidak melakukan perubahan ini, segera hubungi administrator untuk mengamankan akun Anda.
    </p>
</div>
<?php else: ?>
<p>Email ini dikirim ke alamat email baru Anda sebagai konfirmasi bahwa perubahan telah berhasil.</p>
<?php endif; ?>

<hr>

<h3 style="color: #333;">Tips Keamanan Akun:</h3>
<ul style="line-height: 1.8;">
    <li>🔐 Gunakan password yang kuat dan unik</li>
    <li>🔄 Perbarui password secara berkala</li>
    <li>🚫 Jangan bagikan informasi login Anda</li>
    <li>👁️ Periksa aktivitas akun secara rutin</li>
    <li>📧 Pastikan email Anda aman dan hanya Anda yang bisa mengaksesnya</li>
</ul>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Jika Anda tidak melakukan perubahan ini atau mencurigai aktivitas yang tidak sah, segera hubungi administrator sistem di admin@smkn8bone.sch.id
</p>

<?= $this->endSection() ?>
