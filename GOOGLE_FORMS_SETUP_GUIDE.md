# 📝 Panduan Setup Google Forms - Kuesioner Feedback SIMACCA

## 🎯 Link Google Forms (Setelah dibuat)
**Form URL:** [Isi setelah form dibuat]  
**Response Sheet:** [Isi setelah terhubung ke Sheets]

---

## 🚀 LANGKAH 1: SETUP AWAL

### A. Buat Form Baru
1. Buka **Google Forms**: https://forms.google.com
2. Klik **"+ Blank"** atau **"Blank Quiz"**
3. Ubah judul: **"Kuesioner Feedback SIMACCA"**
4. Deskripsi:
```
Sistem Monitoring Absensi dan Catatan Cara Ajar

⏱️ Waktu pengisian: ~5 menit
🎯 Tujuan: Evaluasi cepat kepuasan dan kebutuhan pengguna

Feedback Anda sangat membantu kami mengembangkan SIMACCA lebih baik.
Terima kasih atas partisipasi Anda! 🙏
```

### B. Pengaturan Form (Settings ⚙️)
Klik ikon **⚙️ Settings** di kanan atas:

**Tab "General":**
- ☑ Collect email addresses (untuk follow-up)
- ☐ Limit to 1 response (biarkan unchecked agar bisa isi berkali-kali jika diperlukan)
- ☑ Respondents can edit after submit (opsional)
- ☐ See summary charts and text responses

**Tab "Presentation":**
- ☑ Show progress bar
- ☑ Shuffle question order (opsional, untuk randomisasi)
- Confirmation message:
```
Terima kasih atas feedback Anda! 🎉

Masukan Anda sangat berharga untuk pengembangan SIMACCA.

Tim Pengembang SIMACCA
```

---

## 📋 LANGKAH 2: STRUKTUR PERTANYAAN

### SECTION 1: INFORMASI RESPONDEN

**Question 1: Peran Anda**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Admin
  - Guru Mata Pelajaran
  - Wali Kelas
  - Siswa
- **Settings:** Go to section based on answer (untuk conditional logic)
  - Admin → Section 4A
  - Guru Mata Pelajaran → Section 4B
  - Wali Kelas → Section 4C
  - Siswa → Section 4D

**Question 2: Lama menggunakan SIMACCA**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - < 1 bulan
  - 1-3 bulan
  - 3-6 bulan
  - > 6 bulan

**Question 3: Frekuensi penggunaan**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Setiap hari
  - 2-3x seminggu
  - 1x seminggu
  - Jarang (< 1x seminggu)

---

### SECTION 2: KEPUASAN PENGGUNA

**Add Section:** Klik icon "+" dengan 2 kotak (Add Section)
- Section title: **⭐ KEPUASAN PENGGUNA**
- Description:
```
Berikan nilai 1-5 untuk setiap aspek:
1 = Sangat Tidak Puas | 2 = Tidak Puas | 3 = Cukup | 4 = Puas | 5 = Sangat Puas
```

**Question 4: Kemudahan login dan navigasi menu**
- Type: **Linear scale**
- Required: **Yes** ✅
- Scale: **1 to 5**
- Label 1: Sangat Tidak Puas
- Label 5: Sangat Puas

**Question 5: Tampilan visual dan desain aplikasi**
- Type: **Linear scale**
- Required: **Yes** ✅
- Scale: **1 to 5**
- Label 1: Sangat Tidak Puas
- Label 5: Sangat Puas

**Question 6: Kecepatan loading dan performa sistem**
- Type: **Linear scale**
- Required: **Yes** ✅
- Scale: **1 to 5**
- Label 1: Sangat Tidak Puas
- Label 5: Sangat Puas

**Question 7: Kemudahan menggunakan fitur utama Anda**
- Type: **Linear scale**
- Required: **Yes** ✅
- Scale: **1 to 5**
- Label 1: Sangat Tidak Puas
- Label 5: Sangat Puas

**Question 8: Pengalaman menggunakan SIMACCA di HP/mobile**
- Type: **Linear scale**
- Required: **Yes** ✅
- Scale: **1 to 5**
- Label 1: Sangat Tidak Puas
- Label 5: Sangat Puas

