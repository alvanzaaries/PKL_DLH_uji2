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
    public function index(Request $request)
    {
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

        // Filter berdasarkan kapasitas izin
        // Karena kapasitas_izin sekarang VARCHAR, kita extract angka dan bandingkan dengan rentang
        if ($request->filled('kapasitas')) {
            $kapasitasRange = $request->kapasitas;
            
            $query->where(function($q) use ($kapasitasRange) {
                // Extract angka dari string kapasitas_izin menggunakan REGEXP
                
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

        $tptkb = $query->latest()->paginate(10);

        // Get list kabupaten untuk filter dropdown
        $kabupatenList = IndustriBase::whereHas('tptkb')
            ->distinct()
            ->pluck('kabupaten')
            ->sort()
            ->values();

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

        return view('Industri.tptkb.index', compact('tptkb', 'kabupatenList', 'yearStats', 'locationStats', 'capacityStats'));
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

        // Create Industri
        $industri = IndustriBase::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'kabupaten' => $validated['kabupaten'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
        ]);

        // Create TPTKB
        Tptkb::create([
            'industri_id' => $industri->id,
            'pemberi_izin' => $validated['pemberi_izin'],
            'sumber_bahan_baku' => $validated['sumber_bahan_baku'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
            'masa_berlaku' => $validated['masa_berlaku'],
        ]);

        return redirect()->route('tptkb.index')->with('success', 'Data TPT-KB berhasil ditambahkan!');
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
        ]);

        $tptkb = Tptkb::findOrFail($id);

        // Update Industri
        $tptkb->industri->update([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'kabupaten' => $validated['kabupaten'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'tanggal' => $validated['tanggal'],
        ]);

        // Update TPTKB
        $tptkb->update([
            'pemberi_izin' => $validated['pemberi_izin'],
            'sumber_bahan_baku' => $validated['sumber_bahan_baku'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
            'masa_berlaku' => $validated['masa_berlaku'],
        ]);

        return redirect()->route('tptkb.index')->with('success', 'Data TPT-KB berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tptkb = Tptkb::findOrFail($id);
        $tptkb->delete();
        $tptkb->industri->delete();

        return redirect()->route('tptkb.index')->with('success', 'Data TPT-KB berhasil dihapus!');
    }
}