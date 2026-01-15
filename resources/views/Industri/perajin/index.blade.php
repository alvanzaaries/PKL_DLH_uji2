<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Perajin</title>
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
            background: #166534;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(21, 128, 61, 0.3);
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

        .filter-card {
            background: var(--white);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 25px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 20px;
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
            font-family: 'Inter', sans-serif;
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

        /* Statistics Section */
        .statistics-section {
            display: flex;
            gap: 20px;
            justify-content: center; /* pusatkan kartu statistik secara horizontal */
            flex-wrap: wrap;
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Batasi ukuran canvas agar chart berada di tengah kartu (lebih besar) */
        .chart-container canvas {
            max-width: 300px;
            max-height: 300px;
            width: 100%;
            height: auto;
            display: block;
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
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .detail-item {
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
        }

        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 15px;
            color: var(--primary);
            font-weight: 500;
        }

        .detail-item-full {
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
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
                    <span style="font-size: 24px;"></span>
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

            <div class="page-header">
                <div>
                    <h1 class="page-title">Data Perajin / End User</h1>
                    <p class="page-subtitle">Daftar Perajin dan Pengguna Akhir</p>
                </div>
                @auth
                <a href="{{ route('perajin.create') }}" class="btn btn-primary">
                    + Tambah Data Baru
                </a>
                @endauth
            </div>

            <div class="filter-card">
                <form method="GET" action="{{ route('perajin.index') }}">
                    <div class="filter-grid">
                        <div class="filter-group">
                            <label>Nama Perusahaan</label>
                            <input type="text" name="search" class="filter-input" placeholder="Cari nama atau nomor izin..." value="{{ request('search') }}">
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
                        <a href="{{ route('perajin.index') }}" class="btn-reset">↻ Reset Filter</a>
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
                
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h2 class="table-title">Daftar Perajin</h2>
                    <div class="result-count">
                        Total: <strong>{{ $perajin->total() }}</strong> perajin
                    </div>
                </div>

                <div class="table-container">
                    @if($perajin->count() > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Perusahaan</th>
                                    <th>Tanggal</th>
                                    <th>Nomor Izin</th>
                                    <th>Kabupaten/Kota</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Kontak</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perajin as $index => $item)
                                    <tr>
                                        <td>{{ $perajin->firstItem() + $index }}</td>
                                        <td>{{ $item->industri->nama }}</td>
                                        <td>{{ $item->industri->tanggal ? \Carbon\Carbon::parse($item->industri->tanggal)->format('d/m/Y') : '-' }}</td>
                                        <td>{{ $item->industri->nomor_izin }}</td>
                                        <td>{{ $item->industri->kabupaten }}</td>
                                        <td>{{ $item->industri->penanggungjawab }}</td>
                                        <td>{{ $item->industri->kontak }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view" onclick='showDetail(@json($item))'><i class="fas fa-info-circle"></i> Lihat</button>
                                                @auth
                                                <a href="{{ route('perajin.edit', $item) }}" class="btn-action btn-edit"><i class="fas fa-pen"></i> Edit</a>
                                                <form action="{{ route('perajin.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete"><i class="fas fa-times"></i> Hapus</button>
                                                </form>
                                                @endauth
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination">
                            {{ $perajin->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <div style="font-size: 48px;">📂</div>
                            <div style="font-size: 18px; margin: 10px 0;">Tidak ada data ditemukan</div>
                            <p style="font-size: 14px;">Silakan ubah filter atau tambah data baru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div id="detailModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">🎨 Detail Perajin</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Nama Perusahaan</div>
                        <div class="detail-value" id="modal-nama">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Nomor Izin</div>
                        <div class="detail-value" id="modal-nomor-izin">-</div>
                    </div>
                    <div class ="detail-item">
                        <div class="detail-label">Tanggal</div>
                        <div class="detail-value" id="modal-tanggal">-</div>
                    </div>
                    <div class="detail-item detail-item-full">
                        <div class="detail-label">Alamat Lengkap</div>
                        <div class="detail-value" id="modal-alamat">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Kabupaten/Kota</div>
                        <div class="detail-value" id="modal-kabupaten">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Penanggung Jawab</div>
                        <div class="detail-value" id="modal-penanggungjawab">-</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Kontak</div>
                        <div class="detail-value" id="modal-kontak">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDetail(item) {
            document.getElementById('modal-nama').textContent = item.industri.nama;
            document.getElementById('modal-nomor-izin').textContent = item.industri.nomor_izin;
            document.getElementById('modal-alamat').textContent = item.industri.alamat;
            document.getElementById('modal-kabupaten').textContent = item.industri.kabupaten;
            document.getElementById('modal-penanggungjawab').textContent = item.industri.penanggungjawab;
            document.getElementById('modal-kontak').textContent = item.industri.kontak;

            document.getElementById('detailModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('detailModal').style.display = 'none';
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

        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-success');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
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

            // Generate Statistics Charts
            @php
                // Statistik Tahun (dari created_at)
                $tahunStats = $perajin->groupBy(function($item) {
                    return \Carbon\Carbon::parse($item->created_at)->format('Y');
                })->map(function($group) {
                    return $group->count();
                })->sortKeys();

                // Statistik Kabupaten
                $kabupatenStats = $perajin->groupBy('industri.kabupaten')->map(function($group) {
                    return $group->count();
                })->sortByDesc(function($count) {
                    return $count;
                });

                
            @endphp

            // Chart Tahun
            const ctxTahun = document.getElementById('chartTahun').getContext('2d');
            new Chart(ctxTahun, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($tahunStats->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($tahunStats->values()) !!},
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
                    labels: {!! json_encode($kabupatenStats->keys()) !!},
                    datasets: [{
                        data: {!! json_encode($kabupatenStats->values()) !!},
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

            
        });
    </script>
</body>
</html>
