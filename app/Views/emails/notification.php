<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>

<h2 style="color: #333; margin-top: 0;"><?= esc($title) ?></h2>

<div style="line-height: 1.8;">
    <?= $content ?>
</div>

<hr>

<p style="font-size: 13px; color: #6c757d;">
    Email notifikasi ini dikirim secara otomatis oleh sistem SIMACCA.
</p>

<?= $this->endSection() ?>
