# Flash Message Component System

## 📋 Overview

Sistem flash message terpusat yang hanya menampilkan **SATU pesan** dengan prioritas tertinggi. Ini meningkatkan user experience dengan mencegah multiple flash messages yang membingungkan.

## 🎯 Features

### 1. **Priority-Based Display**
Sistem secara otomatis memilih dan menampilkan hanya satu pesan berdasarkan prioritas:

```
errors > error > warning > success_custom > success > info
```

### 2. **Supported Message Types**

| Type | Priority | Usage | Example |
|------|----------|-------|---------|
| `errors` | 1 (Highest) | Multiple validation errors | Form validation failures |
| `error` | 2 | Single error message | Operation failed |
| `warning` | 3 | Warning message | Caution needed |
| `success_custom` | 4 | Custom success with title | Special success with details |
| `success` | 5 | Standard success | Operation successful |
| `info` | 6 (Lowest) | Information message | General information |

### 3. **Consistent Styling**
- Modern, clean design with Tailwind CSS
- Color-coded by message type
- Icon indicators
- Dismissible with close button
- Smooth animations

## 📖 Usage

### In Views

Simply include the component in your view:

```php
<!-- Flash Messages -->
<?= view('components/alerts') ?>
```

**Optional**: Show all messages instead of priority-based single message:

```php
<!-- Show all flash messages -->
<?= view('components/alerts', ['showAll' => true]) ?>
```

### In Controllers

#### Standard Messages

```php
// Success message
$this->session->setFlashdata('success', 'Data berhasil disimpan!');

// Error message
$this->session->setFlashdata('error', 'Terjadi kesalahan saat menyimpan data.');

// Info message
$this->session->setFlashdata('info', 'Pastikan data sudah benar.');

// Warning message
$this->session->setFlashdata('warning', 'Perhatian: Data akan dihapus permanen!');
```

#### Multiple Errors (Validation)

```php
// Array of errors
$errors = [
    'Email tidak valid',
    'Password minimal 8 karakter',
    'Nama wajib diisi'
];
$this->session->setFlashdata('errors', $errors);
```

#### Custom Success with Title

```php
// Custom success with title and detailed message
$this->session->setFlashdata('success_custom', [
    'title' => 'Sudah Beres! ⚡',
    'message' => 'Ternyata absen sudah diisi <strong>Pak Budi</strong>. Bapak/Ibu tidak perlu input ulang. Terima kasih bantuannya!'
]);
```

## 🎨 Message Examples

### Success (Standard)
```php
session()->setFlashdata('success', 'Data berhasil disimpan!');
```
**Result**: Green box with checkmark icon

### Success (Custom)
```php
session()->setFlashdata('success_custom', [
    'title' => 'Berhasil! 🎉',
    'message' => 'Profil Anda telah diperbarui dengan sukses.'
]);
```
**Result**: Larger box with gradient background, custom title, and detailed message

### Error
```php
session()->setFlashdata('error', 'Gagal menghapus data. Silakan coba lagi.');
```
**Result**: Red box with exclamation icon

### Errors (Multiple)
```php
session()->setFlashdata('errors', [
    'Email sudah terdaftar',
    'Username minimal 5 karakter'
]);
```
**Result**: Red box with bulleted list of errors

### Warning
```php
session()->setFlashdata('warning', 'Anda akan keluar dari sistem.');
```
**Result**: Yellow box with warning icon

### Info
```php
session()->setFlashdata('info', 'Absensi untuk jadwal ini sudah ada.');
```
**Result**: Blue box with info icon

## 🔄 Priority Example

If controller sets multiple messages:

```php
session()->setFlashdata('success', 'Data tersimpan');
session()->setFlashdata('info', 'Silakan cek email');
session()->setFlashdata('error', 'Gagal mengirim email');
```

**Only `error` will be displayed** (highest priority among the three).

## 🛠️ Technical Details

### Helper Function

Located in: `app/Helpers/component_helper.php`

```php
render_alerts($showAll = false)
```

- **$showAll**: `false` (default) = show only highest priority
- **$showAll**: `true` = show all messages

### Component File

Located in: `app/Views/components/alerts.php`

Automatically calls `render_alerts()` function.

## ✅ Benefits

1. **Better UX**: Users see only the most important message
2. **Cleaner UI**: No multiple stacked messages
3. **Consistent Design**: Same styling across all pages
4. **Easy Maintenance**: Update one component, affects all pages
5. **Flexible**: Support for standard and custom messages

## 📝 Migration Notes

All views have been migrated from individual flash message blocks to use the centralized component:

- **Before**: 50+ lines of repeated code per view
- **After**: 1 line: `<?= view('components/alerts') ?>`

Total files updated: **22 views**

## 🔍 Testing Scenarios

1. **Single message**: Set one flashdata, verify it displays
2. **Multiple messages**: Set multiple, verify only highest priority shows
3. **Custom success**: Test with title and HTML in message
4. **Errors array**: Test with validation errors
5. **Close button**: Click X to dismiss message
6. **Responsive**: Test on mobile, tablet, desktop

## 📚 Related Files

- Component: `app/Views/components/alerts.php`
- Helper: `app/Helpers/component_helper.php`
- Used in: 22+ view files across admin, guru, siswa modules

---

**Last Updated**: 2026-01-13
**Version**: 2.0
**Status**: Production Ready ✅
