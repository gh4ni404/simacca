# 🎨 Template System Guide - SIMACCA

**Created:** 2026-01-11  
**Version:** 1.0.0

---

## 📋 Overview

Sistem template yang terstruktur dan konsisten untuk maintenance yang lebih mudah. Semua views sekarang menggunakan template system dengan reusable components.

---

## 🗂️ Struktur Template

```
app/Views/
├── templates/
│   ├── main_layout.php      # Layout utama (dengan sidebar & navbar)
│   ├── auth_layout.php      # Layout untuk auth pages (login, register, dll)
│   └── print_layout.php     # Layout untuk print pages
├── components/
│   ├── alerts.php           # Flash message alerts
│   ├── buttons.php          # Button components
│   ├── cards.php            # Card components
│   ├── forms.php            # Form input components
│   ├── modals.php           # Modal components
│   ├── tables.php           # Table components
│   └── badges.php           # Status badge components
└── [modules]/
    └── *.php                # View files yang extend templates
```

---

## 🎯 Template Layouts

### 1. Main Layout (`templates/main_layout.php`)

**Digunakan untuk:** Dashboard, CRUD pages, dan semua authenticated pages

**Features:**
- ✅ Sidebar navigation
- ✅ Top navbar with user menu
- ✅ Breadcrumb (optional)
- ✅ Flash messages
- ✅ Mobile responsive
- ✅ Chart.js integration
- ✅ Flatpickr date picker
- ✅ Modal helper scripts

**Usage:**
```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <h1>Your Content Here</h1>
</div>
<?= $this->endSection() ?>
```

---

### 2. Auth Layout (`templates/auth_layout.php`)

**Digunakan untuk:** Login, Register, Forgot Password, Access Denied

**Features:**
- ✅ Centered card design
- ✅ Gradient background
- ✅ No sidebar/navbar
- ✅ Auto-hide flash messages
- ✅ Animated card entrance
- ✅ Mobile responsive

**Usage:**
```php
<?= $this->extend('templates/auth_layout') ?>

<?= $this->section('title') ?>
Login
<?= $this->endSection() ?>

<?= $this->section('header') ?>
<i class="fas fa-graduation-cap text-5xl text-indigo-600"></i>
<h2 class="mt-6 text-3xl font-extrabold">Login</h2>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="<?= base_url('login/process'); ?>" method="POST">
    <!-- Form content -->
</form>
<?= $this->endSection() ?>
```

---

### 3. Print Layout (`templates/print_layout.php`)

**Digunakan untuk:** Print pages (Absensi, Jurnal, Laporan)

**Features:**
- ✅ Print-optimized styles
- ✅ Kop surat format
- ✅ Page break controls
- ✅ Print & close buttons (hidden when printing)
- ✅ Signature section
- ✅ A4/Letter page size support

**Usage:**
```php
<?= $this->extend('templates/print_layout') ?>

<?= $this->section('title') ?>
Laporan Absensi
<?= $this->endSection() ?>

<?= $this->section('page_size') ?>
A4
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Kop Surat -->
<div class="kop-surat">
    <h1>NAMA SEKOLAH</h1>
    <p>Alamat, Telp, Email</p>
</div>

<!-- Content -->
<table>
    <!-- Table content -->
</table>

<!-- Signature -->
<div class="signature-section">
    <div class="signature-box">
        <p>Mengetahui,</p>
        <p>Kepala Sekolah</p>
        <div class="signature-line">
            <p><strong>Nama Kepala Sekolah</strong></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
```

---

## 🧩 Reusable Components

### 1. Alerts (`components/alerts.php`)

**Auto-loaded di semua templates.** Menampilkan flash messages.

**Flash Message Types:**
- `success` - Green alert
- `error` - Red alert
- `warning` - Yellow alert
- `info` - Blue alert
- `errors` - Multiple error messages (array)

**Usage in Controller:**
```php
return redirect()->back()->with('success', 'Data berhasil disimpan!');
return redirect()->back()->with('error', 'Terjadi kesalahan!');
return redirect()->back()->with('warning', 'Perhatian!');
return redirect()->back()->with('info', 'Informasi penting');
```

