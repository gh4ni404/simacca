# 🐧 Panduan Lengkap Instalasi Docker di WSL 2 (Windows Subsystem for Linux)

Panduan ini berisi langkah demi langkah untuk menginstal dan mengonfigurasi **Docker Engine & Docker Compose Plugin** di dalam lingkungan **WSL 2 (Ubuntu)** pada sistem operasi Windows.

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

3. **Buka terminal Ubuntu di WSL**, lalu lanjutkan ke langkah instalasi di bawah.

---

## ⚡ Instalasi Docker Engine Native di WSL 2 (Sangat Direkomendasikan)

Metode ini memasang Docker langsung di dalam kernel Linux WSL 2. **Kelebihan**: Sangat ringan, hemat RAM (tidak perlu Docker Desktop GUI), stabil, dan cepat.

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
*(Setelah perintah ini, jalankan `newgrp docker` atau tutup terminal WSL lalu buka kembali agar izin user aktif).*

### Langkah 6: Aktifkan Systemd di WSL (Agar Docker Otomatis Berjalan saat WSL Nyala)
WSL 2 versi modern sudah mendukung **systemd**. Cek konfigurasi `/etc/wsl.conf`:

```bash
sudo nano /etc/wsl.conf
```
Tambahkan/pastikan ada konfigurasi berikut:
```ini
[boot]
systemd=true
```
*(Tekan `Ctrl + O`, `Enter` untuk menyimpan, lalu `Ctrl + X` untuk keluar).*

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

## 📂 Tips Penting: Penempatan Folder Project di WSL

> [!IMPORTANT]
> **Selalu letakkan folder project di Linux Home filesystem (`~/simacca` atau `/home/$USER/simacca`), BUKAN di `/mnt/c/Users/...`!**
> 
> * **Kecepatan**: Operasi file di Linux native (`~`) hingga **5x - 10x lebih cepat** daripada mengakses partisi Windows (`/mnt/c/`).
> * **Izin File**: Izin Linux (`chmod`, `chown`, symlink) bekerja 100% sempurna di native Linux.

Untuk berpindah ke folder project Linux:
```bash
cd ~
mkdir -p ~/simacca
cd ~/simacca
```

---

## 🎯 Langkah Selanjutnya: Menjalankan SIMACCA
Setelah Docker siap di WSL, lanjutkan ke panduan deployment di [DOCKER_DEPLOYMENT_GUIDE.md](../../DOCKER_DEPLOYMENT_GUIDE.md) untuk menarik image dari Docker Hub dan menjalankan aplikasi!
