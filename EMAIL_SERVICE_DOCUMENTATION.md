# 📧 Email Service Configuration - SIMACCA

**Status:** ✅ **COMPLETED** (2026-01-15)

## 📋 Overview

Implementasi lengkap email service untuk SIMACCA dengan fitur password reset, welcome email, dan notifikasi sistem.

---

## 🎯 Features Implemented

### ✅ Password Reset System
- ✅ Forgot password flow dengan email verification
- ✅ Secure token generation dengan SHA-256 hashing
- ✅ Token expiration (1 hour validity)
- ✅ One-time use tokens
- ✅ Email template dengan branded design

### ✅ Email Templates
- ✅ Password reset email
- ✅ Welcome email untuk user baru
- ✅ General notification email
- ✅ Email configuration test template
- ✅ Responsive email layout dengan branding SIMACCA

### ✅ Security Features
- ✅ Token stored as hashed (SHA-256)
- ✅ Token expiration validation
- ✅ One-time token usage enforcement
- ✅ Email enumeration protection
- ✅ Automatic token cleanup

### ✅ Helper Functions
- ✅ `send_email()` - Generic email sending
- ✅ `send_password_reset_email()` - Password reset
- ✅ `send_welcome_email()` - New user welcome
- ✅ `send_notification_email()` - General notifications
- ✅ `test_email_configuration()` - Test email setup

### ✅ CLI Commands
- ✅ `php spark email:test [email]` - Test email configuration
- ✅ `php spark token:cleanup` - Clean expired tokens

---

## 📁 Files Created/Modified

### New Files Created (14 files)

#### 1. Database Migration
```
app/Database/Migrations/2026-01-15-031500_CreatePasswordResetTokensTable.php
```
- Table: `password_reset_tokens`
- Fields: id, email, token, created_at, expires_at, used_at
- Indexes: email, token, expires_at

#### 2. Model
```
app/Models/PasswordResetTokenModel.php
```
- Token creation with expiration
- Token verification
- Mark token as used
- Cleanup expired/used tokens

#### 3. Helper
```
app/Helpers/email_helper.php
```
- Generic email functions
- Password reset email
- Welcome email
- Notification email
- Test email configuration

#### 4. Email Templates (5 files)
```
app/Views/emails/email_layout.php       - Base layout
app/Views/emails/password_reset.php     - Password reset
app/Views/emails/welcome.php            - Welcome new user
app/Views/emails/notification.php       - General notification
app/Views/emails/test.php               - Test email
```

#### 5. Auth Views
```
app/Views/auth/reset_password.php       - Reset password form
```

#### 6. CLI Commands (2 files)
```
app/Commands/EmailTest.php              - Test email configuration
app/Commands/TokenCleanup.php           - Clean expired tokens
```

### Modified Files (4 files)

#### 1. AuthController
```
app/Controllers/AuthController.php
```
- Complete `processForgotPassword()` implementation
- Complete `processResetPassword()` implementation
- Added PasswordResetTokenModel
- Email helper integration

#### 2. Email Config
```
app/Config/Email.php
```
- Constructor to load from .env variables
- Dynamic configuration support

#### 3. Autoload Config
```
app/Config/Autoload.php
```
- Auto-load email helper

#### 4. Environment Config
```
.env.production
```
- Complete email configuration section
- SMTP settings for Gmail/Outlook/Yahoo
- Configuration notes and instructions

---

## ⚙️ Configuration Guide

### Step 1: Copy Environment File

```bash
cp .env.production .env
```

### Step 2: Configure Email Settings

Edit `.env` file and update email section:

```env
#--------------------------------------------------------------------
# EMAIL CONFIGURATION
#--------------------------------------------------------------------
email.fromEmail = noreply@smkn8bone.sch.id
email.fromName = SIMACCA - SMK Negeri 8 Bone
email.protocol = smtp

# SMTP Configuration
email.SMTPHost = smtp.gmail.com
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password-here
email.SMTPPort = 587
email.SMTPCrypto = tls
email.mailType = html
```

### Step 3: Gmail Configuration

**For Gmail SMTP:**

1. **Enable 2-Step Verification**
   - Go to: https://myaccount.google.com/security
   - Enable 2-Step Verification

