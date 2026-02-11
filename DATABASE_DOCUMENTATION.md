# Dokumentasi Database - Sistem Informasi Industri Kehutanan

## 📊 Struktur Database

### 1. Tabel `industri_base` (Tabel Induk)
Tabel utama yang menyimpan informasi dasar industri.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| nama | VARCHAR(255) | NO | Nama perusahaan |
| alamat | TEXT | YES | Alamat lengkap |
| kabupaten | VARCHAR(100) | YES | Kabupaten/Kota |
| latitude | DECIMAL(10,8) | YES | Koordinat latitude |
| longitude | DECIMAL(11,8) | YES | Koordinat longitude |
| penanggungjawab | VARCHAR(255) | YES | Nama penanggung jawab |
| kontak | VARCHAR(50) | YES | Nomor kontak |
| nomor_izin | VARCHAR(100) | YES | Nomor SK/Izin |
| tanggal | DATE | YES | Tanggal SK |
| status | ENUM('Aktif','Tidak Aktif') | NO | Status operasional (default: Aktif) |
| dokumen_izin | VARCHAR(255) | YES | Path file dokumen PDF |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Indexes:**
- PRIMARY: `id`
- INDEX: `kabupaten`, `status`

---

### 2. Tabel `industri_primer` (Industri PBPHH)
Tabel untuk industri pengolahan hasil hutan primer.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| industri_id | BIGINT UNSIGNED | NO | Foreign Key → industri_base.id |
| pemberi_izin | VARCHAR(100) | YES | Instansi pemberi izin |
| total_nilai_investasi | BIGINT | YES | Total investasi (Rupiah) |
| total_pegawai | INTEGER | YES | Jumlah pegawai |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Relasi:**
- `industri_id` → `industri_base.id` (ON DELETE CASCADE)
- Many-to-Many dengan `master_jenis_produksi` melalui pivot table

---

### 3. Tabel `industri_sekunder` (Industri PBUI)
Tabel untuk industri pengolahan hasil hutan sekunder.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| industri_id | BIGINT UNSIGNED | NO | Foreign Key → industri_base.id |
| pemberi_izin | VARCHAR(100) | YES | Instansi pemberi izin |
| total_nilai_investasi | BIGINT | YES | Total investasi (Rupiah) |
| total_pegawai | INTEGER | YES | Jumlah pegawai |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Relasi:**
- `industri_id` → `industri_base.id` (ON DELETE CASCADE)
- Many-to-Many dengan `master_jenis_produksi` melalui pivot table

---

### 4. Tabel `tptkb` (Tempat Penimbunan TPT-KB)
Tabel untuk tempat penimbunan kayu bulat.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| industri_id | BIGINT UNSIGNED | NO | Foreign Key → industri_base.id |
| kapasitas_tampung | DECIMAL(15,2) | YES | Kapasitas (m³) |
| sumber_bahan_baku | VARCHAR(255) | YES | Asal bahan baku |
| masa_berlaku | DATE | YES | Tanggal masa berlaku izin |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Relasi:**
- `industri_id` → `industri_base.id` (ON DELETE CASCADE)

---

### 5. Tabel `end_users` (Perajin/End User)
Tabel untuk perajin dan pengguna akhir.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| industri_id | BIGINT UNSIGNED | NO | Foreign Key → industri_base.id |
| jenis_kerajinan | VARCHAR(100) | YES | Jenis produk kerajinan |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Relasi:**
- `industri_id` → `industri_base.id` (ON DELETE CASCADE)

---

### 6. Tabel Pivot: `industri_jenis_produksi`
Tabel penghubung untuk relasi many-to-many antara industri dengan jenis produksi.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| industri_primer_id | BIGINT UNSIGNED | YES | FK → industri_primer.id |
| industri_sekunder_id | BIGINT UNSIGNED | YES | FK → industri_sekunder.id |
| jenis_produksi_id | BIGINT UNSIGNED | NO | FK → master_jenis_produksi.id |
| kapasitas_izin | DECIMAL(15,2) | YES | Kapasitas izin (m³/tahun) |
| nama_custom | VARCHAR(255) | YES | Nama custom jika jenis_produksi_id = 5 (Lainnya) |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Indexes:**
- FOREIGN KEY: `industri_primer_id`, `industri_sekunder_id`, `jenis_produksi_id`

---

### 7. Tabel `master_jenis_produksi`
Tabel master untuk jenis produksi.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| nama | VARCHAR(100) | NO | Nama jenis produksi |
| kategori | VARCHAR(50) | YES | Kategori (primer/sekunder) |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Data Default:**
1. Kayu Gergajian (primer)
2. Kayu Olahan (sekunder)
3. Veneer (primer)
4. Plywood (sekunder)
5. Lainnya (primer & sekunder)

