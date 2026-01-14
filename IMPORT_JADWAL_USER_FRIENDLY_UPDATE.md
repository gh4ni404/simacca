# 🎉 Update: Import Jadwal User-Friendly dengan Dropdown

**Tanggal Update**: 2026-01-14  
**Versi**: 2.0 (User-Friendly Edition)

---

## 🚀 Apa yang Baru?

### Masalah Sebelumnya ❌
Admin harus:
- 🤔 Mengingat ID Guru (1, 2, 3, ...)
- 🤔 Mengingat ID Mata Pelajaran (1, 2, 3, ...)
- 🤔 Mengingat ID Kelas (1, 2, 3, ...)
- 📝 Buka aplikasi → cek ID → copy → paste ke Excel
- 😫 Proses berulang dan memakan waktu

### Solusi Baru ✅
Admin sekarang bisa:
- 😊 **Pilih dari Dropdown** - Tidak perlu mengingat ID!
- 👀 **Lihat Nama Lengkap** - Format: `Nama Guru (NIP)`
- 📊 **Data Referensi** tersedia di sheet terpisah
- ⚡ **Lebih Cepat** - Langsung pilih dari list
- 🎯 **Lebih Akurat** - Mengurangi kesalahan input

---

## 📊 Perbandingan Template

### Template Lama:
```
| HARI   | JAM MULAI | JAM SELESAI | GURU_ID | MATA_PELAJARAN_ID | KELAS_ID | SEMESTER | TAHUN AJARAN |
|--------|-----------|-------------|---------|-------------------|----------|----------|--------------|
| Senin  | 07:00:00  | 08:30:00    | 1       | 1                 | 1        | Ganjil   | 2023/2024    |
| Senin  | 08:30:00  | 10:00:00    | 2       | 2                 | 1        | Ganjil   | 2023/2024    |
```
❌ Harus ingat: ID 1 = siapa? ID 2 = mapel apa?

### Template Baru:
```
| HARI   | JAM MULAI | JAM SELESAI | NAMA GURU                        | MATA PELAJARAN        | KELAS    | SEMESTER | TAHUN AJARAN |
|--------|-----------|-------------|----------------------------------|-----------------------|----------|----------|--------------|
| Senin ↓| 07:00:00  | 08:30:00    | Ahmad Yani (196501011990031001) ↓| Matematika (MAT) ↓    | X RPL 1 ↓| Ganjil ↓ | 2023/2024    |
```
✅ Pilih dari dropdown! Jelas dan mudah!

*(↓ = Ada dropdown)*

---

## 🎨 Struktur Template Baru

### 📁 File: `template-import-jadwal-2026-01-14.xlsx`

```
┌─────────────────────────────────────────────┐
│ Sheet 1: Template Import Jadwal     ⭐      │
│ ├─ Header dengan 8 kolom                    │
│ ├─ Dropdown di 50 baris                     │
│ └─ Sample data 1 baris                      │
├─────────────────────────────────────────────┤
│ Sheet 2: Data Guru                   📋     │
│ ├─ ID | NIP | NAMA LENGKAP                  │
│ └─ Auto-populated dari database             │
├─────────────────────────────────────────────┤
│ Sheet 3: Data Mata Pelajaran         📚     │
│ ├─ ID | KODE | NAMA MATA PELAJARAN          │
│ └─ Auto-populated dari database             │
├─────────────────────────────────────────────┤
│ Sheet 4: Data Kelas                  🏫     │
│ ├─ ID | NAMA KELAS                          │
│ └─ Auto-populated dari database             │
├─────────────────────────────────────────────┤
│ Sheet 5: Petunjuk                    📖     │
│ └─ Panduan lengkap cara pengisian           │
└─────────────────────────────────────────────┘
```

---

## 🎯 Cara Menggunakan (Step-by-Step)

### 1️⃣ Download Template
```
Admin → Jadwal Mengajar → Button "Import" → Button "Download Template"
```
File: `template-import-jadwal-2026-01-14.xlsx` akan terdownload

### 2️⃣ Buka di Excel
- Buka file di Microsoft Excel atau LibreOffice Calc
- **Sheet aktif**: "Template Import Jadwal"

### 3️⃣ Isi Data dengan Dropdown

#### Kolom A - HARI:
1. Klik cell di kolom HARI
2. Lihat tanda **▼** (dropdown arrow)
3. Klik dropdown
4. Pilih: Senin / Selasa / Rabu / Kamis / Jumat

#### Kolom B & C - JAM:
- Ketik manual: `HH:MM:SS`
- Contoh: `07:00:00`, `08:30:00`, `10:00:00`

