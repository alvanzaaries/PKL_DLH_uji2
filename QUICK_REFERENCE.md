# 📝 Quick Reference - Table Per Type (TPT) Pattern Implementation

## ✅ Completed Implementation

### Database Structure
- ✅ Parent table `industries` dengan common fields
- ✅ 4 Child tables dengan foreign keys (CASCADE delete):
  - `industri_primer` (PBPHH)
  - `industri_sekunder` (PBUI)
  - `tpt_kb` (Tempat Pengumpulan Kayu Bulat)
  - `end_user` (Perajin)

### Models
- ✅ Abstract base `Industri` class
- ✅ Concrete `IndustriBase` class (untuk insert parent)
- ✅ 4 Child models dengan proper relationships
- ✅ All models configured dengan `belongsTo` dan `hasOne` relationships

### Controllers
- ✅ `DashboardController` - Count real data dari database
- ✅ `IndustriPrimerController` - Full CRUD dengan parent-child insert logic
  - `index()` - List dengan eager loading dan whereHas filtering
  - `create()` - Form dengan kabupaten dropdown dari API
  - `store()` - Insert ke parent + child dengan FK

### Views
- ✅ `dashboard.blade.php` - Centered hero dengan 4 clickable cards
- ✅ `industri-primer/index.blade.php` - Table view dengan filter (nama, kabupaten, kapasitas)
- ✅ `industri-primer/create.blade.php` - Form dengan API integration
- ✅ View updated untuk akses parent data via `$item->industri->field`

## 🔧 How It Works

### Create New Record
```php
// Step 1: Insert parent
$industri = IndustriBase::create([
    'nama' => 'PT Example',
    'alamat' => 'Jl. Example',
    'type' => 'primer',
    // ... other common fields
]);

// Step 2: Insert child dengan FK
IndustriPrimer::create([
    'industri_id' => $industri->id,
    'pemberi_izin' => '...',
    // ... other specific fields
]);
```

### Query with Filter
```php
// Filter parent fields
IndustriPrimer::with('industri')
    ->whereHas('industri', function($q) {
        $q->where('kabupaten', 'Semarang');
    })
    ->get();

// Filter child fields
IndustriPrimer::where('kapasitas_izin', '>= 6001')->get();
```

### Access Data in View
```blade
@foreach($industriPrimer as $item)
    {{ $item->industri->nama }}        {{-- Parent data --}}
    {{ $item->industri->kabupaten }}   {{-- Parent data --}}
    {{ $item->jenis_produksi }}        {{-- Child data --}}
    {{ $item->kapasitas_izin }}        {{-- Child data --}}
@endforeach
```

## 📁 File Structure

```
app/
├── Http/Controllers/
│   ├── DashboardController.php         (✅ Updated)
│   └── IndustriPrimerController.php    (✅ Updated)
└── Models/
    ├── Industri.php                    (Abstract base)
    ├── IndustriBase.php                (Concrete for insert)
    ├── IndustriPrimer.php              (✅ Updated)
    ├── IndustriSekunder.php            (✅ Updated)
    ├── TptKb.php                       (✅ Updated)
    └── EndUser.php                     (✅ Updated)

database/migrations/
├── 2026_01_06_000000_create_industries_table.php      (Parent)
├── 2026_01_06_081045_create_industri_primer_table.php (Child)
├── 2026_01_07_035945_create_industri_sekunders_table.php
├── 2026_01_07_035945_create_tpt_kbs_table.php
└── 2026_01_07_035946_create_end_users_table.php

resources/views/
├── dashboard.blade.php                 (✅ Working)
└── industri-primer/
    ├── index.blade.php                 (✅ Updated)
    └── create.blade.php                (✅ Working)
```

## 🎯 Next Steps

### Immediate TODO:
1. ⏳ Test insert data via form
2. ⏳ Create controllers untuk IndustriSekunder, TptKb, EndUser
3. ⏳ Create views untuk 3 industri lainnya
4. ⏳ Implement Edit & Delete functionality

