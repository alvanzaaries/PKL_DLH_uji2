<?php

namespace App\Http\Controllers;

use App\Models\Tptkb;
use App\Models\IndustriBase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TptkbController extends Controller implements HasMiddleware
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
     * Auto-update status menjadi "Tidak Aktif" jika masa berlaku sudah kadaluarsa
     */
    private function updateExpiredStatus()
    {
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        
        // Ambil semua TPTKB yang masa berlakunya sudah lewat dan statusnya masih Aktif
        $expiredTptkb = Tptkb::with('industri')
            ->where('masa_berlaku', '<', $today)
            ->whereHas('industri', function($q) {
                $q->where('status', 'Aktif');
            })
            ->get();

        // Update status menjadi "Tidak Aktif"
        foreach ($expiredTptkb as $tptkb) {
            $tptkb->industri->update(['status' => 'Tidak Aktif']);
        }

        // Log jumlah yang diupdate jika ada
        if ($expiredTptkb->count() > 0) {
            \Log::info("Auto-updated {$expiredTptkb->count()} expired TPTKB to 'Tidak Aktif' status");
        }
    }

    public function index(Request $request)
    {
        // Auto-update status berdasarkan masa berlaku izin
        $this->updateExpiredStatus();

        $query = Tptkb::with('industri');

        // Filter
        if ($request->filled('nama')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama . '%');
            });
        }

        if ($request->filled('kabupaten')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->where('kabupaten', $request->kabupaten);
            });
        }

        // Filter berdasarkan sumber bahan baku
        if ($request->filled('sumber_bahan_baku')) {
            $query->where('sumber_bahan_baku', $request->sumber_bahan_baku);
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

        // Filter berdasarkan kapasitas izin
        // Menggunakan logika yang sama dengan IndustriSekunderController
        $kapasitasFilter = $request->filled('kapasitas') ? $request->kapasitas : null;

        // Jika ada filter kapasitas, lakukan filtering di collection level
        if ($kapasitasFilter) {
            $allFiltered = (clone $query)->latest()->get();

            $filteredCollection = $allFiltered->filter(function($item) use ($kapasitasFilter) {
                // Ekstrak angka dari kapasitas_izin
                $kapasitasStr = $item->kapasitas_izin ?? '0';
                // Ekstrak angka dari string seperti "1500 m³/tahun" atau "2000"
                preg_match('/(\d+[\d\.,]*)/', $kapasitasStr, $matches);
                if (isset($matches[1])) {
                    // Hapus titik/koma pemisah ribuan dan konversi ke integer
                    $numericValue = (int)str_replace(['.', ','], '', $matches[1]);
                    
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
                    
                    return $matches_range;
                }
                
                // Tidak ada kapasitas yang valid
                return false;
            })->values();

            // Paginate the filtered collection manually
            $perPage = 10;
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
            $currentPageItems = $filteredCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            $tptkb = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentPageItems,
                $filteredCollection->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        } else {
            // Tidak ada filter kapasitas, gunakan pagination biasa
            $tptkb = $query->latest()->paginate(10);
        }

        // Get list kabupaten untuk filter dropdown
        $kabupatenList = IndustriBase::whereHas('tptkb')
            ->distinct()
            ->pluck('kabupaten')
            ->sort()
            ->values();

        // Get list sumber bahan baku sesuai form create
        $sumberBahanBakuList = [
            'Hutan Alam' => 'Hutan Alam',
            'Perhutani' => 'Hutan Tanaman',
            'Hutan Rakyat' => 'Hutan Rakyat'
        ];

        // Data untuk visualisasi chart — gunakan hasil dari query yang sudah difilter
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

        // 2. Distribusi lokasi industri (Top 10 Kabupaten)
        $locationStats = $filteredData->groupBy('industri.kabupaten')
            ->map->count()
            ->sortDesc()
            ->take(10);

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

        return view('Industri.tptkb.index', compact(
            'tptkb', 
            'kabupatenList', 
            'sumberBahanBakuList',
            'yearStats', 
            'locationStats', 
            'capacityStats'
        ));
    }

    public function create()
    {
        return view('Industri.tptkb.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:255',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'sumber_bahan_baku' => 'required|string|max:255',
            'kapasitas_izin' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'masa_berlaku' => 'required|date',
        ]);

        // Cek apakah masa berlaku sudah kadaluarsa
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $masaBerlaku = \Carbon\Carbon::parse($validated['masa_berlaku'])->startOfDay();
        $status = $masaBerlaku->lt($today) ? 'Tidak Aktif' : 'Aktif';

        // Create Industri
        $industri = IndustriBase::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'kabupaten' => $validated['kabupaten'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'type' => 'tpt_kb',
            'status' => $status, // Set status otomatis berdasarkan masa berlaku
        ]);

        // Create TPTKB
        Tptkb::create([
            'industri_id' => $industri->id,
            'pemberi_izin' => $validated['pemberi_izin'],
            'sumber_bahan_baku' => $validated['sumber_bahan_baku'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
            'masa_berlaku' => $validated['masa_berlaku'],
        ]);

        $message = 'Data TPT-KB berhasil ditambahkan!';
        if ($status === 'Tidak Aktif') {
            $message .= ' Status diset "Tidak Aktif" karena masa berlaku sudah kadaluarsa.';
        }

        return redirect()->route('tptkb.index')->with('success', $message);
    }

    public function edit($id)
    {
        $tptkb = Tptkb::with('industri')->findOrFail($id);
        return view('Industri.tptkb.edit', compact('tptkb'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:255',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'sumber_bahan_baku' => 'required|string|max:255',
            'kapasitas_izin' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'masa_berlaku' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $tptkb = Tptkb::findOrFail($id);

        // Cek apakah masa berlaku sudah kadaluarsa
        // Jika sudah kadaluarsa, override status menjadi "Tidak Aktif" meskipun user pilih "Aktif"
        $today = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
        $masaBerlaku = \Carbon\Carbon::parse($validated['masa_berlaku'])->startOfDay();
        
        if ($masaBerlaku->lt($today)) {
            $validated['status'] = 'Tidak Aktif';
            $statusOverridden = true;
        } else {
            $statusOverridden = false;
        }

        // Update Industri
        $tptkb->industri->update([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'kabupaten' => $validated['kabupaten'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
        ]);

        // Update TPTKB
        $tptkb->update([
            'pemberi_izin' => $validated['pemberi_izin'],
            'sumber_bahan_baku' => $validated['sumber_bahan_baku'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
            'masa_berlaku' => $validated['masa_berlaku'],
        ]);

        $message = 'Data TPT-KB berhasil diperbarui!';
        if ($statusOverridden) {
            $message .= ' Status otomatis diubah menjadi "Tidak Aktif" karena masa berlaku sudah kadaluarsa.';
        }

        return redirect()->route('tptkb.index')->with('success', $message);
    }

    public function destroy($id)
    {
        $tptkb = Tptkb::findOrFail($id);
        $tptkb->delete();
        $tptkb->industri->delete();

        return redirect()->route('tptkb.index')->with('success', 'Data TPT-KB berhasil dihapus!');
    }
}