# Layout Migration Guide

## Overview

Panduan untuk migrasi dari `main_layout.php` ke `desktop_layout.php` dan `mobile_layout.php`.

## Migration Strategy

### Option 1: Gradual Migration (Recommended)

Migrate views satu per satu untuk minimize risk:

1. **Keep existing layout intact** - `main_layout.php` tetap ada
2. **Test new layouts** - Test di `/layout/example`
3. **Migrate important views first** - Dashboard, login, etc.
4. **Test thoroughly** - Test di desktop dan mobile
5. **Continue migration** - Migrate remaining views

### Option 2: Quick Migration

Migrate semua views sekaligus (for experienced teams):

1. **Global find & replace**
2. **Comprehensive testing**
3. **Rollback plan ready**

## Step-by-Step Migration

### Step 1: Test New Layouts

```bash
# Access example page
http://your-domain.test/layout/example

# Test layout switcher
http://your-domain.test/layout/desktop
http://your-domain.test/layout/mobile
http://your-domain.test/layout/auto

# Check device info
http://your-domain.test/layout/device-info
```

### Step 2: Update Individual Views

#### Before:
```php
<?= $this->extend('templates/main_layout') ?>
```

#### After:
```php
<?= $this->extend(get_device_layout()) ?>
```

### Step 3: Update Views by Priority

