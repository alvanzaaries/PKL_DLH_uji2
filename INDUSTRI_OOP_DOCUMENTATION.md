# Dokumentasi Struktur OOP Industri

## Arsitektur Inheritance

```
Industri (Abstract Base Class)
│
├── IndustriPrimer (PBPHH)
├── IndustriSekunder (PBUI)
├── TptKb (Tempat Pengumpulan Kayu Bulat)
└── EndUser (Perajin)
```

## 1. Base Class: Industri

**File:** `app/Models/Industri.php`

### Common Fields (Inherited oleh semua child):
- `nama` - Nama perusahaan
- `alamat` - Alamat lengkap
- `penanggungjawab` - Nama pimpinan/direktur
- `kabupaten` - Kabupaten/Kota
- `kontak` - Nomor telepon/email
- `nomor_izin` - Nomor izin/NIB/SS

### Methods:
- `abstract getJenisIndustri(): string` - Harus di-implement oleh child
- `scopeByKabupaten()` - Filter berdasarkan kabupaten
- `scopeByNama()` - Filter berdasarkan nama

## 2. Child Classes

### A. IndustriPrimer (PBPHH)
**Table:** `industri_primer`

#### Additional Fields:
- `pemberi_izin` - Instansi pemberi izin
- `jenis_produksi` - Jenis produksi
- `kapasitas_izin` - Kapasitas izin (enum: <= 2000, 2001-6000, >= 6001)
- `pelaporan` - Status pelaporan (Aktif/Tidak Aktif/Pending)
- `dokumen_izin` - Path file PDF

#### Methods:
- `getJenisIndustri()` → "Primer/PBPHH"
- `scopeByKapasitas($kapasitas)` - Filter kapasitas
- `isAktif()` - Check status aktif

#### Contoh Penggunaan:
```php
// Create
$primer = IndustriPrimer::create([
    'nama' => 'PT Kayu Lestari',
    'alamat' => 'Jl. Kehutanan No. 123',
    'penanggungjawab' => 'Budi Santoso',
    'kabupaten' => 'Kabupaten Semarang',
    'kontak' => '081234567890',
    'pemberi_izin' => 'Dinas Kehutanan',
    'jenis_produksi' => 'Kayu Olahan',
    'kapasitas_izin' => '<= 2000',
    'nomor_izin' => 'NIB-12345',
    'pelaporan' => 'Aktif'
]);

// Query dengan inheritance
$aktif = IndustriPrimer::where('pelaporan', 'Aktif')->get();
$diSemarang = IndustriPrimer::byKabupaten('Kabupaten Semarang')->get();
$kapasitasKecil = IndustriPrimer::byKapasitas('<= 2000')->get();

// Method check
if ($primer->isAktif()) {
    echo "Industri sedang aktif";
}
```

### B. IndustriSekunder (PBUI)
**Table:** `industri_sekunder`

#### Additional Fields:
- `pemberi_izin`
- `jenis_produksi` - Jenis produksi/komoditas
- `kapasitas_izin` - Kapasitas (enum)

#### Methods:
- `getJenisIndustri()` → "Sekunder/PBUI"
- `scopeByKapasitas($kapasitas)`

#### Contoh Penggunaan:
```php
$sekunder = IndustriSekunder::create([
    'nama' => 'CV Furniture Jaya',
    'alamat' => 'Jl. Industri No. 45',
    'penanggungjawab' => 'Ahmad Wijaya',
    'kabupaten' => 'Kota Semarang',
    'kontak' => '082345678901',
    'pemberi_izin' => 'Dinas Perindustrian',
    'jenis_produksi' => 'Mebel',
    'kapasitas_izin' => '2001 - 6000',
    'nomor_izin' => 'NIB-23456'
]);
```

### C. TptKb (Tempat Pengumpulan Kayu Bulat)
**Table:** `tpt_kb`

#### Additional Fields:
- `pemberi_izin`
- `sumber_bahan_baku` - Enum: Hutan Alam/Perhutani/Hutan Rakyat
- `kapasitas_izin` - Kapasitas (enum)
- `masa_berlaku` - Tanggal masa berlaku

