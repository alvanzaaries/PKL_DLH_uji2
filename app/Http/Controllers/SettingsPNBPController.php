<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kph;
use App\Models\User;

class SettingsPNBPController extends Controller
{
    /**
     * Menampilkan halaman pengaturan PNBP dan daftar KPH.
     */
    public function index()
    {
        $kphs = Kph::orderBy('nama')->get();
        $usersCount = User::count();
        return view('PNBP.admin.settings.index', compact('kphs', 'usersCount'));
    }

    /**
     * Menyimpan data KPH baru dari form pengaturan.
     */
    public function storeKph(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:kphs,nama|max:100',
        ]);

        Kph::create(['nama' => $request->nama]);

        return back()->with('success', 'KPH berhasil ditambahkan.');
    }

    /**
     * Menghapus data KPH yang dipilih.
     */
    public function destroyKph(Kph $kph)
    {
        $kph->delete();
        return back()->with('success', 'KPH berhasil dihapus.');
    }
}
