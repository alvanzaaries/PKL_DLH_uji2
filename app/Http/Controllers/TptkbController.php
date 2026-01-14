<?php

namespace App\Http\Controllers;

use App\Models\Tptkb;
use App\Models\IndustriBase;
use Illuminate\Http\Request;

class TptkbController extends Controller
{
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

        if ($request->filled('kapasitas')) {
            $query->where('kapasitas_izin', 'like', '%' . $request->kapasitas . '%');
        }

        // Filter berdasarkan tahun (dari created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $tptkb = $query->latest()->paginate(10);

        // Get list kabupaten untuk filter dropdown
        $kabupatenList = IndustriBase::whereHas('tptkb')
            ->distinct()
            ->pluck('kabupaten')
            ->sort()
            ->values();

        // Data untuk visualisasi chart
        // 1. Distribusi perusahaan per tahun (berdasarkan created_at)
        $allData = Tptkb::with('industri')->get();
        $yearStats = $allData->groupBy(function($item) {
            return $item->created_at->format('Y');
        })->map->count()->sortKeys();

        // 2. Distribusi lokasi industri (Top 10 Kabupaten)
        $locationStats = $allData->groupBy('industri.kabupaten')
            ->map->count()
            ->sortDesc()
            ->take(10);

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

        return view('tptkb.index', compact('tptkb', 'kabupatenList', 'yearStats', 'locationStats', 'capacityStats'));
    }

    public function create()
    {
        return view('tptkb.create');
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
        return view('tptkb.edit', compact('tptkb'));
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