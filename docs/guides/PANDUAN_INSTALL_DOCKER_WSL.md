# 🐧 Panduan Lengkap Instalasi Docker di WSL 2 (Windows Subsystem for Linux)

Panduan ini berisi langkah demi langkah untuk menginstal dan mengkonfigurasi **Docker & Docker Compose** di dalam lingkungan **WSL 2 (Ubuntu/Debian)** pada sistem operasi Windows.

Terdapat 2 metode yang dapat dipilih:
* **Metode 1 (Direkomendasikan)**: **Docker Engine Native di WSL 2** *(Ringan, hemat RAM, cepat, tanpa perlu install Docker Desktop).*
* **Metode 2**: **Docker Desktop for Windows** *(Berbasis GUI Windows dengan integrasi WSL 2 backend).*

---

## 📌 Prasyarat (Memastikan WSL 2 Aktif di Windows)

Buka **PowerShell (Run as Administrator)** di Windows, lalu jalankan:

1. **Cek versi WSL yang terpasang**:
   ```powershell
   wsl -l -v
   ```
   *Pastikan distro Ubuntu Anda berstatus `VERSION 2`.*

2. **Jika WSL belum terpasang atau masih versi 1**:
   ```powershell
   wsl --install
   # atau set default ke versi 2:
   wsl --set-default-version 2
   ```

3. **Buka terminal Ubuntu di WSL**, lalu lanjutkan ke salah satu metode di bawah.

---

## ⚡ Metode 1: Instalasi Docker Engine Native di WSL 2 (Sangat Direkomendasikan)

Metode ini memasang Docker langsung di dalam kernel Linux WSL 2. **Kelebihan**: Tidak memakan resource RAM besar seperti Docker Desktop dan tidak memerlukan lisensi.

Buka terminal **Ubuntu di WSL** Anda:

### Langkah 1: Update & Install Dependensi Awal
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y ca-certificates curl gnupg lsb-release
```

### Langkah 2: Tambahkan GPG Key Resmi Docker
```bash
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
```

### Langkah 3: Tambahkan Repositori Docker ke APT Sources
```bash
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
```

### Langkah 4: Install Docker Engine, CLI & Docker Compose Plugin
```bash
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

### Langkah 5: Izinkan User Menjalankan Docker Tanpa `sudo`
```bash
sudo usermod -aG docker $USER
```
*(Setelah menjalankan perintah di atas, tutup terminal WSL dan buka kembali agar izin user aktif, atau jalankan `newgrp docker`).*

### Langkah 6: Aktifkan Systemd di WSL (Agar Docker Otomatis Start)
WSL 2 versi modern sudah mendukung **systemd**. Cek konfigurasi `/etc/wsl.conf`:

```bash
sudo nano /etc/wsl.conf
```
Pastikan ada konfigurasi berikut:
```ini
[boot]
systemd=true
```
*(Simpan dengan `Ctrl + O`, `Enter`, lalu `Ctrl + X`).*

Di PowerShell Windows, restart WSL sekali saja:
```powershell
wsl --shutdown
```
Lalu buka kembali terminal Ubuntu WSL.

### Langkah 7: Verifikasi Instalasi
Jalankan di terminal WSL:
```bash
docker --version
docker compose version
docker run hello-world
```
*Jika output menampilkan pesan sukses dari Hello World, Docker telah siap digunakan 100%!*

---

## 🖥️ Metode 2: Menggunakan Docker Desktop for Windows

Jika Anda lebih menyukai tampilan grafis (GUI) di Windows:

1. **Download Installer**:
   Unduh Docker Desktop dari website resmi: [https://www.docker.com/products/docker-desktop/](https://www.docker.com/products/docker-desktop/)
2. **Install di Windows**:
   Jalankan file installer `.exe`. Pastikan opsi **"Use WSL 2 instead of Hyper-V"** dicentang saat instalasi.
3. **Konfigurasi Integrasi WSL**:
   * Buka aplikasi **Docker Desktop**.
   * Masuk ke **Settings (ikon roda gigi) > Resources > WSL Integration**.
   * Centang **"Enable integration with my default WSL distro"**.
   * Aktifkan toggle distro **Ubuntu** Anda.
   * Klik **Apply & Restart**.
4. **Verifikasi di Terminal WSL**:
   Buka terminal Ubuntu WSL dan jalankan:
   ```bash
   docker --version
   docker compose version
   ```

---

## 💡 Tips & Trik Optimasi WSL 2 untuk Docker

### 1. Membatasi Penggunaan RAM oleh WSL 2 (Agar Windows Tidak Berat)
Secara default, WSL 2 bisa menggunakan hingga 50% atau lebih dari total RAM Windows Anda. Anda bisa membatasinya dengan membuat file `.wslconfig` di Windows:

1. Buka Run di Windows (`Win + R`), ketik `%USERPROFILE%` lalu tekan Enter.
2. Buat file bernama `.wslconfig` (jika belum ada), lalu isi konfigurasi ini:
   ```ini
   [wsl2]
   memory=4GB       # Batas maksimal RAM untuk WSL (sesuaikan misal 4GB / 6GB)
   processors=4     # Jumlah core CPU yang dialokasikan
   ```
3. Restart WSL melalui PowerShell: `wsl --shutdown`.

---

## 🎯 Langkah Selanjutnya
Setelah Docker terpasang di WSL:
Ikuti panduan deployment SIMACCA di [DOCKER_DEPLOYMENT_GUIDE.md](../../DOCKER_DEPLOYMENT_GUIDE.md) untuk menyalakan container aplikasi!
