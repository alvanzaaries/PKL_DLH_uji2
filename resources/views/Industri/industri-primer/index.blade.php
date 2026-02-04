@extends('Industri.layouts.sidebar')

@section('title', 'Data Industri Primer (PBPHH)')

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

        .container {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

                /* Header Section */
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
        
        /* Table */
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

        .badge-jenis {
            display: inline-block;
            background: linear-gradient(135deg, #24a148 0%, #198038 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            margin: 2px;
            box-shadow: 0 1px 3px rgba(36, 161, 72, 0.3);
        }

        .badge-lainnya {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            margin: 2px;
            box-shadow: 0 1px 3px rgba(249, 115, 22, 0.3);
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

        .btn-document {
            background: #dc2626;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-document:hover {
            background: #b91c1c;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .btn-document i {
            font-size: 14px;
        }

        /* Select2 Custom Styling */
        .select2-container {
            max-width: 100%;
        }

        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 6px 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            color: var(--text-main);
            font-size: 14px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 8px;
            font-size: 14px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--accent);
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

        /* Pagination */
        .pagination {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid var(--border);
            text-decoration: none;
            color: var(--text-main);
            font-size: 14px;
            font-weight: 500;
        }

        .pagination a:hover {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .pagination .active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
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

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
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

            .header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .filter-actions {
                flex-direction: column;
            }

            .btn-search, .btn-reset {
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

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Data Industri Primer (PBUI)</h1>
                <p class="page-subtitle">Daftar perusahaan industri primer pengolahan hasil hutan</p>
            </div>
            @auth
            @if(auth()->user()->role === 'admin')
            <div style="display: flex; gap: 12px;">
                <button onclick="exportToExcel()" class="btn btn-primary" style="background: #16a34a;">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button onclick="openImportModal()" class="btn btn-primary" style="background: #0ea5e9;">
                    <i class="fas fa-file-excel"></i> Import Excel
                </button>
                <a href="{{ route('industri-primer.create') }}" class="btn btn-primary">
                    <span>+</span> Tambah Data Baru
                </a>
            </div>
            @endif
            @endauth
        </div>

        <!-- Filter Section -->
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
                <form method="GET" action="{{ route('industri-primer.index') }}">
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
                            <label>Jenis Produksi</label>
                            <select name="jenis_produksi" class="filter-input">
                                <option value="">-- Semua Jenis Produksi --</option>
                                @foreach($jenisProduksiList as $jenis)
                                    <option value="{{ $jenis->id }}" {{ request('jenis_produksi') == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama }}
                                    </option>
                                @endforeach
                                @if($customNames->count() > 0)
                                    <optgroup label="──────────────">
                                        @foreach($customNames as $customName)
                                            <option value="{{ $customName }}" {{ request('jenis_produksi') == $customName ? 'selected' : '' }}>
                                                {{ $customName }} (Custom)
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Kapasitas (m³/tahun)</label>
                            <select name="kapasitas" class="filter-input">
                                <option value="">-- Semua Kapasitas --</option>
                                <option value="0-1999" {{ request('kapasitas') == '0-1999' ? 'selected' : '' }}>0 - 1.999 m³/tahun</option>
                                <option value="2000-5999" {{ request('kapasitas') == '2000-5999' ? 'selected' : '' }}>2.000 - 5.999 m³/tahun</option>
                                <option value=">=6000" {{ request('kapasitas') == '>=6000' ? 'selected' : '' }}>>= 6.000 m³/tahun</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Pemberi Izin</label>
                            <select name="pemberi_izin" class="filter-input">
                                <option value="">-- Semua Pemberi Izin --</option>
                                <option value="Menteri Kehutanan" {{ request('pemberi_izin') == 'Menteri Kehutanan' ? 'selected' : '' }}>Menteri Kehutanan</option>
                                <option value="BKPM" {{ request('pemberi_izin') == 'BKPM' ? 'selected' : '' }}>BKPM</option>
                                <option value="Gubernur" {{ request('pemberi_izin') == 'Gubernur' ? 'selected' : '' }}>Gubernur</option>
                                <option value="Bupati/Walikota" {{ request('pemberi_izin') == 'Bupati/Walikota' ? 'selected' : '' }}>Bupati/Walikota</option>
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
                        <a href="{{ route('industri-primer.index') }}" class="btn-reset">↻ Reset Filter</a>
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
                <h3><i class="fas fa-chart-pie" style="color: var(--accent); margin-right: 8px;"></i>Sebaran Kapasitas Izin</h3>
                <div class="chart-container">
                    <canvas id="chartKapasitas"></canvas>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="table-card">
            <div class="table-header">
                <h2 class="table-title">Daftar Perusahaan</h2>
                <div class="result-count">
                    Total: <strong>{{ $industriPrimer->total() }}</strong> perusahaan
                </div>
            </div>

            <div class="table-container">
                @if($industriPrimer->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center;">No</th>
                            <th style="text-align: center;">Nama Perusahaan</th>
                            <th style="text-align: center;">Tanggal SK</th>
                            <th style="text-align: center;">Kabupaten/Kota</th>
                            <th style="text-align: center;">Penanggung Jawab</th>
                            <th style="text-align: center;">Jenis Produksi</th>
                            <th style="text-align: center;">Kapasitas Izin</th>
                            <th style="text-align: center;">Nomor SK</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($industriPrimer as $index => $item)
                        <tr>
                            <td>{{ $industriPrimer->firstItem() + $index }}</td>
                            <td><strong>{{ $item->industri->nama }}</strong></td>
                            <td>{{ $item->industri->tanggal ? \Carbon\Carbon::parse($item->industri->tanggal)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->industri->kabupaten }}</td>
                            <td>{{ $item->industri->penanggungjawab }}</td>
                            <td>
                                @foreach($item->jenisProduksi as $jp)
                                    @php
                                        $displayName = $jp->pivot->nama_custom ?: $jp->nama;
                                        $badgeClass = $jp->pivot->nama_custom ? 'badge-lainnya' : 'badge-jenis';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $displayName }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach($item->jenisProduksi as $jp)
                                    @php
                                        $displayName = $jp->pivot->nama_custom ?: $jp->nama;
                                    @endphp
                                    <div>{{ $displayName }}: {{ $jp->pivot->kapasitas_izin }}</div>
                                @endforeach
                            </td>
                            <td style="text-align: center;">
                                @auth
                                    @if(auth()->user()->role === 'admin')
                                        {{ $item->industri->nomor_izin }}
                                    @else
                                        -
                                    @endif
                                @else
                                    -
                                @endauth
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
                                    <button class="btn-action btn-view" onclick='showDetail(@json($item))'>Lihat</button>
                                    @auth
                                    @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('industri-primer.edit', $item->id) }}" class="btn-action btn-edit">Edit</a>
                                    <form action="{{ route('industri-primer.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete('{{ $item->industri->nama }}')">
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
                    <div class="empty-state-text">Tidak ada data ditemukan</div>
                    <p style="font-size: 14px;">Silakan ubah filter atau tambah data baru</p>
                </div>
                @endif
            </div>

            @if($industriPrimer->hasPages())
            <div class="pagination">
                {{ $industriPrimer->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Detail Perusahaan -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Detail Perusahaan</h2>
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
                    <tr>
                        <td class="detail-label-col">Total Nilai Investasi</td>
                        <td class="detail-value-col" id="modal-investasi">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Total Pegawai</td>
                        <td class="detail-value-col" id="modal-pegawai">-</td>
                    </tr>
                </table>

                <div class="detail-section-title">Detail Izin & Produksi</div>
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
                        <td class="detail-label-col">Pemberi Izin</td>
                        <td class="detail-value-col" id="modal-pemberi-izin">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Jenis Produksi & Kapasitas</td>
                        <td class="detail-value-col" id="modal-jenis-produksi-list">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Dokumen Izin</td>
                        <td class="detail-value-col" id="modal-dokumen">-</td>
                    </tr>
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
    <!-- Select2 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/filter-collapse.js') }}"></script>

    <script>
        const isLoggedIn = @json(auth()->check());
        const isAdmin = @json(auth()->check() && auth()->user()->role === 'admin');

        function showDetail(item) {
            // Populate modal dengan data
            document.getElementById('modal-nama').textContent = item.industri.nama;
            document.getElementById('modal-nomor-izin').textContent = isAdmin ? item.industri.nomor_izin : '-';
            document.getElementById('modal-alamat').textContent = item.industri.alamat;
            document.getElementById('modal-kabupaten').textContent = item.industri.kabupaten;
            document.getElementById('modal-penanggungjawab').textContent = item.industri.penanggungjawab;
            document.getElementById('modal-kontak').textContent = item.industri.kontak;
            document.getElementById('modal-pemberi-izin').textContent = item.pemberi_izin;
            
            // Total Nilai Investasi
            if (item.total_nilai_investasi) {
                document.getElementById('modal-investasi').textContent = 'Rp ' + parseInt(item.total_nilai_investasi).toLocaleString('id-ID');
            } else {
                document.getElementById('modal-investasi').textContent = '-';
            }
            
            // Total Pegawai
            if (item.total_pegawai) {
                document.getElementById('modal-pegawai').textContent = parseInt(item.total_pegawai).toLocaleString('id-ID') + ' orang';
            } else {
                document.getElementById('modal-pegawai').textContent = '-';
            }
            
            // Tampilkan multiple jenis produksi dengan kapasitas
            if (item.jenis_produksi && item.jenis_produksi.length > 0) {
                let produksiHTML = '<ul style="margin: 0; padding-left: 20px;">';
                item.jenis_produksi.forEach(jp => {
                    const namaJenis = jp.pivot.nama_custom || jp.nama;
                    const kapasitas = jp.pivot.kapasitas_izin || 0;
                    produksiHTML += `<li><strong>${namaJenis}</strong>: ${parseInt(kapasitas).toLocaleString('id-ID')} m³/tahun</li>`;
                });
                produksiHTML += '</ul>';
                document.getElementById('modal-jenis-produksi-list').innerHTML = produksiHTML;
            } else {
                document.getElementById('modal-jenis-produksi-list').textContent = '-';
            }
            
            // Tanggal (from parent industri)
            if(item.industri && item.industri.tanggal) {
                const t = new Date(item.industri.tanggal);
                document.getElementById('modal-tanggal').textContent = t.toLocaleDateString('id-ID');
            } else {
                document.getElementById('modal-tanggal').textContent = '-';
            }

            // Handle dokumen izin - Security: Same response whether document exists or not for unauthenticated users
            const dokumenElement = document.getElementById('modal-dokumen');
            if (isAdmin) {
                // Admin: Show view and download links if document exists
                if (item.dokumen_izin) {
                    const viewUrl = `/industri/primer/${item.id}/view-dokumen`;
                    const downloadUrl = `/industri/primer/${item.id}/dokumen`;
                    
                    dokumenElement.innerHTML = `
                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                            <a href="${viewUrl}" 
                               target="_blank" 
                               style="color: #15803d; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; padding: 8px 16px; background: #f0fdf4; border-radius: 6px; border: 1px solid #bbf7d0; transition: all 0.2s;"
                               onmouseover="this.style.background='#dcfce7'"
                               onmouseout="this.style.background='#f0fdf4'">
                                <i class="fas fa-eye" style="color: #15803d;"></i>
                                <span>Lihat Dokumen</span>
                                <i class="fas fa-external-link-alt" style="font-size: 12px;"></i>
                            </a>
                            <a href="${downloadUrl}" 
                               style="color: #dc2626; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; padding: 8px 16px; background: #fef2f2; border-radius: 6px; border: 1px solid #fecaca; transition: all 0.2s;"
                               onmouseover="this.style.background='#fee2e2'"
                               onmouseout="this.style.background='#fef2f2'">
                                <i class="fas fa-download" style="color: #dc2626;"></i>
                                <span>Download PDF</span>
                            </a>
                        </div>
                    `;
                } else {
                    dokumenElement.textContent = '-';
                }
            } else {
                // Non-admin (including unauthenticated): Always show "-" (prevents information disclosure)
                dokumenElement.textContent = '-';
            }

            // Tampilkan koordinat
            const latitude = item.industri.latitude;
            const longitude = item.industri.longitude;
            
            if (latitude && longitude) {
                document.getElementById('modal-latitude').textContent = parseFloat(latitude).toFixed(6);
                document.getElementById('modal-longitude').textContent = parseFloat(longitude).toFixed(6);
                document.getElementById('modal-map-container').style.display = 'block';
                
                // Tampilkan peta setelah modal ditampilkan
                setTimeout(() => initDetailMap(parseFloat(latitude), parseFloat(longitude)), 100);
            } else {
                document.getElementById('modal-latitude').textContent = '-';
                document.getElementById('modal-longitude').textContent = '-';
                document.getElementById('modal-map-container').style.display = 'none';
            }

            // Tampilkan modal
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

        // Close modal ketika click di luar modal
        window.onclick = function(event) {
            const modal = document.getElementById('detailModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Close modal dengan ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Konfirmasi delete
        function confirmDelete(namaPerusahaan) {
            return confirm(`Apakah Anda yakin ingin menghapus perusahaan "${namaPerusahaan}"?\n\nData yang dihapus tidak dapat dikembalikan!`);
        }

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

            $('select[name="jenis_produksi"]').select2({
                placeholder: '-- Semua Jenis --',
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
        <div class="modal-content" style="max-width: 650px; border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0e6027 0%, #0a4d1f 100%); color: white; padding: 24px; border-bottom: none;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-file-excel" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h2 class="modal-title" style="margin: 0; font-size: 20px; font-weight: 700;">Import Data dari Excel</h2>
                        <p style="margin: 4px 0 0; font-size: 13px; opacity: 0.9;">Upload file Excel untuk menambahkan data secara massal</p>
                    </div>
                </div>
                <span class="close-btn" onclick="closeImportModal()" style="color: white; opacity: 0.9; font-size: 28px;">&times;</span>
            </div>
            <div class="modal-body" style="padding: 32px;">
                <div id="importAlert" style="display: none;"></div>
                
                <!-- Drag & Drop Area -->
                <div id="dropZone" style="margin-bottom: 24px; border: 3px dashed #d1d5db; border-radius: 16px; padding: 40px; text-align: center; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); cursor: pointer; transition: all 0.3s ease;" 
                     onclick="document.getElementById('importFile').click();"
                     ondragover="event.preventDefault(); this.style.borderColor='#0e6027'; this.style.background='#ecfdf5';"
                     ondragleave="this.style.borderColor='#d1d5db'; this.style.background='linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%)';"
                     ondrop="handleDrop(event)">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #0e6027 0%, #0a4d1f 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(14, 96, 39, 0.3);">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 36px; color: white;"></i>
                        </div>
                        <div>
                            <p style="font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 8px;">
                                <span id="fileName">Klik atau Drag & Drop file Excel di sini</span>
                            </p>
                            <p style="font-size: 14px; color: #64748b; margin: 0;">
                                Format: <span style="color: #0e6027; font-weight: 600;">.xlsx</span> atau <span style="color: #0e6027; font-weight: 600;">.xls</span> (Maks. 10MB)
                            </p>
                        </div>
                        <input type="file" id="importFile" accept=".xlsx,.xls" style="display: none;" onchange="handleFileSelect(event)">
                    </div>
                </div>

                <!-- File Info -->
                <div id="fileInfo" style="display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; background: #0e6027; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-file-excel" style="color: white; font-size: 18px;"></i>
                        </div>
                        <div style="flex: 1;">
                            <p id="selectedFileName" style="margin: 0; font-weight: 600; color: #0f172a; font-size: 14px;"></p>
                            <p id="selectedFileSize" style="margin: 4px 0 0; font-size: 12px; color: #64748b;"></p>
                        </div>
                        <button onclick="clearFile()" style="background: none; border: none; color: #ef4444; cursor: pointer; padding: 8px; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='none'">
                            <i class="fas fa-times-circle" style="font-size: 20px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div id="importProgress" style="display: none; margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 14px; font-weight: 600; color: #0f172a;">Mengupload...</span>
                        <span id="progressPercent" style="font-size: 14px; font-weight: 700; color: #0e6027;">0%</span>
                    </div>
                    <div style="background: #e5e7eb; height: 12px; border-radius: 6px; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                        <div id="progressBar" style="background: linear-gradient(90deg, #0e6027, #0a4d1f); height: 100%; width: 0%; transition: width 0.3s; border-radius: 6px; position: relative; overflow: hidden;">
                            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); animation: shimmer 2s infinite;"></div>
                        </div>
                    </div>
                    <p id="progressText" style="text-align: center; margin-top: 8px; color: #64748b; font-size: 13px;"></p>
                </div>

                <!-- Import Result -->
                <div id="importResult" style="display: none; margin-top: 24px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 12px; padding: 20px;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                        <div style="width: 40px; height: 40px; background: #0e6027; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check" style="color: white; font-size: 18px;"></i>
                        </div>
                        <h4 style="color: #0f172a; margin: 0; font-size: 16px; font-weight: 700;">Hasil Import</h4>
                    </div>
                    <div id="resultContent" style="color: #166534;"></div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                    <button onclick="closeImportModal()" class="btn" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 12px 24px; font-weight: 600; border-radius: 10px; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button onclick="processImport()" id="btnImport" class="btn btn-primary" style="background: linear-gradient(135deg, #0e6027 0%, #0a4d1f 100%); padding: 12px 32px; font-weight: 600; border-radius: 10px; box-shadow: 0 4px 12px rgba(14, 96, 39, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(14, 96, 39, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(14, 96, 39, 0.3)'">
                        <i class="fas fa-upload"></i> Upload & Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>

    <script>
        function handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                displayFileInfo(file);
            }
        }

        function handleDrop(event) {
            event.preventDefault();
            const dropZone = event.currentTarget;
            dropZone.style.borderColor = '#d1d5db';
            dropZone.style.background = 'linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%)';
            
            const file = event.dataTransfer.files[0];
            if (file) {
                const fileInput = document.getElementById('importFile');
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;
                displayFileInfo(file);
            }
        }

        function displayFileInfo(file) {
            const fileName = document.getElementById('selectedFileName');
            const fileSize = document.getElementById('selectedFileSize');
            const fileInfo = document.getElementById('fileInfo');
            const dropZone = document.getElementById('dropZone');
            
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.style.display = 'block';
            dropZone.style.display = 'none';
        }

        function clearFile() {
            document.getElementById('importFile').value = '';
            document.getElementById('fileInfo').style.display = 'none';
            document.getElementById('dropZone').style.display = 'block';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function openImportModal() {
            document.getElementById('importModal').style.display = 'block';
            // Reset form
            clearFile();
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
            document.getElementById('progressPercent').textContent = '30%';
            document.getElementById('progressText').textContent = 'Mengupload file...';
            document.getElementById('btnImport').disabled = true;

            // Send AJAX request
            fetch('{{ route("industri-primer.import") }}', {
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
                document.getElementById('progressPercent').textContent = '100%';
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
            
            // Update button based on import result
            const btnImport = document.getElementById('btnImport');
            if (data.errors_count === 0 && data.success > 0) {
                // Semua berhasil - ubah jadi tombol "Lihat Data"
                btnImport.innerHTML = '<i class="fas fa-table"></i> Lihat Data';
                btnImport.onclick = function() {
                    window.location.reload();
                };
            } else {
                // Masih ada error - tetap "Upload & Import"
                btnImport.innerHTML = '<i class="fas fa-upload"></i> Upload & Import';
                btnImport.onclick = function() {
                    processImport();
                };
            }
        }

        function displayImportErrors(errors) {
            displayImportResult({ total: 0, success: 0, errors_count: errors.length, errors: errors });
        }

        function exportToExcel() {
            // Ambil parameter filter dari form
            const form = document.querySelector('form[action="{{ route('industri-primer.index') }}"]');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            // Redirect ke route export dengan parameter filter
            window.location.href = '{{ route("industri-primer.export") }}?' + params.toString();
        }
    </script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
         integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
         crossorigin=""></script>
@endpush

