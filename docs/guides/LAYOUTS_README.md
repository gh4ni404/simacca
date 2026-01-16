# Layouts System - README

## 📚 Documentation Index

Sistem layout SIMACCA telah dipisahkan menjadi desktop dan mobile view. Berikut adalah dokumentasi lengkapnya:

### 📖 Main Documentation

1. **[LAYOUT_GUIDE.md](LAYOUT_GUIDE.md)** - Panduan lengkap penggunaan layout
   - Penjelasan fitur desktop & mobile layout
   - Cara penggunaan helper functions
   - Contoh implementasi di views dan controllers
   - CSS classes yang tersedia
   - Best practices

2. **[LAYOUT_MIGRATION.md](LAYOUT_MIGRATION.md)** - Panduan migrasi views yang sudah ada
   - Strategy migrasi (gradual vs quick)
   - Step-by-step migration guide
   - Checklist untuk setiap view
   - Timeline recommendation
   - Common issues & solutions

3. **[QUICK_TEST_LAYOUTS.md](QUICK_TEST_LAYOUTS.md)** - Panduan cepat untuk testing
   - Quick start testing
   - Testing checklist
   - Troubleshooting
   - Visual verification

## 🗂️ File Structure

```
app/
├── Views/
│   ├── templates/
│   │   ├── desktop_layout.php      # Desktop-optimized layout
│   │   ├── mobile_layout.php       # Mobile-optimized layout
│   │   ├── main_layout.php         # Original responsive layout (deprecated)
│   │   ├── auth_layout.php         # Auth pages layout
│   │   └── print_layout.php        # Print layout
│   └── examples/
│       └── layout_example.php      # Example implementation
├── Controllers/
│   └── LayoutSwitcher.php          # Layout switching controller
├── Helpers/
│   └── auth_helper.php             # Device detection functions
└── Config/
    └── Routes.php                  # Routes for layout switcher

docs/
└── guides/
    ├── LAYOUT_GUIDE.md             # Main guide
    ├── LAYOUT_MIGRATION.md         # Migration guide
    ├── QUICK_TEST_LAYOUTS.md       # Testing guide
    └── LAYOUTS_README.md           # This file

LAYOUT_SEPARATION_SUMMARY.md       # Project summary
```

## 🚀 Quick Start

### 1. Test the Implementation

```bash
# Start your development server
php spark serve

# Open browser and visit:
http://localhost:8080/layout/example
```

### 2. Use in Your Views

```php
<?= $this->extend(get_device_layout()) ?>

<?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h2>Your Page Title</h2>
        </div>
        <div class="card-body">
            <!-- Your content here -->
        </div>
    </div>
<?= $this->endSection() ?>
```

### 3. Test on Mobile

```
1. Open Chrome DevTools (F12)
2. Toggle Device Toolbar (Ctrl+Shift+M)
3. Select mobile device
4. Refresh page
```

## 🎯 Key Features

### Desktop Layout
- ✅ Horizontal navigation bar
- ✅ Dropdown menus on hover
- ✅ Wide content area (1400px)
- ✅ Multi-column layouts
- ✅ Desktop-optimized spacing

### Mobile Layout
- ✅ Sticky top navigation
- ✅ Slide-out menu from right
- ✅ Bottom navigation bar (iOS/Android style)
- ✅ Touch-optimized (44px minimum touch targets)
- ✅ Safe area support for iPhone X+
- ✅ Vertical card stacking

## 📱 Device Detection

```php
// Check device type
if (is_mobile_device()) {
    // Mobile-specific code
}

if (is_tablet_device()) {
    // Tablet-specific code
}

// Get device type as string
$device = get_device_type(); // 'mobile', 'tablet', or 'desktop'

// Get appropriate layout automatically
$layout = get_device_layout();
```

## 🔧 Helper Functions

| Function | Description |
|----------|-------------|
| `is_mobile_device()` | Check if device is mobile phone |
| `is_tablet_device()` | Check if device is tablet |
| `get_device_type()` | Get device type ('mobile', 'tablet', 'desktop') |
| `get_device_layout()` | Get appropriate layout based on device |
| `set_layout_preference()` | Manually set layout preference |
| `clear_layout_preference()` | Clear preference (auto-detect) |

## 🛣️ Available Routes

| Route | Description |
|-------|-------------|
| `/layout/example` | Example page with all features |
| `/layout/desktop` | Switch to desktop layout |
| `/layout/mobile` | Switch to mobile layout |
| `/layout/auto` | Enable auto-detection |
| `/layout/device-info` | Device information (JSON) |

## 📊 Layout Comparison

