<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;">Test Email Configuration ✅</h2>

<p>Selamat! Konfigurasi email SIMACCA Anda berfungsi dengan baik.</p>

<div class="info-box">
    <p style="margin: 0;"><strong>Detail Pengiriman:</strong></p>
    <ul style="margin: 10px 0 0; padding-left: 20px;">
        <li>Waktu Pengiriman: <strong><?= esc($timestamp) ?> WIB</strong></li>
        <li>Status: <strong style="color: #28a745;">Berhasil</strong></li>
    </ul>
</div>

<p>Sistem email Anda sudah siap digunakan untuk:</p>
<ul>
    <li>✉️ Reset Password</li>
    <li>✉️ Welcome Email untuk User Baru</li>
    <li>✉️ Notifikasi Sistem</li>
    <li>✉️ Laporan Otomatis</li>
</ul>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Ini adalah email test otomatis. Anda dapat mengabaikan email ini.
</p>

<?= $this->endSection() ?>
