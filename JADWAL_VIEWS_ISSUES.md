# Issues Found in app/Views/admin/jadwal

## 📋 Summary of Issues

### ✅ Already Fixed (CSRF):
1. **create.php** - getCsrfToken() function added ✅
2. **edit.php** - getCsrfToken() function added ✅

### ⚠️ Issues Found:

---

## 1. ❌ Typo in import.php Line 20

**File:** `app/Views/admin/jadwal/import.php`

**Line 20:**
```php
<li>Hari: Senin, Selasa, Rabu, Kamis, Jumat, </li>
```

**Issue:** Trailing comma dan tidak lengkap (kurang Sabtu jika diperlukan)

**Fix:**
```php
<li>Hari: Senin, Selasa, Rabu, Kamis, Jumat</li>
```

**Severity:** Low (cosmetic)

---

## 2. ⚠️ Inconsistent tahun_ajaran Field

**Files Affected:**
- `create.php` - Uses `<select>` dropdown
- `edit.php` - Uses `<input type="text">` with validation

**Issue:** Inconsistency between create and edit forms

**create.php (Line 161-175):**
```php
<select id="tahun_ajaran" name="tahun_ajaran" ...>
    <option value="">Pilih Tahun Ajaran</option>
    <?php foreach ($tahunAjaranList as $key => $value): ?>
        <option value="<?= $key; ?>">...</option>
    <?php endforeach; ?>
</select>
```

**edit.php (Line 161-171):**
```php
<input type="text" 
    id="tahun_ajaran" 
    name="tahun_ajaran" 
    value="<?= old('tahun_ajaran', $jadwal['tahun_ajaran']); ?>" 
    placeholder="2024/2025" ...>
```

**Recommended Fix:** Use `<select>` in both forms for consistency

**Severity:** Medium (UX inconsistency)

---

## 3. ⚠️ Missing Timepicker Initialization

**Files Affected:**
- `create.php` (Line 108, 124)
- `edit.php` (Line 108, 124)

**Issue:** Forms use class `timepicker` but no initialization script

**HTML:**
```php
<input type="text" 
    id="jam_mulai" 
    name="jam_mulai" 
    class="timepicker ..." 
    required>
```

**JavaScript:** Missing initialization like:
```javascript
// Expected but not found:
$('.timepicker').timepicker({ ... });
```

**Current State:** Users must type time manually (HH:MM:SS format)

**Recommended Fix:** 
- Option 1: Add timepicker library (jQuery timepicker) and init
- Option 2: Change to `<input type="time">` HTML5 input
- Option 3: Keep as is but add better placeholder

**Severity:** Medium (UX issue, but functional)

---

## 4. ⚠️ Missing AJAX Error Feedback

**Files Affected:**
- `create.php` (Line 269-271)
- `edit.php` (Line 264-266)

**Current Code:**
```javascript
.catch(error => {
    console.error('Error checking conflict:', error);
});
```

**Issue:** Error only logged to console, user tidak dapat feedback

**Recommended Fix:**
```javascript
.catch(error => {
    console.error('Error checking conflict:', error);
    // Show user-friendly error message
    alert('Terjadi kesalahan saat mengecek konflik jadwal. Silakan coba lagi.');
    // Or better: show in-page notification
    conflictAlert.classList.remove('hidden');
    conflictMessage.textContent = 'Tidak dapat mengecek konflik jadwal. Silakan coba lagi.';
});
```

**Severity:** Low (edge case)

---

## 5. ✅ CSRF Token - Already Fixed

Both create.php and edit.php already have `getCsrfToken()` function implemented correctly.

---

## 6. ⚠️ Potential XSS in index.php

**File:** `app/Views/admin/jadwal/index.php`

**Lines 190-191:** Complex nested ternary operator for badge colors

**Code:**
```php
<?= $item['hari'] == 'Senin' ? 'bg-red-100 text-red-800' : 
    ($item['hari'] == 'Selasa' ? 'bg-yellow-100 text-yellow-800' : 
        ($item['hari'] == 'Rabu' ? 'bg-green-100 text-green-800' : 
            ($item['hari'] == 'Kamis' ? 'bg-blue-100 text-blue-800' : 
                ($item['hari'] == 'Jumat' ? 'bg-purple-100 text-purple-800' : 
                    'bg-gray-100 text-gray-800')))) ?>
```

**Issue:** 
- Hard to read
- Potential XSS if $item['hari'] is not sanitized
- Not maintainable

**Recommended Fix:** Use helper function or array mapping
```php
<?php
$hariBadgeColors = [
    'Senin' => 'bg-red-100 text-red-800',
    'Selasa' => 'bg-yellow-100 text-yellow-800',
    'Rabu' => 'bg-green-100 text-green-800',
    'Kamis' => 'bg-blue-100 text-blue-800',
    'Jumat' => 'bg-purple-100 text-purple-800',
];
$badgeColor = $hariBadgeColors[$item['hari']] ?? 'bg-gray-100 text-gray-800';
?>
<span class="px-2 py-1 text-xs font-semibold rounded-full <?= esc($badgeColor) ?>">
    <?= esc($item['hari']) ?>
</span>
```

**Severity:** Medium (code quality + potential security)

---

## 7. ℹ️ Missing Validation in import.php

**File:** `app/Views/admin/jadwal/import.php`

**Line 146:** Form submission confirmation

**Current:** Only client-side confirmation

**Recommendation:** Backend validation is essential (should already be in controller)

**Severity:** Low (assuming controller handles validation)

---

## 📊 Priority Matrix

| Issue | Severity | Impact | Effort | Priority |
|-------|----------|--------|--------|----------|
| 1. Typo in import.php | Low | Cosmetic | 1 min | Low |
| 2. Inconsistent tahun_ajaran | Medium | UX | 10 min | **High** |
| 3. Missing timepicker init | Medium | UX | 30 min | **High** |
| 4. Missing AJAX error feedback | Low | UX | 5 min | Medium |
| 5. CSRF Token | ✅ Fixed | - | - | - |
| 6. Complex ternary in index.php | Medium | Code Quality | 10 min | **High** |
| 7. Import validation | Low | Security | 0 min* | Low |

*Assuming controller already validates

---

## 🔧 Recommended Fixes Order

### Quick Wins (< 15 minutes):
1. ✅ Fix typo in import.php
2. ✅ Add AJAX error feedback
3. ✅ Refactor ternary operator in index.php

### Medium Priority (< 30 minutes):
4. ✅ Make tahun_ajaran consistent (use select in edit.php)

### Optional Enhancement:
5. ⚠️ Add timepicker library (or change to HTML5 time input)

---

## 🎯 Execution Plan

1. Fix typo in import.php (1 min)
2. Refactor badge colors in index.php (5 min)
3. Add AJAX error feedback in create.php & edit.php (5 min)
4. Make tahun_ajaran consistent in edit.php (10 min)
5. Document timepicker requirement for future (already typed input works)

Total estimated time: ~25 minutes
