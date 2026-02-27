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

        /* =============================================
           PAGE HEADER - CLEAN OFFICE STYLE
        ============================================= */
        .page-header {
            background: #ffffff;
            padding: 0;
            border-radius: 12px;
            margin-bottom: 24px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #0a5c2e;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .page-header-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px 28px;
        }

        .page-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .page-header-icon {
            width: 48px;
            height: 48px;
            background: #f0fdf4;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #bbf7d0;
            flex-shrink: 0;
        }

        .page-header-icon i {
            font-size: 22px;
            color: #0a5c2e;
        }

        .page-title {
            font-size: 20px;
            color: #0f172a;
            font-weight: 700;
            margin-bottom: 3px;
            letter-spacing: -0.3px;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 13px;
            font-weight: 400;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .page-subtitle i {
            font-size: 10px;
            color: #0a5c2e;
        }

        .page-header-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        /* =============================================
           BUTTONS
        ============================================= */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-primary {
            background: #0a5c2e;
            color: white;
            border: 1px solid #0a5c2e;
        }

        .btn-primary:hover {
            background: #074d26;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(10, 92, 46, 0.25);
            color: white;
        }

        /* =============================================
           FILTER SECTION
        ============================================= */

        /* =============================================
           TABLE COMPONENTS
        ============================================= */
        .table-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .table-header {
            padding: 18px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafbfc;
        }

        .table-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-title i {
            color: #0a5c2e;
            font-size: 15px;
        }

        .result-count {
            font-size: 13px;
            color: #64748b;
            background: #f1f5f9;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .result-count strong {
            color: #0a5c2e;
            font-weight: 700;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        thead {
            background: #0a5c2e;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        th {
            padding: 13px 14px;
            text-align: left;
            font-weight: 600;
            color: rgba(255,255,255,0.95);
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            white-space: nowrap;
            border-bottom: none;
        }

        th:first-child { padding-left: 20px; }
        td:first-child { padding-left: 20px; }

        td {
            padding: 13px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
            color: #374151;
            vertical-align: middle;
        }

        tbody tr {
            background: #ffffff;
            transition: background 0.15s ease;
        }

        tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        tbody tr:hover {
            background: #f0fdf4 !important;
        }

        /* Company name cell */
        .company-name {
            font-weight: 600;
            color: #0a5c2e;
            font-size: 14px;
        }

        .company-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-jenis {
            display: inline-block;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            margin: 2px 2px;
            white-space: nowrap;
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
            gap: 6px;
            align-items: center;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-view {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .btn-view:hover {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .btn-edit {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .btn-edit:hover {
            background: #dcfce7;
            color: #166534;
        }

        .btn-delete {
            background: #fff1f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-delete:hover {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-status-aktif {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #86efac;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-status-aktif::before {
            content: '';
            display: block;
            width: 7px;
            height: 7px;
            background: #22c55e;
            border-radius: 50%;
        }

        .badge-status-nonaktif {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff1f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-status-nonaktif::before {
            content: '';
            display: block;
            width: 7px;
            height: 7px;
            background: #ef4444;
            border-radius: 50%;
        }

        /* =============================================
           STATISTICS SECTION
        ============================================= */
        .statistics-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #ffffff;
            padding: 18px 20px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .stat-card h3 {
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card h3 i {
            color: #0a5c2e;
            font-size: 14px;
        }

        .chart-container {
            position: relative;
            height: 210px;
            margin: 0 auto;
        }

        /* =============================================
           PAGINATION
        ============================================= */
        .pagination {
            margin-top: 0;
            padding: 16px 20px;
            display: flex;
            justify-content: center;
            gap: 6px;
            border-top: 1px solid #f1f5f9;
        }

        .pagination a,
        .pagination span {
            padding: 7px 13px;
            border-radius: 7px;
            border: 1px solid #e2e8f0;
            text-decoration: none;
            color: #475569;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.15s;
        }

        .pagination a:hover {
            background: #0a5c2e;
            color: white;
            border-color: #0a5c2e;
        }

        .pagination .active {
            background: #0a5c2e;
            color: white;
            border-color: #0a5c2e;
        }

        /* =============================================
           EMPTY STATE
        ============================================= */
        .empty-state {
            text-align: center;
            padding: 70px 20px;
            color: #94a3b8;
        }

        .empty-state-icon {
            width: 72px;
            height: 72px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .empty-state-icon i {
            font-size: 30px;
            color: #94a3b8;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 600;
        }

        .empty-state p {
            font-size: 14px;
            color: #94a3b8;
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
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header-main">
                <div class="page-header-left">
                    <div class="page-header-icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div>
                        <h1 class="page-title">Data TPT-KB</h1>
                        <p class="page-subtitle">
                            <i class="fas fa-circle" style="font-size: 6px; color: #86efac;"></i>
                            Dinas Lingkungan Hidup &bull; Tempat Penampungan Tebangan Kayu Bulat
                        </p>
                    </div>
                </div>
                <div class="page-header-actions">
                    @auth
                    @if(auth()->user()->role === 'admin')
                    <button onclick="exportToExcel()" class="btn btn-primary">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button onclick="openImportModal()" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import Excel
                    </button>
                    <a href="{{ route('tptkb.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Data
                    </a>
                    @endif
                    @endauth
                </div>
            </div>
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
                <h2 class="table-title"><i class="fas fa-table"></i>Daftar Perusahaan TPTKB</h2>
                <div class="result-count">
                    Total: <strong>{{ $tptkb->total() }}</strong> perusahaan
                </div>
            </div>

            <div class="table-container">
                @if($tptkb->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th style="text-align: center;">No</th>
                            <th style="text-align: center;">Nama Perusahaan</th>
                            <th style="text-align: center;">Tanggal SK</th>
                            <th style="text-align: center;">Kabupaten/Kota</th>
                            <th style="text-align: center;">Penanggung Jawab</th>
                            <th style="text-align: center;">Sumber Bahan Baku</th>
                            <th style="text-align: center;">Kapasitas (m³/tahun)</th>
                            <th style="text-align: center;">Masa Berlaku</th>
                            <th style="text-align: center;">Status Izin</th>
                            <th style="text-align: center;">Status</th>
                            <th style="text-align: center;">Aksi</th>
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
                                    <form action="{{ route('tptkb.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="event.preventDefault(); if(confirm('Apakah Anda yakin ingin menghapus data ini?')) { this.submit(); } return false;">
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
        const isAdmin = {{ auth()->check() && auth()->user()->role === 'admin' ? 'true' : 'false' }};
        
        function showDetail(item, isAktif) {
            document.getElementById('modal-nama').textContent = item.industri.nama;
            document.getElementById('modal-nomor-izin').textContent = isAdmin ? item.industri.nomor_izin : '-';
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
            <div class="modal-body" style="padding: 32px; max-height: 70vh; overflow-y: auto;">
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
                    <div id="resultContent" style="color: #166534; max-height: 300px; overflow-y: auto; padding-right: 8px; scrollbar-width: thin; scrollbar-color: #16a34a #e5e7eb;"></div>
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
                            <div style="font-size: 24px; font-weight: 700; color: #0e6027;">${data.success}</div>
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
            const form = document.querySelector('form[action="{{ route('tptkb.index') }}"]');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            window.location.href = '{{ route("tptkb.export") }}?' + params.toString();
        }
    </script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
         integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
         crossorigin=""></script>
@endpush
