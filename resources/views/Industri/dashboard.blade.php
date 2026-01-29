@extends('Industri.layouts.sidebar')

@section('title', 'Dashboard - Dinas Lingkungan Hidup dan Kehutanan')

@push('styles')
<style>
        :root {
            --primary: #0f172a;
            --accent: #15803d;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
        }

        .container {
            max-width: 100%;
            margin: 0;
            padding: 20px 30px;
        }

        /* Hero Section - Clean */
        .hero {
            padding: 40px 0 30px;
            background: var(--white);
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 30px;
            text-align: center;
        }

        .hero-title {
            font-size: 28px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-subtitle {
            color: #64748b;
            font-size: 16px;
        }

        /* Alert Banner - Minimalist */
        .alert-mini {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: #991b1b;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
            cursor: pointer;
        }

        .stat-card:hover {
            border-color: var(--accent);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        .stat-label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            display: block;
        }

        .stat-desc {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Highlight Section */
        .total-banner {
            background: #022d0d;
            color: var(--white);
            padding: 40px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .total-text h3 { font-size: 18px; opacity: 0.9; font-weight: 400; }
        .total-text h2 { font-size: 48px; font-weight: 800; }

        /* Carousel Content */
        .section-header {
            margin-bottom: 24px;
            border-left: 4px solid var(--accent);
            padding-left: 15px;
        }

        .carousel-box {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .slide-placeholder {
            height: 350px;
            background: #d6ebdd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-weight: 500;
        }

        /* Footer */
        footer {
            background: var(--white);
            border-top: 1px solid #e2e8f0;
            padding: 40px 0;
            margin-top: 60px;
            font-size: 14px;
            color: #64748b;
        }

        .footer-grid {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Table Styles */
        .table-container {
            background-color: var(--white);
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .data-table th, .data-table td {
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .data-table th {
            font-weight: 600;
            color: var(--text-main);
            background-color: #f8fafc;
        }

        .data-table tr:hover {
            background-color: #f1f5f9;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }
        
        .badge-status-aktif {
            background: transparent;
            color: green;
        }
        
        .badge-status-nonaktif {
            background: transparent;
            color: red;
        }

        .badge-primer { background-color: #dbeafe; color: #1e40af; }
        .badge-sekunder { background-color: #dcfce7; color: #166534; }
        .badge-tptkb { background-color: #fef9c3; color: #854d0e; }
        .badge-perajin { background-color: #f3e8ff; color: #6b21a8; }


        @media (max-width: 768px) {
            .total-banner { flex-direction: column; text-align: center; gap: 20px; }
        }
    </style>
@endpush

@section('content')
    <header class="hero">
        <div class="container">
            <h1 class="hero-title">Dashboard Industri Kehutanan</h1>
            <p class="hero-subtitle">Pengelolaan Hasil Hutan Provinsi Jawa Tengah</p>
        </div>
    </header>

    <main class="container">
        <div class="stats-grid">
            <a href="{{ route('industri-primer.index') }}" class="stat-card">
                <div class="stat-label">Industri Primer</div>
                <span class="stat-number">{{ number_format($statistics['primer_pbphh']) }}</span>
                <div class="stat-desc">Izin PBPHH Aktif • Klik untuk lihat data</div>
            </a>
            <a href="{{ route('industri-sekunder.index') }}" class="stat-card">
                <div class="stat-label">Industri Sekunder</div>
                <span class="stat-number">{{ number_format($statistics['sekunder_pbui']) }}</span>
                <div class="stat-desc">Izin PBUI Aktif</div>
            </a>
            <a href="{{ route('tptkb.index') }}" class="stat-card">
                <div class="stat-label">Pengolahan TPT-KB</div>
                <span class="stat-number">{{ number_format($statistics['tpt_kb']) }}</span>
                <div class="stat-desc">Terdaftar di Database</div>
            </a>
            <a href="{{ route('perajin.index') }}" class="stat-card">
                <div class="stat-label">Industri Perajin</div>
                <span class="stat-number">{{ number_format($statistics['perajin']) }}</span>
                <div class="stat-desc">Usaha Mikro & Kecil</div>
            </a>
        </div>

        <div class="total-banner">
            <div class="total-text">
                <h3>Total Industri Terintegrasi</h3>
                <h2>{{ number_format($statistics['total_industri']) }} <span style="font-size: 20px; font-weight: 300;">Unit Usaha</span></h2>
            </div>
        </div>

        <div class="table-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h3 style="color: var(--primary); font-size: 18px; margin: 0;">Daftar Semua Industri</h3>
                <form action="{{ route('industri.dashboard') }}" method="GET" style="display: flex; gap: 10px; max-width: 100%; flex-wrap: wrap;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / Lokasi..." style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; width: 250px;">
                    <select name="status" style="padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px;">
                        <option value="">-- Semua Status --</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    <button type="submit" style="background: var(--accent); color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500;">Cari</button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('industri.dashboard') }}" style="background: #e2e8f0; color: #475569; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; display: flex; align-items: center;">Reset</a>
                    @endif
                </form>
            </div>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Industri</th>
                            <th>Jenis</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataIndustri as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>
                                @php
                                    $rawType = $item->type;
                                    $label = match($rawType) {
                                        'primer', 'PBPHH' => 'Industri Primer',
                                        'sekunder', 'PBUI' => 'Industri Sekunder',
                                        'tpt_kb' => 'TPT-KB',
                                        'perajin', 'end_user' => 'Perajin',
                                        default => ucwords(str_replace('_', ' ', $rawType))
                                    };
                                    $badge = match($rawType) {
                                        'primer', 'PBPHH' => 'badge-primer',
                                        'sekunder', 'PBUI' => 'badge-sekunder',
                                        'tpt_kb' => 'badge-tptkb',
                                        'perajin', 'end_user' => 'badge-perajin',
                                        default => 'badge-primer'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $label }}</span>
                            </td>
                            <td>{{ $item->kabupaten }}</td>
                            <td>
                                @if($item->status == 'Aktif')
                                    <span class="badge-status-aktif">Aktif</span>
                                @else
                                    <span class="badge-status-nonaktif">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #94a3b8;">Tidak ada data industri ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- <section>
            <div class="section-header">
                <h2 style="font-size: 20px; color: var(--primary);">Dokumentasi Kegiatan Industri</h2>
            </div>
            <div class="carousel-box">
                <div class="slide-placeholder">
                    [ Salam dari kami SIRANAAR (Sistemnya Rakan, Nadzif, dan Alvanzaar) ]
                </div>
            </div>
        </section> -->

    </main>
@endsection     