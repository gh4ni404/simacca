# .env File Rules & Common Mistakes

## ⚠️ Critical Rule: NO PHP Code in .env Files!

**.env files are PLAIN TEXT files, NOT PHP files!**

---

## ❌ WRONG - Don't Do This

### 1. Using PHP Constants
```ini
# ❌ WRONG - PHP constants don't work in .env
session.savePath = WRITEPATH . 'session'
logger.path = WRITEPATH . 'logs/'
app.baseURL = ROOTPATH . '/public'
```

### 2. Using PHP Concatenation
```ini
# ❌ WRONG - PHP concatenation doesn't work
database.default.hostname = 'localhost' . ':3306'
app.url = 'https://' . 'example.com'
```

### 3. Using PHP Functions
```ini
# ❌ WRONG - PHP functions don't work
encryption.key = base64_encode(random_bytes(32))
app.timezone = date_default_timezone_get()
```

### 4. Using PHP Variables
```ini
# ❌ WRONG - PHP variables don't work
$baseUrl = 'http://localhost'
app.baseURL = $baseUrl
```

---

## ✅ CORRECT - Do This

### 1. Use Literal Strings
```ini
# ✅ CORRECT - Plain text string
app.baseURL = 'https://simacca.smkn8bone.sch.id/'
database.default.hostname = 'localhost'
```

### 2. Use Numbers Directly
```ini
# ✅ CORRECT - Plain numbers
logger.threshold = 4
session.expiration = 28800
database.default.port = 3306
```

### 3. Use Booleans as Strings
```ini
# ✅ CORRECT - String 'true' or 'false'
app.forceGlobalSecureRequests = true
database.default.DBDebug = false
```

### 4. Comment Out Lines You Don't Need
```ini
# ✅ CORRECT - Commented out (uses default from Config class)
# session.savePath = null
# logger.path = WRITEPATH . 'logs/'
```

---

## 📋 Common .env Mistakes in SIMACCA

### Mistake 1: session.savePath = null
**Problem:**
```ini
session.savePath = null  # Treated as literal string "null"
```

**Solution:**
```ini
# session.savePath = null  # Comment it out to use default
```

### Mistake 2: logger.path = WRITEPATH . 'logs/'
**Problem:**
```ini
logger.path = WRITEPATH . 'logs/'  # WRITEPATH is not recognized
```

**Solution:**
```ini
# logger.path = WRITEPATH . 'logs/'  # Comment it out
```

### Mistake 3: Using Single vs Double Quotes
**Both work, but be consistent:**
```ini
# ✅ Both OK
app.baseURL = 'https://example.com/'
app.baseURL = "https://example.com/"

# ✅ But don't mix unnecessarily
```

---

## 🎯 How .env Files Work

### 1. Parsed as Key-Value Pairs
```ini
key = value
```

### 2. CodeIgniter Reads These Values
```php
// In Config classes
public string $baseURL = env('app.baseURL', 'http://localhost');
```

### 3. Constants Are Evaluated in PHP, Not .env
```php
// In Config/Logger.php
public string $path = env('logger.path', WRITEPATH . 'logs/');
                                        // ↑ Default value uses WRITEPATH
```

---

## 📖 Correct .env.production for SIMACCA

### Environment
```ini
CI_ENVIRONMENT = production
```

### App
```ini
app.baseURL = 'https://simacca.smkn8bone.sch.id/'
app.forceGlobalSecureRequests = true
app.CSPEnabled = false
```

### Database
```ini
database.default.hostname = localhost
database.default.database = smknbone_simacca_database
database.default.username = smknbone_simacca_user
database.default.password = gi2Bw~,_bU+8
database.default.DBDriver = MySQLi
database.default.port = 3306
database.default.DBDebug = false
database.default.pConnect = true
```

### Encryption
```ini
# Generate with: php spark key:generate
encryption.key = YOUR_GENERATED_KEY_HERE
```

