<?php

namespace App\Http\Controllers;

use App\Models\Reconciliation;
use App\Models\Kph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function upload(Request $request)
    {
        // Use Master Kph table
        $kphOptions = Kph::orderBy('nama')->pluck('nama');

        return view('PNBP.user.upload', compact('kphOptions'));
    }

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

        return view('PNBP.user.history', compact('reconciliations', 'totals'));
    }
}
