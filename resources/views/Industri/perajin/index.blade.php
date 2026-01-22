@extends('Industri.layouts.sidebar')

@section('title', 'Data Perajin')

@push('styles')
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

        .badge-status-aktif {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-status-nonaktif {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
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
@endpush

@section('content')
        <div class="container">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Data Perajin / End User</h1>
                    <p class="page-subtitle">Daftar Perajin dan Pengguna Akhir</p>
                </div>
                @auth
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('perajin.create') }}" class="btn btn-primary">
                    + Tambah Data Baru
                </a>
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
                    <form method="GET" action="{{ route('perajin.index') }}">
                        <div class="filter-grid">
                            <div class="filter-group">
                                <label>Nama Pemilik</label>
                            <input type="text" name="search" class="filter-input" placeholder="Cari nama atau nomor SK" value="{{ request('search') }}">
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
                        <a href="{{ route('perajin.index') }}" class="btn-reset">↻ Reset Filter</a>
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
                                    <th>Nama Pemilik</th>
                                    <th>Tanggal SK</th>
                                    <th>Nomor SK</th>
                                    <th>Kabupaten/Kota</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Kontak</th>
                                    <th>Status</th>
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
                                            @if($item->industri->status == 'Aktif')
                                                <span class="badge-status-aktif">Aktif</span>
                                            @else
                                                <span class="badge-status-nonaktif">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view" onclick='showDetail(@json($item))'><i class="fas fa-info-circle"></i> Lihat</button>
                                                @auth
                                                @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('perajin.edit', $item) }}" class="btn-action btn-edit"><i class="fas fa-pen"></i> Edit</a>
                                                <form action="{{ route('perajin.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete"><i class="fas fa-times"></i> Hapus</button>
                                                </form>
                                                @endif
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
                <h2 class="modal-title">Detail Perajin</h2>
                <span class="close-btn" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="detail-section-title">Informasi Umum</div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label-col">Nama Pemilik</td>
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

                <div class="detail-section-title">Detail Izin</div>
                <table class="table-detail">
                    <tr>
                        <td class="detail-label-col">Nomor SK</td>
                        <td class="detail-value-col" id="modal-nomor-izin">-</td>
                    </tr>
                    <tr>
                        <td class="detail-label-col">Tanggal SK</td>
                        <td class="detail-value-col" id="modal-tanggal">-</td>
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
@endsection

@push('scripts')
    <script src="{{ asset('js/filter-collapse.js') }}"></script>
    <script>
        function showDetail(item) {
            document.getElementById('modal-nama').textContent = item.industri.nama;
            document.getElementById('modal-nomor-izin').textContent = item.industri.nomor_izin;
            document.getElementById('modal-alamat').textContent = item.industri.alamat;
            document.getElementById('modal-kabupaten').textContent = item.industri.kabupaten;
            document.getElementById('modal-penanggungjawab').textContent = item.industri.penanggungjawab;
            document.getElementById('modal-kontak').textContent = item.industri.kontak;
            
            // Format tanggal izin
            if(item.industri.tanggal) {
                const date = new Date(item.industri.tanggal);
                document.getElementById('modal-tanggal').textContent = date.toLocaleDateString('id-ID');
            } else {
                document.getElementById('modal-tanggal').textContent = '-';
            }

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

            
        });
    </script>
@endpush