> **Note**: Auth views (login, forgot password, etc.) should NOT be migrated. They use `templates/auth_layout.php` which is already optimized. See [Auth Views section](#auth-views-4-files---no-migration-needed) below.

#### High Priority (Migrate First)
1. Dashboard pages (most frequently accessed):
   - `app/Views/admin/dashboard.php`
   - `app/Views/guru/dashboard.php`
   - `app/Views/siswa/dashboard.php`
   - `app/Views/walikelas/dashboard.php`

#### Medium Priority
3. Absensi pages (heavily used on mobile)
4. Jurnal pages
5. Jadwal pages

#### Low Priority (Migrate Last)
6. Admin management pages
7. Report pages
8. Settings pages

### Step 4: Automated Migration Script

Create a PHP script to help with migration:

```php
<?php
// File: update_layouts.php

$viewsPath = APPPATH . 'Views/';
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath)
);

// Folders to skip (auth views use their own layout)
$skipFolders = ['auth'];

$updated = 0;
$skipped = 0;
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        
        // Skip auth folder
        $shouldSkip = false;
        foreach ($skipFolders as $folder) {
            if (strpos($filePath, DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR) !== false) {
                echo "Skipped (auth): " . $filePath . "\n";
                $skipped++;
                $shouldSkip = true;
                break;
            }
        }
        
        if ($shouldSkip) {
            continue;
        }
        
        $content = file_get_contents($filePath);
        
        // Skip if already using get_device_layout()
        if (strpos($content, 'get_device_layout()') !== false) {
            continue;
        }
        
        // Skip if using auth_layout (double check)
        if (strpos($content, 'auth_layout') !== false) {
            echo "Skipped (auth_layout): " . $filePath . "\n";
            $skipped++;
            continue;
        }
        
        // Replace main_layout with get_device_layout()
        $newContent = str_replace(
            "<?= \$this->extend('templates/main_layout') ?>",
            "<?= \$this->extend(get_device_layout()) ?>",
            $content
        );
        
        if ($content !== $newContent) {
            file_put_contents($filePath, $newContent);
            echo "Updated: " . $filePath . "\n";
            $updated++;
        }
    }
}

echo "\nTotal files updated: $updated\n";
echo "Total files skipped: $skipped\n";
```

**Usage:**
```bash
php update_layouts.php
```

## Manual Migration Checklist

### For Each View File:

- [ ] Backup the file
- [ ] Update `$this->extend()` to use `get_device_layout()`
- [ ] Test in desktop view
- [ ] Test in mobile view (Chrome DevTools)
- [ ] Check responsive layout
- [ ] Verify buttons are touch-friendly
- [ ] Test form inputs
- [ ] Check navigation works
- [ ] Verify flash messages display correctly
- [ ] Test on real device if possible

## Views to Migrate

### Auth Views (4 files) - ⚠️ **NO MIGRATION NEEDED**

**Status**: ✅ **KEEP USING `auth_layout.php`**

Auth views should **NOT** be migrated to desktop/mobile layout system because:

1. ✅ **Already Responsive** - `auth_layout.php` is fully responsive with Tailwind utilities
2. ✅ **No Navigation Needed** - Auth pages don't require navigation bars, sidebars, or menus
3. ✅ **Centered Card Design** - Standard best practice for authentication pages
4. ✅ **Simplified Structure** - Auth pages benefit from minimal, focused layout
5. ✅ **Production Ready** - Current implementation matches design references

**Files** (Keep using `templates/auth_layout`):
- ✅ `app/Views/auth/login.php` - Already optimal
- ✅ `app/Views/auth/forgot_password.php` - Already optimal
- ✅ `app/Views/auth/reset_password.php` - Already optimal
- ✅ `app/Views/auth/change_password.php` - Already optimal

**Reference**: See [LOGIN_PAGE_MIGRATION_ANALYSIS.md](../summary/LOGIN_PAGE_MIGRATION_ANALYSIS.md) for detailed analysis.

> **Note**: Auth layout uses `templates/auth_layout.php` which is specifically designed for authentication pages. It should remain separate from the main application layouts (desktop/mobile) as they serve different purposes.

### Admin Views (30+ files)
- [ ] `app/Views/admin/dashboard.php`
- [ ] `app/Views/admin/absensi/index.php`
- [ ] `app/Views/admin/guru/index.php`
- [ ] `app/Views/admin/guru/create.php`
- [ ] `app/Views/admin/guru/edit.php`
- [ ] `app/Views/admin/guru/show.php`
- [ ] `app/Views/admin/guru/import.php`
- [ ] `app/Views/admin/siswa/index.php`
- [ ] `app/Views/admin/siswa/tambah.php`
- [ ] `app/Views/admin/siswa/edit.php`
- [ ] `app/Views/admin/siswa/show.php`
- [ ] `app/Views/admin/siswa/import.php`
- [ ] `app/Views/admin/kelas/index.php`
- [ ] `app/Views/admin/kelas/create.php`
- [ ] `app/Views/admin/kelas/edit.php`
- [ ] `app/Views/admin/kelas/show.php`
- [ ] `app/Views/admin/kelas/statistics.php`
- [ ] `app/Views/admin/mata_pelajaran/index.php`
- [ ] `app/Views/admin/mata_pelajaran/create.php`
- [ ] `app/Views/admin/mata_pelajaran/edit.php`
- [ ] `app/Views/admin/jadwal/index.php`
- [ ] `app/Views/admin/jadwal/create.php`
- [ ] `app/Views/admin/jadwal/edit.php`
- [ ] `app/Views/admin/jadwal/import.php`
- [ ] `app/Views/admin/laporan/absensi.php`
- [ ] `app/Views/admin/laporan/absensi_detail.php`
- [ ] `app/Views/admin/laporan/statistik.php`

### Guru Views (15+ files)
- [ ] `app/Views/guru/dashboard.php`
- [ ] `app/Views/guru/absensi/index.php`
- [ ] `app/Views/guru/absensi/create.php`
- [ ] `app/Views/guru/absensi/edit.php`
- [ ] `app/Views/guru/absensi/show.php`
- [ ] `app/Views/guru/jurnal/index.php`
- [ ] `app/Views/guru/jurnal/create.php`
- [ ] `app/Views/guru/jurnal/edit.php`
- [ ] `app/Views/guru/jurnal/show.php`
- [ ] `app/Views/guru/jadwal/index.php`
- [ ] `app/Views/guru/laporan/index.php`
- [ ] `app/Views/guru/laporan/index_enhanced.php`

### Wali Kelas Views (8+ files)
- [ ] `app/Views/walikelas/dashboard.php`
- [ ] `app/Views/walikelas/siswa/index.php`
- [ ] `app/Views/walikelas/absensi/index.php`
- [ ] `app/Views/walikelas/izin/index.php`
- [ ] `app/Views/walikelas/laporan/index.php`

### Siswa Views (10+ files)
- [ ] `app/Views/siswa/dashboard.php`
- [ ] `app/Views/siswa/jadwal/index.php`
- [ ] `app/Views/siswa/absensi/index.php`
- [ ] `app/Views/siswa/izin/index.php`
- [ ] `app/Views/siswa/izin/create.php`
- [ ] `app/Views/siswa/profil/index.php`

### Profile Views (1 file)
- [ ] `app/Views/profile/index.php`

## Testing Checklist

After migration, test these scenarios:

### Desktop Testing
- [ ] Navigation menu works
- [ ] Dropdown menus appear on hover
- [ ] User profile dropdown works
- [ ] Forms are properly sized
- [ ] Tables display correctly
- [ ] Buttons have proper spacing
- [ ] Cards layout properly
- [ ] Flash messages appear correctly

### Mobile Testing
- [ ] Top navigation bar is sticky
- [ ] Hamburger menu opens slide-out panel
- [ ] Bottom navigation works
- [ ] Bottom nav items highlight correctly
- [ ] Touch targets are at least 44px
- [ ] Forms are mobile-friendly
- [ ] Tables scroll horizontally if needed
- [ ] Cards stack vertically
- [ ] Flash messages are readable

### Cross-browser Testing
- [ ] Chrome (Desktop & Mobile)
- [ ] Firefox (Desktop & Mobile)
- [ ] Safari (Desktop & iOS)
- [ ] Edge (Desktop)

## Rollback Plan

If issues occur:

### Quick Rollback (Individual View)
```php
// Change back to:
<?= $this->extend('templates/main_layout') ?>
```

### Full Rollback (All Views)
```bash
# Use Git to revert
git checkout HEAD -- app/Views/
```

### Partial Rollback
Keep new layouts but revert specific views that have issues.

## Common Issues & Solutions

### Issue 1: Layout Not Detected
**Problem:** Always showing desktop layout on mobile

**Solution:**
```php
// Clear session cache
clear_layout_preference();

// Check user agent detection
var_dump(is_mobile_device());
```

### Issue 2: Bottom Nav Not Showing
**Problem:** Bottom navigation missing on mobile

**Solution:**
- Ensure user is logged in
- Check if user role is configured in bottom nav array
- Verify mobile layout is being used

### Issue 3: Menu Items Missing
**Problem:** Some menu items don't appear

**Solution:**
- Check `get_sidebar_menu()` function
- Verify user role permissions
- Check route definitions

### Issue 4: CSS Classes Not Working
**Problem:** Buttons or cards don't look right

**Solution:**
- Ensure using correct classes: `btn`, `card`, `badge`
- Check if custom CSS conflicts with layout CSS
- Verify Tailwind CSS is loaded

## Performance Considerations

### Server-Side Detection
Device detection happens on server (PHP level):
- No JavaScript required
- Fast detection
- Works with disabled JavaScript

### Layout Caching
Consider caching layout preference:
```php
// In BaseController or middleware
if (!session()->has('layout_preference')) {
    session()->set('layout_preference', get_device_layout());
}
```

## Best Practices After Migration

1. **Monitor Analytics**: Track mobile vs desktop usage
2. **Gather Feedback**: Ask users about their experience
3. **Optimize Assets**: Consider lazy loading for mobile
4. **Regular Testing**: Test on real devices periodically
5. **Update Documentation**: Keep this guide updated

## Support & Troubleshooting

### Get Device Info
```php
// In your view or controller
echo "Device Type: " . get_device_type();
echo "Is Mobile: " . (is_mobile_device() ? 'Yes' : 'No');
echo "Is Tablet: " . (is_tablet_device() ? 'Yes' : 'No');
```

### Debug Layout Selection
```php
// Add to view for debugging
<?php if (ENVIRONMENT === 'development'): ?>
<div class="fixed bottom-4 right-4 bg-black text-white p-2 rounded text-xs">
    Layout: <?= basename(get_device_layout()) ?><br>
    Device: <?= get_device_type() ?>
</div>
<?php endif; ?>
```

## Special Case: Auth Layout

### Why Auth Views Don't Need Migration

Authentication pages (`app/Views/auth/*.php`) use `templates/auth_layout.php` and should **NOT** be migrated to desktop/mobile layout system.

**Key Differences**:

| Feature | Auth Layout | Main Layout (Desktop/Mobile) |
|---------|-------------|------------------------------|
| Navigation | ❌ None | ✅ Full navigation system |
| Sidebar | ❌ None | ✅ Desktop sidebar |
| Bottom Nav | ❌ None | ✅ Mobile bottom nav |
| Design | Centered card | Full-width application |
| Purpose | Authentication | Application features |
| Responsive | ✅ Already responsive | ✅ Device-specific |

### Auth Layout Features

`templates/auth_layout.php` provides:
- ✅ Centered card design (standard for auth pages)
- ✅ Gradient background
- ✅ Fully responsive with Tailwind utilities
- ✅ Clean, minimal interface
- ✅ Auto-hide alerts
- ✅ fadeInUp animation
- ✅ Mobile-friendly forms

### When to Use Each Layout

```php
// ✅ Use auth_layout for authentication pages
<?= $this->extend('templates/auth_layout') ?>
// Files: login.php, forgot_password.php, reset_password.php, change_password.php

// ✅ Use get_device_layout() for application pages
<?= $this->extend(get_device_layout()) ?>
// Files: dashboard.php, absensi/*.php, jurnal/*.php, etc.

// ✅ Use print_layout for print pages
<?= $this->extend('templates/print_layout') ?>
// Files: print reports, PDFs, etc.
```

### Testing Auth Pages

When testing authentication pages, verify:

**Desktop (Chrome, Firefox, Edge, Safari)**:
- [ ] Centered card appears correctly
- [ ] Form fields are properly sized
- [ ] Gradient background displays
- [ ] Validation errors show properly
- [ ] Links are clickable
- [ ] Responsive breakpoints work

**Mobile (iOS Safari, Chrome Android)**:
- [ ] Card width adjusts for small screens
- [ ] Touch targets are 44px minimum
- [ ] Keyboard doesn't obscure form
- [ ] Auto-focus works properly
- [ ] Password visibility toggle (if implemented)
- [ ] "Remember me" checkbox is tappable

**Tablets (iPad, Android tablets)**:
- [ ] Layout scales appropriately
- [ ] Not too wide or too narrow
- [ ] Portrait and landscape modes work

### Optional Auth Enhancements

If you want to improve auth pages in the future:

1. **Larger Touch Targets** (Mobile UX)
   ```css
   /* Ensure minimum 44px touch targets */
   .auth-input { min-height: 44px; }
   .auth-button { min-height: 48px; }
   ```

2. **Password Visibility Toggle**
   ```html
   <button type="button" class="toggle-password">
       <i class="fas fa-eye"></i>
   </button>
   ```

3. **Loading States**
   ```javascript
   button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
   ```

4. **Accessibility Improvements**
   ```html
   <label for="username" aria-label="Username">Username</label>
   <input id="username" aria-required="true" aria-invalid="false">
   ```

### Auth Layout vs Device Layout Summary

**Don't migrate auth views** because:
1. They have different requirements (no navigation)
2. Current implementation is production-ready
3. Centered card design is best practice
4. No benefit from device-specific layouts
5. Maintenance complexity increases without value

**Focus migration efforts** on:
- Dashboard pages (benefit from device layouts)
- Absensi/Jurnal pages (heavy mobile usage)
- Admin pages (desktop-optimized tables)

---

## Timeline Recommendation

### Week 1: Preparation & Testing
- Day 1-2: Test new layouts with example page
- Day 3-4: ~~Migrate and test authentication pages~~ **SKIP** (use auth_layout)
- Day 5: Migrate and test dashboards

### Week 2: Main Features
- Day 1-2: Migrate absensi pages
- Day 3-4: Migrate jurnal and jadwal pages
- Day 5: Testing and bug fixes

### Week 3: Admin & Reports
- Day 1-3: Migrate admin management pages
- Day 4-5: Migrate report pages

### Week 4: Final Testing & Launch
- Day 1-3: Comprehensive testing
- Day 4: Fix remaining issues
- Day 5: Deploy to production

---

**Last Updated**: 2026-01-16  
**Version**: 1.0.0