**Features:**
- ✅ Icon per type
- ✅ Close button
- ✅ Auto-dismissible after 5 seconds
- ✅ Transition animations

---

### 2. Buttons (`components/buttons.php`)

**Helper Functions:**

#### `button($variant, $text, $icon, $attrs)`
Generate button element
```php
<?= button('primary', 'Save', 'save', ['type' => 'submit']) ?>
<?= button('danger', 'Delete', 'trash', ['onclick' => 'confirmDelete()']) ?>
```

**Variants:** `primary`, `secondary`, `success`, `warning`, `danger`, `info`, `outline`

#### `button_link($variant, $text, $icon, $href, $attrs)`
Generate link styled as button
```php
<?= button_link('primary', 'Add New', 'plus', base_url('admin/guru/create')) ?>
<?= button_link('secondary', 'Back', 'arrow-left', base_url('admin/guru')) ?>
```

#### `icon_button($icon, $variant, $attrs)`
Generate icon-only button (small)
```php
<?= icon_button('edit', 'primary', ['onclick' => 'edit(' . $id . ')']) ?>
<?= icon_button('trash', 'danger', ['onclick' => 'delete(' . $id . ')']) ?>
```

---

### 3. Cards (`components/cards.php`)

**Helper Functions:**

#### `card_start($title, $icon, $actions)` & `card_end()`
Standard card with header
```php
<?= card_start('Data Guru', 'users', [
    button_link('primary', 'Add', 'plus', base_url('admin/guru/create'))
]) ?>
    <!-- Card content -->
<?= card_end() ?>
```

#### `stat_card($label, $value, $icon, $color, $link)`
Statistics card for dashboard
```php
<?= stat_card('Total Siswa', '250', 'users', 'blue', base_url('admin/siswa')) ?>
<?= stat_card('Guru Aktif', '42', 'chalkboard-teacher', 'green') ?>
```

**Colors:** `blue`, `green`, `yellow`, `red`, `purple`, `indigo`, `gray`

#### `empty_state($icon, $title, $description, $actionText, $actionUrl)`
Empty state component
```php
<?= empty_state(
    'inbox', 
    'Belum ada data', 
    'Mulai dengan menambahkan data pertama',
    'Tambah Data',
    base_url('admin/guru/create')
) ?>
```

#### `info_card($icon, $title, $content, $color)`
Info card with colored border
```php
<?= info_card('info-circle', 'Perhatian', 'Pastikan data sudah benar', 'blue') ?>
```

---

### 4. Forms (`components/forms.php`)

**Helper Functions:**

#### `form_input($name, $label, $value, $attrs)`
Text input field with validation
```php
<?= form_input('username', 'Username', old('username'), [
    'required' => true,
    'placeholder' => 'Enter username'
]) ?>
```

**Supported types:** `text`, `email`, `number`, `tel`, `url`, `date`, `time`

#### `form_textarea($name, $label, $value, $attrs)`
Textarea field
```php
<?= form_textarea('keterangan', 'Keterangan', old('keterangan'), [
    'rows' => 4,
    'required' => true
]) ?>
```

#### `form_select($name, $label, $options, $selected, $attrs)`
Select dropdown
```php
<?= form_select('role', 'Role', [
    'admin' => 'Administrator',
    'guru' => 'Guru',
    'siswa' => 'Siswa'
], old('role'), ['required' => true]) ?>
```

#### `form_file($name, $label, $attrs)`
File upload input
```php
<?= form_file('foto', 'Upload Foto', [
    'accept' => 'image/*',
    'help' => 'Max 2MB, format: JPG, PNG'
]) ?>
```

#### `form_checkbox($name, $label, $checked, $value)`
Checkbox input
```php
<?= form_checkbox('is_active', 'Aktif', true, '1') ?>
```

**Auto Features:**
- ✅ Validation error display
- ✅ Old input restoration
- ✅ Required field indicator (*)
- ✅ Icon support
- ✅ Help text