**Question 9: Kepuasan keseluruhan terhadap SIMACCA**
- Type: **Linear scale**
- Required: **Yes** ✅
- Scale: **1 to 5**
- Label 1: Sangat Tidak Puas
- Label 5: Sangat Puas

---

### SECTION 3: CONDITIONAL SECTIONS (Berdasarkan Role)

#### SECTION 4A: FITUR UTAMA - ADMIN

**Add Section:** 🎯 FITUR UTAMA (ADMIN)

**Question 10A: Fitur mana yang paling sering Anda gunakan?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Manajemen Guru/Siswa
  - Manajemen Jadwal
  - Monitoring Absensi
  - Laporan
  - Other: [Allow "Other" option]

**Question 11A: Kesulitan terbesar saat menggunakan SIMACCA**
- Type: **Paragraph**
- Required: **No** ☐
- Description: *Ceritakan tantangan atau kendala yang Anda hadapi*

**After Section:** Go to Section 5 (Masalah Teknis)

---

#### SECTION 4B: FITUR UTAMA - GURU MATA PELAJARAN

**Add Section:** 🎯 FITUR UTAMA (GURU MAPEL)

**Question 10B: Berapa lama rata-rata waktu input absensi 1 kelas?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - < 2 menit (sangat cepat)
  - 2-5 menit (cepat)
  - 5-10 menit (cukup)
  - > 10 menit (lama)

**Question 11B: Apakah fitur berikut membantu pekerjaan Anda?**
- Type: **Multiple choice grid**
- Required: **Yes** ✅
- Rows:
  - Tampilan card untuk mobile
  - Tombol "Semua Hadir" (bulk action)
  - Jurnal KBM dengan foto
- Columns:
  - Ya
  - Tidak
  - Tidak tahu

**After Section:** Go to Section 5 (Masalah Teknis)

---

#### SECTION 4C: FITUR UTAMA - WALI KELAS

**Add Section:** 🎯 FITUR UTAMA (WALI KELAS)

**Question 10C: Apakah Anda bisa memantau absensi siswa dengan mudah?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Ya, sangat mudah
  - Ya, cukup mudah
  - Agak sulit
  - Sangat sulit

**Question 11C: Fitur mana yang paling membantu sebagai wali kelas?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Monitoring absensi kelas
  - Approve/reject izin siswa
  - Laporan kehadiran
  - Data siswa
  - Other: [Allow "Other" option]

**After Section:** Go to Section 5 (Masalah Teknis)

---

#### SECTION 4D: FITUR UTAMA - SISWA

**Add Section:** 🎯 FITUR UTAMA (SISWA)

**Question 10D: Apakah Anda bisa melihat kehadiran dan jadwal dengan jelas?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Ya, sangat jelas
  - Ya, cukup jelas
  - Kurang jelas
  - Tidak jelas

**Question 11D: Apakah proses pengajuan izin/sakit mudah dilakukan?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Sangat mudah
  - Mudah
  - Cukup
  - Sulit
  - Belum pernah coba

**After Section:** Go to Section 5 (Masalah Teknis)

---

### SECTION 5: MASALAH TEKNIS

**Add Section:** 🐛 MASALAH TEKNIS

**Question 12: Apakah Anda pernah mengalami error/bug?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Tidak pernah
  - Jarang (1-2 kali)
  - Kadang-kadang (3-5 kali)
  - Sering (> 5 kali)

**Question 13: Jika pernah error, masalah apa yang paling sering terjadi?**
- Type: **Checkboxes** (allow multiple selection)
- Required: **No** ☐
- Options:
  - Tidak bisa login / tiba-tiba logout
  - Data tidak tersimpan
  - Halaman loading lambat
  - Error saat submit form
  - Error saat upload file/foto
  - Tampilan berantakan di mobile
  - Other: [Allow "Other" option]

---

### SECTION 6: SARAN & KEBUTUHAN

**Add Section:** 💡 SARAN & KEBUTUHAN

**Question 14: Fitur baru apa yang PALING Anda butuhkan?**
- Type: **Checkboxes** (max 3 selections)
- Required: **Yes** ✅
- Description: *Pilih maksimal 3 fitur*
- Options:
  - Notifikasi email/WhatsApp
  - QR Code untuk absensi
  - Mobile app (Android/iOS)
  - Dashboard yang lebih informatif
  - Export laporan PDF otomatis
  - Rekap absensi bulanan
  - Grafik/chart statistik
  - Tutorial video / user manual
  - Other: [Allow "Other" option]
