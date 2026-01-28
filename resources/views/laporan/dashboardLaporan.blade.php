@extends('laporan/layouts.layout')

@section('title', 'Dashboard')@section('page-title', 'Dashboard Pelaporan Industri')@section('content'){{-- Custom CSS for Dashboard --}}
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 1024px) {
            .dashboard-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .dashboard-card {
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #E5E7EB;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .card-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: #111827;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .card-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6B7280;
        }

        .main-chart-section {
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #E5E7EB;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #111827;
        }

        .section-subtitle {
            font-size: 0.875rem;
            color: #6B7280;
        }

        /* Layout for Charts: 2/3 Main, 1/3 Side */
        .charts-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .charts-container {
                grid-template-columns: 2fr 1fr;
            }
        }

        .donut-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #F3F4F6;
        }

        .donut-item:last-child {
            border-bottom: none;
        }

        .donut-info h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #1F2937;
            margin-bottom: 0.25rem;
        }

        .donut-info p {
            font-size: 0.8rem;
            color: #6B7280;
        }

        .donut-canvas-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .donut-percent-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 0.75rem;
            font-weight: 700;
            color: #1A4030;
        }

        .cta-section {
            background: linear-gradient(135deg, #1A4030 0%, #166534 100%);
            border-radius: 12px;
            padding: 2rem;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>

    @php
        // PHP Logic remains the same, calculating stats
        $bulanSekarang = date('n');
        $namaBulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];

        $totalPerusahaan = isset($companies) ? count($companies) : 0;
        $perusahaanLapor = 0;
        if (isset($companies)) {
            foreach ($companies as $company) {
                if (($company->laporan[$bulanSekarang - 1] ?? null) == 'ok') {
                    $perusahaanLapor++;
                }
            }
        }
        $persentase = $totalPerusahaan > 0 ? round(($perusahaanLapor / $totalPerusahaan) * 100, 1) : 0;

        // Calculate types
        $countsByType = ['primer' => 0, 'sekunder' => 0, 'tpt_kb' => 0];
        $reportedByType = ['primer' => 0, 'sekunder' => 0, 'tpt_kb' => 0];
        if (isset($companies)) {
            foreach ($companies as $c) {
                $t = $c->type ?? null;
                if (!in_array($t, ['primer', 'sekunder', 'tpt_kb']))
                    continue;
                $countsByType[$t]++;
                if (($c->laporan[$bulanSekarang - 1] ?? null) == 'ok') {
                    $reportedByType[$t]++;
                }
            }
        }
        $percentPrimer = $countsByType['primer'] > 0 ? round(($reportedByType['primer'] / $countsByType['primer']) * 100, 1) : 0;
        $percentSekunder = $countsByType['sekunder'] > 0 ? round(($reportedByType['sekunder'] / $countsByType['sekunder']) * 100, 1) : 0;
        $percentTptkb = $countsByType['tpt_kb'] > 0 ? round(($reportedByType['tpt_kb'] / $countsByType['tpt_kb']) * 100, 1) : 0;
    @endphp


    {{-- Stats Grid --}}
    <div class="dashboard-grid">
        {{-- Total Perusahaan --}}
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #d6f6d3ff; color: #000000ff;">
                <i class="fas fa-building"></i>
            </div>
            <div class="card-value">{{ $totalPerusahaan }}</div>
            <div class="card-label">Total Perusahaan Terdaftar</div>
        </div>

        {{-- Sudah Melapor --}}
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #DCFCE7; color: #16A34A;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="card-value">{{ $perusahaanLapor }} <span style="font-size: 1rem; color: #16A34A; font-weight: 600;">({{ $persentase }}%)</span></div>
            <div class="card-label">Sudah lapor bulan {{ $namaBulan[$bulanSekarang] ?? $bulanSekarang }}</div>
            <div style="width: 100%; height: 6px; background: #F3F4F6; border-radius: 99px; margin-top: 1rem; overflow: hidden;">
                <div style="width: {{ $persentase }}%; height: 100%; background: #16A34A; border-radius: 99px;"></div>
            </div>
        </div>

        {{-- Belum Melapor --}}
        <div class="dashboard-card">
            <div class="card-icon" style="background-color: #FEE2E2; color: #DC2626;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="card-value">{{ $totalPerusahaan - $perusahaanLapor }}</div>
            <div class="card-label">Belum lapor bulan ini</div>
        </div>
    </div>

    {{-- Charts Container --}}
    <div class="charts-container">
        {{-- Main Line Chart --}}
        <div class="main-chart-section">
            <div class="section-header">
                <div>
                    <h3 class="section-title">Tren Kepatuhan Bulanan</h3>
                    <p class="section-subtitle">Grafik jumlah perusahaan yang melapor setiap bulan</p>
                </div>
                <div style="padding: 0.5rem 1rem; background: #F3F4F6; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                    Tahun {{ request('tahun', date('Y')) }}
                </div>
            </div>
            <div style="height: 320px; width: 100%;">
                <canvas id="laporanChart"></canvas>
            </div>
        </div>

        {{-- Side Column: Donut Charts --}}
        <div class="main-chart-section">
             <div class="section-header">
                <div>
                    <h3 class="section-title">Per Jenis Industri</h3>
                    <p class="section-subtitle">Status bulan {{ $namaBulan[$bulanSekarang] ?? $bulanSekarang }}</p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                {{-- Primer --}}
                <div class="donut-item">
                    <div class="donut-info">
                        <h4>Industri Primer</h4>
                        <p>{{ $reportedByType['primer'] }} dari {{ $countsByType['primer'] }} perusahaan</p>
                    </div>
                    <div class="donut-canvas-wrapper">
                        <canvas id="donutPrimer"></canvas>
                        <div class="donut-percent-text">{{ $percentPrimer }}%</div>
                    </div>
                </div>

                {{-- Sekunder --}}
                <div class="donut-item">
                    <div class="donut-info">
                        <h4>Industri Sekunder</h4>
                        <p>{{ $reportedByType['sekunder'] }} dari {{ $countsByType['sekunder'] }} perusahaan</p>
                    </div>
                    <div class="donut-canvas-wrapper">
                        <canvas id="donutSekunder"></canvas>
                        <div class="donut-percent-text">{{ $percentSekunder }}%</div>
                    </div>
                </div>

                 {{-- TPT-KB --}}
                <div class="donut-item">
                    <div class="donut-info">
                        <h4>TPT-KB</h4>
                        <p>{{ $reportedByType['tpt_kb'] }} dari {{ $countsByType['tpt_kb'] }} perusahaan</p>
                    </div>
                    <div class="donut-canvas-wrapper">
                        <canvas id="donutTptkb"></canvas>
                        <div class="donut-percent-text">{{ $percentTptkb }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Call to Action: Monitoring --}}
    <div class="cta-section">
        <div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">Monitoring Detail</h3>
            <p style="opacity: 0.9; max-width: 500px;">Lihat rincian status pelaporan setiap perusahaan secara lengkap dalam format tabel.</p>
        </div>
        <a href="{{ route('laporan.monitoring') }}" style="background: white; color: #166534; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; transition: transform 0.2s;">
            <span>Buka Tabel Monitoring</span>
            <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    @push('scripts')
        {{-- Chart.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Data from PHP
                @php
                    $monthlyCounts = array_fill(0, 12, 0);
                    if (isset($companies)) {
                        foreach ($companies as $company) {
                            foreach ($company->laporan as $monthIndex => $status) {
                                if ($status == 'ok')
                                    $monthlyCounts[$monthIndex]++;
                            }
                        }
                    }
                @endphp
                const monthlyCounts = @json($monthlyCounts);
                const ctx = document.getElementById('laporanChart').getContext('2d');

                // Gradient Fill for Main Chart
                let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(22, 163, 74, 0.2)'); // Green-600 with opacity
                gradient.addColorStop(1, 'rgba(22, 163, 74, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                        datasets: [{
                            label: 'Perusahaan Melapor',
                            data: monthlyCounts,
                            borderColor: '#16A34A', // Green-600
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointBackgroundColor: '#FFFFFF',
                            pointBorderColor: '#16A34A',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                padding: 12,
                                titleFont: { size: 13, family: "'Inter', sans-serif" },
                                bodyFont: { size: 14, family: "'Inter', sans-serif", weight: 'bold' },
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function (context) { return context.parsed.y + ' Perusahaan'; }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { borderDash: [4, 4], color: '#F3F4F6', drawBorder: false },
                                ticks: { precision: 0, font: { family: "'Inter', sans-serif", size: 11 }, color: '#9CA3AF' }
                            },
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { font: { family: "'Inter', sans-serif", size: 11 }, color: '#9CA3AF' }
                            }
                        },
                        interaction: { intersect: false, mode: 'index' },
                    }
                });

                // DONUT CHARTS
                function makeDonutChart(canvasId, percent) {
                    const ctx = document.getElementById(canvasId).getContext('2d');
                    const dataValues = [percent, 100 - percent];
                    const bgColors = ['#16A34A', '#E5E7EB']; // Green-600 & Gray-200

                    return new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Sudah', 'Belum'],
                            datasets: [{
                                data: dataValues,
                                backgroundColor: bgColors,
                                borderWidth: 0,
                                borderRadius: 5,
                                hoverBackgroundColor: ['#15803d', '#D1D5DB'],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: false } // We use custom text in center
                            }
                        }
                    });
                }

                makeDonutChart('donutPrimer', {{ $percentPrimer }});
                makeDonutChart('donutSekunder', {{ $percentSekunder }});
                makeDonutChart('donutTptkb', {{ $percentTptkb }});
            });
        </script>
    @endpush

@endsection