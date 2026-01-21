# Fitur "Lainnya" untuk Jenis Produksi

## Ringkasan
Fitur ini memungkinkan pengguna untuk memilih "Lainnya" dari dropdown jenis produksi dan mengisi nama jenis produksi custom secara manual.

## Implementasi

### 1. Database Schema
- **Tabel**: `industri_jenis_produksi` (pivot table)
- **Kolom Baru**: `nama_custom` (string, nullable)
- **Migration**: `2026_01_21_083015_add_custom_name_to_industri_jenis_produksi_table.php`

### 2. Master Data
Ditambahkan record baru di `master_jenis_produksi`:
- **Nama**: "Lainnya"
- **Kategori**: "both" (tersedia untuk Primer dan Sekunder)
- **Satuan**: "unit"
- **Keterangan**: "Jenis produksi lainnya (isi manual)"

### 3. Model Updates
**IndustriSekunder.php** & **IndustriPrimer.php**:
```php
public function jenisProduksi()
{
    return $this->morphToMany(
        MasterJenisProduksi::class,
        'industri',
        'industri_jenis_produksi',
        'industri_id',
        'jenis_produksi_id'
    )->withPivot('kapasitas_izin', 'nama_custom')->withTimestamps();
}
```

### 4. Controller Logic
**IndustriSekunderController.php**:

**Validasi**:
```php
'jenis_produksi' => 'required|array|min:1',
'jenis_produksi.*' => 'required|exists:master_jenis_produksi,id',
'kapasitas_izin' => 'required|array',
'kapasitas_izin.*' => 'required|string|max:255',
'nama_custom' => 'nullable|array',
'nama_custom.*' => 'nullable|string|max:255',
```

**Store & Update**:
```php
$jenisProduksiData = [];
foreach ($validated['jenis_produksi'] as $index => $jenisProduksiId) {
    $jenisProduksiData[$jenisProduksiId] = [
        'kapasitas_izin' => $validated['kapasitas_izin'][$index] ?? '0',
        'nama_custom' => $validated['nama_custom'][$index] ?? null
    ];
}
$industriSekunder->jenisProduksi()->attach($jenisProduksiData);
```

### 5. Views - Conditional Input

**JavaScript Logic (create.blade.php & edit.blade.php)**:
```javascript
let lainnyaId = null;

// Cari ID untuk "Lainnya"
masterJenisProduksi.forEach(jp => {
    if (jp.nama === 'Lainnya') {
        lainnyaId = jp.id;
    }
});

function toggleCustomInput(index) {
    const select = document.querySelector(`.jenis-select[data-index="${index}"]`);
    const customContainer = document.getElementById(`customInput_${index}`);
    const customInput = customContainer.querySelector('input[name="nama_custom[]"]');
    
    if (select.value == lainnyaId) {
        customContainer.style.display = 'block';
        customInput.required = true;
    } else {
        customContainer.style.display = 'none';
        customInput.required = false;
        customInput.value = '';
    }
}
```

**HTML Structure**:
```html
<select name="jenis_produksi[]" class="form-select jenis-select" 
        data-index="${jenisProduksiCounter}" 
        onchange="toggleCustomInput(${jenisProduksiCounter})" required>
    ${optionsHTML}
</select>

<!-- Conditional input (hidden by default) -->
<div id="customInput_${jenisProduksiCounter}" style="display: none;">
    <label>Sebutkan Jenis Produksi</label>
    <input type="text" name="nama_custom[]" placeholder="Masukkan jenis produksi...">
</div>
```

### 6. Display Logic (index.blade.php)
```blade
@foreach($item->jenisProduksi as $jp)
    @php
        $displayName = $jp->pivot->nama_custom ?: $jp->nama;
        $badgeClass = $jp->pivot->nama_custom ? 'badge-lainnya' : 'badge-jenis';
    @endphp
    <span class="badge {{ $badgeClass }}">{{ $displayName }}</span>
@endforeach
```

## User Experience

### Alur Penggunaan:
1. User klik "Tambah Jenis Produksi"
2. Pilih dropdown → Ada opsi "Lainnya" di paling bawah
3. Ketika "Lainnya" dipilih → Muncul input text di bawahnya
4. User ketik nama jenis produksi custom
5. Data tersimpan di `pivot.nama_custom`

### Display:
- **Jenis produksi normal**: Badge hijau dengan nama dari master
- **Jenis produksi custom**: Badge oranye dengan nama dari `nama_custom`

## Keuntungan
- ✅ Tidak perlu menambah record master untuk setiap jenis produksi baru
- ✅ Fleksibilitas tinggi untuk user
- ✅ Data tetap terstruktur (ada ID master untuk "Lainnya")
- ✅ Custom name tersimpan per-perusahaan di pivot table
- ✅ UI/UX intuitif dengan conditional display
- ✅ Validasi otomatis: custom input wajib diisi jika pilih "Lainnya"

## Files Changed
1. `database/migrations/2026_01_21_083015_add_custom_name_to_industri_jenis_produksi_table.php` ✅
2. `database/seeders/MasterJenisProduksiSeeder.php` ✅
3. `app/Models/IndustriSekunder.php` ✅
4. `app/Models/IndustriPrimer.php` ✅
5. `app/Http/Controllers/IndustriSekunderController.php` ✅
6. `resources/views/Industri/industri-sekunder/create.blade.php` ✅
7. `resources/views/Industri/industri-sekunder/edit.blade.php` ✅
8. `resources/views/Industri/industri-sekunder/index.blade.php` ✅

## Status
✅ **SELESAI** - Fitur sudah terimplementasi lengkap dan siap digunakan!

Server sedang berjalan di: http://127.0.0.1:8000
