# 📁 AKSES FILE DOKUMEN DARI FILE MANAGER

## 🎯 Quick Access Guide

### Langkah Cepat:
1. **Buka File Explorer** (tekan `Win + E`)
2. **Copy dan paste path ini** ke address bar:
   ```
   D:\Laragon-project\PKL_DLH_uji2\storage\app\public\dokumen-izin\primer
   ```
3. **Tekan Enter**
4. **Buka folder tahun** (contoh: `2026`)
5. **Semua PDF ada di sana!** ✅

---

## 📂 Struktur Folder

```
📁 dokumen-izin/
└── 📁 primer/
    ├── 📁 2026/
    │   ├── 📄 PRIMER_PT_Example_20260114_152345_abc123.pdf
    │   ├── 📄 PRIMER_CV_Test_20260114_153012_def456.pdf
    │   └── 📄 ... (file PDF lainnya)
    └── 📁 2027/
        └── 📄 ...
```

---

## 🔍 Format Nama File

**Pattern:**
```
PRIMER_[NAMA_PERUSAHAAN]_[TANGGAL_JAM]_[KODE_UNIK].pdf
```

**Contoh:**
```
PRIMER_PT_Kayu_Lestari_Indonesia_20260114_152345_a1b2c3.pdf
```

- `PRIMER` → Tipe industri
- `PT_Kayu_Lestari_Indonesia` → Nama perusahaan
- `20260114` → Tanggal (14 Jan 2026)
- `152345` → Jam (15:23:45)
- `a1b2c3` → Kode unik

---

## 💡 Apa yang Bisa Dilakukan?

✅ **Buka file** → Double-click untuk buka PDF  
✅ **Copy file** → Ctrl+C, Ctrl+V ke folder lain  
✅ **Share** → Kirim via email/WhatsApp  
✅ **Print** → Langsung print dari File Explorer  
✅ **Backup** → Copy seluruh folder ke external drive  
✅ **Rename** → ⚠️ Jangan rename! Akan break link di sistem  
✅ **Delete** → ⚠️ Jangan hapus manual! Hapus via web interface

---

## 🌐 Akses Via Browser

**URL Pattern:**
```
http://127.0.0.1:8000/storage/dokumen-izin/primer/2026/NAMA_FILE.pdf
```

**Dari Tabel:**
Klik tombol "📄 Lihat PDF" di kolom Dokumen

---

## ⚠️ IMPORTANT NOTES

### ❌ JANGAN:
- Rename file manual (akan break database link)
- Hapus file manual (database tidak sync)
- Pindah file ke folder lain (URL akan broken)

### ✅ LAKUKAN:
- Hapus via web interface (tombol "Hapus")
- Update via form edit
- Backup seluruh folder secara berkala

---

## 🔧 Troubleshooting Cepat

### File tidak muncul?
```bash
# Jalankan di terminal (dari root project):
php artisan storage:link
```

### Path tidak bisa diakses?
1. Cek apakah folder ada
2. Cek permissions folder
3. Restart File Explorer

### File corrupt atau tidak bisa dibuka?
1. Cek ukuran file (harus > 0 KB)
2. Buka dengan PDF reader lain
3. Re-upload file via form

---

## 📋 Checklist Backup

**Backup Rutin (Recommended: setiap akhir bulan)**

```
[ ] Copy folder dokumen-izin ke external drive
[ ] Verify semua file ter-copy dengan benar
[ ] Test buka beberapa file random
[ ] Simpan backup dengan label tanggal (backup_202601.zip)
[ ] Simpan backup ke 2 lokasi berbeda (redundancy)
```

**Lokasi Backup Recommended:**
1. External Hard Drive
2. Cloud Storage (Google Drive / OneDrive)
3. Network Drive Kantor
4. USB Flash Drive (backup darurat)

---

## 📊 Quick Stats

**Total Storage Usage:**  
Cek ukuran folder `dokumen-izin` di File Explorer

**Jumlah File:**  
Klik folder → Properties → lihat "Contains: X files"

**File Terbesar:**  
Sort by "Size" (descending) di File Explorer

---

## 📞 Need Help?

1. Baca [PANDUAN_UPLOAD_DOKUMEN.md](PANDUAN_UPLOAD_DOKUMEN.md) untuk detail lengkap
2. Cek [QUICK_REFERENCE.md](QUICK_REFERENCE.md) untuk technical reference
3. Pastikan symlink storage sudah dibuat

---

**Last Updated:** 14 Januari 2026  
**Version:** 1.0  
**Created by:** GitHub Copilot
