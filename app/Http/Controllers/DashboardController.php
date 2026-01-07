<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\ReconciliationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Parameters
        $year = $request->input('year');
        $quarter = $request->input('quarter');

        // 2. Base Queries
        $reconQuery = Reconciliation::query();
        $detailQuery = ReconciliationDetail::query()
            ->join('reconciliations', 'reconciliation_details.reconciliation_id', '=', 'reconciliations.id');

        // 3. Apply Filters
        if ($year) {
            $reconQuery->where('year', $year);
            $detailQuery->where('reconciliations.year', $year);
        }
        if ($quarter) {
            $reconQuery->where('quarter', $quarter);
            $detailQuery->where('reconciliations.quarter', $quarter);
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
            ->limit(5)
            ->get();

        // Card 4: Stats per Jenis SDH
        $statsJenis = $detailQuery->clone()
            ->select('jenis_sdh', DB::raw('SUM(volume) as total_vol'), DB::raw('SUM(setor_nilai) as total_setor'))
            ->groupBy('jenis_sdh')
            ->get();

        // 5. Filter Data Options (Dropdowns)
        $availableYears = Reconciliation::select('year')->distinct()->orderByDesc('year')->pluck('year');
        $availableQuarters = Reconciliation::select('quarter')->distinct()->orderBy('quarter')->pluck('quarter');

        return view('admin.dashboard', compact(
            'totalFiles', 
            'financials', 
            'topWilayah', 
            'statsJenis', 
            'availableYears', 
            'availableQuarters'
        ));
    }
}

