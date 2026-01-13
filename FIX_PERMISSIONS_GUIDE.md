# Permission Fix Guide for Production Server

## Issue Summary
The connection test detected that the following directories are not writable:
- `/writable` (main directory)
- `/writable/session`
- `/writable/uploads`
- `/writable/logs`

This will cause session handling, file uploads, and logging to fail.

## Root Cause
When files are uploaded via FTP or deployed, the file ownership and permissions may not be set correctly for the web server to write to these directories.

## Solution for cPanel/Shared Hosting

### Option 1: Using cPanel File Manager (Recommended)

1. **Login to cPanel**
   - URL: https://smkn8bone.sch.id:2083 (or your hosting provider's cPanel URL)

2. **Navigate to File Manager**
   - Go to: `public_html/simacca` (or wherever your application is deployed)

3. **Fix Permissions for writable directory:**
   - Right-click on the `writable` folder
   - Select "Change Permissions"
   - Set permissions to `755` (or `775` if 755 doesn't work)
   - **Important:** Check the box "Recurse into subdirectories"
   - Click "Change Permissions"

4. **Verify subdirectories:**
   - Ensure these directories exist and have proper permissions:
     - `writable/session` → 755 or 775
     - `writable/uploads` → 755 or 775
     - `writable/logs` → 755 or 775
     - `writable/cache` → 755 or 775

### Option 2: Using SSH/Terminal (If you have SSH access)

```bash
# Navigate to your application directory
cd /home2/smknbone/simacca_public

# Set permissions for writable directory and all subdirectories
chmod -R 755 writable/

# If 755 doesn't work, try 775
chmod -R 775 writable/

# Verify permissions
ls -la writable/
```

### Option 3: Create .htaccess for directory permissions

Add this to `writable/.htaccess` (should already exist):

```apache
<IfModule authz_core_module>
    Require all denied
</IfModule>
<IfModule !authz_core_module>
    Deny from all
</IfModule>
```

## Understanding Permission Codes

- **755** = Owner can read/write/execute, Group and Others can read/execute
  - Owner: rwx (7)
  - Group: r-x (5)
  - Others: r-x (5)

- **775** = Owner and Group can read/write/execute, Others can read/execute
  - Owner: rwx (7)
  - Group: rwx (7)
  - Others: r-x (5)

## Troubleshooting

### If permissions still fail after setting to 755:

1. **Try 775 permissions:**
   ```bash
   chmod -R 775 writable/
   ```

2. **Check directory ownership:**
   ```bash
   ls -la
   # The writable directory should be owned by your cPanel user or the web server user
   ```

3. **If ownership is wrong, fix it (requires appropriate permissions):**
   ```bash
   # Replace 'username' with your actual cPanel username
   chown -R username:username writable/
   ```

### If you see "logs" directory missing:

```bash
# Create the logs directory if it doesn't exist
mkdir -p writable/logs
chmod 755 writable/logs
```

## Verification Steps

After fixing permissions:

1. **Re-run the connection test:**
   - URL: `https://simacca.smkn8bone.sch.id/connection-test.php`
   - All permission tests should now show "PASS"

2. **Test the application:**
   - Try logging in
   - Upload a file (if applicable)
   - Check that sessions work properly

3. **Check error logs:**
   - Look for any permission-related errors in:
     - cPanel Error Log
     - `writable/logs/` directory (if any log files were created)

## Security Notes

⚠️ **Important Security Considerations:**

1. **Never use 777 permissions** - This makes directories writable by everyone and is a security risk
2. **The writable directory should NOT be web-accessible** - The .htaccess file should block direct access
3. **Delete connection-test.php after testing** - It contains sensitive information about your setup
4. Use 755 when possible, only use 775 if 755 doesn't work

## Common cPanel Hosting Permission Values

| Permission | Use Case | Security Level |
|------------|----------|----------------|
| 755 | Directories (preferred) | High |
| 775 | Directories (if 755 fails) | Medium |
| 644 | PHP files | High |
| 600 | Config files with passwords | Very High |

## Quick Command Reference

```bash
# Check current permissions
ls -la writable/

# Fix all writable directories at once
chmod -R 755 writable/

# Create missing directories
mkdir -p writable/logs writable/cache writable/session writable/uploads

# Set permissions after creating directories
chmod -R 755 writable/

# Verify the fix
ls -la writable/
```

## Contact Your Hosting Provider If:

- You cannot change permissions via cPanel File Manager
- You don't have SSH access and the File Manager doesn't work
- Permissions revert back after changing them
- You need the web server user/group information

Most hosting providers can quickly fix permission issues for you.

## After Fixing

Once all tests pass:

1. ✅ Delete `connection-test.php` from your public directory
2. ✅ Test your application thoroughly
3. ✅ Monitor the error logs for any new issues
4. ✅ Consider setting up automated backups of the writable directory

---

**Last Updated:** 2026-01-14
**Application:** SIMACCA - Student Attendance Management System
**Server:** smkn8bone.sch.id (cPanel)