| Feature | Desktop | Mobile | Original |
|---------|---------|--------|----------|
| Navigation | Horizontal | Top + Bottom | Responsive |
| Max Width | 1400px | 100% | 1280px |
| Touch Targets | Standard | 44px min | Standard |
| Bottom Nav | ❌ | ✅ | ❌ |
| Safe Area | ❌ | ✅ | ❌ |

## 🎨 CSS Classes

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

## 📝 Migration Path

### For Existing Views

**Option 1: No Change (Recommended for now)**
```php
<?= $this->extend('templates/main_layout') ?>
```
Views tetap bekerja dengan layout responsive yang ada.

**Option 2: Use Auto-Detection**
```php
<?= $this->extend(get_device_layout()) ?>
```
Layout otomatis dipilih berdasarkan device.

**Option 3: Force Specific Layout**
```php
<?= $this->extend('templates/desktop_layout') ?>
// or
<?= $this->extend('templates/mobile_layout') ?>
```

### Migration Priority

1. **High Priority** (Mobile-heavy usage)
   - Login/Auth pages
   - Dashboard pages
   - Absensi pages

2. **Medium Priority**
   - Jadwal pages
   - Jurnal pages
   - Profile pages

3. **Low Priority** (Admin/Desktop-heavy)
   - Admin management pages
   - Report pages
   - Settings pages

## 🧪 Testing

### Manual Testing
```bash
# Desktop view
Open browser normally → Visit /layout/example

# Mobile view
Chrome DevTools (F12) → Toggle Device (Ctrl+Shift+M) → Refresh
```

### Automated Testing
```bash
# Check if routes work
php spark routes | grep layout

# Verify files exist
ls app/Views/templates/*.php
ls docs/guides/LAYOUT*.md
```

## ✅ Verification Checklist

- [x] Desktop layout file created
- [x] Mobile layout file created
- [x] Helper functions added
- [x] Controller created
- [x] Routes configured
- [x] Documentation complete
- [x] Example view created
- [ ] **Tested on real mobile device** ⚠️ Do this!
- [ ] **Migrated critical views** ⚠️ Optional

## 🐛 Troubleshooting

### Layout not switching?
1. Visit `/layout/auto` to clear preference
2. Clear browser cache
3. Check console for errors

### Device detection wrong?
1. Visit `/layout/device-info` to see detection
2. Use manual switcher: `/layout/desktop` or `/layout/mobile`
3. Check user agent string

### Menu not working?
1. Ensure you're logged in
2. Check user role permissions
3. Verify JavaScript has no errors

## 📞 Support

### Need Help?
- Read [LAYOUT_GUIDE.md](LAYOUT_GUIDE.md) for detailed usage
- Check [QUICK_TEST_LAYOUTS.md](QUICK_TEST_LAYOUTS.md) for testing
- Review [LAYOUT_MIGRATION.md](LAYOUT_MIGRATION.md) for migration steps

### Found a Bug?
- Check console for errors
- Test on different devices
- Verify helper functions work

## 🎯 Next Steps

1. **Test the Implementation**
   ```
   Visit: http://localhost:8080/layout/example
   ```

2. **Read Documentation**
   - Start with [QUICK_TEST_LAYOUTS.md](QUICK_TEST_LAYOUTS.md)
   - Then [LAYOUT_GUIDE.md](LAYOUT_GUIDE.md)
   - Finally [LAYOUT_MIGRATION.md](LAYOUT_MIGRATION.md)

3. **Test on Mobile Device**
   - Use Chrome DevTools
   - Test on real device if possible

4. **Start Migration (Optional)**
   - Migrate high-priority views first
   - Test thoroughly after each migration
   - Keep original layout as fallback

## 📈 Benefits

### For Users
- 🚀 Better mobile experience
- 📱 Native app-like feel
- ⚡ Faster navigation
- 👆 Touch-optimized interface

### For Developers
- 🧹 Cleaner code separation
- 🔧 Easier maintenance
- 🎨 Platform-specific features
- 📊 Better performance optimization

## 🎉 Summary

**Status**: ✅ **READY TO USE**

Sistem layout baru telah siap digunakan. Anda dapat:
- ✅ Langsung test di `/layout/example`
- ✅ Gunakan di views baru dengan `get_device_layout()`
- ✅ Migrate existing views secara gradual
- ✅ Keep using old layout (backward compatible)

**No Breaking Changes**: Semua views yang ada tetap bekerja normal!

---

**Version**: 1.0.0  
**Created**: 2026-01-16  
**Author**: Rovo Dev  
**Status**: Production Ready ✅
