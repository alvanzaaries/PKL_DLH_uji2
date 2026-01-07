<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data statistik untuk dashboard
        $statistics = [
            'primer_pbphh' => 856, // Jumlah industri Primer/PBPHH
            'sekunder_pbui' => 1247, // Jumlah industri Sekunder/PBUI
            'tpt_kb' => 645, // Jumlah industri TPT-KB
            'perajin' => 390, // Jumlah Perajin/Endprimer
        ];

        // Total keseluruhan industri
        $statistics['total_industri'] = array_sum($statistics);

        return view('dashboard', compact('statistics'));
    }
}
