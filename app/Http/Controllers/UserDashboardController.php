<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\Kph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    /**
     * Menampilkan form upload laporan untuk pengguna.
     */
    public function upload(Request $request)
    {
        // Gunakan daftar KPH dari tabel master.
        $kphOptions = Kph::orderBy('nama')->pluck('nama');

        return view('PNBP.user.upload', compact('kphOptions'));
    }

    /**
     * Menampilkan riwayat upload beserta ringkasan dan data chart.
     */
    public function history(Request $request)
    {
        $userId = Auth::id();

        $reconciliations = Reconciliation::query()
            ->where('user_id', $userId)
            ->latest()
            ->withCount('details')
            ->withSum('details as total_volume', 'volume')
            ->withSum('details as total_lhp_nilai', 'lhp_nilai')
            ->withSum('details as total_billing_nilai', 'billing_nilai')
            ->withSum('details as total_setor_nilai', 'setor_nilai')
            ->get();

        $totals = [
            'total_upload' => $reconciliations->count(),
            'total_baris' => (int) $reconciliations->sum('details_count'),
            'total_volume' => (float) $reconciliations->sum(fn ($r) => (float) ($r->total_volume ?? 0)),
            'total_lhp_nilai' => (float) $reconciliations->sum(fn ($r) => (float) ($r->total_lhp_nilai ?? 0)),
            'total_billing_nilai' => (float) $reconciliations->sum(fn ($r) => (float) ($r->total_billing_nilai ?? 0)),
            'total_setor_nilai' => (float) $reconciliations->sum(fn ($r) => (float) ($r->total_setor_nilai ?? 0)),
        ];

        $detailQuery = DB::table('reconciliation_details')
            ->join('reconciliations', 'reconciliation_details.reconciliation_id', '=', 'reconciliations.id')
            ->where('reconciliations.user_id', $userId);

        $statsSatuan = $detailQuery->clone()
            ->select('reconciliation_details.satuan', DB::raw('SUM(reconciliation_details.volume) as total_volume'))
            ->groupBy('reconciliation_details.satuan')
            ->get();

        $volumeByCategory = [
            'HASIL HUTAN KAYU' => 0,
            'HASIL HUTAN BUKAN KAYU (HHBK)' => 0,
            'HASIL HUTAN LAINNYA' => 0,
        ];

        foreach ($statsSatuan as $row) {
            $unit = strtolower(trim((string) $row->satuan));
            $vol = (float) ($row->total_volume ?? 0);

            if (in_array($unit, ['m3', 'm^3', 'kbk'])) {
                $volumeByCategory['HASIL HUTAN KAYU'] += $vol;
            } elseif (in_array($unit, ['ton', 'kg'])) {
                $addedVol = ($unit === 'kg') ? $vol / 1000 : $vol;
                $volumeByCategory['HASIL HUTAN BUKAN KAYU (HHBK)'] += $addedVol;
            } else {
                $volumeByCategory['HASIL HUTAN LAINNYA'] += $vol;
            }
        }

        $jenisStatsRaw = $detailQuery->clone()
            ->select('reconciliation_details.jenis_sdh', 'reconciliation_details.satuan', DB::raw('SUM(reconciliation_details.volume) as total_volume'))
            ->groupBy('reconciliation_details.jenis_sdh', 'reconciliation_details.satuan')
            ->get();

        $jenisStats = $jenisStatsRaw
            ->groupBy('jenis_sdh')
            ->map(function ($rows, $jenis) {
                $totalVolume = (float) $rows->sum('total_volume');
                $units = $rows->pluck('satuan')->filter()->unique()->values();
                $unitLabel = $units->count() === 1 ? (string) $units->first() : 'campuran';

                return (object) [
                    'jenis_sdh' => $jenis,
                    'total_volume' => $totalVolume,
                    'unit' => $unitLabel,
                ];
            })
            ->values();

        return view('PNBP.user.history', compact('reconciliations', 'totals', 'volumeByCategory', 'jenisStats'));
    }
}
