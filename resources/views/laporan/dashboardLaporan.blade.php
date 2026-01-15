@extends('laporan/layouts.dashboard')

@section('title', 'Monitoring Pelaporan')

@section('page-title', 'Monitoring Pelaporan Industri')

@section('content')

<style>
    /* LOCAL SCOPED STYLES FOR DATA TABLE */
    
    /* Container: Sharp & Flat */
    .content-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 4px; /* Slight radius, almost square */
        box-shadow: none; /* Removed shadow for flat look */
    }

    /* Header: Official & Structured */
    .card-header {
        padding: 1.5rem;
        background-color: white;
        border-bottom: 2px solid #F3F4F6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title h2 {
        font-family: 'Inter', sans-serif;
        font-weight: 700;
        font-size: 1.125rem;
        color: #111827;
        margin: 0;
        letter-spacing: -0.025em;
    }

    .card-title p {
        font-size: 0.8rem;
        color: #6B7280;
        margin-top: 4px;
    }

    /* Control Ribbon (Filters) */
    .filter-ribbon {
        background-color: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #4B5563;
    }

    .filter-input {
        height: 38px;
        padding: 0 0.75rem;
        border: 1px solid #D1D5DB;
        border-radius: 4px; /* Professional squared look */
        font-size: 0.875rem;
        color: #111827;
        background-color: white;
        min-width: 180px;
        transition: border-color 0.15s;
    }

    .filter-input:focus {
        outline: none;
        border-color: #0F2F24; /* Brand Primary */
        box-shadow: 0 0 0 1px #0F2F24;
    }

    /* Buttons: Solid & Authoritative */
    .btn {
        height: 38px;
        padding: 0 1.25rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
        text-decoration: none;
    }

    .btn-primary {
        background-color: #0F2F24; /* Deep Green */
        color: white;
    }
    .btn-primary:hover { background-color: #183F32; }

    .btn-secondary {
        background-color: white;
        border-color: #D1D5DB;
        color: #374151;
    }
    .btn-secondary:hover { background-color: #F3F4F6; border-color: #9CA3AF; }

    .btn-export {
        background-color: #FFFBEB; /* Light Gold Tint */
        border-color: #D4AF37; /* Gold Border */
        color: #92400E;
    }
    .btn-export:hover { background-color: #FEF3C7; }

    /* Table: The Ledger Style */
    .table-container {
        overflow-x: auto;
        width: 100%;
    }

    .ledger-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .ledger-table thead {
        background-color: #F3F4F6;
        border-bottom: 2px solid #E5E7EB;
    }

    .ledger-table th {
        text-align: left;
        padding: 0.875rem 1rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 700;
        color: #6B7280;
        white-space: nowrap;
    }

    /* Column Specifics */
    .ledger-table th.col-center { text-align: center; }
    .ledger-table th.col-month { width: 45px; text-align: center; padding: 0.875rem 0.25rem; }

    .ledger-table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #E5E7EB;
        color: #374151;
        vertical-align: middle;
    }

    .ledger-table tbody tr:hover {
        background-color: #F9FAFB;
    }

    .ledger-table tbody tr:last-child td { border-bottom: none; }

    /* Data Typography */
    .company-name {
        font-weight: 600;
        color: #111827;
        text-decoration: none;
    }
    .company-name:hover { color: #D4AF37; text-decoration: underline; }

    .meta-info {
        font-size: 0.75rem;
        color: #6B7280;
    }

    /* Status Indicators: Clean Iconography over Badges */
    .status-cell { text-align: center; padding: 0.5rem 0; border-left: 1px solid #F3F4F6; }
    
    .status-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .st-ok { color: #059669; } /* Green 600 */
    .st-fail { color: #DC2626; } /* Red 600 */
    .st-wait { color: #D1D5DB; } /* Gray 300 */

    /* Empty State */
    .empty-state {
        padding: 4rem 1rem;
        text-align: center;
        background-color: #F9FAFB;
        color: #6B7280;
    }

    /* Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1.5rem;
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
    }

    .stat-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 4px;
        padding: 1rem;
    }

    .stat-card-link {
        display: block;
        text-decoration: none;
        color: inherit;
        transition: border-color 0.15s, box-shadow 0.15s, transform 0.15s;
    }

    .stat-card-link:hover {
        border-color: #D4AF37;
        box-shadow: 0 1px 0 rgba(0,0,0,0.03);
        transform: translateY(-1px);
    }

    .stat-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #6B7280;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .stat-subtitle {
        font-size: 0.75rem;
        color: #6B7280;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: #E5E7EB;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 0.75rem;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #059669 0%, #10b981 100%);
        transition: width 0.3s ease;
    }

    .stat-percentage {
        font-size: 0.875rem;
        font-weight: 600;
        color: #059669;
        margin-top: 0.5rem;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .filter-ribbon { flex-direction: column; align-items: stretch; gap: 1rem; }
        .filter-input { width: 100%; }
        .filter-actions { display: flex; gap: 0.5rem; }
        .btn { flex: 1; justify-content: center; }
        .card-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
    }
</style>

<div class="content-card">
    <div class="card-header">
        <div class="card-title">
            <h2>MONITORING PELAPORAN</h2>
            <p>Tahun {{ request('tahun', date('Y')) }}</p>
        </div>
        {{-- <a href="#" class="btn btn-export">
            <i class="fas fa-file-excel"></i>
            <span>Unduh Laporan</span>
        </a> --}}
    </div>

    <!-- Statistics Cards -->
    @php
        $totalPerusahaan = isset($companies) ? count($companies) : 0;
        $perusahaanLapor = 0;
        $totalLaporanMasuk = 0;
        $bulanSekarang = date('n'); // 1-12 (Januari = 1, Desember = 12)
        
        if(isset($companies)) {
            foreach($companies as $company) {
                // Cek status laporan di bulan sekarang (index array dimulai dari 0, bulan dari 1)
                $statusBulanIni = $company->laporan[$bulanSekarang - 1] ?? null;
                
                // Jika sudah lapor di bulan ini (status 'ok')
                if($statusBulanIni == 'ok') {
                    $perusahaanLapor++;
                }
                
                // Hitung total laporan masuk (untuk statistik tambahan jika diperlukan)
                foreach($company->laporan as $status) {
                    if($status == 'ok') {
                        $totalLaporanMasuk++;
                    }
                }
            }
        }
        
        $persentase = $totalPerusahaan > 0 ? round(($perusahaanLapor / $totalPerusahaan) * 100, 1) : 0;
        $totalLaporanDiharapkan = $totalPerusahaan * $bulanSekarang;
        
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
    @endphp

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-label">Total Perusahaan</div>
            <div class="stat-value">{{ $totalPerusahaan }}</div>
            <div class="stat-subtitle">Terdaftar di sistem</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Sudah Melapor</div>
            <div class="stat-value">{{ $perusahaanLapor }}</div>
            <div class="stat-subtitle">Laporan bulan {{ $namaBulan[$bulanSekarang] ?? $bulanSekarang }}</div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: {{ $persentase }}%"></div>
            </div>
            <div class="stat-percentage">{{ $persentase }}% dari total</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Belum Melapor</div>
            <div class="stat-value" style="color: #DC2626;">{{ $totalPerusahaan - $perusahaanLapor }}</div>
            <div class="stat-subtitle">Belum lapor bulan {{ $namaBulan[$bulanSekarang] ?? $bulanSekarang }}</div>
        </div>
{{-- 
        <div class="stat-card">
            <div class="stat-label">Total Laporan Masuk</div>
            <div class="stat-value" style="color: #059669;">{{ $totalLaporanMasuk }}</div>
            <div class="stat-subtitle">Dari {{ $totalLaporanDiharapkan }} yang diharapkan (s.d bulan ini)</div>
        </div> --}}
    </div>

    <div class="filter-ribbon">
        <form method="GET" action="{{ route('pelaporan.index') }}" style="display: contents;">
            
            <div class="filter-group">
                <label class="filter-label" for="search">Cari Perusahaan</label>
                <input type="text" id="searchCompany" placeholder="Ketik nama perusahaan..." class="filter-input" style="min-width: 250px;">
            </div>

            <div class="filter-group">
                <label class="filter-label" for="kabupaten">Wilayah Administrasi</label>
                <select name="kabupaten" id="kabupaten" class="filter-input">
                    <option value=""> Seluruh Wilayah</option>
                    @if(isset($kabupatens))
                        @foreach($kabupatens as $kab)
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
                        $startYear = 2026   ; // Adjusted for realism
                    @endphp
                    @for($year = $currentYear; $year >= $startYear; $year--)
                        <option value="{{ $year }}" {{ (request('tahun', $currentYear) == $year) ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label" for="jenis_laporan">Kategori Laporan</label>
                <select name="jenis_laporan" id="jenis_laporan" class="filter-input">
                    <option value="">Semua Kategori</option>
                    @if(isset($jenisLaporans))
                        @foreach($jenisLaporans as $jenis)
                            <option value="{{ $jenis }}" {{ request('jenis_laporan') == $jenis ? 'selected' : '' }}>
                                {{ $jenis }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="filter-actions" style="margin-left: auto; display: flex; gap: 8px;">
                <a href="{{ route('pelaporan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-undo-alt"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <div class="table-container">
        @if(isset($companies) && count($companies) > 0)
        <table class="ledger-table">
            <thead>
                <tr>
                    <th class="col-center" style="width: 60px;">No</th>
                    <th style="min-width: 250px;">Identitas Perusahaan</th>
                    <th style="width: 180px;">Lokasi</th>
                    @foreach(['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGS', 'SEP', 'OKT', 'NOV', 'DES'] as $month)
                        <th class="col-month">{{ $month }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td class="col-center" style="color: #9CA3AF;">{{ $loop->iteration }}</td>
                    <td>
                        <div style="display: flex; flex-direction: column;">
                            <a href="{{ route('industri.laporan', $company->id) }}" class="company-name">
                                {{ $company->nama }}
                            </a>
                            <span class="meta-info">No Izin : {{ $company->nomor_izin ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="meta-info">{{ $company->kabupaten }}</td>
                    
                    @foreach($company->laporan as $status)
                        <td class="status-cell">
                            @if($status == 'ok')
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

<script>
    // AJAX Search untuk perusahaan
    let searchTimeout;
    const searchInput = document.getElementById('searchCompany');
    const tableBody = document.querySelector('.ledger-table tbody');
    const emptyState = document.querySelector('.empty-state');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim().toLowerCase();
            
            // Debounce - tunggu 300ms setelah user berhenti mengetik
            searchTimeout = setTimeout(() => {
                filterCompanies(searchTerm);
            }, 300);
        });
    }
    
    function filterCompanies(searchTerm) {
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
                    row.querySelector('td:first-child').textContent = visibleCount;
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
                emptyState.querySelector('h3').textContent = 'Perusahaan Tidak Ditemukan';
                emptyState.querySelector('p').textContent = `Tidak ada perusahaan dengan nama "${searchTerm}"`;
            }
        } else {
            if (tableBody.parentElement) {
                tableBody.parentElement.style.display = '';
            }
            if (emptyState && visibleCount > 0) {
                emptyState.style.display = 'none';
            }
        }
    }
</script>

@endsection