<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Industri Sekunder (IPHHK)</title>
    <link rel="icon" href="{{ asset('logo jateng.webp') }}" type="image/webp">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
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

        .site-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            margin-right: 12px;
            display: inline-block;
            vertical-align: middle;
        }

        .back-link {
            text-decoration: none;
            color: var(--accent);
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
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
            font-weight: 600;   F]
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
            background: #166534;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.3);
        }

        /* Filter Section */
        .filter-card {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 25px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .filter-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Segoe UI', Arial, sans-serif;
            transition: border-color 0.2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--accent);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .btn-filter {
            background: var(--accent);
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-filter:hover {
            background: #166534;
        }

        .btn-reset {
            background: #f1f5f9;
            color: var(--text-main);
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-reset:hover {
            background: #e2e8f0;
        }

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
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #ffffff;
            font-size: 14px;
            border-bottom: 3px solid #14532d;
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
            background: #10b981;
            color: white;
        }

        .btn-edit:hover {
            background: #059669;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        /* Select2 Custom Styling */
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
            background: linear-gradient(135deg, var(--accent) 0%, #166534 100%);
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
            }

            .menu-text {
                display: none;
            }

            .sidebar-title {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <img src="{{ asset('logo jateng.webp') }}" alt="Logo Jawa Tengah">
            </div>
            <div class="sidebar-text">
                <p class="sidebar-text-top">Pemerintah Provinsi Jawa Tengah</p>
                <p class="sidebar-text-bottom">Dinas Lingkungan Hidup<br>dan Kehutanan</p>
            </div>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('industri.dashboard') }}" class="menu-item {{ request()->routeIs('industri.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large menu-icon"></i>
                <span class="menu-text">Beranda</span>
            </a>
            
            <a href="{{ route('industri-primer.index') }}" class="menu-item {{ request()->routeIs('industri-primer.*') ? 'active' : '' }}">
                <i class="fas fa-industry menu-icon"></i>
                <span class="menu-text">Industri Primer</span>
            </a>
            
            <a href="{{ route('industri-sekunder.index') }}" class="menu-item {{ request()->routeIs('industri-sekunder.*') ? 'active' : '' }}">
                <i class="fas fa-microchip menu-icon"></i>
                <span class="menu-text">Industri Sekunder</span>
            </a>
            
            <a href="{{ route('tptkb.index') }}" class="menu-item {{ request()->routeIs('tptkb.*') ? 'active' : '' }}">
                <i class="fas fa-map-marked-alt menu-icon"></i>
                <span class="menu-text">TPTKB</span>
            </a>
            
            <a href="{{ route('perajin.index') }}" class="menu-item {{ request()->routeIs('perajin.*') ? 'active' : '' }}">
                <i class="fas fa-tools menu-icon"></i>
                <span class="menu-text">Perajin</span>
            </a>
            
            <div style="margin-top: 30px; padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.1);">
                @guest
                <a href="{{ route('login', ['from' => url()->current()]) }}" class="menu-item" style="background: rgba(76, 175, 80, 0.2); border-radius: 8px; justify-content: center;">
                    <i class="fas fa-sign-in-alt menu-icon"></i>
                    <span class="menu-text" style="font-weight: 600;">Login Admin</span>
                </a>
                @else
                <div style="color: rgba(255, 255, 255, 0.9); margin-bottom: 15px; text-align: center;">
                    <i class="fas fa-user-circle" style="font-size: 36px; margin-bottom: 8px;"></i>
                    <div style="font-size: 13px; font-weight: 600;">{{ Auth::user()->name }}</div>
                    <div style="font-size: 11px; opacity: 0.7;">{{ Auth::user()->email }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="menu-item" style="width: 100%; background: rgba(239, 68, 68, 0.2); border: none; cursor: pointer; border-radius: 8px; justify-content: center;">
                        <i class="fas fa-sign-out-alt menu-icon"></i>
                        <span class="menu-text" style="font-weight: 600;">Logout</span>
                    </button>
                </form>
                @endguest
            </div>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Navigation -->
        <nav>
            <div class="nav-content">
                <div class="logo-area">
                    <span class="logo-text">Dinas Lingkungan Hidup dan Kehutanan</span>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                    @auth
                    <div class="user-info">
                        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        <span style="font-size: 14px; font-weight: 500;">{{ Auth::user()->name }}</span>
                    </div>
                    @else
                    <a href="{{ route('login', ['from' => url()->current()]) }}" style="padding: 8px 20px; background: var(--accent); color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 14px; transition: all 0.2s;">
                        <i class="fas fa-sign-in-alt"></i> Portal Login
                    </a>
                    @endauth
                </div>
            </div>
            
            <!-- Alert Messages -->
            @if(session('success'))
            <div class="alert alert-success" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-top: none;">
                <span style="font-size: 20px;">✓</span>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-error" style="margin: 0; border-radius: 0; border-left: none; border-right: none; border-top: none;">
                <span style="font-size: 20px;">⚠</span>
                <span>{{ session('error') }}</span>
            </div>
            @endif
        </nav>

        <script>
            // Auto-hide alerts after 1.5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.alert');
                if (alerts.length > 0) {
                    setTimeout(function() {
                        alerts.forEach(function(alert) {
                            alert.style.transition = 'opacity 0.3s ease';
                            alert.style.opacity = '0';
                            setTimeout(function() {
                                alert.remove();
                            }, 300);
                        });
                    }, 1500);
                }
            });
        </script>

        <div class="container">

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Data Industri Sekunder (PBUI)</h1>
                <p class="page-subtitle">Daftar perusahaan industri sekunder pengolahan hasil hutan</p>
            </div>
            @auth
            <a href="{{ route('industri-sekunder.create') }}" class="btn btn-primary">
                <span>+</span> Tambah Data Baru
            </a>
            @endauth
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <form method="GET" action="{{ route('industri-sekunder.index') }}">
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
                        <label>Kapasitas Izin</label>
                        <select name="kapasitas" class="filter-input">
                            <option value="">-- Semua Kapasitas --</option>
                            <option value="0-1999" {{ request('kapasitas') == '0-1999' ? 'selected' : '' }}>0 - 1999 m³/tahun</option>
                            <option value="2000-5999" {{ request('kapasitas') == '2000-5999' ? 'selected' : '' }}>2000 - 5999 m³/tahun</option>
                            <option value=">= 6000" {{ request('kapasitas') == '>= 6000' ? 'selected' : '' }}>>= 6000 m³/tahun</option>
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
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Cari Data</button>
                    <a href="{{ route('industri-sekunder.index') }}" class="btn-reset">↻ Reset Filter</a>
                </div>
            </form>
        </div>

        <!-- Statistics Section -->
        <div class="statistics-section">
            <div class="stat-card">
                <h3>📊 Sebaran Per Tahun</h3>
                <div class="chart-container">
                    <canvas id="chartTahun"></canvas>
                </div>
            </div>
            <div class="stat-card">
                <h3>🗺️ Sebaran Kabupaten/Kota</h3>
                <div class="chart-container">
                    <canvas id="chartKabupaten"></canvas>
                </div>
            </div>
            <div class="stat-card">
                <h3>📦 Sebaran Kapasitas Izin</h3>
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
                    Total: <strong>{{ $industriSekunder->total() }}</strong> perusahaan
                </div>
            </div>

            <div class="table-container">
                @if($industriSekunder->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Tanggal</th>
                            <th>Kabupaten/Kota</th>
                            <th>Penanggung Jawab</th>
                            <th>Jenis Produksi</th>
                            <th>Kapasitas Izin</th>
                            <th>Nomor Izin</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($industriSekunder as $index => $item)
                        <tr>
                            <td>{{ $industriSekunder->firstItem() + $index }}</td>
                            <td><strong>{{ $item->industri->nama }}</strong></td>
                            <td>{{ $item->industri->tanggal ? \Carbon\Carbon::parse($item->industri->tanggal)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $item->industri->kabupaten }}</td>
                            <td>{{ $item->industri->penanggungjawab }}</td>
                            <td>{{ $item->jenis_produksi }}</td>
                            <td>{{ $item->kapasitas_izin }}</td>
                            <td>{{ $item->industri->nomor_izin }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick='showDetail(@json($item))'>Lihat</button>
                                    @auth
                                    <a href="{{ route('industri-sekunder.edit', $item->id) }}" class="btn-action btn-edit">Edit</a>
                                    <form action="{{ route('industri-sekunder.destroy', $item->id) }}" method="POST" style="display: inline;" onsubmit="return confirmDelete('{{ $item->industri->nama }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete">Hapus</button>
                                    </form>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="empty-state">
                    <div class="empty-state-icon">📂</div>
                    <div class="empty-state-text">Tidak ada data ditemukan</div>
                    <p style="font-size: 14px;">Silakan ubah filter atau tambah data baru</p>
                </div>
                @endif
            </div>

            @if($industriSekunder->hasPages())
            <div class="pagination">
                {{ $industriSekunder->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
    </div>
    <!-- End Main Wrapper -->

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
                </table>

                <div class="detail-section-title">Detail Izin & Produksi</div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label-col">Nomor Izin</td>
                        <td class="detail-value-col" id="modal-nomor-izin">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Tanggal Izin</td>
                        <td class="detail-value-col" id="modal-tanggal">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Pemberi Izin</td>
                        <td class="detail-value-col" id="modal-pemberi-izin">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Jenis Produksi</td>
                        <td class="detail-value-col" id="modal-jenis-produksi">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Kapasitas Izin</td>
                        <td class="detail-value-col" id="modal-kapasitas">-</td>
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
                </table>
            </div>
        </div>
    </div>

    <script>
        function showDetail(item) {
            // Populate modal dengan data
            document.getElementById('modal-nama').textContent = item.industri.nama;
            document.getElementById('modal-nomor-izin').textContent = item.industri.nomor_izin;
            document.getElementById('modal-alamat').textContent = item.industri.alamat;
            document.getElementById('modal-kabupaten').textContent = item.industri.kabupaten;
            document.getElementById('modal-penanggungjawab').textContent = item.industri.penanggungjawab;
            document.getElementById('modal-kontak').textContent = item.industri.kontak;
            document.getElementById('modal-pemberi-izin').textContent = item.pemberi_izin;
            document.getElementById('modal-jenis-produksi').textContent = item.jenis_produksi;
            document.getElementById('modal-kapasitas').textContent = item.kapasitas_izin;
            
            // Format tanggal izin
            if(item.industri.tanggal) {
                const date = new Date(item.industri.tanggal);
                document.getElementById('modal-tanggal').textContent = date.toLocaleDateString('id-ID');
            } else {
                document.getElementById('modal-tanggal').textContent = '-';
            }

            // Tampilkan modal
            document.getElementById('detailModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
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
</body>
</html>