---

### 8. Tabel `users`
Tabel untuk autentikasi admin.

| Kolom | Tipe | Null | Keterangan |
|-------|------|------|------------|
| id | BIGINT UNSIGNED | NO | Primary Key |
| name | VARCHAR(255) | NO | Nama lengkap |
| email | VARCHAR(255) | NO | Email (unique) |
| email_verified_at | TIMESTAMP | YES | - |
| password | VARCHAR(255) | NO | Hashed password |
| role | ENUM('admin','user') | NO | Role pengguna (default: user) |
| remember_token | VARCHAR(100) | YES | - |
| created_at | TIMESTAMP | YES | - |
| updated_at | TIMESTAMP | YES | - |

**Indexes:**
- UNIQUE: `email`

---

## 🔗 Diagram Relasi

```
industri_base (1) ──< (1) industri_primer
                  ──< (1) industri_sekunder
                  ──< (1) tptkb
                  ──< (1) end_users

industri_primer (N) ──< (M) master_jenis_produksi
                        via industri_jenis_produksi

industri_sekunder (N) ──< (M) master_jenis_produksi
                          via industri_jenis_produksi
```

---

## 📁 Model Files

### 1. `IndustriBase.php`
Model induk untuk semua jenis industri.

**Relasi:**
- `hasOne('IndustriPrimer')` 
- `hasOne('IndustriSekunder')`
- `hasOne('Tptkb')`
- `hasOne('EndUser')`

**Scope:**
- `scopeActive()` - Filter industri aktif
- `scopeByKabupaten()` - Filter per kabupaten

---

### 2. `IndustriPrimer.php`
Model untuk industri primer.

**Relasi:**
- `belongsTo('IndustriBase', 'industri_id')`
- `belongsToMany('MasterJenisProduksi')->withPivot('kapasitas_izin', 'nama_custom')`

**Fillable:**
- industri_id, pemberi_izin, total_nilai_investasi, total_pegawai

---

### 3. `IndustriSekunder.php`
Model untuk industri sekunder.

**Relasi:**
- `belongsTo('IndustriBase', 'industri_id')`
- `belongsToMany('MasterJenisProduksi')->withPivot('kapasitas_izin', 'nama_custom')`

**Fillable:**
- industri_id, pemberi_izin, total_nilai_investasi, total_pegawai

---

### 4. `Tptkb.php`
Model untuk TPTKB.

**Relasi:**
- `belongsTo('IndustriBase', 'industri_id')`

**Fillable:**
- industri_id, kapasitas_tampung, sumber_bahan_baku, masa_berlaku

---

### 5. `EndUser.php` (Perajin)
Model untuk perajin.

**Relasi:**
- `belongsTo('IndustriBase', 'industri_id')`

**Fillable:**
- industri_id, jenis_kerajinan

---

## 🎯 Helper Classes

### `KabupatenHelper.php`
Helper untuk normalisasi nama kabupaten/kota di Jawa Tengah.

**Method:**
- `getValidNames()` - Return array kabupaten valid
- `normalize($input)` - Normalisasi input ke format standar

**Data:**
- 29 Kabupaten
- 6 Kota
- Total: 35 wilayah di Jawa Tengah

---

## 📦 Import Classes

### Excel Import dengan Maatwebsite/Excel

**1. IndustriPrimerImport.php**
- Kolom 1-8: Data dasar (nama, alamat, koordinat, dll)
- Kolom 9: Total Nilai Investasi (optional, INTEGER)
- Kolom 10: Total Pegawai (optional, INTEGER)
- Kolom 11+: Jenis produksi & kapasitas

**2. IndustriSekunderImport.php**
- Struktur sama dengan IndustriPrimer
- Kategori jenis produksi: 'sekunder'

**3. TptkbImport.php**
- Kolom 1-8: Data dasar
- Kolom 9: Kapasitas Tampung
- Kolom 10: Sumber Bahan Baku
- Kolom 11: Masa Berlaku

**4. PerajinImport.php**
- Kolom 1-8: Data dasar
- Kolom 9: Jenis Kerajinan

---

## 🔄 Export Classes

### Excel Export dengan Maatwebsite/Excel

