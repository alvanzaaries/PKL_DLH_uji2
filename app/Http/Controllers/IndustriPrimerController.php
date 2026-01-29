<?php

namespace App\Http\Controllers;

use App\Models\IndustriPrimer;
use App\Models\MasterJenisProduksi;
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
        $masterJenisProduksi = MasterJenisProduksi::aktif()
            ->kategori('primer')
            ->orderBy('nama')
            ->get();
            
        return view('Industri.industri-primer.create', compact('masterJenisProduksi'));
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
            'dokumen_izin' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ]);

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
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'type' => 'primer',
        ]);

        // Step 2: Insert ke tabel industri_primer (child) dengan FK industri_id
        $industriPrimer = IndustriPrimer::create([
            'industri_id' => $industri->id, // FK ke parent
            'pemberi_izin' => $validated['pemberi_izin'],
            'kapasitas_izin' => $validated['kapasitas_izin'][0] ?? '0', // Kapasitas default (bisa diupdate nanti)
            'dokumen_izin' => $validated['dokumen_izin'] ?? null,
        ]);

        // Step 3: Attach jenis produksi ke tabel pivot dengan kapasitas masing-masing
        $jenisProduksiData = [];
        foreach ($validated['jenis_produksi'] as $index => $jenisProduksiId) {
            $jenisProduksiData[$jenisProduksiId] = [
                'kapasitas_izin' => $validated['kapasitas_izin'][$index] ?? '0',
                'nama_custom' => $validated['nama_custom'][$index] ?? null
            ];
        }
        $industriPrimer->jenisProduksi()->attach($jenisProduksiData);

        // Redirect dengan pesan sukses
        return redirect()->route('industri-primer.index')
            ->with('success', 'Data industri primer berhasil ditambahkan!');
    }

    /**
     * Tampilkan daftar industri primer dengan filtering
     */
    public function index(Request $request)
    {
        // Query dengan join ke tabel industries (parent) dan eager load jenis produksi
        $query = IndustriPrimer::with(['industri', 'jenisProduksi']);

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
        // Contoh: Perusahaan punya Veneer 2500 dan Gergajian 4500
        // - Filter < 3000 → muncul (karena Veneer 2500 memenuhi)
        // - Filter 3000-5999 → muncul (karena Gergajian 4500 memenuhi)
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

        // Ambil daftar jenis produksi dari master_jenis_produksi untuk filter dropdown
        $jenisProduksiList = MasterJenisProduksi::aktif()
            ->kategori('primer')
            ->orderBy('nama')
            ->get();
        
        // Ambil custom names yang unik dari pivot table untuk ditampilkan di filter
        $customNames = \DB::table('industri_jenis_produksi')
            ->where('industri_type', 'App\\Models\\IndustriPrimer')
            ->whereNotNull('nama_custom')
            ->distinct()
            ->pluck('nama_custom')
            ->filter()
            ->sort()
            ->values();
            
        // Parse jenis produksi yang mengandung multiple values tidak diperlukan lagi
        // karena sudah menggunakan master table

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
            'customNames',
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
        $industriPrimer = IndustriPrimer::with(['industri', 'jenisProduksi'])->findOrFail($id);
        
        $masterJenisProduksi = MasterJenisProduksi::aktif()
            ->kategori('primer')
            ->orderBy('nama')
            ->get();
        
        return view('Industri.industri-primer.edit', compact('industriPrimer', 'masterJenisProduksi'));
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
            'dokumen_izin' => 'nullable|file|mimes:pdf|max:5120',
            'hapus_dokumen' => 'nullable|in:0,1'
        ]);

        // Find records
        $industriPrimer = IndustriPrimer::findOrFail($id);
        $industri = \App\Models\IndustriBase::findOrFail($industriPrimer->industri_id);

        // Handle hapus dokumen jika user memilih untuk menghapus
        if ($request->input('hapus_dokumen') == '1') {
            // Hapus file fisik dari storage jika ada
            if ($industriPrimer->dokumen_izin) {
                Storage::disk('public')->delete($industriPrimer->dokumen_izin);
            }
            // Set ke null untuk update database
            $validated['dokumen_izin'] = null;
        }
        // Handle file upload jika ada file baru dengan penamaan terstruktur
        elseif ($request->hasFile('dokumen_izin')) {
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
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
        ]);

        // Update child table (industri_primer)
        $updateData = [
            'pemberi_izin' => $validated['pemberi_izin'],
            'kapasitas_izin' => $validated['kapasitas_izin'][0] ?? '0',
        ];

        // Update dokumen_izin jika ada perubahan (upload baru atau hapus)
        // Gunakan array_key_exists karena nilai bisa null (saat hapus dokumen)
        if (array_key_exists('dokumen_izin', $validated)) {
            $updateData['dokumen_izin'] = $validated['dokumen_izin'];
        }

        $industriPrimer->update($updateData);

        // Sync jenis produksi dengan kapasitas masing-masing
        $jenisProduksiData = [];
        foreach ($validated['jenis_produksi'] as $index => $jenisProduksiId) {
            $jenisProduksiData[$jenisProduksiId] = [
                'kapasitas_izin' => $validated['kapasitas_izin'][$index] ?? '0',
                'nama_custom' => $validated['nama_custom'][$index] ?? null
            ];
        }
        $industriPrimer->jenisProduksi()->sync($jenisProduksiData);

        // Buat pesan sukses yang informatif
        $successMessage = 'Data industri primer berhasil diupdate!';
        
        // Tambahkan info dokumen jika ada perubahan
        if ($request->input('hapus_dokumen') == '1') {
            $successMessage .= ' Dokumen izin telah dihapus.';
        } elseif ($request->hasFile('dokumen_izin')) {
            $successMessage .= ' Dokumen izin baru telah diupload.';
        }

        return redirect()->route('industri-primer.index')
            ->with('success', $successMessage)
            ->with('updated_id', $id); // Kirim ID yang diupdate untuk highlight
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
     * View dokumen izin inline di browser (Requires Admin Authentication)
     */
    public function viewDokumen($id)
    {
        // Security: Require admin authentication to prevent information disclosure
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang dapat melihat dokumen.');
        }

        $industriPrimer = IndustriPrimer::with('industri')->findOrFail($id);
        
        // Check if document exists
        if (!$industriPrimer->dokumen_izin) {
            abort(404, 'Dokumen tidak ditemukan!');
        }

        $disk = Storage::disk('public');
        $relativePath = $industriPrimer->dokumen_izin;

        // Cek keberadaan file pada disk 'public'
        if ($disk->exists($relativePath)) {
            try {
                // Return file dengan header inline agar bisa dibuka di browser
                return response()->file($disk->path($relativePath), [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . basename($relativePath) . '"'
                ]);
            } catch (\Exception $e) {
                \Log::error('Storage view failed: ' . $e->getMessage());
                abort(500, 'Gagal membuka file.');
            }
        }

        // Fallback: cek langsung di storage/app/public
        $fullPath = storage_path('app/public/' . $relativePath);
        if (file_exists($fullPath)) {
            \Log::warning("File exists at storage path but not via Storage disk: {$fullPath}");
            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($relativePath) . '"'
            ]);
        }

        \Log::warning('File not found for view: ' . $relativePath);
        abort(404, 'File tidak ditemukan di server!');
    }

    /**
     * Download dokumen izin (Requires Authentication)
     */
    public function downloadDokumen($id)
    {
        // Security: Require authentication to prevent information disclosure
        if (!auth()->check()) {
            abort(403, 'Akses ditolak. Dokumen ini memerlukan autentikasi.');
        }

        $industriPrimer = IndustriPrimer::with('industri')->findOrFail($id);
        
        // Check if document exists
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

    /**
     * Import data dari Excel
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:10240' // Max 10MB
            ]);

            $file = $request->file('file');
            $filePath = $file->getRealPath();

            \Log::info('Starting import from file: ' . $file->getClientOriginalName());

            // Process import
            $importer = new \App\Imports\IndustriPrimerImport();
            $result = $importer->import($filePath);

            \Log::info('Import completed', $result);

            return response()->json([
                'success' => true,
                'message' => "Berhasil import {$result['success']} data dari {$result['total']} baris",
                'data' => $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in import', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi file gagal: ' . implode(', ', $e->errors()['file'] ?? ['Unknown error'])
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Import error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal import data: ' . $e->getMessage()
            ], 500);
        }
    }
}
