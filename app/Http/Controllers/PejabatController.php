<?php

namespace App\Http\Controllers;

use App\Models\Pejabat;
use Illuminate\Http\Request;

class PejabatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pejabats = Pejabat::orderBy('is_active', 'desc')->get();
        return view('laporan.pejabat.index', compact('pejabats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:255',
        ]);

        $pejabat = new Pejabat($request->all());

        // If this is set to active, or it's the first one, make it active
        if ($request->has('is_active') || Pejabat::count() == 0) {
            $this->deactivateAll();
            $pejabat->is_active = true;
        } else {
            $pejabat->is_active = false;
        }

        $pejabat->save();

        return redirect()->route('pejabat.index')->with('success', 'Data Pejabat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pejabat $pejabat)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'pangkat' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:255',
        ]);

        $pejabat->fill($request->all());

        if ($request->has('is_active') && $request->is_active) {
            $this->deactivateAll();
            $pejabat->is_active = true;
        }

        $pejabat->save();

        return redirect()->route('pejabat.index')->with('success', 'Data Pejabat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pejabat $pejabat)
    {
        if ($pejabat->is_active) {
            return back()->with('error', 'Tidak dapat menghapus pejabat yang sedang aktif.');
        }

        $pejabat->delete();
        return redirect()->route('pejabat.index')->with('success', 'Data Pejabat berhasil dihapus.');
    }

    /**
     * Set a pejabat as active.
     */
    public function activate(Pejabat $pejabat)
    {
        $this->deactivateAll();
        $pejabat->is_active = true;
        $pejabat->save();

        return redirect()->route('pejabat.index')->with('success', 'Pejabat aktif telah diperbarui.');
    }

    private function deactivateAll()
    {
        Pejabat::query()->update(['is_active' => false]);
    }
}