---

### 5. Modals (`components/modals.php`)

**Helper Functions:**

#### `modal_start($id, $title, $size)` & `modal_end($buttons)`
Reusable modal
```php
<?= modal_start('addModal', 'Tambah Data', 'lg') ?>
    <form action="<?= base_url('admin/guru/store') ?>" method="POST">
        <?= csrf_field() ?>
        <!-- Form fields -->
    </form>
<?= modal_end([
    button('secondary', 'Cancel', '', ['onclick' => 'closeModal("addModal")']),
    button('primary', 'Save', 'save', ['type' => 'submit'])
]) ?>

<!-- Open modal button -->
<button onclick="openModal('addModal')">Open Modal</button>
```

**Sizes:** `sm`, `md`, `lg`, `xl`, `2xl`

#### `confirm_modal($id, $title, $message, $confirmText, $cancelText)`
Confirmation modal (for delete, etc)
```php
<?= confirm_modal(
    'deleteModal',
    'Konfirmasi Hapus',
    'Apakah Anda yakin ingin menghapus data ini?',
    'Ya, Hapus',
    'Batal'
) ?>

<!-- Listen for confirm event -->
<script>
document.addEventListener('confirmed', function(e) {
    if (e.detail.modalId === 'deleteModal') {
        // Execute delete action
        window.location.href = deleteUrl;
    }
});
</script>
```

#### `modal_scripts()`
Modal helper scripts (auto-included in main_layout)
```javascript
openModal('modalId')   // Open modal
closeModal('modalId')  // Close modal
// Press ESC to close any open modal
```

---

### 6. Tables (`components/tables.php`)

**Helper Functions:**

#### `table_start()`, `table_header($columns)`, `table_end()`
Responsive table
```php
<?= table_start() ?>
    <?= table_header(['No', 'Nama', 'Email', 'Status', 'Aksi']) ?>
    <?php foreach ($data as $key => $item): ?>
    <tr>
        <td class="px-6 py-4"><?= $key + 1 ?></td>
        <td class="px-6 py-4"><?= esc($item['nama']) ?></td>
        <td class="px-6 py-4"><?= esc($item['email']) ?></td>
        <td class="px-6 py-4"><?= status_badge($item['status']) ?></td>
        <td class="px-6 py-4">
            <?= icon_button('edit', 'primary') ?>
            <?= icon_button('trash', 'danger') ?>
        </td>
    </tr>
    <?php endforeach; ?>
<?= table_end() ?>
```

#### `badge($text, $color)`
Simple badge
```php
<?= badge('Active', 'green') ?>
<?= badge('Pending', 'yellow') ?>
<?= badge('Inactive', 'red') ?>
```

---

### 7. Badges (`components/badges.php`)

#### `status_badge($status)`
Smart status badge with icon
```php
<?= status_badge('H') ?>      // Hadir (green)
<?= status_badge('S') ?>      // Sakit (yellow)
<?= status_badge('I') ?>      // Izin (blue)
<?= status_badge('A') ?>      // Alpha (red)
<?= status_badge('active') ?> // Aktif (green)
<?= status_badge('pending') ?> // Menunggu (yellow)
```

**Supported Statuses:**
- Absensi: `H`, `S`, `I`, `A`, `Hadir`, `Sakit`, `Izin`, `Alpha`
- Status: `active`, `inactive`, `pending`, `approved`, `rejected`

---

## 🚀 Migration Guide

### Refactoring Existing Views

**Before (Old Way):**
```php
<!DOCTYPE html>
<html>
<head>
    <title>Page Title</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom styles -->
</head>
<body>
    <div class="container">
        <h1>My Page</h1>
        
        <?php if (session()->has('success')): ?>
        <div class="alert alert-success">
            <?= session('success') ?>
        </div>
        <?php endif; ?>
        
        <form action="..." method="POST">
            <input type="text" name="username">
            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
```

