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
            'dokumen_izin' => 'nullable|file|mimes:pdf|max:5120', // max 5MB
        ]);

        // Upload dokumen izin jika ada
        if ($request->hasFile('dokumen_izin')) {
            $file = $request->file('dokumen_izin');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen-izin', $fileName, 'public');
            $validated['dokumen_izin'] = $filePath;
        }

        // Step 1: Insert ke tabel industries (parent) dulu
        $industri = \App\Models\IndustriBase::create([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kabupaten' => $validated['kabupaten'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
            'type' => 'primer',
        ]);

        // Step 2: Insert ke tabel industri_primer (child) dengan FK industri_id
        IndustriPrimer::create([
            'industri_id' => $industri->id, // FK ke parent
            'pemberi_izin' => $validated['pemberi_izin'],
            'jenis_produksi' => $validated['jenis_produksi'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
            'dokumen_izin' => $validated['dokumen_izin'] ?? null,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('industri-primer.index')
            ->with('success', 'Data industri primer berhasil ditambahkan!');
    }

    /**
     * Tampilkan daftar industri primer dengan filtering
     */
    public function index(Request $request)
    {
        // Query dengan join ke tabel industries (parent)
        $query = IndustriPrimer::with('industri'); // Eager load relationship

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

        // Filter berdasarkan kapasitas izin (dari tabel industri_primer)
        if ($request->filled('kapasitas')) {
            $query->where('kapasitas_izin', $request->kapasitas);
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
            return \App\Models\IndustriBase::where('type', 'primer')
                ->distinct()
                ->orderBy('kabupaten')
                ->pluck('kabupaten');
        });

        return view('industri-primer.index', compact('industriPrimer', 'kabupatenList'));
    }

    /**
     * Tampilkan form edit industri primer
     */
    public function edit($id)
    {
        $industriPrimer = IndustriPrimer::with('industri')->findOrFail($id);
        
        return view('industri-primer.edit', compact('industriPrimer'));
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
            'kontak' => 'required|string|max:255',
            'pemberi_izin' => 'required|string|max:255',
            'jenis_produksi' => 'required|string|max:255',
            'kapasitas_izin' => 'required|string|max:255',
            'nomor_izin' => 'required|string|max:255',
            'dokumen_izin' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        // Find records
        $industriPrimer = IndustriPrimer::findOrFail($id);
        $industri = \App\Models\IndustriBase::findOrFail($industriPrimer->industri_id);

        // Handle file upload jika ada file baru
        if ($request->hasFile('dokumen_izin')) {
            // Hapus file lama jika ada
            if ($industriPrimer->dokumen_izin) {
                Storage::disk('public')->delete($industriPrimer->dokumen_izin);
            }

            $file = $request->file('dokumen_izin');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen-izin', $fileName, 'public');
            $validated['dokumen_izin'] = $filePath;
        }

        // Update parent table (industries)
        $industri->update([
            'nama' => $validated['nama'],
            'alamat' => $validated['alamat'],
            'penanggungjawab' => $validated['penanggungjawab'],
            'kabupaten' => $validated['kabupaten'],
            'kontak' => $validated['kontak'],
            'nomor_izin' => $validated['nomor_izin'],
        ]);

        // Update child table (industri_primer)
        $updateData = [
            'pemberi_izin' => $validated['pemberi_izin'],
            'jenis_produksi' => $validated['jenis_produksi'],
            'kapasitas_izin' => $validated['kapasitas_izin'],
        ];

        if (isset($validated['dokumen_izin'])) {
            $updateData['dokumen_izin'] = $validated['dokumen_izin'];
        }

        $industriPrimer->update($updateData);

        return redirect()->route('industri-primer.index')
            ->with('success', 'Data industri primer berhasil diupdate!');
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
}
