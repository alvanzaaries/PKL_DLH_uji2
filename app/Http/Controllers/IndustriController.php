<?php

namespace App\Http\Controllers;

use App\Models\Industri;
use App\Models\Laporan;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIndustriRequest;
use App\Http\Requests\UpdateIndustriRequest;
use Illuminate\Support\Facades\DB;

class IndustriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        // Ambil filter dari request
        $tahun = request('tahun', date('Y')); // Tahun dari filter atau tahun saat ini
        $kabupatenKota = request('kabupaten'); // Filter kabupaten/kota
        $jenisLaporan = request('jenis_laporan'); // Filter jenis laporan
        $statusIndustri = request('status_industri', 'aktif'); // Default: hanya industri aktif

        $bulanSekarang = (int) date('n'); // Bulan saat ini (1-12)
        $tahunSekarang = (int) date('Y'); // Tahun saat ini

        // Query dengan filter, exclude industri end_user
        $query = Industri::query();
        $query->where(function ($q) {
            $q->whereNull('type')->orWhereNotIn('type', ['end_user']);
        });

        // Filter berdasarkan status industri (default: hanya aktif)
        if ($statusIndustri !== 'semua') {
            $query->where('status', $statusIndustri);
        }

        if ($kabupatenKota) {
            $query->where('kabupaten', $kabupatenKota);
        }
        $companies = $query->get()->map(function ($industri) use ($tahun, $bulanSekarang, $tahunSekarang, $jenisLaporan) {
            $laporanPerBulan = [];
            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $laporanQuery = $industri->laporan()
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan);
                if ($jenisLaporan) {
                    $laporanQuery->where('jenis_laporan', $jenisLaporan);
                }
                $adaLaporan = $laporanQuery->exists();
                if ($adaLaporan) {
                    $laporanPerBulan[] = 'ok';
                } elseif ($tahun < $tahunSekarang || ($tahun == $tahunSekarang && $bulan < $bulanSekarang)) {
                    $laporanPerBulan[] = 'fail';
                } else {
                    $laporanPerBulan[] = 'wait';
                }
            }
            return (object) [
                'id' => $industri->id,
                'nomor_izin' => $industri->nomor_izin,
                'nama' => $industri->nama,
                'kabupaten' => $industri->kabupaten,
                'type' => $industri->type ?? $industri->getJenisIndustri(),
                'laporan' => $laporanPerBulan
            ];
        });

        // Hitung persentase pelaporan per jenis (tanpa end_user)
        $countsByType = ['primer' => 0, 'sekunder' => 0, 'tpt_kb' => 0];
        $reportedByType = ['primer' => 0, 'sekunder' => 0, 'tpt_kb' => 0];
        foreach ($companies as $c) {
            $t = $c->type ?? null;
            if (!in_array($t, ['primer', 'sekunder', 'tpt_kb']))
                continue;
            $countsByType[$t]++;
            $statusThisMonth = $c->laporan[$bulanSekarang - 1] ?? null;
            if ($statusThisMonth == 'ok') {
                $reportedByType[$t]++;
            }
        }
        $percentPrimer = $countsByType['primer'] > 0 ? round(($reportedByType['primer'] / $countsByType['primer']) * 100, 1) : 0;
        $percentSekunder = $countsByType['sekunder'] > 0 ? round(($reportedByType['sekunder'] / $countsByType['sekunder']) * 100, 1) : 0;
        $percentTptkb = $countsByType['tpt_kb'] > 0 ? round(($reportedByType['tpt_kb'] / $countsByType['tpt_kb']) * 100, 1) : 0;

        // Ambil semua kabupaten/kota untuk dropdown filter
        $kabupatens = Industri::distinct()
            ->orderBy('kabupaten')
            ->pluck('kabupaten')
            ->filter();

        // Ambil semua jenis laporan dari constant (best practice: single source of truth)
        $jenisLaporans = Laporan::getJenisLaporan();

        // Hitung jumlah laporan masuk per jenis (untuk kartu navigasi)
        $laporanCountsQuery = Laporan::query()
            ->select('jenis_laporan', DB::raw('COUNT(*) as total'))
            ->whereYear('tanggal', $tahun)
            ->groupBy('jenis_laporan');

        if ($kabupatenKota) {
            $laporanCountsQuery->whereHas('industri', function ($q) use ($kabupatenKota) {
                $q->where('kabupaten', $kabupatenKota);
            });
        }

        $laporanCountsByJenis = $laporanCountsQuery
            ->pluck('total', 'jenis_laporan')
            ->toArray();

        return view('laporan/dashboardLaporan', compact(
            'companies',
            'months',
            'kabupatens',
            'tahun',
            'jenisLaporans',
            'laporanCountsByJenis',
            'reportedByType',
            'percentPrimer',
            'percentSekunder',
            'percentTptkb',
            'statusIndustri'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIndustriRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Industri $industri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Industri $industri)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIndustriRequest $request, Industri $industri)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Industri $industri)
    {
        //
    }
}
