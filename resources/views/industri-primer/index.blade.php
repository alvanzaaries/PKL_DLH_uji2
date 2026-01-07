<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Industri Primer (PBPHH) - SIIPPHH</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Navigation */
        nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 20px 0;
            margin-bottom: 30px;
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
            display: flex;
            align-items: center;
            gap: 5px;
            transition: opacity 0.2s;
        }

        .back-link:hover {
            opacity: 0.7;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8fafc;
        }

        th {
            padding: 14px 20px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
        }

        td {
            padding: 16px 20px;
            font-size: 14px;
            border-bottom: 1px solid var(--border);
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-aktif {
            background: #dcfce7;
            color: #166534;
        }

        .badge-tidak-aktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }

        .empty-state-text {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #64748b;
        }

        /* Pagination */
        .pagination {
            padding: 20px 25px;
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

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .table-container {
                overflow-x: scroll;
            }

            table {
                min-width: 900px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-content">
            <div class="logo-area">
                <span style="font-size: 24px;">🌲</span>
                <span class="logo-text">Dinas Lingkungan Hidup dan Kehutanan</span>
            </div>
            <a href="{{ route('dashboard') }}" class="back-link">
                ← Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Data Industri Primer (PBPHH)</h1>
                <p class="page-subtitle">Daftar perusahaan industri primer pengelolaan hasil hutan</p>
            </div>
            <a href="{{ route('industri-primer.create') }}" class="btn btn-primary">
                <span>➕</span> Tambah Data Baru
            </a>
        </div>

        <!-- Filter Section -->
        <div class="filter-card">
            <form action="{{ route('industri-primer.index') }}" method="GET">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label>Nama Perusahaan</label>
                        <input 
                            type="text" 
                            name="nama" 
                            class="filter-input" 
                            placeholder="Cari nama perusahaan..."
                            value="{{ request('nama') }}"
                        >
                    </div>
                    <div class="filter-group">
                        <label>Kabupaten/Kota</label>
                        <select 
                            name="kabupaten" 
                            class="filter-input"
                        >
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                            @foreach($kabupatenList as $kab)
                            <option value="{{ $kab }}" {{ request('kabupaten') == $kab ? 'selected' : '' }}>
                                {{ $kab }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Kapasitas Izin</label>
                        <input 
                            type="text" 
                            name="kapasitas" 
                            class="filter-input" 
                            placeholder="Cari kapasitas..."
                            value="{{ request('kapasitas') }}"
                        >
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">🔍 Cari Data</button>
                    <a href="{{ route('industri-primer.index') }}" class="btn-reset">↻ Reset Filter</a>
                </div>
            </form>
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
                            <th>No</th>
                            <th>Nama Perusahaan</th>
                            <th>Kabupaten/Kota</th>
                            <th>Penanggung Jawab</th>
                            <th>Jenis Produksi</th>
                            <th>Kapasitas Izin</th>
                            <th>Nomor Izin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($industriPrimer as $index => $industri)
                        <tr>
                            <td>{{ $industriPrimer->firstItem() + $index }}</td>
                            <td><strong>{{ $industri->nama }}</strong></td>
                            <td>{{ $industri->kabupaten }}</td>
                            <td>{{ $industri->penanggungjawab }}</td>
                            <td>{{ $industri->jenis_produksi }}</td>
                            <td>{{ $industri->kapasitas_izin }}</td>
                            <td>{{ $industri->nomor_izin }}</td>
                            <td>
                                @if($industri->pelaporan == 'Aktif')
                                    <span class="badge badge-aktif">✓ Aktif</span>
                                @elseif($industri->pelaporan == 'Tidak Aktif')
                                    <span class="badge badge-tidak-aktif">✗ Tidak Aktif</span>
                                @else
                                    <span class="badge badge-pending">⏳ Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick="viewDetail({{ $industri->id }})">👁 Lihat</button>
                                    <button class="btn-action btn-edit">✏️ Edit</button>
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

            @if($industriPrimer->hasPages())
            <div class="pagination">
                {{ $industriPrimer->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        function viewDetail(id) {
            alert('Fitur detail akan segera ditambahkan untuk ID: ' + id);
            // Nanti bisa diarahkan ke halaman detail
            // window.location.href = '/industri-primer/' + id;
        }
    </script>
</body>
</html>
