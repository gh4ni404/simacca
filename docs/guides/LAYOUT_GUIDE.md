# Layout Guide - Desktop & Mobile Layouts

## Overview

SIMACCA sekarang memiliki layout terpisah untuk desktop dan mobile view untuk pengalaman pengguna yang lebih optimal.

## File Layout

### 1. Desktop Layout
- **File**: `app/Views/templates/desktop_layout.php`
- **Fitur**:
  - Horizontal navigation bar dengan dropdown menu
  - Layout lebar untuk desktop (max-width: 1400px)
  - User menu dengan foto profil di kanan atas
  - Footer dengan informasi tambahan
  - Optimized untuk layar besar

### 2. Mobile Layout
- **File**: `app/Views/templates/mobile_layout.php`
- **Fitur**:
  - Sticky top navigation bar
  - Slide-out menu dari kanan
  - Bottom navigation bar dengan icon (iOS/Android style)
  - Touch-optimized buttons (min-height: 44px)
  - Responsive cards dan spacing
  - Safe area support untuk iPhone X+

### 3. Original Layout (Deprecated)
- **File**: `app/Views/templates/main_layout.php`
- Masih tersedia untuk backward compatibility
- Menggunakan Tailwind responsive classes (sm:, md:, lg:)

## Helper Functions

### Device Detection

```php
// Cek apakah device mobile
if (is_mobile_device()) {
    // Kode untuk mobile
}

// Cek apakah device tablet
if (is_tablet_device()) {
    // Kode untuk tablet
}

// Get device type as string
$deviceType = get_device_type(); // Returns: 'mobile', 'tablet', or 'desktop'
```

### Layout Selection

```php
// Auto-detect device dan pilih layout yang sesuai
$layout = get_device_layout();

// Atau tentukan default layout
$layout = get_device_layout('templates/desktop_layout');

// Set manual layout preference (disimpan di session)
set_layout_preference('templates/mobile_layout');

// Clear preference (kembali ke auto-detection)
clear_layout_preference();
```

## Penggunaan di Views

### Metode 1: Auto-Detection (Recommended)

```php
<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h2>Judul Halaman</h2>
    </div>
    <div class="card-body">
        <p>Konten halaman...</p>
    </div>
</div>
<?= $this->endSection() ?>
```

### Metode 2: Explicit Layout

```php
// Untuk Desktop
<?= $this->extend('templates/desktop_layout') ?>

// Untuk Mobile
<?= $this->extend('templates/mobile_layout') ?>

// Untuk Original (responsive)
<?= $this->extend('templates/main_layout') ?>
```

### Metode 3: Conditional Layout

```php
<?php
// Di controller atau view
if (is_mobile_device()) {
    $layout = 'templates/mobile_layout';
} else {
    $layout = 'templates/desktop_layout';
}
?>
<?= $this->extend($layout) ?>
```

## Penggunaan di Controllers

### Option 1: Set di Constructor

```php
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    protected $layout;
    
    public function __construct()
    {
        // Auto-detect device
        $this->layout = get_device_layout();
    }
    
    public function index()
    {
        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'layout' => $this->layout
        ]);
    }
}
```

### Option 2: Pass to View

```php
public function index()
{
    $data = [
        'title' => 'Dashboard',
        'layout' => get_device_layout()
    ];
    
    return view('admin/dashboard', $data);
}
```

## Layout Sections

Semua layout mendukung sections yang sama:

### Content Section (Required)
```php
<?= $this->section('content') ?>
    <!-- Main content here -->
<?= $this->endSection() ?>
```

### Styles Section (Optional)
```php
<?= $this->section('styles') ?>
<style>
    /* Custom CSS */
</style>
<?= $this->endSection() ?>
```

### Scripts Section (Optional)
```php
<?= $this->section('scripts') ?>
<script>
    // Custom JavaScript
</script>
<?= $this->endSection() ?>
```

### Actions Section (Optional)
```php
<?= $this->section('actions') ?>
<a href="<?= base_url('admin/guru/tambah'); ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i> Tambah Guru
</a>
<?= $this->endSection() ?>
```

