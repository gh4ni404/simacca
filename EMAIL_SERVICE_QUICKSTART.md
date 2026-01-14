# 🚀 Email Service Quick Start Guide

**Quick setup guide for SIMACCA Email Service**

---

## ⚡ 5-Minute Setup

### 1️⃣ Configure Email Settings

Edit `.env` file:

```env
# Email Configuration
email.fromEmail = noreply@smkn8bone.sch.id
email.fromName = SIMACCA - SMK Negeri 8 Bone
email.protocol = smtp

# Gmail SMTP (Recommended)
email.SMTPHost = smtp.gmail.com
email.SMTPUser = your-email@gmail.com
email.SMTPPass = your-app-password-here
email.SMTPPort = 587
email.SMTPCrypto = tls
email.mailType = html
```

### 2️⃣ Gmail App Password Setup

1. Go to: https://myaccount.google.com/security
2. Enable **2-Step Verification**
3. Go to: https://myaccount.google.com/apppasswords
4. Create app password for "Mail"
5. Copy 16-character password
6. Paste in `email.SMTPPass`

### 3️⃣ Run Migration

```bash
php spark migrate
```

### 4️⃣ Test Email

```bash
php spark email:test admin@example.com
```

Expected output:
```
✓ Email test berhasil dikirim ke admin@example.com
Email configuration is working correctly!
```

---

## 🎯 Common Use Cases

### Password Reset

User flow:
1. User clicks "Lupa Password?" on login page
2. User enters email address
3. System sends reset link (valid 1 hour)
4. User clicks link and enters new password
5. User logs in with new password

**URLs:**
- Forgot password: `/forgot-password`
- Reset password: `/reset-password/{token}`

### Send Welcome Email

```php
helper('email');
send_welcome_email(
    'user@example.com',
    'username123',
    'TempPass123',
    'guru_mapel'
);
```

### Send Notification

```php
helper('email');
send_notification_email(
    'teacher@example.com',
    'Reminder: Isi Jurnal',
    'Jurnal KBM Reminder',
    '<p>Jangan lupa isi jurnal KBM hari ini.</p>'
);
```

---

## 🔧 Maintenance

### Daily Token Cleanup (Automated)

Add to crontab:

```bash
# Clean expired tokens daily at 3 AM
0 3 * * * cd /var/www/simacca && php spark token:cleanup
```

Manual cleanup:

```bash
php spark token:cleanup
```

---

## 🐛 Quick Troubleshooting

### Email Not Sending?

**1. Check Credentials**
```bash
# Verify .env file
cat .env | grep email
```

**2. Test Connection**
```bash
php spark email:test your-email@example.com
```

**3. Check Logs**
```bash
tail -f writable/logs/log-$(date +%Y-%m-%d).log
```

### Gmail Issues?

- ✅ Use App Password (not account password)
- ✅ Enable 2-Step Verification
- ✅ Check https://myaccount.google.com/lesssecureapps (should be OFF)

### Email Goes to Spam?

- Use proper from address (noreply@yourdomain.com)
- Setup SPF/DKIM records for your domain
- Test with: https://www.mail-tester.com/

---

## 📚 More Information

See **EMAIL_SERVICE_DOCUMENTATION.md** for:
- Complete configuration guide
- All SMTP providers
- Security best practices
- API documentation
- Troubleshooting guide
- Email template customization

---

## 📞 Support

**Issues?** Check:
1. `.env` configuration
2. Email logs in `writable/logs/`
3. Run `php spark email:test`

**Contact:** SIMACCA Development Team

---

**Last Updated:** 2026-01-15
