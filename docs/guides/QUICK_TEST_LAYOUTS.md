# Quick Test Guide - Desktop & Mobile Layouts

## 🚀 Quick Start Testing

### Step 1: Access Test Pages

```
# Example page with layout switcher
http://localhost:8080/layout/example

# Device information (JSON)
http://localhost:8080/layout/device-info

# Manual layout switching
http://localhost:8080/layout/desktop
http://localhost:8080/layout/mobile
http://localhost:8080/layout/auto
```

### Step 2: Test Desktop View

1. Open browser normally (Chrome, Firefox, Edge)
2. Visit: `http://localhost:8080/layout/example`
3. You should see:
   - ✅ Horizontal navigation bar at top
   - ✅ Logo and menu items in header
   - ✅ User profile with photo on right
   - ✅ Dropdown menus on hover
   - ✅ Wide content area (1400px max)
   - ✅ No bottom navigation

### Step 3: Test Mobile View

#### Option A: Using Chrome DevTools
1. Open Chrome
2. Press `F12` to open DevTools
3. Press `Ctrl+Shift+M` (or click Toggle Device Toolbar icon)
4. Select a mobile device (e.g., iPhone 12, Pixel 5)
5. Visit: `http://localhost:8080/layout/example`
6. You should see:
   - ✅ Sticky top navigation bar
   - ✅ Hamburger menu button (☰)
   - ✅ Bottom navigation bar with icons
   - ✅ Slide-out menu from right
   - ✅ Touch-optimized buttons (larger)
   - ✅ Cards stack vertically

#### Option B: Using Real Device
1. Connect phone to same network
2. Find your computer's IP address
   ```bash
   # Windows
   ipconfig
   
   # Mac/Linux
   ifconfig
   ```
3. Visit: `http://YOUR_IP:8080/layout/example`

### Step 4: Test Layout Switcher

1. Start on desktop view
2. Click "Mobile Layout" button
3. Page should reload with mobile layout
4. Click "Desktop Layout" button
5. Should switch back to desktop layout
6. Click "Auto Detect" button
7. Should use automatic device detection

### Step 5: Test Device Detection

Visit: `http://localhost:8080/layout/device-info`

You should see JSON output like:
```json
{
  "device_type": "desktop",
  "is_mobile": false,
  "is_tablet": false,
  "current_layout": "auto",
  "user_agent": "Mozilla/5.0...",
  "browser": "Chrome",
  "platform": "Windows",
  "version": "120.0"
}
```

## 🧪 Testing Checklist

### Desktop Layout
- [ ] Navigation bar shows horizontally
- [ ] Logo and title visible
- [ ] Menu items visible without clicking
- [ ] Dropdown menus work on hover
- [ ] User profile shows name and role
- [ ] User dropdown works (Profile, Change Password, Logout)
- [ ] Content area is wide (not mobile-squeezed)
- [ ] Buttons have normal size
- [ ] Cards display in grid (multiple columns)
- [ ] Footer shows at bottom

### Mobile Layout
- [ ] Top bar is sticky (stays on scroll)
- [ ] Hamburger menu button visible
- [ ] Clicking hamburger opens slide-out menu
- [ ] Clicking outside menu closes it
- [ ] Bottom navigation bar visible
- [ ] Bottom nav has 4 icons (role-based)
- [ ] Clicking bottom nav items works
- [ ] Active page highlighted in bottom nav
- [ ] Buttons are large enough to tap
- [ ] Cards stack vertically (single column)
- [ ] User profile photo clickable

### Common Features
- [ ] Flash messages display correctly
- [ ] Alert close buttons work
- [ ] Page title displays correctly
- [ ] Role name shows in title/footer
- [ ] Forms are usable
- [ ] Links navigate correctly

## 🐛 Troubleshooting

### Issue: Always showing desktop layout on mobile
**Solution:**
```
1. Visit: /layout/auto
2. Clear browser cache
3. Reload page
4. Check device-info to verify detection
```