- **Validation:** Response validation → Select at most → 3

**Question 15: Apa yang paling Anda SUKAI dari SIMACCA?**
- Type: **Paragraph**
- Required: **No** ☐
- Description: *Ceritakan pengalaman positif Anda*

**Question 16: Apa yang paling perlu DIPERBAIKI dari SIMACCA?**
- Type: **Paragraph**
- Required: **No** ☐
- Description: *Saran konstruktif Anda sangat kami hargai*

---

### SECTION 7: REKOMENDASI

**Add Section:** 🎯 REKOMENDASI

**Question 17: Apakah Anda akan merekomendasikan SIMACCA ke sekolah lain?**
- Type: **Multiple choice**
- Required: **Yes** ✅
- Options:
  - Ya, pasti
  - Ya, mungkin
  - Tidak yakin
  - Tidak

**Question 18: Alasan Anda**
- Type: **Paragraph**
- Required: **No** ☐

---

### SECTION 8: KONTAK (OPSIONAL)

**Add Section:** 📞 KONTAK (Opsional untuk follow-up)

**Question 19: Nama**
- Type: **Short answer**
- Required: **No** ☐

**Question 20: Email atau WhatsApp**
- Type: **Short answer**
- Required: **No** ☐
- Validation: Text → Email (jika ingin validasi email)

---

## 🎨 LANGKAH 3: KUSTOMISASI DESAIN

### Theme Customization
1. Klik **Palette icon** 🎨 di kanan atas
2. Pilih **Theme color:** #3B82F6 (Blue - sesuai SIMACCA)
3. Pilih **Background color:** White atau Light Blue
4. Pilih **Font style:** Google Sans atau Roboto
5. Jika ada logo SIMACCA, upload di **Header**

### Header Image (Opsional)
1. Klik area header
2. Upload image logo SIMACCA
3. Recommended size: 1600 x 400 px

---

## 📊 LANGKAH 4: LINK KE GOOGLE SHEETS

### A. Create Response Spreadsheet
1. Klik tab **"Responses"** di Google Forms
2. Klik icon **Google Sheets** hijau
3. Pilih **"Create a new spreadsheet"**
4. Nama: "SIMACCA Feedback Responses"
5. Klik **Create**

### B. Setup Spreadsheet
**Sheet 1: Form Responses**
- Otomatis terisi dari Google Forms
- JANGAN edit kolom ini manual

**Sheet 2: Analysis** (Buat manual)
```
Untuk analisis data:
- Average scores per section
- Response count by role
- Most requested features
- Common issues
```

---

## 🔗 LANGKAH 5: SHARE & DISTRIBUTE

### A. Get Shareable Link
1. Klik **"Send"** button di kanan atas
2. Pilih tab **"Link"** 🔗
3. Klik **"Shorten URL"** (untuk link lebih pendek)
4. Klik **"Copy"**
5. Link format: `https://forms.gle/XXXXXXXXXX`

### B. Distribution Options

**Option 1: Direct Link**
```
https://forms.gle/[your-form-id]
```

**Option 2: QR Code**
1. Gunakan generator QR: https://qr-code-generator.com
2. Input link Google Forms
3. Download QR Code
4. Print atau share digital

**Option 3: Embed di Website**
1. Klik **"Send"** → Tab **"Embed HTML"** `< >`
2. Copy iframe code
3. Paste di halaman SIMACCA (jika ada landing page)

**Option 4: Email Blast**
1. Klik **"Send"** → Tab **"Email"** ✉️
2. Input email addresses atau mailing list
3. Customize subject & message
4. Click **"Send"**

---

## 📧 TEMPLATE PESAN DISTRIBUSI

### Email/WhatsApp Template:

