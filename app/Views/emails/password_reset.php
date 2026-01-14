<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;">Reset Password Anda</h2>

<p>Halo, <strong><?= esc($username) ?></strong>!</p>

<p>Kami menerima permintaan untuk mereset password akun SIMACCA Anda. Jika Anda tidak melakukan permintaan ini, abaikan email ini.</p>

<p>Untuk mereset password Anda, klik tombol di bawah ini:</p>

<div style="text-align: center; margin: 25px 0;">
    <a href="<?= esc($resetUrl) ?>" class="button">Reset Password</a>
</div>

<p>Atau salin dan tempel URL berikut ke browser Anda:</p>
<p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px;">
    <?= esc($resetUrl) ?>
</p>

<div class="alert-box">
    <strong>⚠️ Penting:</strong>
    <ul style="margin: 10px 0 0; padding-left: 20px;">
        <li>Link ini hanya berlaku selama <strong>1 jam</strong></li>
        <li>Link akan expire pada: <strong><?= esc($validUntil) ?> WIB</strong></li>
        <li>Link hanya bisa digunakan <strong>satu kali</strong></li>
    </ul>
</div>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Jika Anda tidak melakukan permintaan reset password, segera hubungi administrator untuk mengamankan akun Anda.
</p>

<?= $this->endSection() ?>
