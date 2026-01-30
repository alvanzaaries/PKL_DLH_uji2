@extends('Industri.layouts.sidebar')

@section('title', 'Data TPT-KB')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/filter-collapse.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
        /* Prevent sidebar overlap */
        body {
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            height: 100vh;
            z-index: 1000;
        }

        .main-wrapper {
            margin-left: 260px;
            width: calc(100% - 260px);
        }

        :root {
            --primary: #0f172a;
            --accent: #15803d;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
            --border: #e2e8f0;
            --success: #16a34a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', 'Arial', 'Helvetica Neue', Helvetica, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
            display: flex;
            margin: 0;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, rgb(26, 64, 48) 0%, #0f2a22 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 15px 12px 20px;
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-logo {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
        }

        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .sidebar-text {
            flex: 1;
            line-height: 1.3;
        }

        .sidebar-text-top {
            font-size: 9px;
            font-weight: 600;
            color: #fbbf24;
            text-transform: uppercase;
            margin: 0 0 2px 0;
            letter-spacing: 0.3px;
        }

        .sidebar-text-bottom {
            font-size: 10px;
            font-weight: 700;
            color: #86efac;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.2px;
            line-height: 1.2;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: #4CAF50;
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-left-color: #4CAF50;
        }

        .menu-icon {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 260px);
            position: relative;
        }

        .container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Navigation */
        nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 20px 0;
            margin-bottom: 30px;
            width: 100%;
        }

        .nav-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .back-link {
            text-decoration: none;
            color: var(--accent);
            font-weight: 500;
            font-size: 14px;
            transition: opacity 0.2s;
        }

        .back-link:hover {
            opacity: 0.7;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--accent);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .page-header {
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 28px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
        }

        .btn-primary:hover {
            background: #198038;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(36, 161, 72, 0.3);
        }

        /* Filter Section - Extended from filter-collapse.css */

        .table-card {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .result-count {
            font-size: 14px;
            color: #64748b;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, #24a148 0%, #198038 100%);
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #ffffff;
            font-size: 14px;
            border-bottom: 3px solid #0e6027;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            color: #374151;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        tbody tr:hover {
            background: #dbeafe;
            transform: scale(1.001);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-jenis {
            background: linear-gradient(180deg, rgb(26, 64, 48) 0%, #5a8a64 100%);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin: 2px;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-view:hover {
            background: #2563eb;
        }

        .btn-edit {
            background: #24a148;
            color: white;
        }

        .btn-edit:hover {
            background: #198038;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .badge-status-aktif {
            display: inline-block;
            background: transparent;
            color: green;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .badge-status-nonaktif {
            display: inline-block;
            background: transparent;
            color: red;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Statistics Section */
        .statistics-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border);
            text-align: center;
        }

        .stat-card h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .chart-container {
            position: relative;
            height: 250px;
            margin: 0 auto;
        }

        .pagination {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%
;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background-color: var(--white);
            margin: 3% auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 800px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, var(--accent) 0%, #198038 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }

        .close-btn {
            color: white;
            font-size: 32px;
            font-weight: 300;
            cursor: pointer;
            line-height: 1;
            transition: transform 0.2s;
        }

        .close-btn:hover {
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 30px;
            background-color: #f8fafc;
        }

        /* Table Detail Style - Spek Kantoran */
        .table-detail {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }

        .table-detail tr:first-child td {
            border-top: none;
        }

        .table-detail td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 14px;
        }

        .table-detail tr:last-child td {
            border-bottom: none;
        }

        .detail-label-col {
            width: 35%;
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px !important;
            letter-spacing: 0.5px;
            border-right: 1px solid #f1f5f9;
        }

        .detail-value-col {
            width: 65%;
            color: #334155;
            font-weight: 500;
        }

        .detail-section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--primary);
            margin: 0 0 12px 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-section-title::before {
            content: '';
            display: block;
            width: 4px;
            height: 16px;
            background: var(--accent);
            border-radius: 2px;
        }

        @media (max-width: 1024px) {
            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .sidebar {
                width: 220px;
            }

            .main-wrapper {
                margin-left: 220px;
                width: calc(100% - 220px);
            }
        }

        @media (max-width: 768px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .btn-filter, .btn-reset {
                width: 100%;
                justify-content: center;
            }

            .sidebar {
                width: 70px;
            }

            .main-wrapper {
                margin-left: 70px;
                width: calc(100% - 70px);
            }

            .menu-text {
                display: none;
            }

            .sidebar-title {
                font-size: 12px;
            }
        }
    </style>
@endpush

@section('content')
        <div class="container">
        <div class="page-header">
            <div>
                <h1 class="page-title">Data TPT-KB</h1>
                <p class="page-subtitle">Daftar Tempat Penampungan Tebangan Kayu Bulat</p>
            </div>
            @auth
            @if(auth()->user()->role === 'admin')
            <div style="display: flex; gap: 12px;">
                <button onclick="openImportModal()" class="btn btn-primary" style="background: #0ea5e9;">
                    <i class="fas fa-file-excel"></i> Import Excel
                </button>            
                <a href="{{ route('tptkb.create') }}" class="btn btn-primary">
                    <span>+</span> Tambah Data Baru
                </a>
            </div>
            @endif
            @endauth
        </div>

        <div class="filter-card">
            <div class="filter-header" onclick="toggleFilter()">
                <div class="filter-header-title">
                    <i class="fas fa-filter"></i>
                    <span>Filter Pencarian</span>
                    <span style="font-size: 12px; color: #64748b; font-weight: normal;" id="activeFilterCount"></span>
                </div>
                <i class="fas fa-chevron-down filter-header-icon" id="filterIcon"></i>
            </div>
            <div class="filter-body" id="filterBody">
                <form method="GET" action="{{ route('tptkb.index') }}">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label>Nama Perusahaan</label>
                            <input type="text" name="nama" class="filter-input" placeholder="Cari nama perusahaan..." value="{{ request('nama') }}">
                        </div>
                        <div class="filter-group">
                            <label>Kabupaten/Kota</label>   
                            <select name="kabupaten" class="filter-input">
                                <option value="">-- Pilih Kabupaten/Kota --</option>
                                @foreach($kabupatenList as $kabupaten)
                                    <option value="{{ $kabupaten }}" {{ request('kabupaten') == $kabupaten ? 'selected' : '' }}>
                                        {{ $kabupaten }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Sumber Bahan Baku</label>
                            <select name="sumber_bahan_baku" class="filter-input">
                                <option value="">-- Semua Sumber --</option>
                                @foreach($sumberBahanBakuList as $id => $nama)
                                    <option value="{{ $id }}" {{ request('sumber_bahan_baku') == $id ? 'selected' : '' }}>
                                        {{ $nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Daya Tampung Izin</label>
                            <select name="kapasitas" class="filter-input">
                                <option value="">-- Semua Daya Tampung --</option>
                                <option value="0-1999" {{ request('kapasitas') == '0-1999' ? 'selected' : '' }}>0 - 1.999 m³/tahun</option>
                                <option value="2000-5999" {{ request('kapasitas') == '2000-5999' ? 'selected' : '' }}>2.000 - 5.999 m³/tahun</option>
                                <option value=">=6000" {{ request('kapasitas') == '>=6000' ? 'selected' : '' }}>>= 6.000 m³/tahun</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Bulan</label>
                            <select name="bulan" class="filter-input">
                                <option value="">-- Semua Bulan --</option>
                                <option value="1" {{ request('bulan') == '1' ? 'selected' : '' }}>Januari</option>
                                <option value="2" {{ request('bulan') == '2' ? 'selected' : '' }}>Februari</option>
                                <option value="3" {{ request('bulan') == '3' ? 'selected' : '' }}>Maret</option>
                                <option value="4" {{ request('bulan') == '4' ? 'selected' : '' }}>April</option>
                                <option value="5" {{ request('bulan') == '5' ? 'selected' : '' }}>Mei</option>
                                <option value="6" {{ request('bulan') == '6' ? 'selected' : '' }}>Juni</option>
                                <option value="7" {{ request('bulan') == '7' ? 'selected' : '' }}>Juli</option>
                                <option value="8" {{ request('bulan') == '8' ? 'selected' : '' }}>Agustus</option>
                                <option value="9" {{ request('bulan') == '9' ? 'selected' : '' }}>September</option>
                                <option value="10" {{ request('bulan') == '10' ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ request('bulan') == '11' ? 'selected' : '' }}>November</option>
                                <option value="12" {{ request('bulan') == '12' ? 'selected' : '' }}>Desember</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Tahun</label>
                            <select name="tahun" class="filter-input">
                                <option value="">-- Semua Tahun --</option>
                                @php
                                    $currentYear = \Carbon\Carbon::now('Asia/Jakarta')->format('Y');
                                    for ($year = $currentYear; $year >= 2020; $year--) {
                                        echo "<option value='$year' " . (request('tahun') == $year ? 'selected' : '') . ">$year</option>";
                                    }
                                @endphp
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Status</label>
                            <select name="status" class="filter-input">
                                <option value="">-- Semua Status --</option>
                                <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Cari Data</button>
                    <a href="{{ route('tptkb.index') }}" class="btn-reset">↻ Reset Filter</a>
                </div>
            </form>
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="statistics-section">
            <div class="stat-card">
                <h3><i class="fas fa-chart-line" style="color: var(--accent); margin-right: 8px;"></i>Sebaran Per Tahun</h3>
                <div class="chart-container">
                    <canvas id="chartTahun"></canvas>
                </div>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-map-marked-alt" style="color: var(--accent); margin-right: 8px;"></i>Sebaran Kabupaten/Kota</h3>
                <div class="chart-container">
                    <canvas id="chartKabupaten"></canvas>
                </div>
            </div>
            <div class="stat-card">
                <h3><i class="fas fa-chart-pie" style="color: var(--accent); margin-right: 8px;"></i>Sebaran Daya Tampung Izin</h3>
                <div class="chart-container">
                    <canvas id="chartKapasitas"></canvas>
                </div>
            </div>
        </div>

        <div class="table-card">
            <div class="table-header">
                <h2 class="table-title">Daftar Perusahaan</h2>
                <div class="result-count">
                    Total: <strong>{{ $tptkb->total() }}</strong> perusahaan
                </div>
            </div>

            <div class="table-container">
                @if($tptkb->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Tanggal SK</th>
                            <th>Kabupaten/Kota</th>
                            <th>Penanggung Jawab</th>
                            <th>Sumber Bahan Baku</th>
                            <th>Kapasitas (m³/tahun)</th>
                            <th>Masa Berlaku</th>
                            <th>Status Izin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tptkb as $index => $item)
                        <tr>
                            <td>{{ $tptkb->firstItem() + $index }}</td>
                            <td><strong>{{ $item->industri->nama }}</strong></td>
                            <td>{{ $item->industri->tanggal ? \Carbon\Carbon::parse($item->industri->tanggal)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->industri->kabupaten }}</td>
                            <td>{{ $item->industri->penanggungjawab }}</td>
                            <td>
                                @if($item->sumberBahanBaku->count() > 0)
                                    @foreach($item->sumberBahanBaku as $sumber)
                                        <span class="badge badge-jenis">{{ $sumber->pivot->nama_custom ?? $sumber->nama }}</span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($item->sumberBahanBaku->count() > 0)
                                    @foreach($item->sumberBahanBaku as $sumber)
                                        <div>{{ $sumber->pivot->nama_custom ?? $sumber->nama }}: {{ number_format($sumber->pivot->kapasitas, 2, ',', '.') }}</div>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->masa_berlaku ? $item->masa_berlaku->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($item->isMasaBerlakuAktif())
                                    <span class="badge badge-success">✓ Aktif</span>
                                @else
                                    <span class="badge badge-danger">✗ Kadaluarsa</span>
                                @endif
                            </td>
                            <td>
                                @if($item->industri->status == 'Aktif')
                                    <span class="badge-status-aktif">Aktif</span>
                                @else
                                    <span class="badge-status-nonaktif">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick='showDetail(@json($item), {{ $item->isMasaBerlakuAktif() ? 'true' : 'false' }})'>Lihat</button>
                                    @auth
                                    @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('tptkb.edit', $item->id) }}" class="btn-action btn-edit">Edit</a>
                                    <form action="{{ route('tptkb.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">Hapus</button>
                                    </form>
                                    @endif
                                    @endauth
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <div style="font-size: 18px; margin: 10px 0;">Tidak ada data ditemukan</div>
                    <p style="font-size: 14px;">Silakan ubah filter atau tambah data baru</p>
                </div>
                @endif
            </div>

            @if($tptkb->hasPages())
            <div class="pagination">
                {{ $tptkb->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
    </div>
    <!-- End Main Wrapper -->

    <!-- Modal Detail -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detail TPT-KB</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-section-title">Informasi Umum</div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label-col">Nama Perusahaan</td>
                        <td class="detail-value-col" id="modal-nama">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Penanggung Jawab</td>
                        <td class="detail-value-col" id="modal-penanggungjawab">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Kontak</td>
                        <td class="detail-value-col" id="modal-kontak">-</td>
                    </tr>
                </table>

                <div class="detail-section-title">Detail Izin & Usaha</div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label-col">Nomor SK</td>
                        <td class="detail-value-col" id="modal-nomor-izin">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Tanggal SK</td>
                        <td class="detail-value-col" id="modal-tanggal">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Masa Berlaku</td>
                        <td class="detail-value-col" id="modal-masa-berlaku">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Status Izin</td>
                        <td class="detail-value-col" id="modal-status">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Pemberi Izin</td>
                        <td class="detail-value-col" id="modal-pemberi-izin">-</td>
                    </tr>
                </table>

                <div class="detail-section-title">Sumber Bahan Baku & Kapasitas</div>
                <table class="table-detail" id="modal-sumber-table">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 10px; font-weight: 600;">Sumber</th>
                            <th style="padding: 10px; font-weight: 600; text-align: right;">Kapasitas (m³/tahun)</th>
                        </tr>
                    </thead>
                    <tbody id="modal-sumber-body">
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 20px;">Tidak ada data</td>
                        </tr>
                    </tbody>
                </table>

                <div class="detail-section-title">Lokasi</div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label-col">Alamat Lengkap</td>
                        <td class="detail-value-col" id="modal-alamat">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Kabupaten/Kota</td>
                        <td class="detail-value-col" id="modal-kabupaten">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Latitude</td>
                        <td class="detail-value-col" id="modal-latitude">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Longitude</td>
                        <td class="detail-value-col" id="modal-longitude">-</td>
                    </tr>
                </table>
                
                <div id="modal-map-container" style="display: none; margin-top: 15px;">
                    <div class="detail-section-title">Peta Lokasi</div>
                    <div id="modal-map" style="height: 650px; border-radius: 8px; border: 1px solid #e2e8f0;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/filter-collapse.js') }}"></script>
    <script>
        function showDetail(item, isAktif) {
            document.getElementById('modal-nama').textContent = item.industri.nama;
            document.getElementById('modal-nomor-izin').textContent = item.industri.nomor_izin;
            document.getElementById('modal-alamat').textContent = item.industri.alamat;
            document.getElementById('modal-kabupaten').textContent = item.industri.kabupaten;
            document.getElementById('modal-penanggungjawab').textContent = item.industri.penanggungjawab;
            document.getElementById('modal-kontak').textContent = item.industri.kontak;
            document.getElementById('modal-pemberi-izin').textContent = item.pemberi_izin;
            
            // Tampilkan sumber bahan baku
            const sumberBody = document.getElementById('modal-sumber-body');
            if (item.sumber_bahan_baku && item.sumber_bahan_baku.length > 0) {
                sumberBody.innerHTML = '';
                item.sumber_bahan_baku.forEach(sumber => {
                    const namaSumber = sumber.pivot.nama_custom || sumber.nama;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td style="padding: 10px;">${namaSumber}</td>
                        <td style="padding: 10px; text-align: right;">${parseFloat(sumber.pivot.kapasitas).toLocaleString('id-ID', {minimumFractionDigits: 2})}</td>
                    `;
                    sumberBody.appendChild(row);
                });
            } else {
                sumberBody.innerHTML = '<tr><td colspan="2" style="text-align: center; padding: 20px;">Tidak ada data sumber bahan baku</td></tr>';
            }
            
            // Format tanggal izin
            if(item.industri.tanggal) {
                const date = new Date(item.industri.tanggal);
                document.getElementById('modal-tanggal').textContent = date.toLocaleDateString('id-ID');
            } else {
                document.getElementById('modal-tanggal').textContent = '-';
            }
            
            // Format masa berlaku
            if(item.masa_berlaku) {
                const date = new Date(item.masa_berlaku);
                document.getElementById('modal-masa-berlaku').textContent = date.toLocaleDateString('id-ID');
            } else {
                document.getElementById('modal-masa-berlaku').textContent = '-';
            }
            
            // Status badge - now uses the passed isAktif parameter
            const statusEl = document.getElementById('modal-status');
            if(isAktif) {
                statusEl.innerHTML = '<span class="badge badge-success">✓ Aktif</span>';
            } else {
                statusEl.innerHTML = '<span class="badge badge-danger">✗ Kadaluarsa</span>';
            }

            // Tampilkan koordinat
            const latitude = item.industri.latitude;
            const longitude = item.industri.longitude;
            
            if (latitude && longitude) {
                document.getElementById('modal-latitude').textContent = parseFloat(latitude).toFixed(6);
                document.getElementById('modal-longitude').textContent = parseFloat(longitude).toFixed(6);
                document.getElementById('modal-map-container').style.display = 'block';
                
                setTimeout(() => initDetailMap(parseFloat(latitude), parseFloat(longitude)), 100);
            } else {
                document.getElementById('modal-latitude').textContent = '-';
                document.getElementById('modal-longitude').textContent = '-';
                document.getElementById('modal-map-container').style.display = 'none';
            }

            document.getElementById('detailModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
            // Hapus peta untuk menghindari memory leak
            if (window.detailMap) {
                window.detailMap.remove();
                window.detailMap = null;
            }
        }

        // Fungsi untuk inisialisasi peta di modal detail
        function initDetailMap(lat, lng) {
            // Hapus peta lama jika ada
            if (window.detailMap) {
                window.detailMap.remove();
            }
            
            // Buat peta baru
            window.detailMap = L.map('modal-map').setView([lat, lng], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(window.detailMap);
            
            // Tambahkan marker
            L.marker([lat, lng])
                .addTo(window.detailMap)
                .bindPopup(`Lokasi: ${lat.toFixed(6)}, ${lng.toFixed(6)}`)
                .openPopup();
            
            // Fix rendering issue in modal
            setTimeout(() => {
                window.detailMap.invalidateSize();
            }, 100);
        }

        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Auto-hide success alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-success');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Toggle filter visibility
        function toggleFilter() {
            const filterBody = document.getElementById('filterBody');
            const filterIcon = document.getElementById('filterIcon');
            
            if (filterBody.style.display === 'none') {
                filterBody.style.display = 'block';
                filterIcon.classList.remove('fa-chevron-down');
                filterIcon.classList.add('fa-chevron-up');
            } else {
                filterBody.style.display = 'none';
                filterIcon.classList.remove('fa-chevron-up');
                filterIcon.classList.add('fa-chevron-down');
            }
        }

        // Prevent filter body clicks from bubbling to header
        document.addEventListener('DOMContentLoaded', function() {
            const filterBody = document.getElementById('filterBody');
            if (filterBody) {
                filterBody.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
        });
    </script>

    <!-- Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('select[name="kabupaten"]').select2({
                placeholder: '-- Pilih Kabupaten/Kota --',
                allowClear: true,
                width: '100%'
            });

            $('select[name="sumber_bahan_baku"]').select2({
                placeholder: '-- Semua Sumber --',
                allowClear: true,
                width: '100%'
            });

            // Generate Statistics Charts - Menggunakan data dari Controller
            // Chart Tahun
            const ctxTahun = document.getElementById('chartTahun').getContext('2d');
            new Chart(ctxTahun, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($yearStats->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($yearStats->values()) !!},
                        backgroundColor: [
                            '#8b5cf6', '#7c3aed', '#6d28d9', '#5b21b6', '#a855f7',
                            '#9333ea', '#a78bfa', '#c4b5fd', '#ddd6fe', '#ede9fe'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Chart Kabupaten
            const ctxKabupaten = document.getElementById('chartKabupaten').getContext('2d');
            new Chart(ctxKabupaten, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($locationStats->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($locationStats->values()) !!},
                        backgroundColor: [
                            '#3b82f6', '#2563eb', '#1d4ed8', '#1e40af', '#1e3a8a',
                            '#60a5fa', '#93c5fd', '#bfdbfe', '#dbeafe', '#eff6ff',
                            '#0ea5e9', '#0284c7', '#0369a1', '#075985', '#0c4a6e'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });

            // Chart Kapasitas
            const ctxKapasitas = document.getElementById('chartKapasitas').getContext('2d');
            new Chart(ctxKapasitas, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($capacityStats->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($capacityStats->values()) !!},
                        backgroundColor: ['#f59e0b', '#d97706', '#b45309']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <!-- Import Modal -->
    <div id="importModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2 class="modal-title">Import Data dari Excel</h2>
                <span class="close-btn" onclick="closeImportModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="importAlert" style="display: none;"></div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--primary);">
                        <i class="fas fa-file-excel" style="color: #10b981;"></i> Pilih File Excel
                    </label>
                    <input type="file" id="importFile" accept=".xlsx,.xls" 
                           style="width: 100%; padding: 12px; border: 2px dashed var(--border); border-radius: 8px; cursor: pointer;">
                    <small style="color: #64748b; display: block; margin-top: 8px;">
                        Format: .xlsx atau .xls (Maksimal 10MB)
                    </small>
                </div>

                <div id="importProgress" style="display: none; margin-bottom: 20px;">
                    <div style="background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div id="progressBar" style="background: linear-gradient(90deg, #10b981, #059669); height: 100%; width: 0%; transition: width 0.3s;"></div>
                    </div>
                    <p id="progressText" style="text-align: center; margin-top: 8px; color: #64748b; font-size: 14px;"></p>
                </div>

                <div id="importResult" style="display: none; margin-top: 20px;">
                    <h4 style="color: var(--primary); margin-bottom: 12px;">Hasil Import:</h4>
                    <div id="resultContent"></div>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                    <button onclick="closeImportModal()" class="btn" style="background: #64748b; color: white;">
                        Batal
                    </button>
                    <button onclick="processImport()" id="btnImport" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload & Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openImportModal() {
            document.getElementById('importModal').style.display = 'block';
            // Reset form
            document.getElementById('importFile').value = '';
            document.getElementById('importAlert').style.display = 'none';
            document.getElementById('importProgress').style.display = 'none';
            document.getElementById('importResult').style.display = 'none';
        }

        function closeImportModal() {
            document.getElementById('importModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('importModal');
            if (event.target == modal) {
                closeImportModal();
            }
        }

        function processImport() {
            const fileInput = document.getElementById('importFile');
            const file = fileInput.files[0];
            
            if (!file) {
                showAlert('Silakan pilih file Excel terlebih dahulu!', 'error');
                return;
            }

            // Validate file type
            const validTypes = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
            if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls)$/)) {
                showAlert('File harus berformat .xlsx atau .xls!', 'error');
                return;
            }

            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                showAlert('Ukuran file maksimal 10MB!', 'error');
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            // Show progress
            document.getElementById('importProgress').style.display = 'block';
            document.getElementById('progressBar').style.width = '30%';
            document.getElementById('progressText').textContent = 'Mengupload file...';
            document.getElementById('btnImport').disabled = true;

            // Send AJAX request
            fetch('{{ route("tptkb.import") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                // Check if response is OK
                if (!response.ok) {
                    // Try to get error message from JSON
                    return response.json().then(data => {
                        throw new Error(data.message || `Server error: ${response.status}`);
                    }).catch(() => {
                        // If JSON parsing fails, throw generic error
                        throw new Error(`Server error: ${response.status} ${response.statusText}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('progressBar').style.width = '100%';
                document.getElementById('progressText').textContent = 'Selesai!';
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    displayImportResult(data.data);
                } else {
                    showAlert(data.message || 'Terjadi kesalahan saat import', 'error');
                    if (data.data && data.data.errors) {
                        displayImportErrors(data.data.errors);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan: ' + error.message, 'error');
                document.getElementById('progressBar').style.width = '0%';
                document.getElementById('progressText').textContent = '';
            })
            .finally(() => {
                document.getElementById('btnImport').disabled = false;
            });
        }

        function showAlert(message, type) {
            const alertDiv = document.getElementById('importAlert');
            alertDiv.className = 'alert alert-' + type;
            alertDiv.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message;
            alertDiv.style.display = 'flex';
        }

        function displayImportResult(data) {
            const resultDiv = document.getElementById('importResult');
            const contentDiv = document.getElementById('resultContent');
            
            let html = `
                <div style="background: white; padding: 16px; border-radius: 8px; border: 1px solid var(--border);">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 12px;">
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #3b82f6;">${data.total}</div>
                            <div style="font-size: 12px; color: #64748b;">Total Baris</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #10b981;">${data.success}</div>
                            <div style="font-size: 12px; color: #64748b;">Berhasil</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 24px; font-weight: 700; color: #ef4444;">${data.errors_count}</div>
                            <div style="font-size: 12px; color: #64748b;">Gagal</div>
                        </div>
                    </div>
            `;
            
            if (data.errors && data.errors.length > 0) {
                html += `
                    <div style="margin-top: 16px; max-height: 200px; overflow-y: auto;">
                        <h5 style="color: #ef4444; margin-bottom: 8px; font-size: 14px;">Error Detail:</h5>
                        <ul style="margin: 0; padding-left: 20px; font-size: 13px; color: #64748b;">
                `;
                data.errors.forEach(error => {
                    html += `<li>Baris ${error.row}: ${error.message}</li>`;
                });
                html += `</ul></div>`;
            }
            
            html += `</div>`;
            contentDiv.innerHTML = html;
            resultDiv.style.display = 'block';
        }

        function displayImportErrors(errors) {
            displayImportResult({ total: 0, success: 0, errors_count: errors.length, errors: errors });
        }
    </script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
         integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
         crossorigin=""></script>
@endpush
