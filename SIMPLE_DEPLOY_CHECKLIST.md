# ✅ Simple Deploy Checklist - SIMACCA

**Last Updated:** 2026-01-14  
**All Issues Fixed - Ready to Deploy!**

---

## 🎯 Upload 8 Files - Step by Step

### Part 1: Upload to simaccaProject Folder

**Navigate:** `/home2/smknbone/simaccaProject/`

1. **Upload these 5 files:**
   - [ ] `app/Helpers/component_helper.php` → overwrite
   - [ ] `app/Views/templates/auth_layout.php` → overwrite
   - [ ] `app/Views/templates/main_layout.php` → overwrite
   - [ ] `app/Config/Paths.php` → overwrite
   - [ ] `.env.production` → upload then continue below...

2. **Special steps for .env file:**
   - [ ] Right-click `.env.production` → **Rename** → `.env`
   - [ ] Right-click `.env` → **Change Permissions** → `600`
   - [ ] Done!

### Part 2: Upload to simacca_public Folder

**Navigate:** `/home2/smknbone/simacca_public/`

3. **Upload these 3 files:**
   - [ ] `public/index.php` → overwrite
   - [ ] `public/connection-test.php` → overwrite
   - [ ] `public/diagnostic.php` → new file

---

## 🧪 Test Everything

### Test 1: Diagnostic
- [ ] Visit: `https://simacca.smkn8bone.sch.id/diagnostic.php`
- [ ] Check all files show `"exists": true`

### Test 2: Connection Test
- [ ] Visit: `https://simacca.smkn8bone.sch.id/connection-test.php`
- [ ] Should show: `"overall": "HEALTHY"`
- [ ] All tests should be `"status": "PASS"`

### Test 3: Website
- [ ] Visit: `https://simacca.smkn8bone.sch.id`
- [ ] Should show: **Login Page** (NO HTTP 500!)
- [ ] Try login
- [ ] Should work!

---

## 🧹 Cleanup (IMPORTANT!)

**Delete these test files from server:**

**Navigate:** `/home2/smknbone/simacca_public/`

- [ ] Delete: `diagnostic.php`
- [ ] Delete: `connection-test.php`

**Why?** They expose system information!

---

## 🆘 If Something Goes Wrong

### Still HTTP 500?

**Check:**
1. Did you rename `.env.production` to `.env`?
2. Is `.env` permission set to `600`?
3. Does `writable/session/` folder exist?
4. Run diagnostic.php - which file is missing?

### Session Error?

**Check:**
1. Does `/home2/smknbone/simaccaProject/writable/session/` exist?
2. Create it: New Folder → `session`
3. Set permission: `775`

### Database Error?

**Check:**
1. Open `.env` file
2. Verify database credentials are correct:
   ```
   database.default.database = smknbone_simacca_database
   database.default.username = smknbone_simacca_user
   database.default.password = gi2Bw~,_bU+8
   ```

---

## ✅ Success = All These Work:

- [x] diagnostic.php shows all files exist
- [x] connection-test.php shows HEALTHY
- [x] Website shows login page
- [x] Can login successfully
- [x] Session works
- [x] No HTTP 500 errors

---

## 📞 Need Help?

**Read these docs:**
1. **FINAL_DEPLOYMENT_GUIDE.md** - Complete guide
2. **ENV_FILE_RULES.md** - About .env files
3. **SESSION_PATH_FIX.md** - Session issues
4. **DEPLOYMENT_UPDATE.md** - Latest changes

**Still stuck?** Check cPanel → Errors log

---

## 🎉 That's It!

**8 files → Upload → Test → Delete test files → Done!**

**Estimated time:** 15-20 minutes

---

**Status:** ✅ READY TO DEPLOY