## CSS Classes

### Buttons
```html
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-danger">Danger</button>
```

### Badges
```html
<span class="badge badge-green">Hadir</span>
<span class="badge badge-yellow">Izin</span>
<span class="badge badge-red">Alpa</span>
```

### Cards
```html
<div class="card">
    <div class="card-header">Header</div>
    <div class="card-body">Content</div>
</div>
```

## Mobile Layout Features

### Bottom Navigation
Bottom navigation otomatis muncul di mobile layout dengan menu sesuai role:

- **Admin**: Beranda, Guru, Siswa, Laporan
- **Guru**: Beranda, Absensi, Jurnal, Laporan
- **Wali Kelas**: Beranda, Siswa, Absensi, Izin
- **Siswa**: Beranda, Jadwal, Absensi, Profil

### Slide-out Menu
Menu full dengan submenu tersedia dengan mengklik icon menu (☰)

### Touch Optimizations
- Minimum touch target: 44px (iOS guidelines)
- Tap highlight color untuk visual feedback
- Prevent double-tap zoom pada buttons
- Safe area support untuk iPhone dengan notch

## Desktop Layout Features

### Horizontal Navigation
- Logo dan title di kiri
- Menu items dengan dropdown support
- User profile dengan foto di kanan

### Wider Content Area
- Max-width: 1400px (vs 1280px di original)
- Better use of screen real estate

### Hover Effects
- Dropdown menu muncul on hover
- Hover states untuk links dan buttons

## Migration Guide

### Untuk View yang Sudah Ada

1. **No Change Required** - Views akan tetap bekerja dengan layout lama
2. **Gradual Migration** - Update views satu per satu dengan:
   ```php
   // Ubah dari:
   <?= $this->extend('templates/main_layout') ?>
   
   // Ke:
   <?= $this->extend(get_device_layout()) ?>
   ```

3. **Test Mobile & Desktop** - Test di kedua device type

### Untuk Controller yang Sudah Ada

Controllers tidak perlu diubah, karena device detection berjalan otomatis di view level.

## Testing

### Test di Browser

1. **Desktop View**: Buka di browser normal
2. **Mobile View**: 
   - Buka Chrome DevTools (F12)
   - Click Toggle Device Toolbar (Ctrl+Shift+M)
   - Pilih device (iPhone, Android, etc)

### Test Layout Preference

```php
// Set to mobile (in controller or route)
set_layout_preference('templates/mobile_layout');
return redirect()->back();

// Set to desktop
set_layout_preference('templates/desktop_layout');
return redirect()->back();

// Clear preference (auto-detect)
clear_layout_preference();
return redirect()->back();
```

## Best Practices

1. **Use Auto-Detection**: Gunakan `get_device_layout()` untuk automatic device detection
2. **Consistent CSS Classes**: Gunakan CSS classes yang sama (btn, card, badge) untuk konsistensi
3. **Test Both Layouts**: Selalu test di desktop dan mobile
4. **Mobile-First Content**: Design konten yang mudah dibaca di mobile
5. **Touch-Friendly**: Gunakan button size yang cukup besar untuk touch (min 44px)
6. **Avoid Horizontal Scroll**: Pastikan konten tidak overflow di mobile

## Troubleshooting

### Layout Tidak Berubah
- Clear browser cache
- Clear session: `clear_layout_preference()`
- Check user agent detection

### Menu Tidak Muncul
- Pastikan user sudah login: `is_logged_in()`
- Check role permissions
- Check `get_sidebar_menu()` function

### Bottom Nav Tidak Muncul
- Bottom nav hanya muncul di mobile layout
- Pastikan user sudah login
- Check role configuration

## Future Enhancements

- [ ] Add layout switcher button (allow users to toggle)
- [ ] Save layout preference to database
- [ ] Add tablet-specific layout
- [ ] Progressive Web App (PWA) support
- [ ] Dark mode support
- [ ] Custom themes per user

---

**Author**: Rovo Dev  
**Date**: 2026-01-16  
**Version**: 1.0.0
