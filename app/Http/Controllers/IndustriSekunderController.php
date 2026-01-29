<?php

namespace App\Http\Controllers;

use App\Models\IndustriSekunder;
use App\Models\MasterJenisProduksi;
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
        // Query dengan join ke tabel industries (parent) dan eager load jenis produksi
        $query = IndustriSekunder::with(['industri', 'jenisProduksi']);

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

        // Filter berdasarkan jenis produksi (dari relasi many-to-many)
        // Support untuk filter berdasarkan ID master, custom name, atau semua "Lainnya"
        if ($request->filled('jenis_produksi')) {
            $filterValue = $request->jenis_produksi;
            
            // Cek apakah filter value adalah ID atau custom name
            if (is_numeric($filterValue)) {
                // Cek apakah ini adalah ID untuk "Lainnya"
                $lainnyaRecord = MasterJenisProduksi::find($filterValue);
                
                if ($lainnyaRecord && $lainnyaRecord->nama === 'Lainnya') {
                    // Filter untuk semua yang punya custom name (tidak peduli value-nya)
                    $query->whereHas('jenisProduksi', function($q) {
                        $q->whereNotNull('industri_jenis_produksi.nama_custom');
                    });
                } else {
                    // Filter by master ID biasa
                    $query->whereHas('jenisProduksi', function($q) use ($filterValue) {
                        $q->where('master_jenis_produksi.id', $filterValue);
                    });
                }
            } else {
                // Filter by specific custom name
                $query->whereHas('jenisProduksi', function($q) use ($filterValue) {
                    $q->where('industri_jenis_produksi.nama_custom', $filterValue);
                });
            }
        }

        // Filter berdasarkan pemberi izin
        if ($request->filled('pemberi_izin')) {
            $query->where('pemberi_izin', $request->pemberi_izin);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->where('status', $request->status);
            });
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

        // Filter berdasarkan kapasitas izin (dari pivot table industri_jenis_produksi)
        // Menggunakan logika OR: tampilkan perusahaan jika MINIMAL SATU jenis produksi memenuhi range
        $kapasitasFilter = $request->filled('kapasitas') ? $request->kapasitas : null;

        // Jika ada filter kapasitas, lakukan filtering di collection level dengan logika OR
        if ($kapasitasFilter) {
            $allFiltered = (clone $query)->latest()->get();

            $filteredCollection = $allFiltered->filter(function($item) use ($kapasitasFilter) {
                // Cek apakah ada minimal satu jenis produksi yang memenuhi range
                foreach ($item->jenisProduksi as $jp) {
                    $numericValue = $jp->pivot->kapasitas_izin ?? 0;
                    
                    // Cek apakah kapasitas ini memenuhi range filter
                    $matches_range = false;
                    switch ($kapasitasFilter) {
                        case '0-1999':
                            $matches_range = $numericValue >= 0 && $numericValue <= 1999;
                            break;
                        case '2000-5999':
                            $matches_range = $numericValue >= 2000 && $numericValue <= 5999;
                            break;
                        case '>=6000':
                            $matches_range = $numericValue >= 6000;
                            break;
                    }
                    
                    // Jika ada satu jenis produksi yang memenuhi, return true (logika OR)
                    if ($matches_range) {
                        return true;
                    }
                }
                
                // Tidak ada satupun jenis produksi yang memenuhi range
                return false;
            })->values();

            // Manual pagination
            $currentPage = \request()->get('page', 1);
            $perPage = 10;
            $currentItems = $filteredCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $industriSekunder = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $filteredCollection->count(),
                $perPage,
                $currentPage,
                ['path' => url()->current(), 'query' => request()->query()]
            );

            // Untuk statistik, gunakan seluruh koleksi yang sudah difilter
            $filteredData = $filteredCollection;
        } else {
            // Ambil data dengan pagination
            $industriSekunder = $query->latest()->paginate(10);
            $filteredData = (clone $query)->get();
        }

        // Ambil daftar kabupaten Jawa Tengah dari API wilayah.id dengan cache
        $kabupatenList = Cache::remember('wilayah_jateng_kabupaten', 86400, function () {
            try {
                $response = Http::timeout(10)->get('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json');
                
                if ($response->successful()) {
                    $data = $response->json();
                    return collect($data)->pluck('name')->sort()->values();
                }
            } catch (\Exception $e) {
                \Log::error('Failed to fetch wilayah data: ' . $e->getMessage());
            }
            
            return \App\Models\IndustriBase::select('kabupaten')
                ->distinct()
                ->orderBy('kabupaten')
                ->pluck('kabupaten');
        });

        // Ambil daftar jenis produksi dari master
        $jenisProduksiList = MasterJenisProduksi::aktif()
            ->kategori('sekunder')
            ->orderBy('nama')
            ->get();
        
        // Ambil custom names yang unik dari pivot table untuk ditampilkan di filter
        $customNames = \DB::table('industri_jenis_produksi')
            ->where('industri_type', 'App\\Models\\IndustriSekunder')
            ->whereNotNull('nama_custom')
            ->distinct()
            ->pluck('nama_custom')
            ->filter()
            ->sort()
            ->values();

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
            // Hitung total kapasitas dari semua jenis produksi
            $totalCapacity = 0;
            foreach ($item->jenisProduksi as $jp) {
                $numericCapacity = $jp->pivot->kapasitas_izin ?? 0;
                $totalCapacity += $numericCapacity;
            }
            
            // Kelompokkan berdasarkan rentang numerik
            if ($totalCapacity >= 0 && $totalCapacity <= 1999) {
                return '0-1999 m³/tahun';
            } elseif ($totalCapacity >= 2000 && $totalCapacity <= 5999) {
                return '2000-5999 m³/tahun';
            } elseif ($totalCapacity >= 6000) {
                return '>=6000 m³/tahun';
            }
            return 'Lainnya';
        })->map->count();

        return view('Industri.industri-sekunder.index', compact(
            'industriSekunder',
            'kabupatenList',
            'jenisProduksiList',
            'customNames',
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
        $masterJenisProduksi = MasterJenisProduksi::aktif()
            ->kategori('sekunder')
            ->orderBy('nama')
            ->get();
            
        return view('Industri.industri-sekunder.create', compact('masterJenisProduksi'));
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'kontak' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'jenis_produksi' => 'required|array|min:1',
            'jenis_produksi.*' => 'required|exists:master_jenis_produksi,id',
            'kapasitas_izin' => 'required|array',
            'kapasitas_izin.*' => 'required|integer|min:0',
            'nama_custom' => 'nullable|array',
            'nama_custom.*' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'nomor_izin' => 'required|string|max:255',
        ]);

        // Step 1: Insert ke tabel industries (parent)
        $industri = \App\Models\IndustriBase::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kabupaten' => $validated['kabupaten'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'type' => 'sekunder',
        ]);

        // Step 2: Insert ke tabel industri_sekunder (child)
        $industriSekunder = IndustriSekunder::create([
            'industri_id' => $industri->id,
            'pemberi_izin' => $validated['pemberi_izin'],
            'kapasitas_izin' => $validated['kapasitas_izin'][0] ?? '0',
        ]);

        // Step 3: Attach jenis produksi ke tabel pivot
        $jenisProduksiData = [];
        foreach ($validated['jenis_produksi'] as $index => $jenisProduksiId) {
            $jenisProduksiData[$jenisProduksiId] = [
                'kapasitas_izin' => $validated['kapasitas_izin'][$index] ?? '0',
                'nama_custom' => $validated['nama_custom'][$index] ?? null
            ];
        }
        $industriSekunder->jenisProduksi()->attach($jenisProduksiData);

        return redirect()->route('industri-sekunder.index')
            ->with('success', 'Data industri sekunder berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit industri sekunder
     */
    public function edit($id)
    {
        $industriSekunder = IndustriSekunder::with(['industri', 'jenisProduksi'])->findOrFail($id);
        
        $masterJenisProduksi = MasterJenisProduksi::aktif()
            ->kategori('sekunder')
            ->orderBy('nama')
            ->get();
        
        return view('Industri.industri-sekunder.edit', compact('industriSekunder', 'masterJenisProduksi'));
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'kontak' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'jenis_produksi' => 'required|array|min:1',
            'jenis_produksi.*' => 'required|exists:master_jenis_produksi,id',
            'kapasitas_izin' => 'required|array',
            'kapasitas_izin.*' => 'required|integer|min:0',
            'nama_custom' => 'nullable|array',
            'nama_custom.*' => 'nullable|string|max:255',
            'tanggal' => 'required|date',
            'nomor_izin' => 'required|string|max:255',
            'status' => 'required|in:Aktif,Tidak Aktif',
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
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
        ]);

        // Update child table (industri_sekunder)
        $industriSekunder->update([
            'pemberi_izin' => $validated['pemberi_izin'],
        ]);

        // Sync jenis produksi (update many-to-many relationship)
        $jenisProduksiData = [];
        foreach ($validated['jenis_produksi'] as $index => $jenisProduksiId) {
            $jenisProduksiData[$jenisProduksiId] = [
                'kapasitas_izin' => $validated['kapasitas_izin'][$index] ?? '0',
                'nama_custom' => $validated['nama_custom'][$index] ?? null
            ];
        }
        $industriSekunder->jenisProduksi()->sync($jenisProduksiData);

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

    /**
     * Import data dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240' // Max 10MB
        ]);

        try {
            $file = $request->file('file');
            $filePath = $file->getRealPath();

            // Process import
            $importer = new \App\Imports\IndustriSekunderImport();
            $result = $importer->import($filePath);

            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$result['success']} data dari {$result['total']} baris",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }
}