2. **Generate App Password**
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" and your device
   - Copy the 16-character password
   - Use this in `email.SMTPPass`

3. **Update Settings**
   ```env
   email.SMTPHost = smtp.gmail.com
   email.SMTPUser = your-email@gmail.com
   email.SMTPPass = xxxx xxxx xxxx xxxx  # App Password
   email.SMTPPort = 587
   email.SMTPCrypto = tls
   ```

### Step 4: Alternative SMTP Providers

**Outlook/Office365:**
```env
email.SMTPHost = smtp.office365.com
email.SMTPUser = your-email@outlook.com
email.SMTPPass = your-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

**Yahoo:**
```env
email.SMTPHost = smtp.mail.yahoo.com
email.SMTPUser = your-email@yahoo.com
email.SMTPPass = your-app-password
email.SMTPPort = 587
email.SMTPCrypto = tls
```

**Custom SMTP:**
```env
email.SMTPHost = mail.yourdomain.com
email.SMTPUser = noreply@yourdomain.com
email.SMTPPass = your-password
email.SMTPPort = 587 or 465
email.SMTPCrypto = tls or ssl
```

### Step 5: Test Email Configuration

```bash
php spark email:test admin@example.com
```

Expected output:
```
Sending test email...
Recipient: admin@example.com

✓ Email test berhasil dikirim ke admin@example.com

Email configuration is working correctly!
```

---

## 🔧 Usage Examples

### 1. Send Password Reset Email

```php
// In controller
helper('email');

$email = 'user@example.com';
$token = $passwordResetTokenModel->createToken($email);
$username = 'john_doe';

$result = send_password_reset_email($email, $token, $username);

if ($result) {
    echo "Password reset email sent!";
}
```

### 2. Send Welcome Email

```php
helper('email');

$email = 'newuser@example.com';
$username = 'new_user';
$temporaryPassword = 'Temp123!';
$role = 'guru_mapel';

send_welcome_email($email, $username, $temporaryPassword, $role);
```

### 3. Send Notification Email

```php
helper('email');

$email = 'teacher@example.com';
$subject = 'Reminder: Isi Jurnal KBM';
$title = 'Reminder Jurnal';
$content = '<p>Anda belum mengisi jurnal KBM untuk hari ini.</p>';

send_notification_email($email, $subject, $title, $content);
```

### 4. Generic Email Sending

```php
helper('email');

$to = 'admin@example.com';
$subject = 'Test Email';
$message = '<h1>Hello World</h1><p>This is a test email.</p>';

$options = [
    'cc' => ['manager@example.com'],
    'bcc' => ['archive@example.com'],
    'attachments' => [WRITEPATH . 'uploads/document.pdf']
];

$result = send_email($to, $subject, $message, $options);
```

---

## 🔐 Security Best Practices

### 1. Token Security
- ✅ Tokens are hashed using SHA-256 before storage
- ✅ Plain token sent via email (never stored)
- ✅ 1 hour expiration time
- ✅ One-time use enforcement
- ✅ Automatic cleanup of expired tokens

### 2. Email Enumeration Protection
```php
// Don't reveal if email exists
if (!$user) {
    // Generic success message
    session()->setFlashdata('success', 'Kalau email terdaftar, instruksi reset sudah dikirim');
    return redirect()->to('/login');
}
```

### 3. Password Requirements
- Minimum 6 characters
- Password confirmation required
- Secure hashing with `PASSWORD_DEFAULT`

### 4. Error Handling
- Exceptions logged to error log
- Generic error messages to users
- No sensitive information in error messages

---

## 🔄 Password Reset Flow

### User Flow
```
1. User clicks "Lupa Password?"
   ↓
2. Enter email address
   ↓
3. System checks if email exists
   ↓
4. Generate secure token (if email exists)
   ↓
5. Send email with reset link
   ↓
6. User clicks link in email
   ↓
7. System verifies token (valid, not expired, not used)
   ↓
8. User enters new password
   ↓
9. System updates password & marks token as used
   ↓