#### Methods:
- `getJenisIndustri()` → "TPT-KB"
- `scopeBySumberBahanBaku($sumber)`
- `isMasaBerlakuAktif()` - Check apakah masa berlaku masih valid

#### Contoh Penggunaan:
```php
$tptkb = TptKb::create([
    'nama' => 'TPT Hutan Jati',
    'alamat' => 'Jl. Perkayuan No. 78',
    'penanggungjawab' => 'Siti Rahayu',
    'kabupaten' => 'Kabupaten Blora',
    'kontak' => '083456789012',
    'pemberi_izin' => 'Dinas Kehutanan Provinsi',
    'sumber_bahan_baku' => 'Perhutani',
    'kapasitas_izin' => '>= 6001',
    'nomor_izin' => 'TPT-34567',
    'masa_berlaku' => '2027-12-31'
]);

// Check masa berlaku
if ($tptkb->isMasaBerlakuAktif()) {
    echo "Izin masih berlaku";
}

// Filter by sumber
$darihutanAlam = TptKb::bySumberBahanBaku('Hutan Alam')->get();
```

### D. EndUser (Perajin)
**Table:** `end_user`

#### Fields (Paling Sederhana):
- Hanya common fields dari base class

#### Methods:
- `getJenisIndustri()` → "End User/Perajin"

#### Contoh Penggunaan:
```php
$perajin = EndUser::create([
    'nama' => 'Perajin Kayu Jepara',
    'alamat' => 'Desa Tahunan, Jepara',
    'penanggungjawab' => 'Pak Hadi',
    'kabupaten' => 'Kabupaten Jepara',
    'kontak' => '084567890123',
    'nomor_izin' => 'NIB-45678'
]);
```

## Keuntungan Inheritance Pattern

### 1. **Code Reusability**
```php
// Semua child bisa pakai method dari parent
$industri = IndustriPrimer::byKabupaten('Semarang')->get();
$sekunder = IndustriSekunder::byKabupaten('Semarang')->get();
```

### 2. **Polymorphism**
```php
function printJenisIndustri(Industri $industri) {
    echo $industri->getJenisIndustri();
}

printJenisIndustri($primer);    // Output: Primer/PBPHH
printJenisIndustri($sekunder);  // Output: Sekunder/PBUI
printJenisIndustri($tptkb);     // Output: TPT-KB
printJenisIndustri($perajin);   // Output: End User/Perajin
```

### 3. **Type Safety**
```php
// Type hinting dengan base class
function getTotalIndustri(Industri $industri): int {
    return $industri->count();
}
```

### 4. **Maintainability**
- Common logic di satu tempat (base class)
- Spesifik logic di child class
- Mudah menambah jenis industri baru

## Query Examples

### Get All Industri by Type
```php
$allPrimer = IndustriPrimer::all();
$allSekunder = IndustriSekunder::all();
$allTptKb = TptKb::all();
$allEndUser = EndUser::all();
```

### Filter dengan Common Methods
```php
// Semua industri di Kabupaten Semarang
$primerSemarang = IndustriPrimer::byKabupaten('Kabupaten Semarang')->get();
$sekunderSemarang = IndustriSekunder::byKabupaten('Kabupaten Semarang')->get();

// Search by nama
$search = IndustriPrimer::byNama('Kayu')->get();
```

### Complex Queries
```php
// Industri Primer di Semarang dengan kapasitas kecil
$result = IndustriPrimer::byKabupaten('Kabupaten Semarang')
    ->byKapasitas('<= 2000')
    ->where('pelaporan', 'Aktif')
    ->get();

// TPT-KB dari Perhutani yang masa berlakunya masih aktif
$tptkb = TptKb::bySumberBahanBaku('Perhutani')
    ->whereDate('masa_berlaku', '>', now())
    ->get();
```

## Upgrade Dashboard Controller

Update `DashboardController` untuk menghitung dari semua jenis:

```php
public function index()
{
    $statistics = [
        'primer_pbphh' => IndustriPrimer::count(),
        'sekunder_pbui' => IndustriSekunder::count(),
        'tpt_kb' => TptKb::count(),
        'perajin' => EndUser::count(),
    ];

    $statistics['total_industri'] = array_sum($statistics);

    return view('dashboard', compact('statistics'));
}
```
