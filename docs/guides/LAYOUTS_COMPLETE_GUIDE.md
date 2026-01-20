# 📱💻 Complete Layouts Guide - SIMACCA

**Panduan Lengkap untuk Desktop & Mobile Layouts System**

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Layout System Architecture](#layout-system-architecture)
3. [Available Layouts](#available-layouts)
4. [Usage Guide](#usage-guide)
5. [Migration Guide](#migration-guide)
6. [Testing Guide](#testing-guide)
7. [Best Practices](#best-practices)

---

## Overview

SIMACCA menggunakan **dual-layout system** yang otomatis menyesuaikan tampilan berdasarkan device user:
- **Desktop Layout** - Untuk layar ≥768px (tablet & desktop)
- **Mobile Layout** - Untuk layar <768px (smartphone)

### Key Features
✅ **Auto-detection** - Sistem otomatis detect device type  
✅ **Manual override** - User bisa switch layout manual  
✅ **Responsive** - Layout menyesuaikan ukuran layar  
✅ **Consistent** - Component library untuk consistency  

---

## Layout System Architecture

### 1. Layout Templates

#### `main_layout.php` (Auto-switching)
Template utama yang otomatis memilih layout berdasarkan device:

```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
    <h1>Your Content Here</h1>
<?= $this->endSection() ?>
```

**How it works:**
- Detects device type via User-Agent
- Loads `desktop_layout.php` atau `mobile_layout.php`
- User dapat override via session

#### `desktop_layout.php`
Layout untuk desktop dengan sidebar navigation:

**Features:**
- Collapsible sidebar
- Top navbar dengan profile dropdown
- Breadcrumb navigation
- Content area yang luas
- Footer dengan copyright

**Structure:**
```html
<div class="flex">
    <aside>Sidebar</aside>
    <main>
        <nav>Top Navbar</nav>
        <div>Breadcrumb</div>
        <div>Content</div>
        <footer>Footer</footer>
    </main>
</div>
```

#### `mobile_layout.php`
Layout untuk mobile dengan bottom navigation:

**Features:**
- Bottom tab bar (5 tabs max)
- Hamburger menu untuk more options
- Top bar dengan title & notifications
- Swipeable content
- Full-width content area

**Structure:**
```html
<div>
    <header>Top Bar</header>
    <main>Content (fullscreen)</main>
    <nav>Bottom Tab Bar</nav>
</div>
```

#### `auth_layout.php`
Layout khusus untuk halaman authentication (login, register, forgot password):

**Features:**
- Centered card design
- Minimal UI, fokus pada form
- Gradient background
- No navigation (clean)

#### `print_layout.php`
Layout untuk printing documents:

**Features:**
- No navigation/sidebar
- Print-optimized CSS
- Clean header/footer
- A4 page size ready

---

## Available Layouts

### Main Layouts

| Layout | File | Use Case |
|--------|------|----------|
| **Auto (Recommended)** | `main_layout.php` | Default untuk semua halaman |
| **Desktop** | `desktop_layout.php` | Force desktop layout |
| **Mobile** | `mobile_layout.php` | Force mobile layout |
| **Auth** | `auth_layout.php` | Login, register pages |
| **Print** | `print_layout.php` | Laporan, cetak dokumen |

### When to Use Each Layout

**Use `main_layout.php` (Auto):**
```php
// ✅ Recommended untuk 90% kasus
<?= $this->extend('templates/main_layout') ?>
```

**Use `desktop_layout.php` (Force Desktop):**
```php
// ⚠️ Hanya jika fitur HARUS di desktop
// Contoh: Complex data entry, wide tables
<?= $this->extend('templates/desktop_layout') ?>
```

**Use `mobile_layout.php` (Force Mobile):**
```php
// ⚠️ Hanya jika fitur mobile-specific
// Contoh: QR scanner, camera access
<?= $this->extend('templates/mobile_layout') ?>
```

**Use `auth_layout.php`:**
```php
// ✅ Untuk authentication pages
<?= $this->extend('templates/auth_layout') ?>
```

**Use `print_layout.php`:**
```php
// ✅ Untuk printable documents
<?= $this->extend('templates/print_layout') ?>
```

---

## Usage Guide

### Basic Usage

#### 1. Create View with Auto Layout

```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <p>Your content here</p>
</div>
<?= $this->endSection() ?>
```

#### 2. Set Page Title & Meta

```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('pageTitle') ?>Dashboard Guru<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Content -->
<?= $this->endSection() ?>
```

#### 3. Add Custom Scripts

```php
<?= $this->section('scripts') ?>
<script>
    console.log('Custom script');
</script>
<?= $this->endSection() ?>
```

#### 4. Add Custom Styles

```php
<?= $this->section('styles') ?>
<style>
    .custom-class {
        color: blue;
    }
</style>
<?= $this->endSection() ?>
```

### Advanced Usage

#### Desktop-Specific Content

```php
<div class="hidden md:block">
    <!-- Only visible on desktop -->
    <table>Complex Table</table>
</div>
```

#### Mobile-Specific Content

```php
<div class="md:hidden">
    <!-- Only visible on mobile -->
    <div class="cards">Simple Cards</div>
</div>
```

#### Conditional Layout by Controller

```php
// In Controller
public function index()
{
    $data = [
        'pageTitle' => 'Dashboard',
        'layoutType' => isMobile() ? 'mobile' : 'desktop'
    ];
    
    return view('guru/dashboard', $data);
}
```

---

## Migration Guide

### Migrating Existing Views to New Layout System

#### Before (Old System)

```php
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <!-- Manual CSS/JS includes -->
</head>
<body>
    <!-- Manual navbar/sidebar -->
    <div class="content">
        Your content
    </div>
    <!-- Manual footer -->
</body>
</html>
```

#### After (New System)

```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
    Your content
<?= $this->endSection() ?>
```

### Step-by-Step Migration

**Step 1: Identify Current Layout Type**
- Full custom HTML? → Migrate to `main_layout.php`
- Auth pages? → Use `auth_layout.php`
- Print pages? → Use `print_layout.php`

**Step 2: Extract Content**
- Copy only the main content (without header/footer/nav)
- Remove CSS/JS includes (handled by layout)

**Step 3: Wrap with Layout**
```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
    <!-- Paste extracted content here -->
<?= $this->endSection() ?>
```

**Step 4: Add Page-Specific Items**
```php
<?= $this->section('pageTitle') ?>Your Page Title<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <!-- Page-specific JS -->
<?= $this->endSection() ?>
```

**Step 5: Test on Both Devices**
- Desktop (≥768px)
- Mobile (<768px)
- Use browser DevTools for testing

### Migration Checklist

```
□ Content extracted from old HTML
□ Layout template extended
□ Page title set
□ Custom scripts moved to scripts section
□ Custom styles moved to styles section
□ Responsive classes added (hidden md:block, etc.)
□ Tested on desktop
□ Tested on mobile
□ No console errors
□ Navigation works
```

---

## Testing Guide

### Quick Testing

#### 1. Browser DevTools
```
1. Open Chrome DevTools (F12)
2. Click "Toggle Device Toolbar" (Ctrl+Shift+M)
3. Select device:
   - iPhone 12 Pro (390x844) - Mobile
   - iPad Air (820x1180) - Tablet/Desktop
   - Desktop (1920x1080) - Desktop
4. Test all features
```

#### 2. Layout Switcher (for testing)
```
URL: http://localhost:8080/layout-switcher

Options:
- Desktop
- Mobile
- Auto (reset)
```

#### 3. Device Testing Matrix

| Device | Screen | Layout | Priority |
|--------|--------|--------|----------|
| iPhone SE | 375x667 | Mobile | High |
| iPhone 12 | 390x844 | Mobile | High |
| Samsung Galaxy | 360x800 | Mobile | High |
| iPad | 768x1024 | Desktop | Medium |
| MacBook | 1280x800 | Desktop | High |
| Desktop FHD | 1920x1080 | Desktop | High |

### Testing Checklist

**Desktop Layout:**
```
□ Sidebar visible & functional
□ Sidebar collapse works
□ Top navbar visible
□ Breadcrumb displays correctly
□ Content area proper width
□ Footer visible
□ Dropdown menus work
□ Profile menu works
□ All links functional
```

**Mobile Layout:**
```
□ Bottom tab bar visible
□ Bottom tabs clickable
□ Hamburger menu works
□ Top bar visible
□ Title displays correctly
□ Content full width
□ No horizontal scroll
□ Touch gestures work
□ All buttons tappable (min 44x44px)
```

### Automated Testing (Optional)

```bash
# Run layout tests
php spark test Layouts
```

---

## Best Practices

### 1. Always Use Auto Layout
```php
// ✅ Good (90% of cases)
<?= $this->extend('templates/main_layout') ?>

// ❌ Avoid unless necessary
<?= $this->extend('templates/desktop_layout') ?>
```

### 2. Mobile-First Design
```php
// ✅ Good - Mobile first, then desktop
<div class="text-sm md:text-base">Text</div>

// ❌ Avoid - Desktop first, then mobile
<div class="text-base sm:text-sm">Text</div>
```

### 3. Use Tailwind Responsive Classes
```php
// ✅ Good
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">

// ❌ Avoid - Custom media queries
<style>
@media (min-width: 768px) { ... }
</style>
```

### 4. Test on Real Devices
- Emulator ≠ Real device
- Test touch interactions
- Test performance on low-end devices

### 5. Keep Content Sections Clean
```php
// ✅ Good - Clean content section
<?= $this->section('content') ?>
<div class="p-6">
    <h1>Title</h1>
    <p>Content</p>
</div>
<?= $this->endSection() ?>

// ❌ Avoid - Including layout logic in content
<?= $this->section('content') ?>
<!DOCTYPE html>
<html>...</html>
<?= $this->endSection() ?>
```

### 6. Use Component Library
```php
// ✅ Good - Use components
<?= card('Title', 'Content', 'icon-name') ?>

// ❌ Avoid - Duplicate HTML
<div class="bg-white rounded shadow p-4">...</div>
```

### 7. Optimize for Performance
```php
// ✅ Good - Lazy load images
<img loading="lazy" src="image.jpg" />

// ✅ Good - Minimize custom scripts
<?= $this->section('scripts') ?>
<script>/* Keep it minimal */</script>
<?= $this->endSection() ?>
```

---

## Components Library

### Using Shared Components

#### Cards
```php
<?= card(
    'Card Title',
    'Card content goes here',
    'fa-user',
    'blue'
) ?>
```

#### Buttons
```php
<?= button(
    'Click Me',
    'primary',
    '/path/to/action',
    'fa-save'
) ?>
```

#### Tables
```php
<?= table(
    ['Name', 'Email', 'Action'],
    $data,
    ['edit' => true, 'delete' => true]
) ?>
```

#### Alerts
```php
<?= alert('success', 'Data berhasil disimpan!') ?>
<?= alert('error', 'Terjadi kesalahan!') ?>
```

#### Modals
```php
<?= modal(
    'modal-id',
    'Modal Title',
    '<p>Modal content</p>',
    ['submit' => 'Save', 'cancel' => 'Close']
) ?>
```

For complete components documentation, see: `app/Views/components/README.md`

---

## Troubleshooting

### Common Issues

**Problem: Layout tidak berganti saat resize browser**
```
Solution:
1. Hard refresh (Ctrl+F5)
2. Clear browser cache
3. Check session: /layout-switcher → Auto
```

**Problem: Bottom nav tidak muncul di mobile**
```
Solution:
1. Check screen width < 768px
2. Inspect element, cek class "md:hidden"
3. Clear CSS cache
```

**Problem: Sidebar overlap dengan content**
```
Solution:
1. Check z-index values
2. Verify flex layout classes
3. Test sidebar collapse function
```

**Problem: Content tidak full width di mobile**
```
Solution:
Add class: w-full atau max-w-full
Remove: container class (adds padding)
```

---

## Additional Resources

### Documentation
- [Component Library](../views/components/README.md)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [CodeIgniter 4 Views](https://codeigniter.com/user_guide/outgoing/views.html)

### Examples
- Guru Dashboard: `app/Views/guru/dashboard.php`
- Admin Panel: `app/Views/admin/dashboard.php`
- Mobile Forms: `app/Views/guru/absensi/create_mobile.php`

### Support
- Email: support@simacca.sch.id
- GitHub Issues: [Report a bug](https://github.com/username/simacca/issues)

---

**Version:** 2.0  
**Last Updated:** January 2026  
**Maintainer:** SIMACCA Dev Team

---

*Happy coding! 🚀*
