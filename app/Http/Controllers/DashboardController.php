<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\ReconciliationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Industri;
use App\Models\IndustriPrimer;
use App\Models\IndustriSekunder;
use App\Models\Tptkb;
use App\Models\Perajin;

class DashboardController extends Controller
{
    /**
    * Menampilkan dashboard admin PNBP beserta ringkasan analitik.
     */
    public function index(Request $request)
    {
        $data = $this->buildDashboardData($request);

        return view('PNBP.admin.dashboard', $data);
    }

    /**
        * Mengekspor statistik dashboard PNBP ke PDF sesuai filter.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->buildDashboardData($request);
        $data['filter'] = $this->buildDashboardFilterLabel($request);

        $pdf = Pdf::loadView('PNBP.admin.dashboard_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => false,
                'defaultFont' => 'Helvetica',
                'isHtml5ParserEnabled' => true,
            ]);

        return $pdf->download('pnbp.' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Menyusun data dashboard berdasarkan filter permintaan.
     */
    private function buildDashboardData(Request $request): array
    {
        // 1. Parameter filter
        $year = $request->input('year');
        $quarter = $request->input('quarter');
        $sampaiQuarter = $request->input('sampai_quarter');
        $kph = trim((string) $request->input('kph', ''));
        $wilayah = trim((string) $request->input('wilayah', ''));

        // 2. Query dasar
        $reconQuery = Reconciliation::query();
        $detailQuery = ReconciliationDetail::query()
            ->join('reconciliations', 'reconciliation_details.reconciliation_id', '=', 'reconciliations.id');

        // 3. Terapkan filter
        if ($year) {
            $reconQuery->where('year', $year);
            $detailQuery->where('reconciliations.year', $year);
        }

        if ($kph !== '') {
            $reconQuery->where('kph', $kph);
            $detailQuery->where('reconciliations.kph', $kph);
        }

        if ($wilayah !== '') {
            $detailQuery->where('reconciliation_details.wilayah', $wilayah);
            $reconQuery->whereHas('details', function ($q) use ($wilayah) {
                $q->where('wilayah', $wilayah);
            });
        }

        if ($quarter) {
            $reconQuery->where('quarter', $quarter);
            $detailQuery->where('reconciliations.quarter', $quarter);
        } elseif ($sampaiQuarter) {
            // "Sampai dengan triwulan" berarti TW 1 s.d. TW terpilih pada tahun yang sama.
            $reconQuery->whereBetween('quarter', [1, $sampaiQuarter]);
            $detailQuery->whereBetween('reconciliations.quarter', [1, $sampaiQuarter]);
        }

        // 4. Agregasi (infografik)
        $totalFiles = $reconQuery->count();

        $financials = $detailQuery->clone()->select(
            DB::raw('SUM(lhp_nilai) as total_lhp'),
            DB::raw('SUM(billing_nilai) as total_billing'),
            DB::raw('SUM(setor_nilai) as total_setor'),
            DB::raw('SUM(volume) as total_volume')
        )->first();

        $topWilayah = $detailQuery->clone()
            ->select('wilayah', DB::raw('SUM(setor_nilai) as total'))
            ->groupBy('wilayah')
            ->orderByDesc('total')
            ->get();

        $wilayahCount = $detailQuery->clone()
            ->whereNotNull('reconciliation_details.wilayah')
            ->where('reconciliation_details.wilayah', '!=', '')
            ->distinct('reconciliation_details.wilayah')
            ->count('reconciliation_details.wilayah');

        $statsJenis = $detailQuery->clone()
            ->select('jenis_sdh', DB::raw('SUM(volume) as total_vol'), DB::raw('SUM(setor_nilai) as total_setor'))
            ->groupBy('jenis_sdh')
            ->get();

        // 6. Hitung volume per kategori (Kayu, HHBK, Lainnya)
        // Logika pengelompokan mengikuti ReconciliationController.
        $volumeByCat = [
            'HASIL HUTAN KAYU' => 0,
            'HASIL HUTAN BUKAN KAYU (HHBK)' => 0,
            'HASIL HUTAN LAINNYA' => 0, 
        ];
        $setorByCat = [
            'HASIL HUTAN KAYU' => 0,
            'HASIL HUTAN BUKAN KAYU (HHBK)' => 0,
            'HASIL HUTAN LAINNYA' => 0, 
        ];

        // Ambil data per satuan untuk menerapkan logika kategori.
        $statsSatuan = $detailQuery->clone()
            ->select('satuan', DB::raw('SUM(volume) as total_vol'), DB::raw('SUM(setor_nilai) as total_setor'))
            ->groupBy('satuan')
            ->get();

        foreach ($statsSatuan as $row) {
            $unit = strtolower(trim((string)$row->satuan));
            $vol = (float) $row->total_vol;
            $setor = (float) $row->total_setor;

            if (in_array($unit, ['m3', 'm^3', 'kbk'])) {
                $volumeByCat['HASIL HUTAN KAYU'] += $vol;
                $setorByCat['HASIL HUTAN KAYU'] += $setor;
            } elseif (in_array($unit, ['ton', 'kg'])) {
                // Jika satuan kg, konversi ke ton (dibagi 1000).
                $addedVol = ($unit === 'kg') ? $vol / 1000 : $vol;
                $volumeByCat['HASIL HUTAN BUKAN KAYU (HHBK)'] += $addedVol;
                $setorByCat['HASIL HUTAN BUKAN KAYU (HHBK)'] += $setor;
            } else {
                $volumeByCat['HASIL HUTAN LAINNYA'] += $vol;
                $setorByCat['HASIL HUTAN LAINNYA'] += $setor;
            }
        }

        // 6b. Terapkan override manual untuk total LHP
        // Rumus: Total = (Jumlah semua detail) - (Jumlah detail yang dioverride) + (Jumlah nilai override)
        $matchingReconIds = $reconQuery->pluck('id');
        $overrides = \App\Models\ReconciliationSummaryOverride::whereIn('reconciliation_id', $matchingReconIds)
            ->where('metric', 'total_nilai_lhp')
            ->whereNull('satuan')
            ->get();

        if ($overrides->isNotEmpty()) {
            $overriddenReconIds = $overrides->pluck('reconciliation_id');
            $originalLhpSum = ReconciliationDetail::whereIn('reconciliation_id', $overriddenReconIds)->sum('lhp_nilai');
            $overrideSum = $overrides->sum('value');
            
            // Sesuaikan total finansial (pastikan nilainya ada).
            if ($financials) {
                $financials->total_lhp = ($financials->total_lhp - $originalLhpSum) + $overrideSum;
            }
        }

        $setorOverrides = \App\Models\ReconciliationSummaryOverride::whereIn('reconciliation_id', $matchingReconIds)
            ->where('metric', 'total_nilai_setor')
            ->whereNull('satuan')
            ->get();

        if ($setorOverrides->isNotEmpty()) {
            $overriddenReconIds = $setorOverrides->pluck('reconciliation_id');
            $originalSetorSum = ReconciliationDetail::whereIn('reconciliation_id', $overriddenReconIds)->sum('setor_nilai');
            $overrideSetorSum = $setorOverrides->sum('value');

            if ($financials) {
                $financials->total_setor = ($financials->total_setor - $originalSetorSum) + $overrideSetorSum;
            }
        }

        // 7. Opsi data filter (dropdown)
        $availableYears = Reconciliation::select('year')->distinct()->orderByDesc('year')->pluck('year');
        $availableQuarters = Reconciliation::select('quarter')->distinct()->orderBy('quarter')->pluck('quarter');
        $availableKph = Reconciliation::select('kph')
            ->whereNotNull('kph')
            ->where('kph', '!=', '')
            ->distinct()
            ->orderBy('kph')
            ->pluck('kph');

        $availableWilayah = ReconciliationDetail::select('wilayah')
            ->whereNotNull('wilayah')
            ->where('wilayah', '!=', '')
            ->distinct()
            ->orderBy('wilayah')
            ->pluck('wilayah');

        return compact(
            'totalFiles',
            'financials',
            'topWilayah',
            'wilayahCount',
            'statsJenis',
            'volumeByCat',
            'setorByCat',
            'availableYears',
            'availableQuarters',
            'availableKph',
            'availableWilayah'
        );
    }

