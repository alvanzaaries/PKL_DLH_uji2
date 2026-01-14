<?php

namespace App\Http\Controllers;

use App\Models\Perajin;
use App\Models\IndustriBase;
use Illuminate\Http\Request;

class PerajinController extends Controller
{
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

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->whereDate('masa_berlaku', '>=', now());
            } elseif ($request->status === 'kadaluarsa') {
                $query->whereDate('masa_berlaku', '<', now());
            }
        }

        $perajin = $query->latest()->paginate(10);

        return view('industri.perajin.index', compact('perajin'));
    }

    public function create()
    {
        return view('industri.perajin.create');
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
            'jenis_kerajinan' => 'required|string|max:255',
            'bahan_baku' => 'required|string|max:255',
            'kapasitas_produksi' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'masa_berlaku' => 'required|date',
        ]);

        // Create industri base
        $industri = IndustriBase::create([
            'jenis_industri' => 'perajin',
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
            'jenis_kerajinan' => $validated['jenis_kerajinan'],
            'bahan_baku' => $validated['bahan_baku'],
            'kapasitas_produksi' => $validated['kapasitas_produksi'],
            'pemberi_izin' => $validated['pemberi_izin'],
            'masa_berlaku' => $validated['masa_berlaku'],
        ]);

        return redirect()->route('industri.perajin.index')
            ->with('success', 'Data perajin berhasil ditambahkan');
    }

    public function edit(Perajin $perajin)
    {
        $perajin->load('industri');
        return view('industri.perajin.edit', compact('perajin'));
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
            'jenis_kerajinan' => 'required|string|max:255',
            'bahan_baku' => 'required|string|max:255',
            'kapasitas_produksi' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'masa_berlaku' => 'required|date',
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

        // Update perajin
        $perajin->update([
            'jenis_kerajinan' => $validated['jenis_kerajinan'],
            'bahan_baku' => $validated['bahan_baku'],
            'kapasitas_produksi' => $validated['kapasitas_produksi'],
            'pemberi_izin' => $validated['pemberi_izin'],
            'masa_berlaku' => $validated['masa_berlaku'],
        ]);

        return redirect()->route('industri.perajin.index')
            ->with('success', 'Data perajin berhasil diperbarui');
    }

    public function destroy(Perajin $perajin)
    {
        $perajin->industri->delete(); // Cascade delete
        return redirect()->route('industri.perajin.index')
            ->with('success', 'Data perajin berhasil dihapus');
    }
}