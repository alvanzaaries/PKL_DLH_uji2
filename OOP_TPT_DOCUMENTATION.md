# Dokumentasi Struktur OOP - Table Per Type (TPT) Pattern

## 📋 Overview

Sistem database menggunakan **Table Per Type (TPT) Inheritance Pattern** dengan Foreign Key relationships untuk mengelola 4 jenis industri pengelolaan hasil hutan.

## 🗂️ Database Structure

### Parent Table: `industries`
Menyimpan data common untuk semua jenis industri.

**Primary Key:** `id`

**Columns:**
- `id` (PK)
- `nama` - Nama perusahaan
- `alamat` - Alamat lengkap
- `penanggungjawab` - Nama direktur/pimpinan
- `kabupaten` - Kabupaten/Kota
- `kontak` - Nomor telepon/email
- `nomor_izin` - Nomor izin/NIB/SS
- `type` - ENUM('primer', 'sekunder', 'tpt_kb', 'end_user')
- `created_at`, `updated_at`

### Child Tables

#### 1. `industri_primer` (PBPHH)
**Primary Key:** `id`  
**Foreign Key:** `industri_id` → `industries.id` (CASCADE)

**Specific Columns:**
- `pemberi_izin`
- `jenis_produksi`
- `kapasitas_izin` - ENUM('<= 2000', '2001 - 6000', '>= 6001')
- `pelaporan` - ENUM('Aktif', 'Tidak Aktif', 'Pending')
- `dokumen_izin` (nullable)

#### 2. `industri_sekunder` (PBUI)
**Primary Key:** `id`  
**Foreign Key:** `industri_id` → `industries.id` (CASCADE)

**Specific Columns:**
- `pemberi_izin`
- `jenis_produksi`
- `kapasitas_izin` - ENUM('<= 2000', '2001 - 6000', '>= 6001')

#### 3. `tpt_kb` (Tempat Pengumpulan Kayu Bulat)
**Primary Key:** `id`  
**Foreign Key:** `industri_id` → `industries.id` (CASCADE)

**Specific Columns:**
- `pemberi_izin`
- `sumber_bahan_baku` - ENUM('Hutan Alam', 'Perhutani', 'Hutan Rakyat')
- `kapasitas_izin` - ENUM('<= 2000', '2001 - 6000', '>= 6001')
- `masa_berlaku` (date)

#### 4. `end_user` (Perajin)
**Primary Key:** `id`  
**Foreign Key:** `industri_id` → `industries.id` (CASCADE)

**Specific Columns:**
- (Hanya memiliki FK, tidak ada kolom tambahan karena untuk usaha mikro)

## 📦 Model Structure

### 1. Abstract Base Model: `Industri`
```php
abstract class Industri extends Model
{
    protected $table = 'industries';
    
    protected $fillable = [
        'nama', 'alamat', 'penanggungjawab', 
        'kabupaten', 'kontak', 'nomor_izin', 'type'
    ];
    
    // Abstract method yang harus di-implement oleh child
    abstract public function getJenisIndustri(): string;
    
    // Common scopes
    public function scopeByKabupaten($query, $kabupaten);
    public function scopeByNama($query, $nama);
    
    // Relationships ke child tables
    public function industriPrimer();
    public function industriSekunder();
    public function tptKb();
    public function endUser();
}
```

### 2. Concrete Model: `IndustriBase`
Model concrete untuk insert data ke tabel `industries`.

```php
class IndustriBase extends Model
{
    protected $table = 'industries';
    // Digunakan untuk insert parent record karena Industri adalah abstract
}
```

### 3. Child Models

#### `IndustriPrimer`
```php
class IndustriPrimer extends Model
{
    protected $table = 'industri_primer';
    
    protected $fillable = [
        'industri_id', 'pemberi_izin', 'jenis_produksi',
        'kapasitas_izin', 'pelaporan', 'dokumen_izin'
    ];
    
    // Relationship ke parent
    public function industri() {
        return $this->belongsTo(IndustriBase::class, 'industri_id');
    }
    
    // Specific methods
    public function isAktif(): bool;
    public function scopeByKapasitas($query, $kapasitas);
}
```

#### `IndustriSekunder`
```php
class IndustriSekunder extends Model
{
    protected $table = 'industri_sekunder';
    // Similar structure to IndustriPrimer
}
```

#### `TptKb`
```php
class TptKb extends Model
{
    protected $table = 'tpt_kb';
    
    // Specific methods
    public function isMasaBerlakuAktif(): bool;
    public function scopeBySumberBahanBaku($query, $sumber);
}
```

