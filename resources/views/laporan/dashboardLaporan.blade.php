@extends('laporan/layouts.layout')

@section('title', 'Monitoring Pelaporan')

@section('page-title', 'Monitoring Pelaporan Industri')

@section('content')

    <style>
        /* LOCAL SCOPED STYLES FOR DATA TABLE */

        /* Container: Sharp & Flat */
        .content-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            /* Slight radius, almost square */
            box-shadow: none;
            /* Removed shadow for flat look */
        }

        /* Header: Official & Structured */
        .card-header {
            padding: 1.5rem;
            background-color: white;
            border-bottom: 2px solid #F3F4F6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title h2 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 1.125rem;
            color: #111827;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .card-title p {
            font-size: 0.8rem;
            color: #6B7280;
            margin-top: 4px;
        }

        /* Control Ribbon (Filters) */
        .filter-ribbon {
            background-color: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #4B5563;
        }

        .filter-input {
            height: 38px;
            padding: 0 0.75rem;
            border: 1px solid #D1D5DB;
            border-radius: 4px;
            /* Professional squared look */
            font-size: 0.875rem;
            color: #111827;
            background-color: white;
            min-width: 180px;
            transition: border-color 0.15s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #0F2F24;
            /* Brand Primary */
            box-shadow: 0 0 0 1px #0F2F24;
        }

        /* Buttons: Solid & Authoritative */
        .btn {
            height: 38px;
            padding: 0 1.25rem;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background-color: #0F2F24;
            /* Deep Green */
            color: white;
        }

        .btn-primary:hover {
            background-color: #183F32;
        }

        .btn-secondary {
            background-color: white;
            border-color: #D1D5DB;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #F3F4F6;
            border-color: #9CA3AF;
        }

        .btn-export {
            background-color: #FFFBEB;
            /* Light Gold Tint */
            border-color: #D4AF37;
            /* Gold Border */
            color: #92400E;
        }

        .btn-export:hover {
            background-color: #FEF3C7;
        }

        /* Table: The Ledger Style */
        .table-container {
            overflow-x: auto;
            width: 100%;
        }

        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .ledger-table thead {
            background-color: #F3F4F6;
            border-bottom: 2px solid #E5E7EB;
        }

        .ledger-table th {
            text-align: left;
            padding: 0.875rem 1rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #6B7280;
            white-space: nowrap;
        }

        /* Column Specifics */
        .ledger-table th.col-center {
            text-align: center;
        }

        .ledger-table th.col-month {
            width: 45px;
            text-align: center;
            padding: 0.875rem 0.25rem;
        }

        .ledger-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #E5E7EB;
            color: #374151;
            vertical-align: middle;
        }

        .ledger-table tbody tr:hover {
            background-color: #F9FAFB;
        }

        .ledger-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Data Typography */
        .company-name {
            font-weight: 600;
            color: #111827;
            text-decoration: none;
        }

        .company-name:hover {
            color: #D4AF37;
            text-decoration: underline;
        }

        .meta-info {
            font-size: 0.75rem;
            color: #6B7280;
        }

        /* Status Indicators: Clean Iconography over Badges */
        .status-cell {
            text-align: center;
            padding: 0.5rem 0;
            border-left: 1px solid #F3F4F6;
        }

        .status-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .st-ok {
            color: #059669;
        }

        /* Green 600 */
        .st-fail {
            color: #DC2626;
        }

        /* Red 600 */
        .st-wait {
            color: #D1D5DB;
        }

        /* Gray 300 */

        /* Empty State */
        .empty-state {
            padding: 4rem 1rem;
            text-align: center;
            background-color: #F9FAFB;
            color: #6B7280;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            padding: 1.5rem;
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
        }

        .stat-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            padding: 1rem;
        }

        .stat-card-link {
            display: block;
            text-decoration: none;
            color: inherit;
            transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
        }

        .stat-card-link:hover {
            border-color: #D4AF37;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.03);
            transform: translateY(-1px);
        }

        .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: #6B7280;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .stat-subtitle {
            font-size: 0.75rem;
            color: #6B7280;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #E5E7EB;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.75rem;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #228B22 0%, #10b981 100%);
            transition: width 0.3s ease;
        }

        .stat-percentage {
            font-size: 0.875rem;
            font-weight: 600;
            color: #059669;
            margin-top: 0.5rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .filter-ribbon {
                flex-direction: column;
                align-items: stretch;
                gap: 1rem;
            }

            .filter-input {
                width: 100%;
            }

            .filter-actions {
                display: flex;
                gap: 0.5rem;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        /* ... style yang sudah ada ... */

        /* --- DOUGHNUT CHART STYLES --- */
        .donut-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .donut-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .donut-card:hover {
            border-color: #228B22;
            transform: translateY(-2px);
        }

        .chart-wrapper {
            position: relative;
            height: 140px;
            width: 140px;
            margin-bottom: 1rem;
        }

        .inner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
            /* Agar tooltip chart tetap jalan */
        }

        .inner-percent {
            font-size: 1.5rem;
            font-weight: 800;
            color: #111827;
            line-height: 1;
            font-family: 'Inter', sans-serif;
        }

        .inner-label {
            font-size: 0.75rem;
            color: #6B7280;
            margin-top: 2px;
        }

        .donut-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        .donut-status {
            font-size: 0.75rem;
            color: #228B22;
            background-color: #F0FDF4;
            padding: 2px 8px;
            border-radius: 99px;
            margin-top: 8px;
            font-weight: 600;
        }
    </style>

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

        <div class="filter-ribbon">
            <form method="GET" action="{{ route('laporan.index') }}" style="display: contents;">

                <div class="filter-group">
                    <label class="filter-label" for="search">Cari Perusahaan</label>
                    <input type="text" id="searchCompany" placeholder="Ketik nama perusahaan..." class="filter-input"
                        style="min-width: 250px;">
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="kabupaten">Wilayah Administrasi</label>
                    <select name="kabupaten" id="kabupaten" class="filter-input">
                        <option value=""> Seluruh Wilayah</option>
                        @if (isset($kabupatens))
                            @foreach ($kabupatens as $kab)
                                <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>
                                    {{ $kab }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="tahun">Periode Tahun</label>
                    <select name="tahun" id="tahun" class="filter-input" style="min-width: 120px;">
                        @php
                            $currentYear = date('Y');
                            $startYear = 2026; // Adjusted for realism
                        @endphp
                        @for ($year = $currentYear; $year >= $startYear; $year--)
                            <option value="{{ $year }}" {{ request('tahun', $currentYear) == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="jenis_laporan">Kategori Laporan</label>
                    <select name="jenis_laporan" id="jenis_laporan" class="filter-input">
                        <option value="">Semua Kategori</option>
                        @if (isset($jenisLaporans))
                            @foreach ($jenisLaporans as $jenis)
                                <option value="{{ $jenis }}" {{ request('jenis_laporan') == $jenis ? 'selected' : '' }}>
                                    {{ $jenis }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="filter-actions" style="margin-left: auto; display: flex; gap: 8px;">
                    <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo-alt"></i> Reset
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="table-container">
            @if (isset($companies) && count($companies) > 0)
                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th class="col-center" style="width: 60px;">No</th>
                            <th style="min-width: 250px;">Identitas Perusahaan</th>
                            <th style="width: 180px;">Lokasi</th>
                            @foreach (['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES'] as $month)
                                <th class="col-month">{{ $month }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td class="col-center" style="color: #9CA3AF;">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="display: flex; flex-direction: column;">
                                        <a href="{{ route('laporan.industri', $company->id) }}" class="company-name">
                                            {{ $company->nama }}
                                        </a>
                                        <span class="meta-info">No Izin : {{ $company->nomor_izin ?? 'N/A' }}</span>
                                        @php
                                            $typeLabel = 'N/A';
                                            if (isset($company->type)) {
                                                switch ($company->type) {
                                                    case 'primer':
                                                        $typeLabel = 'Industri Primer';
                                                        break;
                                                    case 'sekunder':
                                                        $typeLabel = 'Industri Sekunder';
                                                        break;
                                                    case 'tpt_kb':
                                                        $typeLabel = 'TPT-KB';
                                                        break;
                                                    case 'end_user':
                                                        $typeLabel = 'End User / Perajin';
                                                        break;
                                                    default:
                                                        $typeLabel = ucfirst($company->type);
                                                }
                                            }
                                        @endphp
                                        <span class="meta-info">{{ $typeLabel }}</span>
                                    </div>
                                </td>
                                <td class="meta-info">{{ $company->kabupaten }}</td>

                                @foreach ($company->laporan as $status)
                                    <td class="status-cell">
                                        @if ($status == 'ok')
                                            <span class="status-icon st-ok" title="Diterima / Valid">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        @elseif($status == 'fail')
                                            <span class="status-icon st-fail" title="Ditolak / Belum Lengkap">
                                                <i class="fas fa-times-circle"></i>
                                            </span>
                                        @else
                                            <span class="status-icon st-wait" title="Menunggu Pelaporan">
                                                <i class="fas fa-minus"></i>
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div style="margin-bottom: 1rem; color: #D1D5DB;">
                        <i class="far fa-folder-open fa-3x"></i>
                    </div>
                    <h3 style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Data Tidak Ditemukan</h3>
                    <p style="font-size: 0.875rem;">Silakan sesuaikan filter pencarian Anda atau hubungi administrator.</p>
                </div>
            @endif
        </div>

        {{-- Download button moved to layout for guests --}}
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


                // AJAX Search untuk perusahaan (Existing Logic)
                let searchTimeout;
                const searchInput = document.getElementById('searchCompany');
                const tableBody = document.querySelector('.ledger-table tbody');
                const emptyState = document.querySelector('.empty-state');

                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        clearTimeout(searchTimeout);
                        const searchTerm = this.value.trim().toLowerCase();

                        // Debounce - tunggu 300ms setelah user berhenti mengetik
                        searchTimeout = setTimeout(() => {
                            filterCompanies(searchTerm);
                        }, 300);
                    });
                }

                function filterCompanies(searchTerm) {
                    if (!tableBody) return;

                    const rows = tableBody.querySelectorAll('tr');
                    let visibleCount = 0;

                    rows.forEach((row, index) => {
                        const companyName = row.querySelector('.company-name');
                        if (companyName) {
                            const name = companyName.textContent.toLowerCase();

                            if (searchTerm === '' || name.includes(searchTerm)) {
                                row.style.display = '';
                                visibleCount++;
                                // Update nomor urut
                                const firstCol = row.querySelector('td:first-child');
                                if (firstCol) firstCol.textContent = visibleCount;
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    });

                    // Tampilkan/sembunyikan empty state
                    if (visibleCount === 0 && searchTerm !== '') {
                        if (tableBody.parentElement) {
                            tableBody.parentElement.style.display = 'none';
                        }
                        if (emptyState) {
                            emptyState.style.display = 'flex';
                            let h3 = emptyState.querySelector('h3');
                            let p = emptyState.querySelector('p');
                            if (h3) h3.textContent = 'Perusahaan Tidak Ditemukan';
                            if (p) p.textContent = `Tidak ada perusahaan dengan nama "${searchTerm}"`;
                        }
                    } else {
                        if (tableBody.parentElement) {
                            tableBody.parentElement.style.display = '';
                        }
                        if (emptyState) {
                            emptyState.style.display = 'none';
                        }
                    }
                }
            });
        </script>
    @endpush

@endsection