#### Kolom D - NAMA GURU:
1. Klik cell di kolom NAMA GURU
2. Klik dropdown **▼**
3. Lihat list: `Nama Lengkap (NIP)`
4. Pilih guru yang diinginkan
5. **Auto-save format**: `Ahmad Yani (196501011990031001)`

*💡 Tips: Bisa juga lihat sheet "Data Guru" untuk referensi lengkap*

#### Kolom E - MATA PELAJARAN:
1. Klik cell di kolom MATA PELAJARAN
2. Klik dropdown **▼**
3. Lihat list: `Nama Mapel (Kode)`
4. Pilih mapel yang diinginkan
5. **Auto-save format**: `Matematika (MAT)`

*💡 Tips: Bisa juga lihat sheet "Data Mata Pelajaran" untuk referensi*

#### Kolom F - KELAS:
1. Klik cell di kolom KELAS
2. Klik dropdown **▼**
3. Lihat list: `X RPL 1`, `XI RPL 1`, dst
4. Pilih kelas yang diinginkan

*💡 Tips: Bisa juga lihat sheet "Data Kelas" untuk referensi*

#### Kolom G - SEMESTER:
1. Klik cell di kolom SEMESTER
2. Klik dropdown **▼**
3. Pilih: Ganjil / Genap

#### Kolom H - TAHUN AJARAN:
- Ketik manual: `YYYY/YYYY`
- Contoh: `2023/2024`, `2024/2025`

### 4️⃣ Ulangi untuk Baris Berikutnya
- Copy-paste untuk data yang sama
- Atau isi manual dengan dropdown
- Sampai 50 baris tersedia

### 5️⃣ Upload File
1. Save file Excel
2. Kembali ke aplikasi
3. Halaman Import → Upload file
4. ✅ Centang "Lewati jadwal konflik" (recommended)
5. Klik "Proses Import"

### 6️⃣ Lihat Hasil
```
✅ Import selesai. Berhasil: 25, Gagal: 0
```

---

## 🔄 Backward Compatibility

### Template Lama Masih Bisa Digunakan! ✅

Sistem **support 2 format** sekaligus:

#### Format 1: ID (Template Lama)
```excel
| HARI  | JAM MULAI | ... | GURU_ID | MATA_PELAJARAN_ID | KELAS_ID | ...
| Senin | 07:00:00  | ... | 1       | 2                 | 5        | ...
```
✅ **Masih diterima** - ID akan diproses seperti biasa

#### Format 2: Nama (Template Baru)
```excel
| HARI  | JAM MULAI | ... | NAMA GURU            | MATA PELAJARAN    | KELAS    | ...
| Senin | 07:00:00  | ... | Ahmad Yani (1965...) | Matematika (MAT)  | X RPL 1  | ...
```
✅ **Diterima** - Sistem extract NIP/Kode → lookup ID

#### Format 3: Mix (Keduanya)
```excel
Baris 1: ID angka
Baris 2: Nama dengan dropdown
Baris 3: ID angka
```
✅ **Diterima** - Sistem detect otomatis per baris

---

## 🧠 Cara Kerja Auto-Lookup

### Proses Import:

```
1. Baca Excel baris per baris
   ↓
2. Cek kolom Guru: Angka atau String?
   ├─ Angka (1, 2, 3) → Langsung gunakan sebagai ID
   └─ String → Extract info
       ├─ Ada kurung? → Extract NIP → Lookup Guru ID
       └─ Tidak ada kurung? → Cari by nama → Lookup Guru ID
   ↓
3. Cek kolom Mapel: Angka atau String?
   ├─ Angka → Langsung gunakan sebagai ID
   └─ String → Extract kode → Lookup Mapel ID
   ↓
4. Cek kolom Kelas: Angka atau String?
   ├─ Angka → Langsung gunakan sebagai ID
   └─ String → Lookup by nama_kelas → Kelas ID
   ↓
5. Validasi: Guru/Mapel/Kelas ditemukan?
   ├─ Ya → Lanjut validasi konflik
   └─ Tidak → Error: "XXX tidak ditemukan"
   ↓
6. Cek konflik jadwal (guru & kelas)
   ├─ Tidak konflik → Insert ke database ✅
   └─ Konflik → Skip (jika opsi checked) atau Error
```

---

## 📝 Contoh Real Use Case

### Skenario: Admin ingin input 30 jadwal untuk semester baru

