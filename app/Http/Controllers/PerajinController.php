<?php

namespace App\Http\Controllers;

use App\Models\Perajin;
use App\Models\IndustriBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PerajinController extends Controller implements HasMiddleware
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
        $query = Perajin::with('industri');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('industri', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_izin', 'like', "%{$search}%");
            });
        }

        // Filter jenis kerajinan
        if ($request->filled('jenis_kerajinan')) {
            $query->where('jenis_kerajinan', $request->jenis_kerajinan);
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

        $perajin = $query->latest()->paginate(10);

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

        // Data Kabupaten untuk dropdown filter dari API
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
            return IndustriBase::select('kabupaten')
                ->distinct()
                ->orderBy('kabupaten')
                ->pluck('kabupaten');
        });

        return view('Industri.perajin.index', compact('perajin', 'kabupatenList', 'yearStats', 'locationStats'));
    }

    public function create()
    {
        return view('Industri.perajin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:100|unique:industries,nomor_izin',
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:100',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tanggal' => 'required|date',
        ]);

        // Create industri base
        $industri = IndustriBase::create([
            'type' => 'end_user',
            'nama' => $validated['nama'],
            'nomor_izin' => $validated['nomor_izin'],
            'alamat' => $validated['alamat'],
            'kabupaten' => $validated['kabupaten'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kontak' => $validated['kontak'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'tanggal' => $validated['tanggal'],
        ]);

        // Create perajin
        Perajin::create([
            'industri_id' => $industri->id,
        ]);

        return redirect()->route('perajin.index')
            ->with('success', 'Data perajin berhasil ditambahkan');
    }

    public function edit(Perajin $perajin)
    {
        $perajin->load('industri');
        return view('Industri.perajin.edit', compact('perajin'));
    }

    public function update(Request $request, Perajin $perajin)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:100|unique:industries,nomor_izin,' . $perajin->industri_id,
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:100',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'tanggal' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        // Update industri base
        $perajin->industri->update([
            'nama' => $validated['nama'],
            'nomor_izin' => $validated['nomor_izin'],
            'alamat' => $validated['alamat'],
            'kabupaten' => $validated['kabupaten'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kontak' => $validated['kontak'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'tanggal' => $validated['tanggal'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('perajin.index')
            ->with('success', 'Data perajin berhasil diperbarui');
    }

    public function destroy(Perajin $perajin)
    {
        $perajin->industri->delete(); // Cascade delete
        return redirect()->route('perajin.index')
            ->with('success', 'Data perajin berhasil dihapus');
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
            $importer = new \App\Imports\PerajinImport();
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