    /**
     * Menyusun label ringkas untuk filter dashboard.
     */
    private function buildDashboardFilterLabel(Request $request): string
    {
        $parts = [];

        if ($request->filled('year')) {
            $parts[] = 'Tahun ' . $request->input('year');
        }

        if ($request->filled('kph')) {
            $parts[] = 'KPH ' . $request->input('kph');
        }

        if ($request->filled('wilayah')) {
            $parts[] = 'Wilayah ' . $request->input('wilayah');
        }

        if ($request->filled('quarter')) {
            $parts[] = 'Triwulan ' . $request->input('quarter');
        } elseif ($request->filled('sampai_quarter')) {
            $parts[] = 'Sampai TW ' . $request->input('sampai_quarter');
        }

        return $parts ? implode(' | ', $parts) : 'Semua Data';
    }

    /**
     * Menampilkan dashboard publik untuk statistik industri.
     */
    public function publicIndex(Request $request)
    {
        // Hitung data real dari database dengan struktur TPT.
        $statistics = [
            'primer_pbphh' => IndustriPrimer::count(),
            'sekunder_pbui' => IndustriSekunder::count(),
            'tpt_kb' => Tptkb::count(),
            'perajin' => Perajin::count(),
        ];

        // Total keseluruhan industri.
        $statistics['total_industri'] = array_sum($statistics);

        // Ambil data untuk tabel dengan filter pencarian.
        $query = Industri::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kabupaten', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $dataIndustri = $query->orderBy('type')->orderBy('nama')->get();

        return view('Industri.dashboard', compact('statistics', 'dataIndustri'));
    }
}