#### Cara Lama (Template ID):
```
1. Buka aplikasi → Menu Guru → Lihat ID Pak Ahmad (ID: 5)
2. Buka Excel → Ketik: 5
3. Buka aplikasi → Menu Mapel → Lihat ID Matematika (ID: 3)
4. Buka Excel → Ketik: 3
5. Buka aplikasi → Menu Kelas → Lihat ID X RPL 1 (ID: 8)
6. Buka Excel → Ketik: 8
7. Ulangi 30x untuk semua jadwal
```
**Total waktu**: ~30-45 menit ⏱️  
**Kesalahan**: Tinggi (salah ID, lupa ID) ❌

#### Cara Baru (Template Dropdown):
```
1. Download template (sudah ada semua data referensi)
2. Buka Excel
3. Klik dropdown NAMA GURU → Pilih "Ahmad Yani (196501011990031001)"
4. Klik dropdown MATA PELAJARAN → Pilih "Matematika (MAT)"
5. Klik dropdown KELAS → Pilih "X RPL 1"
6. Ulangi dengan copy-paste cerdas
```
**Total waktu**: ~10-15 menit ⏱️  
**Kesalahan**: Rendah (pilih dari list valid) ✅

**Efisiensi**: **3x lebih cepat!** 🚀

---

## 🎓 Tips & Best Practices

### ✅ DO:
1. **Selalu download template terbaru** sebelum import
2. **Gunakan dropdown** untuk menghindari typo
3. **Lihat sheet referensi** jika ragu dengan data
4. **Copy-paste** data yang sama untuk efisiensi
5. **Centang "Lewati konflik"** untuk import massal
6. **Test dengan 5-10 baris** dulu sebelum import banyak

### ❌ DON'T:
1. **Jangan edit** sheet "Data Guru", "Data Mapel", "Data Kelas"
2. **Jangan ubah** nama kolom header
3. **Jangan ketik manual** jika ada dropdown
4. **Jangan lupa** format jam HH:MM:SS
5. **Jangan import** tanpa cek sample data dulu

---

## 🐛 Troubleshooting

### Problem 1: "Dropdown tidak muncul"
**Solusi**:
- Pastikan menggunakan Microsoft Excel atau LibreOffice (bukan Google Sheets)
- Download template baru
- Jangan copy-paste antar file

### Problem 2: "Guru 'XXX' tidak ditemukan"
**Solusi**:
- Cek sheet "Data Guru" - apakah guru ada di list?
- Pastikan format: `Nama (NIP)` atau gunakan ID angka
- Jika guru baru, tambahkan dulu di menu Guru

### Problem 3: "Data tidak lengkap pada baris X"
**Solusi**:
- Pastikan semua kolom terisi (tidak ada yang kosong)
- Cek khususnya kolom HARI, JAM, GURU, MAPEL, KELAS, SEMESTER, TAHUN AJARAN

### Problem 4: "Konflik jadwal"
**Solusi**:
- Cek jadwal yang sudah ada
- Ubah jam atau hari
- Atau centang "Lewati jadwal konflik" untuk skip data konflik

---

## 📊 Perbandingan Fitur

| Fitur | Template Lama | Template Baru |
|-------|---------------|---------------|
| **Format Input** | ID angka | Nama + Dropdown |
| **Sheet Count** | 2 (Template + Petunjuk) | 5 (Template + 3 Referensi + Petunjuk) |
| **Data Referensi** | Tidak ada | ✅ Ada (3 sheet) |
| **Dropdown** | Tidak ada | ✅ Ada (5 kolom) |
| **User-Friendly** | ⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Error Rate** | Tinggi | Rendah |
| **Speed** | Lambat | Cepat |
| **Learning Curve** | Sulit | Mudah |
| **Backward Compatible** | N/A | ✅ Ya |

---

## 🎯 Kesimpulan

### Keuntungan Update:

1. **⏱️ Hemat Waktu**: 3x lebih cepat dari cara manual
2. **🎯 Lebih Akurat**: Dropdown mengurangi kesalahan input
3. **😊 User-Friendly**: Admin tidak perlu mengingat ID
4. **📊 Data Lengkap**: Referensi tersedia di file Excel
5. **🔄 Fleksibel**: Support format lama dan baru
6. **🚀 Efisien**: Cocok untuk import massal (puluhan/ratusan jadwal)

### Rekomendasi:
- ✅ Gunakan **template baru** untuk semua import kedepannya
- ✅ Template **otomatis update** dengan data terbaru saat download
- ✅ Lebih mudah untuk training admin baru

---

## 📞 Support

Jika ada pertanyaan atau kendala:
1. Lihat sheet "Petunjuk" di template Excel
2. Baca dokumentasi lengkap: `IMPORT_JADWAL_DOCUMENTATION.md`
3. Hubungi administrator sistem

---

**Selamat Menggunakan Fitur Import Baru!** 🎉