10. User can login with new password
```

### System Flow Diagram
```
┌─────────────────┐
│ Forgot Password │
│      Page       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Enter Email     │
│ Validate Input  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐      No      ┌──────────────────┐
│ Email Exists?   │─────────────>│ Generic Success  │
└────────┬────────┘              │ Message (Security)│
         │ Yes                    └──────────────────┘
         ▼
┌─────────────────┐
│ Generate Token  │
│ (SHA-256 Hash)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Store in DB     │
│ expires_at: +1h │
└────────┬────────┘
         │
         ▼
┌─────────────────┐      Failed    ┌──────────────────┐
│ Send Email      │───────────────>│ Log Error        │
└────────┬────────┘                │ Show Error Msg   │
         │ Success                  └──────────────────┘
         ▼
┌─────────────────┐
│ User Gets Email │
│ Clicks Link     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Verify Token    │
│ - Exists?       │
│ - Not expired?  │
│ - Not used?     │
└────────┬────────┘
         │ Valid
         ▼
┌─────────────────┐
│ Reset Password  │
│ Page            │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Enter New Pass  │
│ Validate        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Update Password │
│ Mark Token Used │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Redirect Login  │
│ Success Message │
└─────────────────┘
```

---

## 🗄️ Database Schema

### password_reset_tokens Table

```sql
CREATE TABLE `password_reset_tokens` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(255) NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `expires_at` DATETIME DEFAULT NULL,
    `used_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `email` (`email`),
    KEY `token` (`token`),
    KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Fields Description

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key |
| `email` | VARCHAR(255) | User's email address |
| `token` | VARCHAR(255) | SHA-256 hashed token |
| `created_at` | DATETIME | Token creation timestamp |
| `expires_at` | DATETIME | Token expiration timestamp (created_at + 1 hour) |
| `used_at` | DATETIME | When token was used (NULL if unused) |

---

## 🔧 Maintenance

### 1. Token Cleanup (Automated)

**Setup Cron Job:**

```bash
# Add to crontab (crontab -e)
# Clean up tokens daily at 3 AM
0 3 * * * cd /path/to/simacca && php spark token:cleanup >> /path/to/logs/token-cleanup.log 2>&1
```

**Manual Cleanup:**

```bash
php spark token:cleanup
```

**Output:**
```
Starting token cleanup...
Cleaned up 5 expired token(s).
Cleaned up 3 used token(s).

Total tokens cleaned: 8
Token cleanup completed!
```

### 2. Email Logs Monitoring

Check email sending logs:

```bash
tail -f writable/logs/log-2026-01-15.log | grep -i email
```

### 3. Test Email Regularly

```bash
# Test monthly or after configuration changes
php spark email:test admin@smkn8bone.sch.id
```

---

## 🐛 Troubleshooting

### Problem: Email not sending

**Check 1: SMTP Credentials**
```php
// Verify in .env
email.SMTPUser = correct-email@gmail.com
email.SMTPPass = correct-app-password
```

**Check 2: Firewall/Port**
```bash
# Test SMTP connection
telnet smtp.gmail.com 587
```

**Check 3: Enable Debug**
```php
// In app/Config/Email.php temporarily
public bool $SMTPDebug = 2; // Add this for debugging
```

**Check 4: Check Logs**
```bash
tail -f writable/logs/log-*.log
```

### Problem: Token expired too quickly

**Solution: Extend expiration time**
```php
// In app/Models/PasswordResetTokenModel.php
// Line ~38
'expires_at' => date('Y-m-d H:i:s', strtotime('+2 hours')), // Change from +1 hour
```

### Problem: Gmail blocks login

**Solution:**
1. Enable 2-Step Verification
2. Use App Password (not account password)
3. Check: https://myaccount.google.com/lesssecureapps (should be OFF)
4. Try: https://accounts.google.com/DisplayUnlockCaptcha

### Problem: Email goes to spam

**Solution:**
1. Setup SPF record for domain
2. Setup DKIM signing
3. Use proper from email (noreply@yourdomain.com)
4. Don't use spam keywords
5. Test with: https://www.mail-tester.com/

---

## 📊 Performance & Scalability

### Email Queue (Future Enhancement)

For high-volume email sending:

```php
// Use CodeIgniter Queue or Laravel Queue
// app/Libraries/EmailQueue.php (future)

