<?php
/**
 * Example View - Layout Usage
 * 
 * This file demonstrates how to use the new desktop/mobile layouts
 */
?>

<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>

<!-- Page Title -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Layout Example</h1>
    <p class="text-gray-600 mt-2">Demonstrasi penggunaan desktop dan mobile layout</p>
</div>

<!-- Device Info Card -->
<div class="card mb-6">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">Device Information</h2>
            <span class="badge badge-<?= is_mobile_device() ? 'green' : 'yellow' ?>">
                <?= ucfirst(get_device_type()) ?>
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-600">Device Type:</label>
                <p class="text-lg font-semibold"><?= ucfirst(get_device_type()) ?></p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Is Mobile:</label>
                <p class="text-lg font-semibold"><?= is_mobile_device() ? 'Yes' : 'No' ?></p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Is Tablet:</label>
                <p class="text-lg font-semibold"><?= is_tablet_device() ? 'Yes' : 'No' ?></p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Current Layout:</label>
                <p class="text-lg font-semibold">
                    <?php 
                    $pref = session()->get('layout_preference');
                    echo $pref ? basename($pref) : 'Auto-detect'; 
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Layout Switcher -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="text-xl font-semibold">Layout Switcher</h2>
    </div>
    <div class="card-body">
        <p class="mb-4 text-gray-600">Switch between desktop and mobile layouts manually:</p>
        <div class="flex flex-wrap gap-3">
            <a href="<?= base_url('layout/desktop') ?>" class="btn btn-primary">
                <i class="fas fa-desktop"></i> Desktop Layout
            </a>
            <a href="<?= base_url('layout/mobile') ?>" class="btn btn-primary">
                <i class="fas fa-mobile-alt"></i> Mobile Layout
            </a>
            <a href="<?= base_url('layout/auto') ?>" class="btn btn-secondary">
                <i class="fas fa-sync"></i> Auto Detect
            </a>
        </div>
    </div>
</div>

<!-- Button Examples -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="text-xl font-semibold">Button Examples</h2>
    </div>
    <div class="card-body">
        <div class="flex flex-wrap gap-3">
            <button class="btn btn-primary">
                <i class="fas fa-save"></i> Primary Button
            </button>
            <button class="btn btn-secondary">
                <i class="fas fa-times"></i> Secondary Button
            </button>
            <button class="btn btn-danger">
                <i class="fas fa-trash"></i> Danger Button
            </button>
        </div>
    </div>
</div>

<!-- Badge Examples -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="text-xl font-semibold">Badge Examples</h2>
    </div>
    <div class="card-body">
        <div class="flex flex-wrap gap-3">
            <span class="badge badge-green">Hadir</span>
            <span class="badge badge-yellow">Izin</span>
            <span class="badge badge-red">Alpa</span>
        </div>
    </div>
</div>

<!-- Grid Example -->
<div class="card mb-6">
    <div class="card-header">
        <h2 class="text-xl font-semibold">Grid Layout Example</h2>
    </div>
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php for ($i = 1; $i <= 6; $i++): ?>
            <div class="bg-gray-100 p-4 rounded-lg text-center">
                <div class="text-3xl text-indigo-600 mb-2">
                    <i class="fas fa-box"></i>
                </div>
                <h3 class="font-semibold">Card <?= $i ?></h3>
                <p class="text-sm text-gray-600">Sample content</p>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Implementation Tips -->
<div class="card">
    <div class="card-header">
        <h2 class="text-xl font-semibold">Implementation Tips</h2>
    </div>
    <div class="card-body">
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <strong>Auto-detection:</strong> Use <code class="bg-gray-100 px-2 py-1 rounded">get_device_layout()</code> in your views
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <strong>Consistent Classes:</strong> Use btn, card, badge classes for consistency
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <strong>Test Both:</strong> Always test in both desktop and mobile views
                </div>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div>
                    <strong>Mobile-First:</strong> Design content that works well on small screens
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('actions') ?>
<div class="flex gap-2">
    <a href="<?= base_url('layout/device-info') ?>" class="btn btn-secondary" target="_blank">
        <i class="fas fa-info-circle"></i> Device Info (JSON)
    </a>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    console.log('Device Type:', '<?= get_device_type() ?>');
    console.log('Is Mobile:', <?= is_mobile_device() ? 'true' : 'false' ?>);
    console.log('Is Tablet:', <?= is_tablet_device() ? 'true' : 'false' ?>);
</script>
<?= $this->endSection() ?>