### Issue: Menu not appearing
**Solution:**
```
1. Ensure you're logged in
2. Check console for JavaScript errors
3. Verify user has proper role
```

### Issue: Bottom nav not showing
**Solution:**
```
1. Only visible in mobile layout
2. Only visible when logged in
3. Check if using correct layout
```

### Issue: Layout not switching
**Solution:**
```
1. Verify routes are loaded (php spark routes | grep layout)
2. Clear session: /layout/auto
3. Check for PHP errors in logs
```

## 📱 Device Testing Matrix

| Device Type | Browser | Expected Layout | Status |
|-------------|---------|-----------------|--------|
| Desktop | Chrome | Desktop | ⏳ Test |
| Desktop | Firefox | Desktop | ⏳ Test |
| Desktop | Edge | Desktop | ⏳ Test |
| iPhone | Safari | Mobile | ⏳ Test |
| iPhone | Chrome | Mobile | ⏳ Test |
| Android | Chrome | Mobile | ⏳ Test |
| iPad | Safari | Desktop | ⏳ Test |
| Tablet | Chrome | Desktop | ⏳ Test |

## 🎯 Key Features to Test

### Desktop-Specific
- [ ] Hover effects on dropdowns
- [ ] Wide layout (1400px container)
- [ ] Multi-column grids
- [ ] Horizontal navigation

### Mobile-Specific
- [ ] Bottom navigation
- [ ] Slide-out menu
- [ ] Touch targets (44px min)
- [ ] Vertical card stacking
- [ ] Safe area (iPhone X+)

## 📊 Visual Verification

### Desktop View Should Look Like:
```
┌────────────────────────────────────────┐
│ Logo  Menu1  Menu2  Menu3    [Profile]│ ← Horizontal Nav
├────────────────────────────────────────┤
│                                        │
│  [────────────Content Area────────────]│ ← Wide (1400px)
│  [Card1] [Card2] [Card3] [Card4]     │ ← Grid Layout
│                                        │
└────────────────────────────────────────┘
```

### Mobile View Should Look Like:
```
┌──────────────────┐
│ Logo        [☰] │ ← Sticky Top Nav
├──────────────────┤
│                  │
│ [Card 1 ──────] │
│ [Card 2 ──────] │ ← Vertical Stack
│ [Card 3 ──────] │
│                  │
│                  │
├──────────────────┤
│ [🏠][📅][✓][👤]│ ← Bottom Nav
└──────────────────┘
```

## 🔍 Debug Mode

Add this to any view for debugging:

```php
<?php if (ENVIRONMENT === 'development'): ?>
<div class="fixed bottom-20 right-4 bg-black text-white p-3 rounded-lg text-xs z-50">
    <strong>Debug Info:</strong><br>
    Device: <?= get_device_type() ?><br>
    Layout: <?= basename(get_device_layout()) ?><br>
    Mobile: <?= is_mobile_device() ? 'Yes' : 'No' ?><br>
    Tablet: <?= is_tablet_device() ? 'Yes' : 'No' ?>
</div>
<?php endif; ?>
```

## ✅ Success Criteria

Your implementation is successful if:

1. ✅ Desktop view shows horizontal navigation
2. ✅ Mobile view shows bottom navigation
3. ✅ Layout auto-switches based on device
4. ✅ Manual switching works (/layout/desktop, /layout/mobile)
5. ✅ All routes are accessible
6. ✅ No JavaScript errors in console
7. ✅ Both layouts are functional
8. ✅ Helper functions work correctly

## 📝 Next Steps After Testing

1. ✅ If all tests pass, start migrating views
2. ✅ Follow LAYOUT_MIGRATION.md for guidance
3. ✅ Start with authentication and dashboard pages
4. ✅ Test each migrated view thoroughly
5. ✅ Deploy to staging/production when ready

---

**Test Status**: ⏳ Ready for Testing  
**Last Updated**: 2026-01-16
