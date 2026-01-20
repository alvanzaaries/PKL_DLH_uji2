<?php

namespace App\Http\Controllers;

use App\Models\IndustriSekunder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IndustriSekunderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth', 'role:admin'], only: [
                'create', 'store', 'edit', 'update', 'destroy'
            ]),
        ];
    }

    /**
     * Tampilkan daftar industri sekunder dengan filtering
     */
    public function index(Request $request)
    {
        // Query dengan join ke tabel industries (parent)
        $query = IndustriSekunder::with('industri'); // Eager load relationship

        // Filter berdasarkan nama (dari tabel industries)
        if ($request->filled('nama')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama . '%');
            });
        }

        // Filter berdasarkan kabupaten (dari tabel industries)
        if ($request->filled('kabupaten')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->where('kabupaten', $request->kabupaten);
            });
        }

        // Filter berdasarkan kapasitas izin (dari tabel industri_sekunder)
        // Karena kapasitas_izin sekarang VARCHAR, kita extract angka dan bandingkan dengan rentang
        if ($request->filled('kapasitas')) {
            $kapasitasRange = $request->kapasitas;
            
            $query->where(function($q) use ($kapasitasRange) {
                // Extract angka dari string kapasitas_izin menggunakan REGEXP atau CAST
                // Untuk MySQL: CAST(REGEXP_SUBSTR(kapasitas_izin, '[0-9]+') AS UNSIGNED)
                // Alternatif lebih portable: filter di PHP setelah query
                
                if ($kapasitasRange == '0-1999') {
                    // Cari data dengan angka 0-1999
                    $q->whereRaw("CAST(REGEXP_REPLACE(kapasitas_izin, '[^0-9]', '') AS UNSIGNED) BETWEEN 0 AND 1999");
                } elseif ($kapasitasRange == '2000-5999') {
                    // Cari data dengan angka 2000-5999
                    $q->whereRaw("CAST(REGEXP_REPLACE(kapasitas_izin, '[^0-9]', '') AS UNSIGNED) BETWEEN 2000 AND 5999");
                } elseif ($kapasitasRange == '>= 6000') {
                    // Cari data dengan angka >= 6000
                    $q->whereRaw("CAST(REGEXP_REPLACE(kapasitas_izin, '[^0-9]', '') AS UNSIGNED) >= 6000");
                }
            });
        }

        // Filter berdasarkan jenis produksi
        if ($request->filled('jenis_produksi')) {
            $query->where('jenis_produksi', $request->jenis_produksi);
        }

        // Filter berdasarkan tahun dan bulan (dari kolom tanggal di tabel industries) dengan logika AND
        if ($request->filled('tahun')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->whereYear('tanggal', $request->tahun);
            });
        }
        
        if ($request->filled('bulan')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->whereMonth('tanggal', $request->bulan);
            });
        }

        // Ambil data dengan pagination
        $industriSekunder = $query->latest()->paginate(10);

        // Ambil daftar kabupaten Jawa Tengah dari API wilayah.id dengan cache
        // ID Provinsi Jawa Tengah = 33
        $kabupatenList = Cache::remember('wilayah_jateng_kabupaten', 86400, function () {
            try {
                $response = Http::timeout(10)->get('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json');
                
                if ($response->successful()) {
                    $data = $response->json();
                    // Ambil hanya nama kabupaten/kota
                    return collect($data)->pluck('name')->sort()->values();
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch wilayah data: ' . $e->getMessage());
            }
            
            // Fallback ke data dari database jika API gagal
            return \App\Models\IndustriBase::select('kabupaten')
                ->distinct()
                ->orderBy('kabupaten')
                ->pluck('kabupaten');
        });

        // Ambil daftar jenis produksi
        $jenisProduksiList = IndustriSekunder::select('jenis_produksi')
            ->distinct()
            ->whereNotNull('jenis_produksi')
            ->where('jenis_produksi', '!=', '')
            ->orderBy('jenis_produksi')
            ->pluck('jenis_produksi');

        // Data untuk visualisasi chart — gunakan DATA YANG SAMA dengan hasil filter
        $filteredData = (clone $query)->get();

        // 1. Distribusi perusahaan per tahun (berdasarkan kolom tanggal di tabel industries)
        // Jika filter bulan aktif, tampilkan per bulan-tahun. Jika tidak, per tahun saja
        $yearStats = $filteredData->groupBy(function($item) use ($request) {
            if ($request->filled('bulan')) {
                // Format: "Jan 2025", "Feb 2025", dll
                return \Carbon\Carbon::parse($item->industri->tanggal)->format('M Y');
            }
            return \Carbon\Carbon::parse($item->industri->tanggal)->format('Y');
        })->map->count()->sortKeys();

        // 2. Distribusi lokasi industri (Top 5 Kabupaten)
        $locationStats = $filteredData->groupBy('industri.kabupaten')
            ->map->count()
            ->sortDesc()
            ->take(5);

        // 3. Distribusi berdasarkan kapasitas izin
        $capacityStats = $filteredData->groupBy(function($item) {
            $capacity = $item->kapasitas_izin;
            
            // Extract angka dari string (misal "1500 m³/tahun" -> 1500)
            preg_match('/\d+/', $capacity, $matches);
            $numericCapacity = isset($matches[0]) ? (int)$matches[0] : 0;
            
            // Kelompokkan berdasarkan rentang numerik
            if ($numericCapacity >= 0 && $numericCapacity <= 1999) {
                return '0-1999 m³/tahun';
            } elseif ($numericCapacity >= 2000 && $numericCapacity <= 5999) {
                return '2000-5999 m³/tahun';
            } elseif ($numericCapacity >= 6000) {
                return '>=6000 m³/tahun';
            }
            return 'Lainnya';
        })->map->count();

        return view('Industri.industri-sekunder.index', compact(
            'industriSekunder', 
            'kabupatenList',
            'jenisProduksiList',
            'yearStats',
            'locationStats',
            'capacityStats'
        ));
    }

    /**
     * Tampilkan form tambah industri sekunder
     */
    public function create()
    {
        return view('Industri.industri-sekunder.create');
    }

    /**
     * Simpan data industri sekunder baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'penanggungjawab' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'jenis_produksi' => 'required|string|max:255',
            'kapasitas_izin' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nomor_izin' => 'required|string|max:255',
        ]);

        // Step 1: Insert ke tabel industries (parent) dulu
        $industri = \App\Models\IndustriBase::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kabupaten' => $validated['kabupaten'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'type' => 'sekunder',
        ]);

        // Step 2: Insert ke tabel industri_sekunder (child) dengan FK industri_id
        IndustriSekunder::create([
            'industri_id' => $industri->id, // FK ke parent
            'pemberi_izin' => $validated['pemberi_izin'],
            'jenis_produksi' => $validated['jenis_produksi'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('industri-sekunder.index')
            ->with('success', 'Data industri sekunder berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit industri sekunder
     */
    public function edit($id)
    {
        $industriSekunder = IndustriSekunder::with('industri')->findOrFail($id);
        
        return view('Industri.industri-sekunder.edit', compact('industriSekunder'));
    }

    /**
     * Update data industri sekunder
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'penanggungjawab' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'jenis_produksi' => 'required|string|max:255',
            'kapasitas_izin' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nomor_izin' => 'required|string|max:255',
        ]);

        // Find records
        $industriSekunder = IndustriSekunder::findOrFail($id);
        $industri = \App\Models\IndustriBase::findOrFail($industriSekunder->industri_id);

        // Update parent table (industries)
        $industri->update([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kabupaten' => $validated['kabupaten'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
        ]);

        // Update child table (industri_sekunder)
        $industriSekunder->update([
            'pemberi_izin' => $validated['pemberi_izin'],
            'jenis_produksi' => $validated['jenis_produksi'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
        ]);

        return redirect()->route('industri-sekunder.index')
            ->with('success', 'Data industri sekunder berhasil diupdate!');
    }

    /**
     * Hapus data industri sekunder
     */
    public function destroy($id)
    {
        $industriSekunder = IndustriSekunder::with('industri')->findOrFail($id);

        // Hapus parent record (child akan terhapus otomatis karena CASCADE)
        $industri = \App\Models\IndustriBase::findOrFail($industriSekunder->industri_id);
        $namaPerusahaan = $industri->nama;
        $industri->delete();

        return redirect()->route('industri-sekunder.index')
            ->with('success', "Perusahaan \"$namaPerusahaan\" berhasil dihapus!");
    }
}
