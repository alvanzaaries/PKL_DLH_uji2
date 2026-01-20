<?php

namespace App\Http\Controllers;

use App\Models\IndustriPrimer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IndustriPrimerController extends Controller implements HasMiddleware
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
     * Tampilkan form tambah industri primer
     */
    public function create()
    {
        return view('Industri.industri-primer.create');
    }

    /**
     * Simpan data industri primer baru
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
            'jenis_produksi_lainnya' => 'nullable|string|max:255',
            'kapasitas_izin' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nomor_izin' => 'required|string|max:255',
            'dokumen_izin' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ]);

        // Jika jenis produksi adalah Lainnya, gunakan input manual
        if ($validated['jenis_produksi'] === 'Lainnya' && $request->filled('jenis_produksi_lainnya')) {
            $validated['jenis_produksi'] = $validated['jenis_produksi_lainnya'];
        }

        // Upload dokumen izin jika ada dengan penamaan terstruktur
        if ($request->hasFile('dokumen_izin')) {
            $file = $request->file('dokumen_izin');
            
            // Format penamaan: PRIMER_[NAMA]_[TANGGAL]_[RANDOM]
            $namaClean = preg_replace('/[^A-Za-z0-9]/', '_', $validated['nama']);
            $namaClean = substr($namaClean, 0, 50); // Batasi panjang nama
            $tanggal = date('Ymd_His');
            $random = substr(md5(uniqid()), 0, 6);
            $fileName = "PRIMER_{$namaClean}_{$tanggal}_{$random}.pdf";
            
            // Simpan ke folder dokumen-izin/primer dengan folder per tahun
            $year = date('Y');
            $filePath = $file->storeAs("dokumen-izin/primer/{$year}", $fileName, 'public');
            $validated['dokumen_izin'] = $filePath;
        }

        // Step 1: Insert ke tabel industries (parent) dulu
        $industri = \App\Models\IndustriBase::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kabupaten' => $validated['kabupaten'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'type' => 'primer',
        ]);

        // Step 2: Insert ke tabel industri_primer (child) dengan FK industri_id
        IndustriPrimer::create([
            'industri_id' => $industri->id, // FK ke parent
            'pemberi_izin' => $validated['pemberi_izin'],
            'jenis_produksi' => $validated['jenis_produksi'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
            'dokumen_izin' => $validated['dokumen_izin'] ?? null,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('industri-primer.index')
            ->with('success', 'Data industri primer berhasil ditambahkan!');
    }

    /**
     * Tampilkan daftar industri primer dengan filtering
     */
    public function index(Request $request)
    {
        // Query dengan join ke tabel industries (parent)
        $query = IndustriPrimer::with('industri'); // Eager load relationship

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

        // Filter berdasarkan jenis produksi (dari tabel industri_primer)
        // Gunakan LIKE untuk partial matching agar bisa menemukan data 
        // walaupun jenis_produksi berisi beberapa nilai (misal: "olahan, papan, dan gergajian")
        if ($request->filled('jenis_produksi')) {
            $query->where('jenis_produksi', 'like', '%' . $request->jenis_produksi . '%');
        }

        // Filter berdasarkan kapasitas izin (dari tabel industri_primer)
        // kapasitas_izin disimpan sebagai string (mis. "1500 m³/tahun") sehingga
        // ekstraksi angka dengan fungsi SQL tidak portable (SQLite tidak mendukung REGEXP_REPLACE dll).
        // Untuk kompatibilitas, jika filter kapasitas di-set, lakukan filtering di PHP setelah mengambil hasil.
        $kapasitasFilter = null;
        if ($request->filled('kapasitas')) {
            $kapasitasFilter = $request->kapasitas;
            // do NOT add SQL whereRaw here because it may not be supported on all drivers
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

        // Jika ada filter kapasitas, ambil semua hasil (setelah filter lain) lalu lakukan filter kapasitas di PHP,
        // kemudian paginasi manual agar kompatibel dengan SQLite dan driver lain.
        if ($kapasitasFilter) {
            $allFiltered = (clone $query)->latest()->get();

            $filteredCollection = $allFiltered->filter(function($item) use ($kapasitasFilter) {
                $capacity = $item->kapasitas_izin ?? '';
                preg_match('/\d+/', $capacity, $matches);
                $numericCapacity = isset($matches[0]) ? (int)$matches[0] : 0;

                if ($kapasitasFilter == '0-1999') {
                    return $numericCapacity >= 0 && $numericCapacity <= 1999;
                } elseif ($kapasitasFilter == '2000-5999') {
                    return $numericCapacity >= 2000 && $numericCapacity <= 5999;
                } elseif ($kapasitasFilter == '>= 6000' || $kapasitasFilter == '>=6000') {
                    return $numericCapacity >= 6000;
                }
                return false;
            })->values();

            // Manual pagination
            $currentPage = \request()->get('page', 1);
            $perPage = 10;
            $currentItems = $filteredCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $industriPrimer = new \Illuminate\Pagination\LengthAwarePaginator(
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
            $industriPrimer = $query->latest()->paginate(10);

            // Ambil daftar kabupaten Jawa Tengah dari API wilayah.id dengan cache
            $filteredData = (clone $query)->get();
        }
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
            return \App\Models\IndustriBase::where('type', 'primer')
                ->distinct()
                ->orderBy('kabupaten')
                ->pluck('kabupaten');
        });

        // Ambil daftar jenis produksi yang sudah terdaftar di database
        $jenisProduksiList = IndustriPrimer::distinct()
            ->orderBy('jenis_produksi')
            ->pluck('jenis_produksi')
            ->filter() // Remove null values
            ->values();
            
        // Parse jenis produksi yang mengandung multiple values (misal: "olahan, papan, gergajian")
        // Split by comma dan ambil unique values
        $jenisProduksiUnique = collect();
        foreach ($jenisProduksiList as $jenis) {
            $split = array_map('trim', explode(',', $jenis));
            foreach ($split as $item) {
                if (!empty($item)) {
                    $jenisProduksiUnique->push($item);
                }
            }
        }
        $jenisProduksiList = $jenisProduksiUnique->unique()->sort()->values();

        // Data untuk visualisasi chart — gunakan DATA YANG SAMA dengan hasil filter
        // Ambil semua record dari query yang sudah diberi filter (clone agar paginate tetap bekerja)
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

        return view('Industri.industri-primer.index', compact(
            'industriPrimer', 
            'kabupatenList',
            'jenisProduksiList',
            'yearStats',
            'locationStats',
            'capacityStats'
        ));
    }

    /**
     * Tampilkan form edit industri primer
     */
    public function edit($id)
    {
        $industriPrimer = IndustriPrimer::with('industri')->findOrFail($id);
        
        return view('Industri.industri-primer.edit', compact('industriPrimer'));
    }

    /**
     * Update data industri primer
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
            'jenis_produksi_lainnya' => 'nullable|string|max:255',
            'kapasitas_izin' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'nomor_izin' => 'required|string|max:255',
            'dokumen_izin' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        // Jika jenis produksi adalah Lainnya, gunakan input manual
        if ($validated['jenis_produksi'] === 'Lainnya' && $request->filled('jenis_produksi_lainnya')) {
            $validated['jenis_produksi'] = $validated['jenis_produksi_lainnya'];
        }

        // Find records
        $industriPrimer = IndustriPrimer::findOrFail($id);
        $industri = \App\Models\IndustriBase::findOrFail($industriPrimer->industri_id);

        // Handle file upload jika ada file baru dengan penamaan terstruktur
        if ($request->hasFile('dokumen_izin')) {
            // Hapus file lama jika ada
            if ($industriPrimer->dokumen_izin) {
                Storage::disk('public')->delete($industriPrimer->dokumen_izin);
            }

            $file = $request->file('dokumen_izin');
            
            // Format penamaan: PRIMER_[NAMA]_[TANGGAL]_[RANDOM]
            $namaClean = preg_replace('/[^A-Za-z0-9]/', '_', $validated['nama']);
            $namaClean = substr($namaClean, 0, 50); // Batasi panjang nama
            $tanggal = date('Ymd_His');
            $random = substr(md5(uniqid()), 0, 6);
            $fileName = "PRIMER_{$namaClean}_{$tanggal}_{$random}.pdf";
            
            // Simpan ke folder dokumen-izin/primer dengan folder per tahun
            $year = date('Y');
            $filePath = $file->storeAs("dokumen-izin/primer/{$year}", $fileName, 'public');
            $validated['dokumen_izin'] = $filePath;
        }

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

        // Update child table (industri_primer)
        $updateData = [
            'pemberi_izin' => $validated['pemberi_izin'],
            'jenis_produksi' => $validated['jenis_produksi'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
        ];

        if (isset($validated['dokumen_izin'])) {
            $updateData['dokumen_izin'] = $validated['dokumen_izin'];
        }

        $industriPrimer->update($updateData);

        return redirect()->route('industri-primer.index')
            ->with('success', 'Data industri primer berhasil diupdate!');
    }

    /**
     * Hapus data industri primer
     */
    public function destroy($id)
    {
        $industriPrimer = IndustriPrimer::with('industri')->findOrFail($id);

        // Hapus file dokumen jika ada
        if ($industriPrimer->dokumen_izin) {
            Storage::disk('public')->delete($industriPrimer->dokumen_izin);
        }

        // Hapus parent record (child akan terhapus otomatis karena CASCADE)
        $industri = \App\Models\IndustriBase::findOrFail($industriPrimer->industri_id);
        $namaPerusahaan = $industri->nama;
        $industri->delete();

        return redirect()->route('industri-primer.index')
            ->with('success', "Perusahaan \"$namaPerusahaan\" berhasil dihapus!");
    }

    /**
     * Download dokumen izin
     */
    public function downloadDokumen($id)
    {
        $industriPrimer = IndustriPrimer::with('industri')->findOrFail($id);
        
        if (!$industriPrimer->dokumen_izin) {
            return redirect()->back()->with('error', 'Dokumen tidak ditemukan!');
        }

        // Prefer menggunakan Storage disk agar streaming dan header ditangani oleh framework
        $disk = Storage::disk('public');
        $relativePath = $industriPrimer->dokumen_izin;

        // Generate nama file yang user-friendly untuk download
        $namaFile = preg_replace('/[^A-Za-z0-9]/', '_', $industriPrimer->industri->nama);
        $downloadName = "Dokumen_Izin_{$namaFile}.pdf";

        // Cek keberadaan file pada disk 'public'
        if ($disk->exists($relativePath)) {
            try {
                return $disk->download($relativePath, $downloadName);
            } catch (\Exception $e) {
                \Log::error('Storage download failed: ' . $e->getMessage());
                // fallback ke response()->download menggunakan path langsung
                $fullPath = $disk->path($relativePath);
                if (file_exists($fullPath)) {
                    return response()->download($fullPath, $downloadName);
                }
                return redirect()->back()->with('error', 'Gagal mengunduh file.');
            }
        }

        // Fallback: cek langsung di storage/app/public
        $fullPath = storage_path('app/public/' . $relativePath);
        if (file_exists($fullPath)) {
            \Log::warning("File exists at storage path but not via Storage disk: {$fullPath}");
            return response()->download($fullPath, $downloadName);
        }

        \Log::warning('File not found for download: ' . $relativePath);
        return redirect()->back()->with('error', 'File tidak ditemukan di server!');
    }
}
