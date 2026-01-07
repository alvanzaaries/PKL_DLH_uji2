<?php

namespace App\Http\Controllers;

use App\Models\IndustriPrimer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IndustriPrimerController extends Controller
{
    /**
     * Tampilkan form tambah industri primer
     */
    public function create()
    {
        return view('industri-primer.create');
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
            'kapasitas_izin' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:255',
            'pelaporan' => 'required|in:Aktif,Tidak Aktif,Pending',
            'dokumen_izin' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ]);

        // Upload dokumen izin jika ada
        if ($request->hasFile('dokumen_izin')) {
            $file = $request->file('dokumen_izin');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen-izin', $fileName, 'public');
            $validated['dokumen_izin'] = $filePath;
        }

        // Simpan ke database
        IndustriPrimer::create($validated);

        // Redirect dengan pesan sukses
        return redirect()->route('industri-primer.create')
            ->with('success', 'Data industri primer berhasil ditambahkan!');
    }

    /**
     * Tampilkan daftar industri primer dengan filtering
     */
    public function index(Request $request)
    {
        $query = IndustriPrimer::query();

        // Filter berdasarkan nama
        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter berdasarkan kapasitas izin
        if ($request->filled('kapasitas')) {
            $query->where('kapasitas_izin', 'like', '%' . $request->kapasitas . '%');
        }

        // Ambil data dengan pagination
        $industriPrimer = $query->latest()->paginate(10);

        // Ambil daftar kabupaten Jawa Tengah dari API wilayah.id dengan cache
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
            return IndustriPrimer::select('kabupaten')
                ->distinct()
                ->orderBy('kabupaten')
                ->pluck('kabupaten');
        });

        return view('industri-primer.index', compact('industriPrimer', 'kabupatenList'));
    }
}
