<?php

namespace App\Http\Controllers;

use App\Models\IndustriSekunder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IndustriSekunderController extends Controller
{
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
        if ($request->filled('kapasitas')) {
            $query->where('kapasitas_izin', $request->kapasitas);
        }

        // Filter berdasarkan tahun (dari created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
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

        // Data untuk visualisasi chart
        // 1. Distribusi perusahaan per tahun (berdasarkan created_at)
        $allData = IndustriSekunder::with('industri')->get();
        $yearStats = $allData->groupBy(function($item) {
            return $item->created_at->format('Y');
        })->map->count()->sortKeys();

        // 2. Distribusi lokasi industri (Top 5 Kabupaten)
        $locationStats = $allData->groupBy('industri.kabupaten')
            ->map->count()
            ->sortDesc()
            ->take(5);

        // 3. Distribusi berdasarkan kapasitas izin
        $capacityStats = $allData->groupBy(function($item) {
            $capacity = $item->kapasitas_izin;
            // Cek dengan format yang sesuai dengan data di database
            if (strpos($capacity, '0 - 1999') !== false || strpos($capacity, '0-1999') !== false) {
                return '0-1999 m³/tahun';
            }
            if (strpos($capacity, '2000 - 5999') !== false || strpos($capacity, '2000-5999') !== false) {
                return '2000-5999 m³/tahun';
            }
            if (strpos($capacity, '>= 6000') !== false || strpos($capacity, '>=6000') !== false) {
                return '>=6000 m³/tahun';
            }
            return 'Lainnya';
        })->map->count();

        return view('industri-sekunder.index', compact(
            'industriSekunder', 
            'kabupatenList',
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
        return view('industri-sekunder.create');
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
        
        return view('industri-sekunder.edit', compact('industriSekunder'));
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
