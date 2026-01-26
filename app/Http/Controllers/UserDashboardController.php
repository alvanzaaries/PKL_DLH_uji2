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

        $totalLhpNilai = (float) $reconciliations->sum(fn ($r) => (float) ($r->total_lhp_nilai ?? 0));
        $totalSetorNilai = (float) $reconciliations->sum(fn ($r) => (float) ($r->total_setor_nilai ?? 0));

        $totals = [
            'total_upload' => $reconciliations->count(),
            'total_baris' => (int) $reconciliations->sum('details_count'),
            'total_volume' => (float) $reconciliations->sum(fn ($r) => (float) ($r->total_volume ?? 0)),
            'total_lhp_nilai' => $totalLhpNilai,
            'total_billing_nilai' => (float) $reconciliations->sum(fn ($r) => (float) ($r->total_billing_nilai ?? 0)),
            'total_setor_nilai' => $totalSetorNilai,
            'total_selisih_lhp_setor' => $totalLhpNilai - $totalSetorNilai,
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

        $statsJenis = $detailQuery->clone()
            ->select(
                'reconciliation_details.jenis_sdh as label',
                'reconciliation_details.satuan',
                DB::raw('SUM(reconciliation_details.volume) as total_volume'),
                DB::raw('SUM(reconciliation_details.lhp_nilai) as total_nilai')
            )
            ->groupBy('reconciliation_details.jenis_sdh', 'reconciliation_details.satuan')
            ->orderByDesc('total_volume')
            ->get();

        return view('PNBP.user.history', compact('reconciliations', 'totals', 'volumeByCategory', 'statsJenis'));
    }
}
