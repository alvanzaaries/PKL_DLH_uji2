<!DOCTYPE html>
<html lang="id">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Perajin - DLHKal</title>
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
            font-family: 'Inter', sans-serif;
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
            padding: 20px 20px 30px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 12px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            color: #5B4A9B;
        }

        .sidebar-title {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin: 0;
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
            grid-template-columns: repeat(2, 1fr);
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
            background: #f8f9fa;
        }

        th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 14px;
            border-bottom: 2px solid #e0e0e0;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #555;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-view {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-delete {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-action:hover {
            opacity: 0.8;
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
            <div class="sidebar-logo">AL</div>
            <h2 class="sidebar-title">DLHK<span style="font-weight: 400;">al</span></h2>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
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
                    <div class="user-info">
                        <div class="user-avatar">A</div>
                        <span style="font-size: 14px; font-weight: 500;">Admin</span>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">
            @if(session('success'))
            <div class="alert alert-success">
                ✓ {{ session('success') }}
            </div>
            @endif

            <div class="page-header">
                <div>
                    <h1 class="page-title">Data Perajin / End User</h1>
                    <p class="page-subtitle">Daftar Perajin dan Pengguna Akhir</p>
                </div>
                <a href="{{ route('perajin.create') }}" class="btn btn-primary">
                    + Tambah Data Baru
                </a>
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
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter">🔍 Cari Data</button>
                        <a href="{{ route('perajin.index') }}" class="btn-reset">↻ Reset Filter</a>
                    </div>
                </form>
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
                                        <td>{{ $item->industri->nomor_izin }}</td>
                                        <td>{{ $item->industri->kabupaten }}</td>
                                        <td>{{ $item->industri->penanggungjawab }}</td>
                                        <td>{{ $item->industri->kontak }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-view" onclick='showDetail(@json($item))'>👁 Lihat</button>
                                                <a href="{{ route('perajin.edit', $item) }}" class="btn-action btn-edit">✏️ Edit</a>
                                                <form action="{{ route('perajin.destroy', $item) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete">🗑️ Hapus</button>
                                                </form>
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
</body>
</html>
