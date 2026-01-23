@extends('laporan.layouts.layout')

@section('title', 'Rekap Laporan')

@section('page-title', 'Rekapitulasi Laporan')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/laporan/custom.css') }}">

    @php
        $namaBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        // Only use request values; don't default so user must pick and apply filters
$bulan = request('bulan') ?: null;
$tahun = request('tahun') ?: null;
$jenis = request('jenis') ?: null;

// Consider filters applied only when all three values are present
$filtersApplied = $bulan && $tahun && $jenis;

$jenisLaporanLabels = [
    'penerimaan_kayu_bulat' => 'Penerimaan Kayu Bulat',
    'penerimaan_kayu_olahan' => 'Penerimaan Kayu Olahan',
    'mutasi_kayu_bulat' => 'Mutasi Kayu Bulat',
    'mutasi_kayu_olahan' => 'Mutasi Kayu Olahan',
    'penjualan_kayu_olahan' => 'Penjualan Kayu Olahan',
];

$jenisLabel = $jenis ? $jenisLaporanLabels[$jenis] ?? 'Laporan' : 'Laporan';
$periodeLabel = $filtersApplied ? ($namaBulan[$bulan] ?? $bulan) . ' ' . $tahun : '';
    @endphp

    <div class="content-card">
        <div id="toast-container" class="toast-container" aria-live="polite"></div>
        <div class="card-header">
            <div class="card-title">
                <h2>REKAP DATA LAPORAN</h2>
                <p>{{ $jenisLabel }} - Periode: {{ $periodeLabel }}</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                @if ($filtersApplied)
                    <a href="{{ route('laporan.rekap.export', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => $jenis]) }}"
                        class="btn btn-export">
                        <i class="fas fa-file-excel"></i> Ekspor Excel
                    </a>
                @endif
            </div>
        </div>

        <!-- Filter Ribbon -->
        <div class="filter-ribbon">
            <form id="main-filter-form" method="GET" action="{{ route('laporan.rekap') }}" style="display: contents;">

                <div class="filter-group">
                    <label class="filter-label" for="bulan">Bulan</label>
                    <select name="bulan" id="bulan" class="filter-input" style="min-width: 150px;">
                        <option value="" {{ !$bulan ? 'selected' : '' }} disabled>Pilih Bulan</option>
                        @foreach ($namaBulan as $key => $nama)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="tahun">Tahun</label>
                    <select name="tahun" id="tahun" class="filter-input" style="min-width: 120px;">
                        <option value="" {{ !$tahun ? 'selected' : '' }} disabled>Pilih Tahun</option>
                        @php
                            $currentYear = date('Y');
                            // Use earliestYear provided by controller when available, otherwise default to 2020.
                            $configuredStartYear = $earliestYear ?? 2020;

                            // Ensure startYear is not in the future relative to current year.
                            $startYear = $configuredStartYear;
                            if ($startYear > $currentYear) {
                                $startYear = $currentYear;
                            }
                        @endphp
                        @for ($year = $currentYear; $year >= $startYear; $year--)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label" for="jenis">Jenis Laporan</label>
                    <select name="jenis" id="jenis" class="filter-input" style="min-width: 220px;">
                        <option value="" {{ !$jenis ? 'selected' : '' }} disabled>Pilih Jenis Laporan</option>
                        @foreach ($jenisLaporanLabels as $key => $label)
                            <option value="{{ $key }}" {{ $jenis == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-actions" style="margin-left: auto; display: flex; gap: 8px;">
                    <a href="{{ route('laporan.rekap') }}" class="btn btn-secondary">
                        <i class="fas fa-undo-alt"></i> Reset
                    </a>
                    <button type="submit" name="apply" value="1" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>


        {{-- stats chatgpt --}}
        @if ($filtersApplied && isset($items) && $items->count() > 0)
            <div class="stats-summary">

                {{-- CARD UMUM --}}
                <div class="stat-card">
                    <div class="stat-label">Total Data</div>
                    <div class="stat-value">{{ $allItems->count() }}</div>
                    <div class="stat-subtitle">Entri laporan</div>
                </div>

                @switch($jenis)
                    {{-- ===================== --}}
                    {{-- TRANSAKSI KAYU BULAT --}}
                    {{-- ===================== --}}
                    @case('penerimaan_kayu_bulat')
                        <div class="stat-card">
                            <div class="stat-label">Total Volume</div>
                            <div class="stat-value" style="color:#059669;">
                                {{ number_format($allItems->sum('volume'), 2) }}
                            </div>
                            <div class="stat-subtitle">m³</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-label">Total Batang</div>
                            <div class="stat-value" style="color:#D97706;">
                                {{ number_format($allItems->sum('jumlah_batang')) }}
                            </div>
                            <div class="stat-subtitle">Batang</div>
                        </div>
                    @break

                    {{-- ====================== --}}
                    {{-- TRANSAKSI KAYU OLAHAN --}}
                    {{-- ====================== --}}
                    @case('penerimaan_kayu_olahan')
                    @case('penjualan_kayu_olahan')
                        <div class="stat-card">
                            <div class="stat-label">Total Volume</div>
                            <div class="stat-value" style="color:#059669;">
                                {{ number_format($allItems->sum('volume'), 2) }}
                            </div>
                            <div class="stat-subtitle">m³</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-label">Total Keping</div>
                            <div class="stat-value" style="color:#D97706;">
                                {{ number_format($allItems->sum('jumlah_keping')) }}
                            </div>
                            <div class="stat-subtitle">Keping</div>
                        </div>
                    @break

                    {{-- ================= --}}
                    {{-- MUTASI (STOK) --}}
                    {{-- ================= --}}
                    @case('mutasi_kayu_bulat')
                    @case('mutasi_kayu_olahan')
                        <div class="stat-card">
                            <div class="stat-label">Persediaan Awal</div>
                            <div class="stat-value">
                                {{ number_format($allItems->sum('persediaan_awal_volume'), 2) }}
                            </div>
                            <div class="stat-subtitle">m³</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-label">Persediaan Akhir</div>
                            <div class="stat-value">
                                {{ number_format($allItems->sum('persediaan_akhir_volume'), 2) }}
                            </div>
                            <div class="stat-subtitle">m³</div>
                        </div>
                    @break
                @endswitch

                {{-- CARD UMUM --}}
                <div class="stat-card">
                    <div class="stat-label">Jumlah Perusahaan</div>
                    <div class="stat-value" style="color:#2563EB;">
                        {{ $allItems->groupBy(fn($i) => optional($i->laporan)->industri_id)->count() }}
                    </div>
                    <div class="stat-subtitle">Yang melapor</div>
                </div>

            </div>
        @endif


        <!-- Data Table -->
        <div class="table-container">
            @if ($filtersApplied)
                @if (isset($items) && $items->count() > 0)
                    {{-- Pagination Info --}}
                    @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div style="padding: 1rem 1.5rem; background: #F9FAFB; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 0.875rem; color: #6B7280;">
                                Menampilkan <strong>{{ $items->firstItem() }}</strong> - <strong>{{ $items->lastItem() }}</strong> 
                                dari <strong>{{ $items->total() }}</strong> data
                            </div>
                            <div style="font-size: 0.875rem; color: #6B7280;">
                                <strong>{{ $items->groupBy(fn($i) => optional($i->laporan)->industri_id)->count() }}</strong> perusahaan pada halaman ini
                            </div>
                        </div>
                    @endif

                    <!-- Filter Detail (Jenis Kayu, Asal Kayu, dll) -->
                    <div class="filter-ribbon mb-4">
                        <form method="GET" action="{{ route('laporan.rekap') }}" style="display: contents;">
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            <input type="hidden" name="jenis" value="{{ $jenis }}">

                            @switch($jenis)
                                @case('penerimaan_kayu_bulat')
                                    <div class="filter-group">
                                        <label class="filter-label" for="jenis_kayu">Jenis Kayu</label>
                                        <select name="jenis_kayu" id="jenis_kayu" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['jenis_kayu'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('jenis_kayu') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label" for="asal_kayu">Asal Kayu</label>
                                        <select name="asal_kayu" id="asal_kayu" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['asal_kayu'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('asal_kayu') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @break

                                @case('penerimaan_kayu_olahan')
                                    <div class="filter-group">
                                        <label class="filter-label" for="jenis_olahan">Jenis Olahan</label>
                                        <select name="jenis_olahan" id="jenis_olahan" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['jenis_olahan'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label" for="asal_kayu">Asal Kayu</label>
                                        <select name="asal_kayu" id="asal_kayu" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['asal_kayu'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('asal_kayu') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @break

                                @case('mutasi_kayu_bulat')
                                    <div class="filter-group">
                                        <label class="filter-label" for="jenis_kayu">Jenis Kayu</label>
                                        <select name="jenis_kayu" id="jenis_kayu" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['jenis_kayu'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('jenis_kayu') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @break

                                @case('mutasi_kayu_olahan')
                                    <div class="filter-group">
                                        <label class="filter-label" for="jenis_olahan">Jenis Olahan</label>
                                        <select name="jenis_olahan" id="jenis_olahan" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['jenis_olahan'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @break

                                @case('penjualan_kayu_olahan')
                                    <div class="filter-group">
                                        <label class="filter-label" for="tujuan_kirim">Tujuan Kirim</label>
                                        <select name="tujuan_kirim" id="tujuan_kirim" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['tujuan_kirim'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('tujuan_kirim') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label" for="jenis_olahan">Jenis Olahan</label>
                                        <select name="jenis_olahan" id="jenis_olahan" class="filter-input">
                                            <option value="">Semua</option>
                                            @foreach ($filterOptions['jenis_olahan'] ?? [] as $item)
                                                <option value="{{ $item }}"
                                                    {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="filter-group">
                                        <label class="filter-label" for="ekspor_impor">Ekspor/Lokal</label>
                                        <select name="ekspor_impor" id="ekspor_impor" class="filter-input"
                                            style="min-width: 120px;">
                                            <option value="">Semua</option>
                                            <option value="ekspor" {{ request('ekspor_impor') == 'ekspor' ? 'selected' : '' }}>
                                                Ekspor</option>
                                            <option value="lokal" {{ request('ekspor_impor') == 'lokal' ? 'selected' : '' }}>
                                                Lokal</option>
                                        </select>
                                    </div>
                                @break
                            @endswitch

                            <div class="filter-actions"
                                style="margin-left: auto; display: flex; gap: 8px; align-items: flex-end;">
                                <a href="{{ route('laporan.rekap', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => $jenis]) }}"
                                    class="btn btn-secondary">
                                    <i class="fas fa-undo-alt"></i> Reset
                                </a>
                                <button type="submit" name="apply" value="1" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>
                    <table class="ledger-table">
                        @switch($jenis)
                            @case('penerimaan_kayu_bulat')
                                <thead>
                                    <tr>
                                        <th class="col-center" style="width: 60px;">No</th>
                                        <th style="min-width: 200px;">Perusahaan</th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'nomor_dokumen', 'label' => 'Nomor Dokumen'])
                                        </th>
                                        <th style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'tanggal', 'label' => 'Tanggal'])
                                        </th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'asal_kayu', 'label' => 'Asal Kayu'])
                                        </th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jenis_kayu', 'label' => 'Jenis Kayu'])
                                        </th>
                                        <th class="col-right" style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jumlah_batang', 'label' => 'Jumlah Batang'])
                                        </th>
                                        <th class="col-right" style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'volume', 'label' => 'Volume (m³)'])
                                        </th>
                                        <th style="min-width: 150px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr>
                                            <td class="col-center" style="color: #9CA3AF;">{{ $items->firstItem() + $index }}</td>
                                            <td>
                                                <div class="company-name">{{ $item->laporan->industri->nama ?? '-' }}</div>
                                                @php
                                                    $indType = optional($item->laporan->industri)->type ?? '';
                                                    $indTypeLabel = match($indType) {
                                                        'primer' => 'Industri Primer',
                                                        'sekunder' => 'Industri Sekunder',
                                                        'tpt_kb' => 'TPT-KB',
                                                        'end_user' => 'End User / Perajin',
                                                        default => $indType,
                                                    };
                                                @endphp
                                                <div class="meta-info">{{ $indTypeLabel }}</div>
                                            </td>
                                            <td>{{ $item->nomor_dokumen }}</td>
                                            <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                            <td>{{ $item->asal_kayu }}</td>
                                            <td>{{ $item->jenis_kayu }}</td>
                                            <td class="col-right">{{ number_format($item->jumlah_batang) }}</td>
                                            <td class="col-right">{{ number_format($item->volume, 2) }}</td>
                                            <td class="meta-info">{{ $item->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td colspan="6" class="col-right" style="padding-right: 2rem; font-weight: 700;">TOTAL KESELURUHAN:</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('jumlah_batang')) }}</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('volume'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            @break

                            @case('penerimaan_kayu_olahan')
                                <thead>
                                    <tr>
                                        <th class="col-center" style="width: 60px;">No</th>
                                        <th style="min-width: 200px;">Perusahaan</th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'nomor_dokumen', 'label' => 'Nomor Dokumen'])
                                        </th>
                                        <th style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'tanggal', 'label' => 'Tanggal'])
                                        </th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'asal_kayu', 'label' => 'Asal Kayu'])
                                        </th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jenis_olahan', 'label' => 'Jenis Olahan'])
                                        </th>
                                        <th class="col-right" style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jumlah_keping', 'label' => 'Jumlah Keping'])
                                        </th>
                                        <th class="col-right" style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'volume', 'label' => 'Volume (m³)'])
                                        </th>
                                        <th style="min-width: 150px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr>
                                            <td class="col-center" style="color: #9CA3AF;">{{ $items->firstItem() + $index }}</td>
                                            <td>
                                                <div class="company-name">{{ $item->laporan->industri->nama ?? '-' }}</div>
                                                <div class="meta-info">{{ $item->laporan->industri->nomor_izin ?? '' }}</div>
                                            </td>
                                            <td>{{ $item->nomor_dokumen }}</td>
                                            <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                            <td>{{ $item->asal_kayu }}</td>
                                            <td>{{ $item->jenis_olahan }}</td>
                                            <td class="col-right">{{ number_format($item->jumlah_keping) }}</td>
                                            <td class="col-right">{{ number_format($item->volume, 2) }}</td>
                                            <td class="meta-info">{{ $item->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td colspan="6" class="col-right" style="padding-right: 2rem; font-weight: 700;">TOTAL KESELURUHAN:</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('jumlah_keping')) }}</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('volume'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            @break

                            @case('mutasi_kayu_bulat')
                                <thead>
                                    <tr>
                                        <th class="col-center" style="width: 60px;">No</th>
                                        <th style="min-width: 200px;">Perusahaan</th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jenis_kayu', 'label' => 'Jenis Kayu'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'persediaan_awal_volume', 'label' => 'Persediaan Awal (m³)'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'penambahan_volume', 'label' => 'Penambahan (m³)'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'penggunaan_pengurangan_volume', 'label' => 'Penggunaan (m³)'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'persediaan_akhir_volume', 'label' => 'Persediaan Akhir (m³)'])
                                        </th>
                                        <th style="min-width: 150px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr>
                                            <td class="col-center" style="color: #9CA3AF;">{{ $items->firstItem() + $index }}</td>
                                            <td>
                                                <div class="company-name">{{ $item->laporan->industri->nama ?? '-' }}</div>
                                                <div class="meta-info">{{ $item->laporan->industri->nomor_izin ?? '' }}</div>
                                            </td>
                                            <td>{{ $item->jenis_kayu }}</td>
                                            <td class="col-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                            <td class="col-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                            <td class="col-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}
                                            </td>
                                            <td class="col-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                            <td class="meta-info">{{ $item->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td colspan="3" class="col-right" style="padding-right: 2rem; font-weight: 700;">TOTAL KESELURUHAN:</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('persediaan_awal_volume'), 2) }}</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('penambahan_volume'), 2) }}</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('penggunaan_pengurangan_volume'), 2) }}
                                        </td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('persediaan_akhir_volume'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            @break

                            @case('mutasi_kayu_olahan')
                                <thead>
                                    <tr>
                                        <th class="col-center" style="width: 60px;">No</th>
                                        <th style="min-width: 200px;">Perusahaan</th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jenis_olahan', 'label' => 'Jenis Olahan'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'persediaan_awal_volume', 'label' => 'Persediaan Awal (m³)'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'penambahan_volume', 'label' => 'Penambahan (m³)'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'penggunaan_pengurangan_volume', 'label' => 'Penggunaan (m³)'])
                                        </th>
                                        <th class="col-right" style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'persediaan_akhir_volume', 'label' => 'Persediaan Akhir (m³)'])
                                        </th>
                                        <th style="min-width: 150px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr>
                                            <td class="col-center" style="color: #9CA3AF;">{{ $items->firstItem() + $index }}</td>
                                            <td>
                                                <div class="company-name">{{ $item->laporan->industri->nama ?? '-' }}</div>
                                                <div class="meta-info">{{ $item->laporan->industri->nomor_izin ?? '' }}</div>
                                            </td>
                                            <td>{{ $item->jenis_olahan }}</td>
                                            <td class="col-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                            <td class="col-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                            <td class="col-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}
                                            </td>
                                            <td class="col-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                            <td class="meta-info">{{ $item->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td colspan="3" class="col-right" style="padding-right: 2rem; font-weight: 700;">TOTAL KESELURUHAN:</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('persediaan_awal_volume'), 2) }}</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('penambahan_volume'), 2) }}</td>
                                        <td class="col-right" style="font-weight: 700;">
                                            {{ number_format($allItems->sum('penggunaan_pengurangan_volume'), 2) }}
                                        </td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('persediaan_akhir_volume'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            @break

                            @case('penjualan_kayu_olahan')
                                <thead>
                                    <tr>
                                        <th class="col-center" style="width: 60px;">No</th>
                                        <th style="min-width: 200px;">Perusahaan</th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'nomor_dokumen', 'label' => 'Nomor Dokumen'])
                                        </th>
                                        <th style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'tanggal', 'label' => 'Tanggal'])
                                        </th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'tujuan_kirim', 'label' => 'Tujuan Kirim'])
                                        </th>
                                        <th style="width: 150px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jenis_olahan', 'label' => 'Jenis Olahan'])
                                        </th>
                                        <th class="col-right" style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'jumlah_keping', 'label' => 'Jumlah Keping'])
                                        </th>
                                        <th class="col-right" style="width: 120px;">
                                            @include('laporan.partials.sortable-header', ['column' => 'volume', 'label' => 'Volume (m³)'])
                                        </th>
                                        <th style="min-width: 150px;">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $index => $item)
                                        <tr>
                                            <td class="col-center" style="color: #9CA3AF;">{{ $items->firstItem() + $index }}</td>
                                            <td>
                                                <div class="company-name">{{ $item->laporan->industri->nama ?? '-' }}</div>
                                                <div class="meta-info">{{ $item->laporan->industri->nomor_izin ?? '' }}</div>
                                            </td>
                                            <td>{{ $item->nomor_dokumen }}</td>
                                            <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                            <td>{{ $item->tujuan_kirim }}</td>
                                            <td>{{ $item->jenis_olahan }}</td>
                                            <td class="col-right">{{ number_format($item->jumlah_keping) }}</td>
                                            <td class="col-right">{{ number_format($item->volume, 2) }}</td>
                                            <td class="meta-info">{{ $item->keterangan ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="total-row">
                                        <td colspan="6" class="col-right" style="padding-right: 2rem; font-weight: 700;">TOTAL KESELURUHAN:</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('jumlah_keping')) }}</td>
                                        <td class="col-right" style="font-weight: 700;">{{ number_format($allItems->sum('volume'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            @break
                        @endswitch
                    </table>

                    {{-- Pagination Links --}}
                    @if($items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div style="padding: 1.5rem; border-top: 1px solid #E5E7EB;">
                            {{ $items->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div style="margin-bottom: 1rem; color: #D1D5DB;">
                            <i class="far fa-folder-open fa-3x"></i>
                        </div>
                        <h3 style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Data Tidak Ditemukan</h3>
                        <p style="font-size: 0.875rem;">Tidak ada data laporan untuk periode dan jenis yang dipilih.</p>
                        <p style="font-size: 0.875rem; margin-top: 0.5rem;">Silakan pilih filter lain atau hubungi
                            administrator.</p>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div style="margin-bottom: 1rem; color: #D1D5DB;">
                        <i class="fas fa-filter fa-3x"></i>
                    </div>
                    <h3 style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Pilih Filter</h3>
                    <p style="font-size: 0.875rem;">Silakan pilih <strong>Bulan</strong>, <strong>Tahun</strong>, dan
                        <strong>Jenis Laporan</strong>, lalu klik <strong>Terapkan Filter</strong> untuk menampilkan data.
                    </p>
                </div>
            @endif
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('main-filter-form');
    const toastContainer = document.getElementById('toast-container');

    function createToast(title, message, variant = 'warning') {
        if (!toastContainer) return;
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + (variant === 'warning' ? 'warning' : variant);
        toast.setAttribute('role','alert');
        toast.innerHTML = `
            <div class="toast-icon ${variant === 'warning' ? 'warning' : ''}">!</div>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
            <button class="toast-close" aria-label="Close">&times;</button>
        `;
        toastContainer.appendChild(toast);

        const closeBtn = toast.querySelector('.toast-close');
        function removeToast() {
            toast.classList.add('toast-exit');
            toast.addEventListener('animationend', () => toast.remove(), { once: true });
        }
        closeBtn.addEventListener('click', removeToast);
        setTimeout(removeToast, 4000);
    }

    if (form) {
        form.addEventListener('submit', function(e){
            const bulan = form.querySelector('[name="bulan"]')?.value;
            const tahun = form.querySelector('[name="tahun"]')?.value;
            const jenis = form.querySelector('[name="jenis"]')?.value;
            if (!bulan || !tahun || !jenis) {
                e.preventDefault();
                createToast('Perhatian', 'Silakan pilih Bulan, Tahun, dan Jenis Laporan sebelum menerapkan filter.', 'warning');
                const firstMissing = !bulan ? form.querySelector('[name="bulan"]') : (!tahun ? form.querySelector('[name="tahun"]') : form.querySelector('[name="jenis"]'));
                if (firstMissing) firstMissing.focus();
            }
        });
    }
});
</script>

@endsection
