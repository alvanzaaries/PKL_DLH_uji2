@extends('laporan/layouts.layout')

@section('title', 'Monitoring Pelaporan')

@section('page-title', 'Monitoring Pelaporan Industri')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/laporan/custom.css') }}">

    <div class="content-card">
        <div class="card-header">
            <div class="card-title">
                <h2>MONITORING PELAPORAN</h2>
                <p>Tahun {{ request('tahun', date('Y')) }}</p>
            </div>
            {{-- <a href="#" class="btn btn-export">
                <i class="fas fa-file-excel"></i>
                <span>Unduh Laporan</span>
            </a> --}}
        </div>

        <!-- Statistics Cards -->
        @php
            $totalPerusahaan = isset($companies) ? count($companies) : 0;
            $perusahaanLapor = 0;
            $totalLaporanMasuk = 0;
            $bulanSekarang = date('n'); // 1-12 (Januari = 1, Desember = 12)

            if (isset($companies)) {
                foreach ($companies as $company) {
                    // Cek status laporan di bulan sekarang (index array dimulai dari 0, bulan dari 1)
                    $statusBulanIni = $company->laporan[$bulanSekarang - 1] ?? null;

                    // Jika sudah lapor di bulan ini (status 'ok')
                    if ($statusBulanIni == 'ok') {
                        $perusahaanLapor++;
                    }

                    // Hitung total laporan masuk (untuk statistik tambahan jika diperlukan)
                    foreach ($company->laporan as $status) {
                        if ($status == 'ok') {
                            $totalLaporanMasuk++;
                        }
                    }
                }
            }

            $persentase = $totalPerusahaan > 0 ? round(($perusahaanLapor / $totalPerusahaan) * 100, 1) : 0;
            $totalLaporanDiharapkan = $totalPerusahaan * $bulanSekarang;

            $namaBulan = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];
        @endphp

        @php
            // Hitung persentase pelaporan per jenis (tanpa end_user)
            $countsByType = ['primer' => 0, 'sekunder' => 0, 'tpt_kb' => 0];
            $reportedByType = ['primer' => 0, 'sekunder' => 0, 'tpt_kb' => 0];

            if (isset($companies)) {
                foreach ($companies as $c) {
                    $t = $c->type ?? null;
                    if (!in_array($t, ['primer', 'sekunder', 'tpt_kb'])) {
                        continue;
                    }
                    $countsByType[$t]++;
                    $statusThisMonth = $c->laporan[$bulanSekarang - 1] ?? null;
                    if ($statusThisMonth == 'ok') {
                        $reportedByType[$t]++;
                    }
                }
            }

            $percentPrimer =
                $countsByType['primer'] > 0 ? round(($reportedByType['primer'] / $countsByType['primer']) * 100, 1) : 0;
            $percentSekunder =
                $countsByType['sekunder'] > 0
                ? round(($reportedByType['sekunder'] / $countsByType['sekunder']) * 100, 1)
                : 0;
            $percentTptkb =
                $countsByType['tpt_kb'] > 0 ? round(($reportedByType['tpt_kb'] / $countsByType['tpt_kb']) * 100, 1) : 0;
        @endphp

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Total Perusahaan</div>
                <div class="stat-value">{{ $totalPerusahaan }}</div>
                <div class="stat-subtitle">Terdaftar di sistem</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Sudah Melapor</div>
                <div class="stat-value">{{ $perusahaanLapor }}</div>
                <div class="stat-subtitle">Laporan bulan {{ $namaBulan[$bulanSekarang] ?? $bulanSekarang }}</div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: {{ $persentase }}%"></div>
                </div>
                <div class="stat-percentage">{{ $persentase }}% dari total</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Belum Melapor</div>
                <div class="stat-value" style="color: #DC2626;">{{ $totalPerusahaan - $perusahaanLapor }}</div>
                <div class="stat-subtitle">Belum lapor bulan {{ $namaBulan[$bulanSekarang] ?? $bulanSekarang }}</div>
            </div>
            {{--
            <div class="stat-card">
                <div class="stat-label">Total Laporan Masuk</div>
                <div class="stat-value" style="color: #059669;">{{ $totalLaporanMasuk }}</div>
                <div class="stat-subtitle">Dari {{ $totalLaporanDiharapkan }} yang diharapkan (s.d bulan ini)</div>
            </div> --}}
        </div>


        {{-- CHART SECTION --}}
        <div style="padding: 1.5rem; border-bottom: 1px solid #E5E7EB;">
            <h3
                style="font-size: 1rem; font-weight: 700; color: #374151; margin-bottom: 1rem; font-family: 'Inter', sans-serif;">
                Tren Kepatuhan Pelaporan ({{ request('tahun', date('Y')) }})
            </h3>
            <div style="height: 300px; width: 100%;">
                <canvas id="laporanChart"></canvas>
            </div>
        </div>

        {{-- DOUGHNUT CHART SECTION --}}
        <div style="padding: 1.5rem; border-bottom: 1px solid #E5E7EB;">
            <h3
                style="font-size: 1rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem; font-family: 'Inter', sans-serif;">
                Kepatuhan per Jenis Industri
            </h3>
            <p style="font-size: 0.875rem; color: #6B7280; margin-bottom: 1.5rem;">
                Persentase perusahaan yang sudah melapor bulan {{ $namaBulan[$bulanSekarang] ?? $bulanSekarang }}.
            </p>

            <div class="donut-grid">
                <div class="donut-card">
                    <div class="chart-wrapper">
                        <canvas id="donutPrimer"></canvas>
                        <div class="inner-text">
                            <div class="inner-percent">{{ $percentPrimer }}%</div>
                        </div>
                    </div>
                    <div class="donut-title">Primer</div>
                    <div class="donut-status">{{ $reportedByType['primer'] }} / {{ $countsByType['primer'] }} Unit</div>
                </div>

                <div class="donut-card">
                    <div class="chart-wrapper">
                        <canvas id="donutSekunder"></canvas>
                        <div class="inner-text">
                            <div class="inner-percent">{{ $percentSekunder }}%</div>
                        </div>
                    </div>
                    <div class="donut-title">Sekunder</div>
                    <div class="donut-status">{{ $reportedByType['sekunder'] }} / {{ $countsByType['sekunder'] }} Unit
                    </div>
                </div>

                <div class="donut-card">
                    <div class="chart-wrapper">
                        <canvas id="donutTptkb"></canvas>
                        <div class="inner-text">
                            <div class="inner-percent">{{ $percentTptkb }}%</div>
                        </div>
                    </div>
                    <div class="donut-title">TPT-KB</div>
                    <div class="donut-status">{{ $reportedByType['tpt_kb'] }} / {{ $countsByType['tpt_kb'] }} Unit</div>
                </div>
            </div>
        </div>

        @php
            // Calculate monthly data for chart
            $monthlyCounts = array_fill(0, 12, 0); // Jan-Dec (0-11)

            if (isset($companies)) {
                foreach ($companies as $company) {
                    // Loop through each month's status
                    foreach ($company->laporan as $monthIndex => $status) {
                        // Check if status is 'ok' (means at least one report exists/valid)
                        if ($status == 'ok') {
                            $monthlyCounts[$monthIndex]++;
                        }
                    }
                }
            }
        @endphp


        {{-- Link to Monitoring --}}
        <div style="display: flex; justify-content: center; padding: 2rem;">
            <a href="{{ route('laporan.monitoring') }}" class="btn btn-primary"
                style="padding: 1rem 2rem; font-size: 1rem;">
                <i class="fas fa-table"></i> Lihat Data Detail (Monitoring)
            </a>
        </div>

    </div>

    @push('scripts')
        {{-- Chart.js CDN --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Data from PHP
                const monthlyCounts = @json($monthlyCounts);
                const ctx = document.getElementById('laporanChart').getContext('2d');

                // Gradient Fill
                let gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(34, 139, 34, 0.2)'); // Forest Green / #228B22 with opacity
                gradient.addColorStop(1, 'rgba(34, 139, 34, 0)');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                            'Des'
                        ],
                        datasets: [{
                            label: 'Perusahaan Melapor',
                            data: monthlyCounts,
                            borderColor: '#228B22', // Forest Green
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointBackgroundColor: '#FFFFFF',
                            pointBorderColor: '#228B22',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4 // Smooth curve
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#1F2937',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    family: "'Inter', sans-serif"
                                },
                                bodyFont: {
                                    size: 14,
                                    family: "'Inter', sans-serif",
                                    weight: 'bold'
                                },
                                cornerRadius: 4,
                                displayColors: false,
                                callbacks: {
                                    label: function (context) {
                                        return context.parsed.y + ' Perusahaan';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [4, 4],
                                    color: '#E5E7EB',
                                    drawBorder: false,
                                },
                                ticks: {
                                    precision: 0,
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11
                                    },
                                    color: '#6B7280'
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    font: {
                                        family: "'Inter', sans-serif",
                                        size: 11
                                    },
                                    color: '#6B7280'
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                    }
                });


                // 3 DOUGHNUT CHARTS: Primer, Sekunder, TPT-KB
                function makeDonutChart(canvasId, percent) {
                    const ctx = document.getElementById(canvasId).getContext('2d');

                    // Warna track (abu-abu) vs Warna isi (Hijau)
                    // Jika 0%, tetap tampilkan abu-abu full
                    const dataValues = [percent, 100 - percent];
                    const bgColors = ['#228B22', '#F3F4F6']; // Forest Green & Cool Gray 100

                    return new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Sudah Lapor', 'Belum Lapor'],
                            datasets: [{
                                data: dataValues,
                                backgroundColor: bgColors,
                                borderWidth: 0, // Hilangkan border putih agar terlihat flat & clean
                                borderRadius: 20, // Membuat ujung bar melengkung (modern look)
                                hoverBackgroundColor: ['#15803d', '#E5E7EB'], // Darker green on hover
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '85%', // Membuat lingkaran lebih tipis
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: '#1F2937',
                                    padding: 10,
                                    cornerRadius: 4,
                                    displayColors: false,
                                    callbacks: {
                                        label: function (context) {
                                            return context.label + ': ' + context.parsed + '%';
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateScale: true,
                                animateRotate: true
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