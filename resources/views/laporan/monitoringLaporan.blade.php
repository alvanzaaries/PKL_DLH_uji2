@extends('laporan/layouts.layout')

@section('title', 'Monitoring Pelaporan')

@section('page-title', 'Monitoring Pelaporan Industri')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/laporan/custom.css') }}">

    <div class="content-card">
        <div class="card-header">
            <div class="card-title">
                <h2>MONITORING DATA PELAPORAN</h2>
                <p>Periode: {{ request('tahun', date('Y')) }}</p>
            </div>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                <i class="fas fa-chart-line"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>

        @php
            // Calculate monthly data for chart (Removed, but might be needed if logic depends on it, but here only table)
            $monthlyCounts = array_fill(0, 12, 0);
            // ... simplified logic as chart is not needed
        @endphp

        <div class="filter-ribbon">
            <form method="GET" action="{{ route('laporan.monitoring') }}" style="display: contents;">

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
                            $startYear = 2020; // Adjusted
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

                <div class="filter-group">
                    <label class="filter-label" for="status_industri">Status Industri</label>
                    <select name="status_industri" id="status_industri" class="filter-input">
                        <option value="aktif" {{ request('status_industri', 'aktif') == 'aktif' ? 'selected' : '' }}>Hanya
                            Aktif</option>
                        <option value="semua" {{ request('status_industri') == 'semua' ? 'selected' : '' }}>Semua Industri
                        </option>
                    </select>
                </div>

                <div class="filter-actions" style="margin-left: auto; display: flex; gap: 8px;">
                    <a href="{{ route('laporan.monitoring') }}" class="btn btn-secondary">
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
                                        @auth
                                            <span class="meta-info">No Izin : {{ $company->nomor_izin ?? 'N/A' }}</span>
                                        @endauth
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
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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