**After (New Way):**
```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    <?= card_start('My Page', 'file') ?>
        <form action="..." method="POST">
            <?= csrf_field() ?>
            <?= form_input('username', 'Username', old('username'), ['required' => true]) ?>
            <?= button('primary', 'Submit', 'save', ['type' => 'submit']) ?>
        </form>
    <?= card_end() ?>
</div>
<?= $this->endSection() ?>
```

**Benefits:**
- ✅ 50% less code
- ✅ Consistent styling
- ✅ Automatic validation display
- ✅ Reusable components
- ✅ Easier maintenance

---

## 📝 Best Practices

### 1. Always Use Templates
```php
// ✅ Good
<?= $this->extend('templates/main_layout') ?>

// ❌ Bad
<!DOCTYPE html>
```

### 2. Use Component Helpers
```php
// ✅ Good
<?= button('primary', 'Save', 'save', ['type' => 'submit']) ?>

// ❌ Bad
<button type="submit" class="bg-blue-600 hover:bg-blue-700...">
    <i class="fas fa-save"></i> Save
</button>
```

### 3. Leverage Flash Messages
```php
// ✅ Good - In Controller
return redirect()->back()->with('success', 'Data saved!');

// ✅ Good - In View (auto-displayed by alerts component)
// No need to manually check session
```

### 4. Use Status Badges
```php
// ✅ Good
<?= status_badge($absensi['status']) ?>

// ❌ Bad
<?php if ($absensi['status'] === 'H'): ?>
    <span class="badge-green">Hadir</span>
<?php elseif ($absensi['status'] === 'S'): ?>
    <span class="badge-yellow">Sakit</span>
<?php endif; ?>
```

### 5. Consistent Card Usage
```php
// ✅ Good
<?= card_start('Data Table', 'table') ?>
    <?= table_start() ?>
    <!-- table content -->
    <?= table_end() ?>
<?= card_end() ?>

// ❌ Bad
<div class="bg-white rounded-lg shadow">
    <div class="p-6">
        <table>...</table>
    </div>
</div>
```

---

## 🔧 Customization

### Adding Custom Components

1. Create file in `app/Views/components/your_component.php`
2. Define helper functions
3. Add to auto-load in `component_helper.php`

**Example:**
```php
// app/Views/components/timeline.php
<?php
if (!function_exists('timeline_item')) {
    function timeline_item($date, $title, $content, $color = 'blue') {
        // Component HTML
    }
}
```

### Extending Layouts

**Create custom layout:**
```php
// app/Views/templates/custom_layout.php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="custom-wrapper">
    <?= $this->renderSection('custom_content') ?>
</div>
<?= $this->endSection() ?>
```

---

## 🎨 Color System

