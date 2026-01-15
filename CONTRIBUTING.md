# 🤝 Contributing to SIMACCA

Terima kasih atas minat Anda untuk berkontribusi ke SIMACCA! Dokumen ini berisi panduan untuk membantu Anda berkontribusi ke project.

---

## 📋 Daftar Isi

1. [Code of Conduct](#-code-of-conduct)
2. [Cara Berkontribusi](#-cara-berkontribusi)
3. [Setup Development Environment](#-setup-development-environment)
4. [Coding Standards](#-coding-standards)
5. [Pull Request Process](#-pull-request-process)
6. [Pelaporan Bug](#-pelaporan-bug)
7. [Feature Request](#-feature-request)

---

## 📜 Code of Conduct

### Our Pledge

Kami berkomitmen untuk membuat project ini menjadi pengalaman yang bebas dari harassment untuk semua orang, terlepas dari:
- Usia
- Ukuran tubuh
- Disabilitas
- Etnisitas
- Gender identity
- Level pengalaman
- Kebangsaan
- Penampilan personal
- Ras
- Agama
- Orientasi seksual

### Expected Behavior

- ✅ Menggunakan bahasa yang ramah dan inklusif
- ✅ Menghormati sudut pandang dan pengalaman yang berbeda
- ✅ Menerima kritik konstruktif dengan baik
- ✅ Fokus pada apa yang terbaik untuk komunitas
- ✅ Menunjukkan empati kepada anggota komunitas lainnya

### Unacceptable Behavior

- ❌ Penggunaan bahasa atau gambar yang bersifat seksual
- ❌ Trolling, komentar menghina, atau serangan personal/politik
- ❌ Public atau private harassment
- ❌ Publishing informasi pribadi orang lain tanpa izin

---

## 🎯 Cara Berkontribusi

Ada banyak cara untuk berkontribusi ke SIMACCA:

### 1. Melaporkan Bug 🐛

Jika Anda menemukan bug, silakan [buat issue](#-pelaporan-bug) dengan detail yang jelas.

### 2. Mengajukan Feature Request 💡

Punya ide untuk fitur baru? [Submit feature request](#-feature-request)!

### 3. Memperbaiki Bug 🔧

- Check [Issues](https://github.com/username/simacca/issues) untuk bug yang perlu diperbaiki
- Look for issues dengan label `good first issue` untuk pemula

### 4. Implementasi Feature ✨

- Check [TODO.md](TODO.md) untuk list feature yang direncanakan
- Diskusikan dengan maintainer sebelum mulai coding

### 5. Improve Documentation 📚

- Perbaiki typo atau error di dokumentasi
- Tambahkan contoh atau penjelasan yang lebih jelas
- Translate dokumentasi ke bahasa lain

### 6. Code Review 👀

- Review pull requests dari contributor lain
- Berikan feedback yang konstruktif

---

## 💻 Setup Development Environment

### Prerequisites

Pastikan Anda sudah install:
- PHP 8.1+
- MySQL 5.7+ atau MariaDB 10.3+
- Composer 2.0+
- Git

📖 Detail lengkap: [REQUIREMENTS.md](REQUIREMENTS.md)

### Setup Steps

```bash
# 1. Fork repository
# Klik tombol "Fork" di GitHub

# 2. Clone fork Anda
git clone https://github.com/YOUR_USERNAME/simacca.git
cd simacca

# 3. Add upstream remote
git remote add upstream https://github.com/ORIGINAL_OWNER/simacca.git

# 4. Install dependencies
composer install

# 5. Setup environment
cp env .env
php spark key:generate

# 6. Configure database di .env
nano .env

# 7. Create database
mysql -u root -p -e "CREATE DATABASE simacca_dev"

# 8. Run migrations dengan dummy data
php spark setup --with-dummy

# 9. Start development server
php spark serve
```

### Verify Setup

```bash
# Test aplikasi
# Buka: http://localhost:8080
# Login: admin / admin123

# Run tests (jika ada)
./vendor/bin/phpunit
```

---

## 📐 Coding Standards

### PHP Coding Style

Kami mengikuti [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.

**Key points:**
- Indentation: 4 spaces (bukan tabs)
- Line length: 120 characters max
- Opening braces `{` on same line untuk class methods
- Use strict types: `declare(strict_types=1);`

**Contoh:**
```php
<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class ExampleController extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Example Page',
            'content' => 'Hello World'
        ];
        
        return view('example/index', $data);
    }
}
```

### CodeIgniter 4 Best Practices

1. **Use Models untuk database operations**
   ```php
   // Good ✅
   $userModel = new UserModel();
   $users = $userModel->findAll();
   
   // Avoid ❌
   $db = \Config\Database::connect();
   $users = $db->query('SELECT * FROM users')->getResult();
   ```

2. **Use Validation**
   ```php
   // Good ✅
   if (!$this->validate($rules)) {
       return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
   }
   ```

3. **Use CSRF Protection**
   ```php
   // Good ✅
   echo csrf_field();
   ```

4. **Sanitize Input**
   ```php
   // Good ✅
   $data = [
       'username' => esc($this->request->getPost('username')),
   ];
   ```

### Database Migrations

**Naming convention:**
```
YYYY-MM-DD-HHMMSS_DescriptionOfMigration.php
```

**Example:**
```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExampleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('example_table');
    }

    public function down()
    {
        $this->forge->dropTable('example_table');
    }
}
```

### Views

- Use CodeIgniter's template system
- Escape output: `<?= esc($variable) ?>`
- Use layouts untuk consistency

### Comments & Documentation

```php
/**
 * Brief description of what this method does
 * 
 * @param string $username Username to check
 * @param int $id Optional user ID to exclude from check
 * @return bool True if username exists, false otherwise
 */
public function isUsernameExists(string $username, int $id = null): bool
{
    // Implementation
}
```

---

## 🔄 Pull Request Process

### Before Submitting PR

- [ ] Code follows project's coding standards
- [ ] All tests pass (jika ada)
- [ ] Documentation updated (jika perlu)
- [ ] Commit messages are clear and descriptive
- [ ] Branch is up-to-date with main branch

### Creating Pull Request

1. **Create feature branch**
   ```bash
   git checkout -b feature/add-new-feature
   # atau
   git checkout -b fix/fix-bug-name
   ```

2. **Make your changes**
   ```bash
   # Edit files
   # Test changes
   ```

3. **Commit changes**
   ```bash
   git add .
   git commit -m "feat: add new feature description"
   ```

4. **Push to your fork**
   ```bash
   git push origin feature/add-new-feature
   ```

5. **Create Pull Request di GitHub**
   - Go to your fork on GitHub
   - Click "New Pull Request"
   - Fill in PR template
   - Submit

### Commit Message Format

Gunakan [Conventional Commits](https://www.conventionalcommits.org/):

```
<type>: <description>

[optional body]

[optional footer]
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

**Examples:**
```bash
feat: add email notification for password reset
fix: resolve double hashing issue in password update
docs: update installation guide with troubleshooting
style: format code according to PSR-12
refactor: optimize database queries in AbsensiModel
test: add unit tests for UserModel
chore: update composer dependencies
```

### PR Title Format

```
[TYPE] Brief description of changes
```

**Examples:**
- `[FEAT] Add profile photo upload feature`
- `[FIX] Fix CSRF token validation error`
- `[DOCS] Update deployment guide`

### PR Description Template

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Documentation update
- [ ] Code refactoring
- [ ] Other (please describe)

## Related Issues
Fixes #123
Related to #456

## Testing
- [ ] Tested on local development
- [ ] Tested on staging
- [ ] All tests pass

## Screenshots (if applicable)
Add screenshots here

## Checklist
- [ ] Code follows project standards
- [ ] Documentation updated
- [ ] No breaking changes
- [ ] Commit messages are clear
```

---

## 🐛 Pelaporan Bug

### Before Reporting

1. **Check existing issues** - Bug mungkin sudah dilaporkan
2. **Try latest version** - Bug mungkin sudah diperbaiki
3. **Test on clean install** - Pastikan bukan configuration issue

### Bug Report Template

```markdown
**Describe the bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

**Expected behavior**
A clear and concise description of what you expected to happen.

**Screenshots**
If applicable, add screenshots to help explain your problem.

**Environment:**
 - OS: [e.g. Windows 10, Ubuntu 20.04]
 - PHP Version: [e.g. 8.1.2]
 - MySQL Version: [e.g. 8.0.28]
 - Browser: [e.g. chrome, safari]
 - Version: [e.g. 1.0.0]

**Error Messages**
```
Paste error messages here
```

**Additional context**
Add any other context about the problem here.
```

---

## 💡 Feature Request

### Feature Request Template

```markdown
**Is your feature request related to a problem? Please describe.**
A clear and concise description of what the problem is. Ex. I'm always frustrated when [...]

**Describe the solution you'd like**
A clear and concise description of what you want to happen.

**Describe alternatives you've considered**
A clear and concise description of any alternative solutions or features you've considered.

**Additional context**
Add any other context or screenshots about the feature request here.

**Benefits**
- How will this feature benefit users?
- How will this feature benefit the project?
```

---

## 🧪 Testing

### Manual Testing

Before submitting PR, test these scenarios:

1. **Fresh Installation**
   ```bash
   php spark migrate:rollback -all
   php spark migrate
   php spark db:seed AdminSeeder
   ```

2. **Feature Testing**
   - Test happy path
   - Test edge cases
   - Test error handling

3. **Browser Testing**
   - Chrome
   - Firefox
   - Safari (jika tersedia)
   - Mobile browsers

### Automated Testing (Coming Soon)

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/unit/UserModelTest.php
```

---

## 📝 Documentation Guidelines

### When to Update Documentation

- Adding new features
- Changing existing behavior
- Fixing bugs that affect usage
- Adding configuration options

### Documentation Files

| File | Purpose |
|------|---------|
| README.md | Project overview & quick start |
| PANDUAN_INSTALASI.md | Detailed installation guide |
| DEPLOYMENT_GUIDE.md | Production deployment |
| FEATURES.md | Feature documentation |
| CHANGELOG.md | Version history |

### Documentation Style

- **Clear and concise** - Avoid jargon
- **Step-by-step** - Include code examples
- **Screenshots** - When helpful
- **Troubleshooting** - Include common issues

---

## 🏷️ Issue Labels

| Label | Description |
|-------|-------------|
| `bug` | Something isn't working |
| `enhancement` | New feature or request |
| `documentation` | Documentation improvements |
| `good first issue` | Good for newcomers |
| `help wanted` | Extra attention needed |
| `question` | Further information requested |
| `wontfix` | This will not be worked on |
| `duplicate` | This issue already exists |

---

## 🎓 Resources

### Learning Resources

- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

### Development Tools

- [PHPStorm](https://www.jetbrains.com/phpstorm/) - PHP IDE
- [VS Code](https://code.visualstudio.com/) - Code editor
- [Git](https://git-scm.com/) - Version control
- [Postman](https://www.postman.com/) - API testing

---

## 👥 Community

### Get Help

- GitHub Issues - Bug reports & questions
- GitHub Discussions - General discussions
- Email - [your-email@example.com]

### Stay Updated

- Watch repository for updates
- Check [CHANGELOG.md](CHANGELOG.md) regularly
- Follow project on social media (jika ada)

---

## 🎉 Recognition

Contributors akan disebutkan di:
- CHANGELOG.md untuk setiap release
- README.md contributors section
- Release notes

---

## 📞 Questions?

Jika Anda memiliki pertanyaan tentang contributing:

1. Check existing [Issues](https://github.com/username/simacca/issues)
2. Check [Discussions](https://github.com/username/simacca/discussions)
3. Create new issue dengan label `question`
4. Contact maintainers

---

## 📄 License

Dengan berkontribusi ke SIMACCA, Anda setuju bahwa kontribusi Anda akan dilisensikan di bawah [MIT License](LICENSE).

---

**Thank you for contributing to SIMACCA! 🚀**

*Your contributions make this project better for everyone.*

---

*Contributing Guide v1.0*  
*Terakhir diupdate: 2026-01-15*