#### `EndUser`
```php
class EndUser extends Model
{
    protected $table = 'end_user';
    // Simplest model, only FK to parent
}
```

## 🔄 Data Flow

### Insert (Create)
```php
// Step 1: Insert to parent table
$industri = IndustriBase::create([
    'nama' => $nama,
    'alamat' => $alamat,
    // ... other common fields
    'type' => 'primer',
]);

// Step 2: Insert to child table with FK
IndustriPrimer::create([
    'industri_id' => $industri->id,
    'pemberi_izin' => $pemberi_izin,
    // ... other specific fields
]);
```

### Query (Read)
```php
// Query dengan eager loading
$data = IndustriPrimer::with('industri')
    ->whereHas('industri', function($q) {
        $q->where('kabupaten', 'Semarang');
    })
    ->get();

// Akses data
foreach($data as $item) {
    $item->industri->nama;      // dari parent
    $item->industri->kabupaten; // dari parent
    $item->jenis_produksi;      // dari child
    $item->kapasitas_izin;      // dari child
}
```

### Update
```php
// Update parent data
$industri = IndustriBase::find($id);
$industri->update([...common fields...]);

// Update child data
$industriPrimer = IndustriPrimer::where('industri_id', $id)->first();
$industriPrimer->update([...specific fields...]);
```

### Delete
```php
// Hapus parent -> child akan otomatis terhapus (CASCADE)
IndustriBase::find($id)->delete();
```

## 🎯 Keuntungan Pattern Ini

1. **No Data Duplication**: Common fields hanya disimpan sekali di parent table
2. **Referential Integrity**: Foreign key constraint memastikan data consistency
3. **Clean Separation**: Specific data terpisah di child tables
4. **Type Safety**: Discriminator column `type` untuk identifikasi jenis industri
5. **Cascading Delete**: Hapus parent otomatis hapus child (data integrity)

## 📊 Relationship Diagram

```
┌─────────────────────┐
│    industries       │ (Parent)
│  PK: id             │
│  - nama             │
│  - alamat           │
│  - kabupaten        │
│  - etc...           │
│  - type (enum)      │
└──────────┬──────────┘
           │
     ┌─────┴─────────────────────┐
     │                           │
┌────▼───────────────┐  ┌───────▼────────────┐
│ industri_primer    │  │ industri_sekunder  │
│ PK: id             │  │ PK: id             │
│ FK: industri_id ───┤  │ FK: industri_id ───┤
│ - pemberi_izin     │  │ - pemberi_izin     │
│ - jenis_produksi   │  │ - jenis_produksi   │
│ - kapasitas_izin   │  │ - kapasitas_izin   │
│ - pelaporan        │  └────────────────────┘
│ - dokumen_izin     │
└────────────────────┘
     │                           │
┌────▼──────────┐       ┌───────▼────────┐
│ tpt_kb        │       │ end_user       │
│ PK: id        │       │ PK: id         │
│ FK: industri_id       │ FK: industri_id
│ - pemberi_izin        │ (no additional)
│ - sumber_bahan        └────────────────┘
│ - kapasitas_izin
│ - masa_berlaku
└───────────────┘
```

## 🔍 Query Examples

### Filter by Kabupaten
```php
IndustriPrimer::whereHas('industri', function($q) {
    $q->where('kabupaten', 'Semarang');
})->get();
```

### Filter by Name + Capacity
```php
IndustriPrimer::with('industri')
    ->whereHas('industri', function($q) use ($nama) {
        $q->where('nama', 'like', "%$nama%");
    })
    ->where('kapasitas_izin', '>= 6001')
    ->get();
```

### Get Full Industry Data
```php
$item = IndustriPrimer::with('industri')->find($id);
echo $item->industri->nama;           // Parent data
echo $item->industri->kabupaten;      // Parent data  
echo $item->jenis_produksi;           // Child data
echo $item->kapasitas_izin;           // Child data
```

## 🛠️ Best Practices

1. **Always use eager loading** (`with('industri')`) untuk avoid N+1 queries
2. **Use transactions** untuk insert/update yang melibatkan parent dan child
3. **Filter parent fields** dengan `whereHas('industri', ...)`
4. **Filter child fields** langsung dengan `where(...)`
5. **Leverage CASCADE delete** untuk data integrity

---

**Created:** 2026-01-07  
**Pattern:** Table Per Type (TPT) Inheritance with Foreign Keys
**Status:** ✅ Fully Implemented