**Standard Colors:**
- `primary`: Blue (#3B82F6)
- `secondary`: Gray (#6B7280)
- `success`: Green (#10B981)
- `warning`: Yellow/Orange (#F59E0B)
- `danger`: Red (#EF4444)
- `info`: Light Blue (#3ABFF8)

**Usage Consistency:**
- Primary: Main actions (Save, Submit, Add)
- Secondary: Cancel, Back, Secondary actions
- Success: Success messages, Active status
- Warning: Warnings, Pending status
- Danger: Delete, Errors, Inactive status
- Info: Information, Help messages

---

## 📊 Performance Tips

1. **Component Loading:** Components auto-load via `component_helper.php`
2. **CSS/JS:** Use CDN for faster loading (already implemented)
3. **Caching:** Enable view caching in production
4. **Minimize Custom Styles:** Use Tailwind utilities instead

---

## 🐛 Troubleshooting

### Components Not Working
```php
// Check if component helper is loaded
// In app/Config/Autoload.php
public $helpers = ['component'];
```

### Modal Not Opening
```php
// Make sure modal_scripts() is included in layout
<?= modal_scripts() ?>

// Check modal ID matches
openModal('myModal')  // ID must match modal_start('myModal')
```

### Flash Messages Not Showing
```php
// Check controller redirect
return redirect()->to('/page')->with('success', 'Message');

// Check if alerts component is included in layout
<?= $this->include('components/alerts') ?>
```

---

## 📚 Examples

### Complete CRUD Page Example
```php
<?= $this->extend('templates/main_layout') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-6">
    
    <!-- Header Card -->
    <?= card_start('Data Guru', 'users', [
        button_link('primary', 'Tambah Guru', 'plus', base_url('admin/guru/create')),
        button_link('success', 'Import Excel', 'file-excel', base_url('admin/guru/import'))
    ]) ?>
    
        <!-- Filters -->
        <form method="GET" class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?= form_input('search', 'Cari', $search ?? '', ['placeholder' => 'Nama atau NIP']) ?>
                <?= form_select('status', 'Status', [
                    '' => 'Semua',
                    'active' => 'Aktif',
                    'inactive' => 'Tidak Aktif'
                ], $status ?? '') ?>
                <?= button('primary', 'Filter', 'search', ['type' => 'submit']) ?>
            </div>
        </form>
        
        <!-- Table -->
        <?php if (!empty($guru)): ?>
            <?= table_start() ?>
                <?= table_header(['No', 'NIP', 'Nama', 'Email', 'Status', 'Aksi']) ?>
                <?php foreach ($guru as $key => $item): ?>
                <tr>
                    <td class="px-6 py-4"><?= $key + 1 ?></td>
                    <td class="px-6 py-4"><?= esc($item['nip']) ?></td>
                    <td class="px-6 py-4"><?= esc($item['nama']) ?></td>
                    <td class="px-6 py-4"><?= esc($item['email']) ?></td>
                    <td class="px-6 py-4"><?= status_badge($item['status']) ?></td>
                    <td class="px-6 py-4 space-x-2">
                        <?= button_link('info', 'Detail', 'eye', base_url('admin/guru/show/' . $item['id'])) ?>
                        <?= button_link('warning', 'Edit', 'edit', base_url('admin/guru/edit/' . $item['id'])) ?>
                        <?= button('danger', 'Hapus', 'trash', [
                            'onclick' => 'confirmDelete(' . $item['id'] . ')'
                        ]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?= table_end() ?>
        <?php else: ?>
            <?= empty_state('users', 'Belum ada data guru', 'Mulai dengan menambahkan guru pertama', 'Tambah Guru', base_url('admin/guru/create')) ?>
        <?php endif; ?>
        
    <?= card_end() ?>
    
</div>

<!-- Delete Confirmation Modal -->
<?= confirm_modal('deleteModal', 'Konfirmasi Hapus', 'Apakah Anda yakin ingin menghapus guru ini?') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let deleteId = null;

function confirmDelete(id) {
    deleteId = id;
    openModal('deleteModal');
}

document.addEventListener('confirmed', function(e) {
    if (e.detail.modalId === 'deleteModal' && deleteId) {
        window.location.href = '<?= base_url('admin/guru/delete/') ?>' + deleteId;
    }
});
</script>
<?= $this->endSection() ?>
```

---

## ✅ Checklist for New Views

When creating a new view, ensure:

- [ ] Extends appropriate template (`main_layout`, `auth_layout`, or `print_layout`)
- [ ] Uses component helpers instead of custom HTML
- [ ] Implements proper flash message handling
- [ ] Uses `status_badge()` for status display
- [ ] Wraps content in cards with `card_start()` / `card_end()`
- [ ] Uses form helpers for inputs with validation
- [ ] Implements responsive design (grid, flexbox)
- [ ] Adds loading states for async actions
- [ ] Uses modals for confirmations
- [ ] Tests on mobile devices

---

## 🚀 Next Steps

After template system implementation:

1. **Refactor Remaining Views** - Apply to all views systematically
2. **Add Toast Notifications** - Replace browser `alert()` with toasts
3. **Implement Pagination Component** - Standardize pagination UI
4. **Add Loading Skeletons** - Improve perceived performance
5. **Dark Mode** - Add theme toggle
6. **Component Library Page** - Create demo page for all components

---

**Last Updated:** 2026-01-11  
**Maintained By:** Development Team  
**Questions?** Check code comments in component files or consult this guide.