```
Kepada Yth. [Admin/Guru/Wali Kelas/Siswa] SIMACCA,

Kami mengundang Anda untuk mengisi kuesioner feedback singkat tentang pengalaman Anda menggunakan SIMACCA.

📋 Kuesioner Feedback SIMACCA
⏱️ Waktu: ~5 menit
🎯 Tujuan: Meningkatkan kualitas sistem

Link kuesioner:
👉 [LINK GOOGLE FORMS]

Feedback Anda sangat berharga untuk pengembangan SIMACCA lebih baik lagi.

Terima kasih atas partisipasi Anda! 🙏

Salam,
Tim Pengembang SIMACCA
```

---

## 📈 LANGKAH 6: MONITORING & ANALYSIS

### A. View Responses
**Real-time Responses:**
1. Buka Google Forms → Tab **"Responses"**
2. Lihat **Summary** (charts otomatis)
3. Lihat **Individual** responses
4. Download responses via **Google Sheets**

### B. Analysis Metrics

**Key Metrics to Track:**
1. **Response Rate:** Total responses / Total users
2. **Average Satisfaction Score:** Avg of Q4-Q9
3. **NPS (Net Promoter Score):** Based on Q17
4. **Top Issues:** From Q13
5. **Most Requested Features:** From Q14

### C. Export Data
**Format Options:**
- CSV: Forms → Responses → ⋮ → Download responses (.csv)
- PDF: Forms → ⋮ → Print → Save as PDF
- Sheets: Already linked

---

## 🔄 LANGKAH 7: FOLLOW-UP ACTIONS

### A. Response Timeline
- **Day 1-3:** Send initial invitation
- **Day 4:** Send reminder to non-responders
- **Day 7:** Send final reminder
- **Day 10:** Close form & analyze
- **Day 14:** Share summary with stakeholders

### B. Analysis Report Template
```
SIMACCA FEEDBACK SUMMARY
========================
Period: [Date Range]
Total Responses: [XX]

SATISFACTION SCORES (Avg 1-5):
- Login & Navigation: [X.X]
- Visual Design: [X.X]
- Performance: [X.X]
- Main Features: [X.X]
- Mobile Experience: [X.X]
- Overall: [X.X]

TOP 3 REQUESTED FEATURES:
1. [Feature name] - XX votes
2. [Feature name] - XX votes
3. [Feature name] - XX votes

TOP 3 ISSUES:
1. [Issue] - XX mentions
2. [Issue] - XX mentions
3. [Issue] - XX mentions

RECOMMENDATION:
- [Net Promoter Score]: XX%
- Would recommend: XX%

ACTION ITEMS:
□ [Priority item based on feedback]
□ [Priority item based on feedback]
□ [Priority item based on feedback]
```

---

## 🛠️ TROUBLESHOOTING

### Common Issues:

**1. Conditional Logic Not Working**
- Pastikan Question 1 (Role) memiliki "Go to section based on answer"
- Check setiap option mengarah ke section yang benar

**2. Validation Error on Q14**
- Ensure validation: "Select at most 3"
- Type harus "Checkboxes", bukan "Multiple choice"

**3. Responses Not Appearing in Sheets**
- Check Sheets link status di Forms → Responses
- Unlink & re-link jika perlu

**4. Email Collection Not Working**
- Check Settings → General → "Collect email addresses" is enabled
- Respondents must be signed in to Google

---

## ✅ CHECKLIST FINAL

Sebelum launch, pastikan:

- ☐ Semua pertanyaan sudah diinput
- ☐ Conditional logic Q1 sudah diset (role-based sections)
- ☐ Linear scale labels sudah benar (1-5)
- ☐ Validation Q14 sudah diset (max 3 selections)
- ☐ Email collection enabled (jika diperlukan)
- ☐ Confirmation message sudah custom
- ☐ Theme/design sudah sesuai brand SIMACCA
- ☐ Google Sheets sudah linked
- ☐ Test form dengan semua role (Admin, Guru, Wali Kelas, Siswa)
- ☐ Shareable link sudah dicopy
- ☐ Distribution message sudah disiapkan

---

## 📞 SUPPORT

Jika ada pertanyaan setup Google Forms:

**Google Forms Help Center:**
https://support.google.com/docs/topic/9055404

**Video Tutorial:**
https://www.youtube.com/results?search_query=google+forms+conditional+logic

---

**Created by:** Tim Pengembang SIMACCA  
**Version:** 1.0  
**Date:** 2026-01-16  
**For:** SIMACCA v1.4.0
