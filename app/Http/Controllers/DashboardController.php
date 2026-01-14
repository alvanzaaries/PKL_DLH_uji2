<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\ReconciliationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models from Incoming (for public dashboard)
use App\Models\IndustriPrimer;
use App\Models\IndustriSekunder;
use App\Models\Tptkb;
use App\Models\Perajin;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard (from HEAD) - Reconciliation analytics
     */
    public function index(Request $request)
    {
        // 1. Filter Parameters
        $year = $request->input('year');
        $quarter = $request->input('quarter');
        $sampaiQuarter = $request->input('sampai_quarter');
        $kph = trim((string) $request->input('kph', ''));
        $wilayah = trim((string) $request->input('wilayah', ''));

        // 2. Base Queries
        $reconQuery = Reconciliation::query();
        $detailQuery = ReconciliationDetail::query()
            ->join('reconciliations', 'reconciliation_details.reconciliation_id', '=', 'reconciliations.id');

        // 3. Apply Filters
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
            // "Sampai dengan quarter" means: TW 1 + TW 2 + ... + selected TW (for that year)
            $reconQuery->whereBetween('quarter', [1, $sampaiQuarter]);
            $detailQuery->whereBetween('reconciliations.quarter', [1, $sampaiQuarter]);
        }

        // 4. Aggregations (Infographics)
        
        // Card 1: Total Uploaded Files
        $totalFiles = $reconQuery->count();

        // Card 2: Financials (Total Nilai Setor, Billing, LHP)
        $financials = $detailQuery->clone()->select(
            DB::raw('SUM(lhp_nilai) as total_lhp'),
            DB::raw('SUM(billing_nilai) as total_billing'),
            DB::raw('SUM(setor_nilai) as total_setor'),
            DB::raw('SUM(volume) as total_volume')
        )->first();

        // Card 3: Top Wilayah (by Setor Nilai)
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

        // Card 4: Stats per Jenis SDH
        $statsJenis = $detailQuery->clone()
            ->select('jenis_sdh', DB::raw('SUM(volume) as total_vol'), DB::raw('SUM(setor_nilai) as total_setor'))
            ->groupBy('jenis_sdh')
            ->get();

        // 5. Filter Data Options (Dropdowns)
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

        return view('admin.dashboard', compact(
            'totalFiles', 
            'financials', 
            'topWilayah', 
            'wilayahCount',
            'statsJenis', 
            'availableYears', 
            'availableQuarters',
            'availableKph',
            'availableWilayah'
        ));
    }

    /**
     * Public Dashboard (from Incoming) - Industry statistics for public viewing
     */
    public function publicIndex()
    {
        // Hitung data real dari database dengan TPT structure
        $statistics = [
            'primer_pbphh' => IndustriPrimer::count(),
            'sekunder_pbui' => IndustriSekunder::count(),
            'tpt_kb' => Tptkb::count(),
            'perajin' => Perajin::count(),
        ];

        // Total keseluruhan industri
        $statistics['total_industri'] = array_sum($statistics);

        return view('dashboard', compact('statistics'));
    }
}
