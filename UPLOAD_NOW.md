# 🚀 UPLOAD NOW - Final Checklist

**All 7 Issues Fixed - Ready for Production!**

---

## 📦 8 Files to Upload

### To: `/home2/smknbone/simaccaProject/app/Helpers/`
- [x] **component_helper.php** ⬅️ UPDATED (added modal_scripts)

### To: `/home2/smknbone/simaccaProject/app/Views/templates/`
- [x] **auth_layout.php**
- [x] **main_layout.php**

### To: `/home2/smknbone/simaccaProject/app/Config/`
- [x] **Paths.php**

### To: `/home2/smknbone/simaccaProject/`
- [x] **.env.production** → RENAME to **.env** + chmod 600

### To: `/home2/smknbone/simacca_public/`
- [x] **index.php**
- [x] **connection-test.php**
- [x] **diagnostic.php**

---

## ⚡ Quick Upload Steps

### 1. cPanel File Manager
Login → File Manager

### 2. Upload to simaccaProject
Navigate: `/home2/smknbone/simaccaProject/`

**Upload files:**
- Drag `app/Helpers/component_helper.php`
- Drag `app/Views/templates/auth_layout.php`
- Drag `app/Views/templates/main_layout.php`
- Drag `app/Config/Paths.php`
- Drag `.env.production`

**Special: Rename .env**
- Right-click `.env.production` → Rename → `.env`
- Right-click `.env` → Change Permissions → `600`

### 3. Upload to simacca_public
Navigate: `/home2/smknbone/simacca_public/`

**Upload files:**
- Drag `public/index.php`
- Drag `public/connection-test.php`
- Drag `public/diagnostic.php`

### 4. Create session folder (if not exists)
Navigate: `/home2/smknbone/simaccaProject/writable/`
- If `session/` folder doesn't exist: New Folder → `session`
- Right-click `session` → Change Permissions → `775`

---

## 🧪 Test (3 URLs)

### 1. Diagnostic
```
https://simacca.smkn8bone.sch.id/diagnostic.php
```
**Check:** All files show `"exists": true`

### 2. Connection Test
```
https://simacca.smkn8bone.sch.id/connection-test.php
```
**Check:** `"overall": "HEALTHY"`

### 3. Website
```
https://simacca.smkn8bone.sch.id
```
**Check:** Login page loads (NO HTTP 500!)

---

## 🧹 Cleanup

**Delete from `/home2/smknbone/simacca_public/`:**
- ❌ diagnostic.php
- ❌ connection-test.php

---

## ✅ Success = 

- [x] Website loads
- [x] Can login
- [x] Dashboard works
- [x] Modals work
- [x] No errors

---

## 🆘 If Error

**"Call to undefined function modal_scripts()"**
→ Did you upload the UPDATED `component_helper.php`?

**"HTTP 500"**
→ Check diagnostic.php - which file is missing?

**"Session error"**
→ Does `writable/session/` folder exist with 775 permission?

**".env not found"**
→ Did you rename `.env.production` to `.env`?

---

## 🎉 That's It!

**Time: 15-20 minutes**
**Difficulty: Easy**
**Risk: Low**

---

**Status:** ✅ READY  
**Go ahead and upload!** 🚀
