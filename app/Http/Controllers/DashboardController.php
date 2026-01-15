<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IndustriPrimer;
use App\Models\IndustriSekunder;
use App\Models\Tptkb;
use App\Models\Perajin;

class DashboardController extends Controller
{
    public function index()
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
