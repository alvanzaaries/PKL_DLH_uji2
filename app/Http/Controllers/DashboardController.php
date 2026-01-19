<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\ReconciliationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $data = $this->buildDashboardData($request);

        return view('PNBP.admin.dashboard', $data);
    }

    /**
     * Export filtered PNBP dashboard statistics to PDF.
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

    private function buildDashboardData(Request $request): array
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

        // 6. Calculate Volume by Category (Kayu, HHBK, Lainnya)
        // Grouping logic matching ReconciliationController
        $volumeByCat = [
            'HASIL HUTAN KAYU' => 0,
            'HASIL HUTAN BUKAN KAYU (HHBK)' => 0,
            'HASIL HUTAN LAINNYA' => 0, 
        ];

        // We need to fetch data grouped by Satuan to apply the categorization logic
        $statsSatuan = $detailQuery->clone()
            ->select('satuan', DB::raw('SUM(volume) as total_vol'))
            ->groupBy('satuan')
            ->get();

        foreach ($statsSatuan as $row) {
            $unit = strtolower(trim((string)$row->satuan));
            $vol = (float) $row->total_vol;

            if (in_array($unit, ['m3', 'm^3', 'kbk'])) {
                $volumeByCat['HASIL HUTAN KAYU'] += $vol;
            } elseif (in_array($unit, ['ton', 'kg'])) {
                $volumeByCat['HASIL HUTAN BUKAN KAYU (HHBK)'] += $vol;
            } else {
                $volumeByCat['HASIL HUTAN LAINNYA'] += $vol;
            }
        }

        // 7. Filter Data Options (Dropdowns)
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
            'availableYears',
            'availableQuarters',
            'availableKph',
            'availableWilayah'
        );
    }

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

        return view('Industri.dashboard', compact('statistics'));
    }
}