**Format Export:**
- **Row 1-3**: Header dengan informasi filter (merged cells)
- **Row 4**: Column headers dengan background hijau (#16a34a)
- **Row 5+**: Data

**Classes:**
1. `IndustriPrimerExport.php` (11 kolom: A-K)
2. `IndustriSekunderExport.php` (11 kolom: A-K)
3. `TptkbExport.php` (9 kolom: A-I)
4. `PerajinExport.php` (7 kolom: A-G)

**Filter Support:**
- Nama perusahaan
- Kabupaten
- Jenis produksi
- Status (Aktif/Tidak Aktif)
- Tahun & Bulan
- Kapasitas

---

## 🚀 Migrasi Database

**Urutan Eksekusi:**
1. `create_users_table`
2. `create_industri_base_table`
3. `create_master_jenis_produksi_table`
4. `create_industri_primer_table`
5. `create_industri_sekunder_table`
6. `create_tptkb_table`
7. `create_end_users_table`
8. `create_industri_jenis_produksi_table` (pivot)
9. `add_investment_and_employees_to_industri_primer_table`
10. `add_investment_and_employees_to_industri_sekunder_table`

**Command:**
```bash
php artisan migrate
```

---

## 🌱 Seeder

### `DatabaseSeeder.php`
Seeds default data:
- Admin user (email: admin@dlhk.go.id)
- Master jenis produksi (5 items)

**Command:**
```bash
php artisan db:seed
```

---

## 🎨 Features

### 1. CRUD Operations
- ✅ Create, Read, Update, Delete untuk semua jenis industri
- ✅ Validasi input dengan Form Request
- ✅ Upload & preview dokumen PDF
- ✅ Koordinat dengan Leaflet Maps

### 2. Import/Export
- ✅ Bulk import dari Excel (.xlsx, .xls)
- ✅ Export dengan filter support
- ✅ Header informatif di Excel
- ✅ Real-time progress bar

### 3. Filter & Search
- ✅ Case-insensitive search
- ✅ Multi-filter (kabupaten, status, tahun, bulan, kapasitas)
- ✅ Collapsible filter panel
- ✅ Active filter count

### 4. Visualisasi
- ✅ Chart.js untuk statistik (Doughnut charts)
- ✅ Legend optimization (max 5-6 items)
- ✅ Responsive design

### 5. Security
- ✅ Authentication dengan Laravel Sanctum
- ✅ Role-based access (admin/user)
- ✅ CSRF protection
- ✅ File validation (PDF only, max 10MB)

---

## 🗺️ Routes

### Public Routes
- `GET /` → Dashboard
- `GET /login` → Login page

### Protected Routes (Auth)
#### Industri Primer
- `GET /industri/primer` → Index
- `GET /industri/primer/create` → Create form
- `POST /industri/primer` → Store
- `GET /industri/primer/{id}` → Show
- `GET /industri/primer/{id}/edit` → Edit form
- `PUT /industri/primer/{id}` → Update
- `DELETE /industri/primer/{id}` → Delete
- `GET /industri/primer/export` → Export Excel
- `POST /industri/primer/import` → Import Excel
- `GET /industri/primer/{id}/dokumen` → Download PDF
- `GET /industri/primer/{id}/view-dokumen` → View PDF

*(Similar routes untuk sekunder, tptkb, perajin)*

---

## 📊 Statistics & Charts

### Dashboard Statistics
- Total Industri Primer
- Total Industri Sekunder
- Total TPTKB
- Total Perajin
- **Grand Total**: Sum of all industries

### Charts (Chart.js)
1. **Sebaran Per Tahun** (Doughnut)
   - Data dari `tanggal` field
   - Max 5 legend items
   
2. **Sebaran Kabupaten/Kota** (Doughnut)
   - Data dari `kabupaten` field
   - Max 6 legend items
   
3. **Sebaran Kapasitas Izin** (Doughnut)
   - Range: 0-1999, 2000-5999, >=6000 m³/tahun
   - All legend items shown (only 3)

---

## 🔧 Configuration Files

### `config/laporan.php`
Configuration untuk sistem laporan (optional/future).

### `config/filesystems.php`
- `public` disk untuk dokumen PDF
- Path: `storage/app/public/dokumen_izin/`

---

## 📝 Notes

### Kolom Optional
- `total_nilai_investasi` dan `total_pegawai` bersifat optional (nullable)
- Form validation: `nullable|integer|min:0`
- Display: Format Rupiah untuk investasi, format ribuan untuk pegawai

### Normalisasi Data
- **Kabupaten**: Otomatis dinormalisasi via `KabupatenHelper`
- **Jenis Produksi**: Support custom name untuk kategori "Lainnya"

### File Upload
- **Max Size**: 10MB
- **Format**: PDF only
- **Storage**: `storage/app/public/dokumen_izin/`
- **Access**: Via symlink `public/storage`

---

**Last Updated**: February 5, 2026
**Version**: 1.0.0