### Session
```ini
session.driver = 'CodeIgniter\Session\Handlers\FileHandler'
# session.savePath = null  # Comment out!
session.cookieName = 'simacca_session'
session.expiration = 28800
session.cookieDomain = '.smkn8bone.sch.id'
session.cookieSecure = true
session.cookieHTTPOnly = true
session.cookieSameSite = 'Lax'
```

### Logging
```ini
logger.threshold = 4
# logger.path = WRITEPATH . 'logs/'  # Comment out!
```

### Cache
```ini
cache.handler = 'file'
cache.prefix = 'simacca_'
cache.ttl = 300
```

---

## 🔍 How to Check Your .env File

### 1. Look for PHP-like Syntax
```bash
# Search for PHP constants (should NOT be in .env uncommented)
grep -E "WRITEPATH|APPPATH|ROOTPATH|FCPATH" .env
```

### 2. Look for Concatenation
```bash
# Search for dot concatenation (should NOT be in .env)
grep "\." .env | grep -v "#"
```

### 3. Look for PHP Functions
```bash
# Search for function calls (should NOT be in .env)
grep "(" .env | grep -v "#" | grep -v "://"
```

---

## 🛠️ Fixing Common Errors

### Error: "Unable to create file null/..."
**Cause:**
```ini
session.savePath = null
```

**Fix:**
```ini
# session.savePath = null
```

### Error: "WRITEPATH not defined"
**Cause:**
```ini
logger.path = WRITEPATH . 'logs/'
```

**Fix:**
```ini
# logger.path = WRITEPATH . 'logs/'
```

### Error: "Unexpected token"
**Cause:**
```ini
app.url = 'http://' . 'example.com'
```

**Fix:**
```ini
app.url = 'http://example.com'
```

---

## 📝 Best Practices

### 1. Keep .env Simple
```ini
# ✅ GOOD - Simple key-value pairs
app.baseURL = 'https://example.com/'
database.default.hostname = localhost
```

### 2. Use Comments
```ini
# ✅ GOOD - Explain what values mean
# Session expiration in seconds (8 hours = 28800)
session.expiration = 28800
```

### 3. Group Related Settings
```ini
#--------------------------------------------------------------------
# DATABASE CONFIGURATION
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = mydb
```

### 4. Don't Commit .env to Git
```gitignore
# .gitignore
.env
.env.production
.env.local
```

### 5. Provide .env.example Template
```ini
# .env.example - Safe to commit
app.baseURL = 'http://localhost:8080'
database.default.hostname = localhost
database.default.database = your_database
database.default.username = your_username
database.default.password = your_password
```

---

## ✅ Quick Validation Checklist

Before deploying .env file:

- [ ] No PHP constants (WRITEPATH, APPPATH, etc.)
- [ ] No PHP concatenation (. operator)
- [ ] No PHP functions (base64_encode, etc.)
- [ ] All values are plain strings, numbers, or booleans
- [ ] Sensitive values (passwords, keys) are set correctly
- [ ] Comments explain non-obvious settings
- [ ] File permissions will be 600 on server
- [ ] File named exactly `.env` (not .env.production)

---

## 🎓 Understanding Config Priority

CodeIgniter reads configuration in this order:

1. **Config Class Default Values**
   ```php
   // app/Config/App.php
   public string $baseURL = 'http://localhost:8080/';
   ```

2. **.env File (if value set)**
   ```ini
   app.baseURL = 'https://production.com/'
   ```

3. **Final Value = .env overrides Config class**

So if you comment out a value in .env, it will use the Config class default!

---

## 📞 Summary

**Golden Rule:** **.env files are NOT PHP files!**

- ✅ Use plain text values
- ✅ Comment out lines you don't need
- ❌ Don't use PHP constants
- ❌ Don't use PHP functions
- ❌ Don't use PHP concatenation

**When in doubt:** Comment it out and let Config class use its default!

---

**Last Updated:** 2026-01-14  
**Application:** SIMACCA  
**Issue:** HTTP 500 from PHP constants in .env
