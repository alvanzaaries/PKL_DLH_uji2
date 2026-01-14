<?php

namespace App\Http\Controllers;

use App\Models\Perajin;
use App\Models\IndustriBase;
use Illuminate\Http\Request;

class PerajinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Perajin::with('industri');

        // Filter pencarian nama atau nomor izin
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('industri', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nomor_izin', 'like', "%{$search}%");
            });
        }

        // Filter kabupaten
        if ($request->filled('kabupaten')) {
            $query->whereHas('industri', function($q) use ($request) {
                $q->where('kabupaten', $request->kabupaten);
            });
        }

        // Filter berdasarkan tahun (dari created_at)
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $perajin = $query->latest()->paginate(10);
        
        // Get kabupaten list untuk filter dari perajin yang ada
        $kabupatenList = \App\Models\IndustriBase::whereHas('perajin')
            ->distinct()
            ->pluck('kabupaten')
            ->sort()
            ->values();

        return view('perajin.index', compact('perajin', 'kabupatenList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('perajin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:100|unique:industries,nomor_izin',
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:100',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
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
        ]);

        // Create perajin
        Perajin::create([
            'industri_id' => $industri->id,
        ]);

        return redirect()->route('perajin.index')
            ->with('success', 'Data perajin berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Perajin $perajin)
    {
        $perajin->load('industri');
        return view('perajin.show', compact('perajin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $perajin = Perajin::with('industri')->findOrFail($id);
        return view('perajin.edit', compact('perajin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $perajin = Perajin::with('industri')->findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:100|unique:industries,nomor_izin,' . $perajin->industri_id,
            'alamat' => 'required|string',
            'kabupaten' => 'required|string|max:100',
            'penanggungjawab' => 'required|string|max:255',
            'kontak' => 'required|string|max:50',
        ]);

        // Update industri base
        $perajin->industri->update([
            'nama' => $validated['nama'],
            'nomor_izin' => $validated['nomor_izin'],
            'alamat' => $validated['alamat'],
            'kabupaten' => $validated['kabupaten'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kontak' => $validated['kontak'],
        ]);

        return redirect()->route('perajin.index')
            ->with('success', 'Data perajin berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Perajin $perajin)
    {
        // Delete industri base (cascade akan delete perajin)
        $perajin->industri->delete();
        
        return redirect()->route('perajin.index')
            ->with('success', 'Data perajin berhasil dihapus');
    }
}