class EmailQueue {
    public function queue($to, $subject, $message) {
        // Add to queue instead of immediate send
    }
    
    public function process() {
        // Process queue in background
    }
}
```

**Cron job for queue processing:**
```bash
* * * * * cd /path/to/simacca && php spark email:queue:process
```

### Rate Limiting

Prevent abuse:

```php
// app/Filters/RateLimitFilter.php (future)
// Limit password reset requests to 3 per hour per IP
```

---

## 📝 API Documentation

### send_email($to, $subject, $message, $options = [])

**Parameters:**
- `$to` (string|array) - Recipient email(s)
- `$subject` (string) - Email subject
- `$message` (string) - Email body (HTML or text)
- `$options` (array) - Optional parameters
  - `cc` (array) - CC recipients
  - `bcc` (array) - BCC recipients
  - `attachments` (array) - File paths to attach

**Returns:** `bool` - Success status

**Example:**
```php
send_email(
    'user@example.com',
    'Test Subject',
    '<p>Test message</p>',
    [
        'cc' => ['manager@example.com'],
        'attachments' => [WRITEPATH . 'file.pdf']
    ]
);
```

### send_password_reset_email($email, $token, $username)

**Parameters:**
- `$email` (string) - Recipient email
- `$token` (string) - Reset token
- `$username` (string) - User's username

**Returns:** `bool` - Success status

### send_welcome_email($email, $username, $temporaryPassword, $role)

**Parameters:**
- `$email` (string) - Recipient email
- `$username` (string) - User's username
- `$temporaryPassword` (string) - Temporary password
- `$role` (string) - User role

**Returns:** `bool` - Success status

### test_email_configuration($to)

**Parameters:**
- `$to` (string) - Test recipient email

**Returns:** `array` - Result with status and message

---

## 🎨 Email Template Customization

### Modify Email Layout

Edit: `app/Views/emails/email_layout.php`

```php
// Change header colors
.email-header {
    background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
}

// Change button colors
.button {
    background-color: #YOUR_BUTTON_COLOR;
}
```

### Add Company Logo

```php
// In email_layout.php header section
<div class="email-header">
    <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" style="height: 50px;">
    <h1>SIMACCA</h1>
</div>
```

### Custom Email Template

Create new template:
```php
// app/Views/emails/custom_notification.php
<?= $this->extend('emails/email_layout') ?>

<?= $this->section('content') ?>
<h2>Custom Notification</h2>
<p><?= $customContent ?></p>
<?= $this->endSection() ?>
```

---

## ✅ Testing Checklist

- [x] Email configuration loaded from .env
- [x] SMTP connection successful
- [x] Test email sent and received
- [x] Password reset email sent
- [x] Password reset token created
- [x] Token expiration works
- [x] Token one-time use enforced
- [x] Password successfully reset
- [x] Welcome email template renders
- [x] Notification email template renders
- [x] Token cleanup command works
- [x] Email test command works
- [x] Error logging functional
- [x] Security measures in place

---

## 📈 Future Enhancements

### Phase 2 (Future)
- [ ] Email queue system for bulk sending
- [ ] Email templates admin panel
- [ ] Email delivery tracking
- [ ] Bounce handling
- [ ] Unsubscribe functionality
- [ ] Email analytics dashboard
- [ ] Multiple language support
- [ ] Email preview before sending
- [ ] Scheduled email sending
- [ ] Email A/B testing

### Phase 3 (Advanced)
- [ ] Integration with SendGrid/Mailgun
- [ ] SMS notification fallback
- [ ] WhatsApp notification integration
- [ ] Push notification support
- [ ] Email template builder (drag & drop)

---

## 👥 Support

**Issues?**
- Check logs: `writable/logs/`
- Run test: `php spark email:test your-email@example.com`
- Check configuration: `.env` email section

**Contact:**
- Developer: Mohd. Abdul Ghani / Dirwan Jaya
- Project: SIMACCA - SMK Negeri 8 Bone

---

## 📄 License

This email service implementation is part of SIMACCA project.

---

**Documentation Version:** 1.0  
**Last Updated:** 2026-01-15  
**Status:** Production Ready ✅