### Future Enhancements:
- Implement search & filter untuk semua industri
- Export data ke Excel/PDF
- Dashboard charts & statistics
- User authentication & authorization
- Audit logs untuk tracking changes

## 📄 Document Upload System

### File Storage Structure
Dokumen izin disimpan dengan struktur folder terorganisir:
```
storage/app/public/dokumen-izin/primer/
├── 2026/
│   ├── PRIMER_PT_Example_20260114_152345_abc123.pdf
│   ├── PRIMER_CV_Test_20260114_153012_def456.pdf
│   └── ...
├── 2027/
│   └── ...
```

### File Naming Convention
Format: `PRIMER_[NAMA_PERUSAHAAN]_[TANGGAL_JAM]_[RANDOM].pdf`
- `PRIMER` = Tipe industri
- `NAMA_PERUSAHAAN` = Nama perusahaan (sanitized, max 50 karakter)
- `TANGGAL_JAM` = Format YmdHis (20260114_152345)
- `RANDOM` = 6 karakter hash untuk uniqueness

**Contoh:**
`PRIMER_PT_Kayu_Lestari_Indonesia_20260114_152345_a1b2c3.pdf`

### Access Methods

#### 1. Via Web Browser (Public URL)
```
http://127.0.0.1:8000/storage/dokumen-izin/primer/2026/PRIMER_PT_Example_20260114_152345_abc123.pdf
```

#### 2. Via Download Button
Klik tombol "📄 Lihat PDF" di tabel → file dibuka di tab baru

#### 3. Via File Manager
**Lokasi fisik di server:**
```
D:\Laragon-project\PKL_DLH_uji2\storage\app\public\dokumen-izin\primer\2026\
```

**Cara akses:**
1. Buka File Explorer
2. Navigate ke: `D:\Laragon-project\PKL_DLH_uji2\storage\app\public\dokumen-izin\primer\`
3. Pilih folder tahun (contoh: `2026`)
4. File PDF ada di sana!

### Validasi Upload
- **Format:** Hanya PDF (`.pdf`)
- **Ukuran Maksimal:** 5 MB (5120 KB)
- **Preview:** Real-time preview dengan nama file dan ukuran
- **Drag & Drop:** Supported untuk upload yang lebih mudah
- **Validasi Client-Side:** Cek ukuran dan tipe file sebelum submit

### Important Notes
⚠️ **Symlink Requirement:**
File dokumen harus accessible via public URL. Pastikan symbolic link sudah dibuat:
```bash
php artisan storage:link
```

Ini membuat link dari `public/storage` → `storage/app/public`, sehingga file bisa diakses via browser.

### Database Storage
Field `dokumen_izin` di tabel `industri_primer` menyimpan **relative path**:
```
dokumen-izin/primer/2026/PRIMER_PT_Example_20260114_152345_abc123.pdf
```

### Features
✅ **Upload dengan Preview** - Lihat nama file dan ukuran sebelum submit  
✅ **Drag & Drop** - Upload file dengan cara drag file ke area upload  
✅ **Real-time Validation** - Validasi ukuran dan tipe file langsung  
✅ **Structured Naming** - Penamaan file terorganisir dan traceable  
✅ **Year-based Folders** - File dikelompokkan per tahun untuk kemudahan management  
✅ **Download/View** - Tombol di tabel untuk view PDF langsung di browser  
✅ **Auto Delete** - File otomatis dihapus saat data industri dihapus

## 🚀 Server Running

```bash
php artisan serve
# Server started at http://127.0.0.1:8000
```

**Access Points:**
- Dashboard: http://127.0.0.1:8000/
- Industri Primer: http://127.0.0.1:8000/industri-primer

## 📚 Documentation Files

1. `OOP_TPT_DOCUMENTATION.md` - Complete technical documentation
2. `QUICK_REFERENCE.md` - This file (quick reference guide)
3. `INDUSTRI_OOP_DOCUMENTATION.md` - Old documentation (for reference)

---

**Status:** ✅ TPT Pattern Fully Implemented & Migrated  
**Last Updated:** 2026-01-07  
**Ready for Testing:** YES
