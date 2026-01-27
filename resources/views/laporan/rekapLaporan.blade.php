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

        $kategoriLabels = [
            'produksi_kayu_bulat' => 'Produksi Kayu Bulat',
            'produksi_kayu_olahan' => 'Produksi Kayu Olahan',
            'penjualan' => 'Penjualan',
            'pemenuhan_bahan_baku' => 'Pemenuhan Bahan Baku',
        ];

        $kategoriIcons = [
            'produksi_kayu_bulat' => 'fa-tree',
            'produksi_kayu_olahan' => 'fa-industry',
            'penjualan' => 'fa-shopping-cart',
            'pemenuhan_bahan_baku' => 'fa-boxes',
        ];

        // Ensure kategori is set with a default value
        $kategori = $kategori ?? 'produksi_kayu_bulat';
        $kategoriLabel = $kategoriLabels[$kategori] ?? 'Statistik';
        $periodeLabel = $tahun ? $tahun : 'Pilih Tahun';

        // GroupBy labels and default
        $groupBy = $groupBy ?? 'asal_kayu';
        $groupByLabels = [
            'asal_kayu' => 'Asal Kayu',
            'jenis_kayu' => 'Jenis Kayu',
            'jenis_olahan' => 'Jenis Olahan',
            'tujuan_kirim' => 'Tujuan Kirim',
        ];
        $groupByLabel = $groupByLabels[$groupBy] ?? 'Asal Kayu';

        // EksporLokal default
        $eksporLokal = $eksporLokal ?? 'semua';
    @endphp

    <div class="page-container px-6 py-8">
        <div id="toast-container" class="toast-container" aria-live="polite"></div>

        <div class="page-header">
            <div class="page-title">
                <h2>REKAP DATA LAPORAN</h2>
                <p>{{ $kategoriLabel }} • Periode: {{ $periodeLabel }}</p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <!-- Filter Ribbon -->
        <div class="filter-ribbon no-print">
            <form id="main-filter-form" method="GET" action="{{ route('laporan.rekap') }}" style="display: contents;">
                <input type="hidden" name="kategori" value="{{ $kategori }}">

                <!-- Bulan filter removed as per new requirement -->


                <div class="filter-group">
                    <label class="filter-label" for="tahun">Tahun</label>
                    <select name="tahun" id="tahun" class="filter-input" style="min-width: 120px;">
                        <option value="" {{ !$tahun ? 'selected' : '' }} disabled>Pilih Tahun</option>
                        @php
                            $currentYear = date('Y');
                            $configuredStartYear = $earliestYear ?? 2020;
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

        <!-- Category Tabs -->
        <div class="category-tabs">
            @foreach ($kategoriLabels as $key => $label)
                <a href="{{ route('laporan.rekap', ['kategori' => $key, 'tahun' => $tahun]) }}"
                    class="tab-item {{ $kategori == $key ? 'active' : '' }}">
                    <i class="fas {{ $kategoriIcons[$key] }}"></i>
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Toggle Buttons for Produksi Kayu Bulat -->
        @if ($kategori === 'produksi_kayu_bulat' && $tahun)
            <div class="filter-ribbon no-print" style="margin-top: 1rem;">
                <div class="filter-group">
                    <label class="filter-label">Tampilkan Berdasarkan</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'groupBy' => 'asal_kayu']) }}"
                            class="btn {{ $groupBy === 'asal_kayu' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-map-marker-alt"></i> Asal Kayu
                        </a>
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'groupBy' => 'jenis_kayu']) }}"
                            class="btn {{ $groupBy === 'jenis_kayu' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-tree"></i> Jenis Kayu
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Toggle Buttons for Produksi Kayu Olahan -->
        @if ($kategori === 'produksi_kayu_olahan' && $tahun)
            <div class="filter-ribbon no-print" style="margin-top: 1rem;">
                <div class="filter-group">
                    <label class="filter-label">Tampilkan Berdasarkan</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'groupBy' => 'asal_kayu']) }}"
                            class="btn {{ $groupBy === 'asal_kayu' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-map-marker-alt"></i> Asal Kayu
                        </a>
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'groupBy' => 'jenis_olahan']) }}"
                            class="btn {{ $groupBy === 'jenis_olahan' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-industry"></i> Jenis Olahan
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Dual-Level Filters for Penjualan -->
        @if ($kategori === 'penjualan' && $tahun)
            <!-- Level 1: Ekspor/Lokal Filter -->
            <div class="filter-ribbon no-print" style="margin-top: 1rem;">
                <div class="filter-group">
                    <label class="filter-label">Filter Penjualan</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'eksporLokal' => 'semua', 'groupBy' => $groupBy]) }}"
                            class="btn {{ $eksporLokal === 'semua' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-list"></i> Semua
                        </a>
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'eksporLokal' => 'ekspor', 'groupBy' => $groupBy]) }}"
                            class="btn {{ $eksporLokal === 'ekspor' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-plane-departure"></i> Ekspor
                        </a>
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'eksporLokal' => 'lokal', 'groupBy' => $groupBy]) }}"
                            class="btn {{ $eksporLokal === 'lokal' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-home"></i> Lokal
                        </a>
                    </div>
                </div>
            </div>

            <!-- Level 2: GroupBy Filter -->
            <div class="filter-ribbon no-print" style="margin-top: 1rem;">
                <div class="filter-group">
                    <label class="filter-label">Tampilkan Berdasarkan</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'eksporLokal' => $eksporLokal, 'groupBy' => 'tujuan_kirim']) }}"
                            class="btn {{ $groupBy === 'tujuan_kirim' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-shipping-fast"></i> Tujuan Kirim
                        </a>
                        <a href="{{ route('laporan.rekap', ['kategori' => $kategori, 'tahun' => $tahun, 'eksporLokal' => $eksporLokal, 'groupBy' => 'jenis_olahan']) }}"
                            class="btn {{ $groupBy === 'jenis_olahan' ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="fas fa-industry"></i> Jenis Olahan
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Content Area -->
        <div class="table-container">
            @if ($tahun)
                @php
                    $hasData = isset($rekapData['data']) && !empty($rekapData['data']);
                @endphp

                @if ($hasData)
                    <!-- Data Table -->
                    <div class="table-container">
                        <table class="ledger-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">
                                        <i class="fas {{ $groupBy === 'asal_kayu' ? 'fa-map-marker-alt' : ($groupBy === 'jenis_kayu' ? 'fa-tree' : ($groupBy === 'jenis_olahan' ? 'fa-industry' : 'fa-shipping-fast')) }}"
                                            style="margin-right: 0.5rem;"></i>{{ $groupByLabel }}
                                    </th>
                                    @foreach ($namaBulan as $bulanNum => $bulanNama)
                                        <th class="col-right">{{ $bulanNama }}</th>
                                    @endforeach
                                    <th class="col-right">
                                        <i class="fas fa-calculator" style="margin-right: 0.5rem;"></i>Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowIndex = 0; @endphp
                                @foreach ($rekapData['data'] as $groupKey => $groupData)
                                    <tr>
                                        <td class="font-medium text-gray-900">
                                            {{ $groupData['nama'] ?? 'N/A' }}
                                        </td>
                                        @foreach (range(1, 12) as $bulan)
                                            <td class="col-right">
                                                {{ number_format($groupData['bulan'][$bulan] ?? 0, 2, ',', '.') }}
                                            </td>
                                        @endforeach
                                        <td class="col-right font-medium">
                                            {{ number_format($groupData['total'] ?? 0, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                    @php $rowIndex++; @endphp
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="total-row">
                                    <td class="col-right pr-4">
                                        <i class="fas fa-chart-line" style="margin-right: 0.5rem;"></i>TOTAL KESELURUHAN
                                    </td>
                                    @foreach (range(1, 12) as $bulan)
                                        <td class="col-right">
                                            {{ number_format($rekapData['grand_total'][$bulan] ?? 0, 2, ',', '.') }}
                                        </td>
                                    @endforeach
                                    <td class="col-right">
                                        {{ number_format($rekapData['grand_total']['total'] ?? 0, 2, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Summary Info -->
                    <div
                        style="margin-top: 1.5rem; padding: 1rem 1.25rem; background: #F9FAFB; border-radius: 4px; border-left: 3px solid #0F2F24;">
                        <p style="font-size: 0.875rem; color: #4B5563; margin: 0; line-height: 1.6;">
                            <i class="fas fa-info-circle" style="color: #0F2F24; margin-right: 0.5rem;"></i>
                            <strong style="color: #111827;">Catatan:</strong> Data ditampilkan dalam satuan <strong>volume
                                (m³)</strong>.
                            Total keseluruhan dihitung dari semua {{ $groupByLabel }} yang melaporkan data pada tahun
                            <strong>{{ $tahun }}</strong>.
                        </p>
                    </div>
                @else
                    <!-- Empty State - No Data -->
                    <div class="empty-state">
                        <div style="margin-bottom: 1rem; color: #D1D5DB;">
                            <i class="fas fa-inbox fa-3x"></i>
                        </div>
                        <h3 style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Tidak Ada Data</h3>
                        <p style="font-size: 0.875rem; color: #6B7280;">
                            Tidak ada data <strong>{{ $kategoriLabel }}</strong> untuk tahun <strong>{{ $tahun }}</strong>.
                        </p>
                        <p style="font-size: 0.75rem; color: #9CA3AF; margin-top: 0.5rem;">
                            Pastikan industri sudah mengupload laporan untuk periode ini.
                        </p>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div style="margin-bottom: 1rem; color: #D1D5DB;">
                        <i class="fas fa-filter fa-3x"></i>
                    </div>
                    <h3 style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Pilih Filter</h3>
                    <p style="font-size: 0.875rem;">Silakan pilih <strong>Tahun</strong>,
                        lalu klik <strong>Terapkan Filter</strong> untuk menampilkan data statistik.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('main-filter-form');
            const toastContainer = document.getElementById('toast-container');

            function createToast(title, message, variant = 'warning') {
                if (!toastContainer) return;
                const toast = document.createElement('div');
                toast.className = 'toast toast-' + (variant === 'warning' ? 'warning' : variant);
                toast.setAttribute('role', 'alert');
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
                form.addEventListener('submit', function (e) {
                    const tahun = form.querySelector('[name="tahun"]')?.value;
                    if (!tahun) {
                        e.preventDefault();
                        createToast('Perhatian', 'Silakan pilih Tahun sebelum menerapkan filter.', 'warning');
                        const firstMissing = form.querySelector('[name="tahun"]');
                        if (firstMissing) firstMissing.focus();
                    }
                });
            }
        });
    </script>

@endsection