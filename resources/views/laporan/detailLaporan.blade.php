@extends('laporan.layouts.layout')

@section('title', 'Detail Laporan')

@section('page-title', 'Detail Laporan')

@section('content')

    {{-- STYLING DARI REKAP LAPORAN (DIADAPTASI) --}}
    <style>
        /* Container: Flat / Tanpa Card Style */
        .page-container {
            background: transparent;
            padding: 0;
        }

        /* Header: Clean & Structured */
        .page-header {
            padding: 0 0 1.5rem 0;
            border-bottom: 2px solid #E5E7EB;
            margin-bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title h2 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #111827;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .page-title p {
            font-size: 0.875rem;
            color: #6B7280;
            margin-top: 4px;
        }

        /* Filter Ribbon */
        .filter-ribbon {
            background-color: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 0; /* Padding vertikal saja karena layout flat */
            margin-bottom: 1.5rem;
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
            border-radius: 4px;
            font-size: 0.875rem;
            color: #111827;
            background-color: white;
            min-width: 180px;
            transition: border-color 0.15s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #0F2F24;
            box-shadow: 0 0 0 1px #0F2F24;
        }

        /* Buttons */
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
            background-color: #0F2F24;
            color: white;
        }

        .btn-primary:hover {
            background-color: #183F32;
        }

        .btn-secondary {
            background-color: white;
            border-color: #D1D5DB;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #F3F4F6;
            border-color: #9CA3AF;
        }

        .btn-print {
            background-color: #ECFDF5;
            border-color: #059669;
            color: #047857;
        }

        .btn-print:hover {
            background-color: #D1FAE5;
        }

        /* Table Style */
        .table-container {
            overflow-x: auto;
            width: 100%;
            background: white; /* Tabel tetap butuh background agar terbaca */
            border: 1px solid #E5E7EB;
            border-radius: 4px;
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

        .ledger-table td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid #E5E7EB;
            color: #374151;
            vertical-align: middle;
        }

        .ledger-table tbody tr:hover {
            background-color: #F9FAFB;
        }

        .ledger-table tbody tr.total-row {
            background-color: #F3F4F6;
            font-weight: 700;
            border-top: 2px solid #0F2F24;
        }

        /* Helpers */
        .col-center { text-align: center; }
        .col-right { text-align: right; }
        
        .empty-state {
            padding: 4rem 1rem;
            text-align: center;
            background-color: #F9FAFB;
            color: #6B7280;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
        }

        /* Print Logic */
        @media print {
            .no-print, .btn, .filter-ribbon { display: none !important; }
            .page-container { background: white; }
            .table-container { border: none; }
        }
    </style>

    @php
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $periodeLabel = ($namaBulan[$bulan] ?? $bulan) . ' ' . $tahun;
        
        // Judul header yang lebih rapi
        $judulHeader = ucwords(str_replace('_', ' ', $jenis));
        // Cari nama perusahaan bila ada industri_id atau dari item pertama
        $companyName = null;
        if(isset($industri_id)) {
            $ind = \App\Models\Industri::find($industri_id);
            $companyName = $ind ? $ind->nama : null;
        }
        if(!$companyName && isset($items) && $items->count()) {
            $companyName = $items->first()->laporan->industri->nama ?? null;
        }
    @endphp

    <div class="page-container px-6 py-8">
        
        <div class="page-header">
            <div class="page-title">
                <h2>DETAIL LAPORAN</h2>
                <p>
                    @if($companyName)
                        <strong>{{ $companyName }}</strong> •
                    @endif
                    {{ $judulHeader }} • Periode: {{ $periodeLabel }} • Total data: {{ $items->count() }}
                </p>
            </div>

            <div style="display: flex; gap: 8px;">
                @php $industriId = $industri_id ?? null; @endphp
                @if($industriId)
                    <a href="{{ route('laporan.industri', ['industri' => $industriId]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Perusahaan
                    </a>
                @else
                    <a href="{{ route('laporan.rekap', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => $jenis]) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Rekap
                    </a>
                @endif
                
                {{-- <button type="button" onclick="window.print()" class="btn btn-print">
                    <i class="fas fa-print"></i> Cetak
                </button> --}}
            </div>
        </div>

        <div class="filter-ribbon no-print">
            <form method="GET" action="{{ route('laporan.detail', ['industri' => $industri_id ?? request('industri'), 'id' => $laporan_id ?? request('id')]) }}" style="display: contents;">
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                @if(isset($industri_id))
                    <input type="hidden" name="industri_id" value="{{ $industri_id }}">
                @endif

                {{-- Filter Logic based on Switch --}}
                @switch($jenis)
                    @case('penerimaan_kayu_bulat')
                    @case('mutasi_kayu_bulat')
                        <div class="filter-group">
                            <label class="filter-label" for="jenis_kayu">Jenis Kayu</label>
                            <select name="jenis_kayu" id="jenis_kayu" class="filter-input">
                                <option value="">Semua</option>
                                @foreach($filterOptions['jenis_kayu'] ?? [] as $item)
                                    <option value="{{ $item }}" {{ request('jenis_kayu') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($jenis == 'penerimaan_kayu_bulat')
                            <div class="filter-group">
                                <label class="filter-label" for="asal_kayu">Asal Kayu</label>
                                <select name="asal_kayu" id="asal_kayu" class="filter-input">
                                    <option value="">Semua</option>
                                    @foreach($filterOptions['asal_kayu'] ?? [] as $item)
                                        <option value="{{ $item }}" {{ request('asal_kayu') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @break

                    @case('penerimaan_kayu_olahan')
                    @case('mutasi_kayu_olahan')
                        <div class="filter-group">
                            <label class="filter-label" for="jenis_olahan">Jenis Olahan</label>
                            <select name="jenis_olahan" id="jenis_olahan" class="filter-input">
                                <option value="">Semua</option>
                                @foreach($filterOptions['jenis_olahan'] ?? [] as $item)
                                    <option value="{{ $item }}" {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($jenis == 'penerimaan_kayu_olahan')
                            <div class="filter-group">
                                <label class="filter-label" for="asal_kayu">Asal Kayu</label>
                                <select name="asal_kayu" id="asal_kayu" class="filter-input">
                                    <option value="">Semua</option>
                                    @foreach($filterOptions['asal_kayu'] ?? [] as $item)
                                        <option value="{{ $item }}" {{ request('asal_kayu') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        @break

                    @case('penjualan_kayu_olahan')
                        <div class="filter-group">
                            <label class="filter-label" for="tujuan_kirim">Tujuan Kirim</label>
                            <select name="tujuan_kirim" id="tujuan_kirim" class="filter-input">
                                <option value="">Semua</option>
                                @foreach($filterOptions['tujuan_kirim'] ?? [] as $item)
                                    <option value="{{ $item }}" {{ request('tujuan_kirim') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label" for="jenis_olahan">Jenis Olahan</label>
                            <select name="jenis_olahan" id="jenis_olahan" class="filter-input">
                                <option value="">Semua</option>
                                @foreach($filterOptions['jenis_olahan'] ?? [] as $item)
                                    <option value="{{ $item }}" {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-group">
                            <label class="filter-label" for="ekspor_impor">Ekspor/Lokal</label>
                            <select name="ekspor_impor" id="ekspor_impor" class="filter-input" style="min-width: 120px;">
                                <option value="">Semua</option>
                                <option value="ekspor" {{ request('ekspor_impor') == 'ekspor' ? 'selected' : '' }}>Ekspor</option>
                                <option value="lokal" {{ request('ekspor_impor') == 'lokal' ? 'selected' : '' }}>Lokal</option>
                            </select>
                        </div>
                        @break
                @endswitch

                <div style="margin-left: auto; display: flex; gap: 8px;">
                    <a href="{{ route('laporan.detail', ['industri' => $industri_id ?? request('industri'), 'id' => $laporan_id ?? request('id')]) }}" class="btn btn-secondary">
                        <i class="fas fa-undo-alt"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Terapkan
                    </button>
                </div>
            </form>
        </div>

        @if($items->count() > 0)
            <div class="table-container">
                <table class="ledger-table">
                    @switch($jenis)
                        @case('penerimaan_kayu_bulat')
                            <thead>
                                <tr>
                                    <th class="col-center" style="width: 50px;">No</th>
                                    <th>Perusahaan</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Tanggal</th>
                                    <th>Asal Kayu</th>
                                    <th>Jenis Kayu</th>
                                    <th class="col-right">Jumlah Batang</th>
                                    <th class="col-right">Volume (m³)</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="col-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="font-medium text-gray-900">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                        <td>{{ $item->nomor_dokumen }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                        <td>{{ $item->asal_kayu }}</td>
                                        <td>{{ $item->jenis_kayu }}</td>
                                        <td class="col-right">{{ number_format($item->jumlah_batang) }}</td>
                                        <td class="col-right">{{ number_format($item->volume, 2) }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="6" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($items->sum('jumlah_batang')) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('volume'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            @break

                        @case('penerimaan_kayu_olahan')
                            <thead>
                                <tr>
                                    <th class="col-center" style="width: 50px;">No</th>
                                    <th>Perusahaan</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Tanggal</th>
                                    <th>Asal Kayu</th>
                                    <th>Jenis Olahan</th>
                                    <th class="col-right">Jumlah Keping</th>
                                    <th class="col-right">Volume (m³)</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="col-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="font-medium text-gray-900">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                        <td>{{ $item->nomor_dokumen }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                        <td>{{ $item->asal_kayu }}</td>
                                        <td>{{ $item->jenis_olahan }}</td>
                                        <td class="col-right">{{ number_format($item->jumlah_keping) }}</td>
                                        <td class="col-right">{{ number_format($item->volume, 2) }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="6" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($items->sum('jumlah_keping')) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('volume'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            @break

                        @case('mutasi_kayu_bulat')
                            <thead>
                                <tr>
                                    <th class="col-center" style="width: 50px;">No</th>
                                    <th>Perusahaan</th>
                                    <th>Jenis Kayu</th>
                                    <th class="col-right">Persediaan Awal</th>
                                    <th class="col-right">Penambahan</th>
                                    <th class="col-right">Penggunaan</th>
                                    <th class="col-right">Persediaan Akhir</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="col-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="font-medium text-gray-900">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                        <td>{{ $item->jenis_kayu }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="3" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($items->sum('persediaan_awal_volume'), 2) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('penambahan_volume'), 2) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('penggunaan_pengurangan_volume'), 2) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('persediaan_akhir_volume'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            @break

                        @case('mutasi_kayu_olahan')
                            <thead>
                                <tr>
                                    <th class="col-center" style="width: 50px;">No</th>
                                    <th>Perusahaan</th>
                                    <th>Jenis Olahan</th>
                                    <th class="col-right">Persediaan Awal</th>
                                    <th class="col-right">Penambahan</th>
                                    <th class="col-right">Penggunaan</th>
                                    <th class="col-right">Persediaan Akhir</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="col-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="font-medium text-gray-900">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                        <td>{{ $item->jenis_olahan }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="3" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($items->sum('persediaan_awal_volume'), 2) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('penambahan_volume'), 2) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('penggunaan_pengurangan_volume'), 2) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('persediaan_akhir_volume'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            @break

                        @case('penjualan_kayu_olahan')
                            <thead>
                                <tr>
                                    <th class="col-center" style="width: 50px;">No</th>
                                    <th>Perusahaan</th>
                                    <th>Nomor Dokumen</th>
                                    <th>Tanggal</th>
                                    <th>Tujuan Kirim</th>
                                    <th>Jenis Olahan</th>
                                    <th class="col-right">Jumlah Keping</th>
                                    <th class="col-right">Volume (m³)</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="col-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="font-medium text-gray-900">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                        <td>{{ $item->nomor_dokumen }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                        <td>{{ $item->tujuan_kirim }}</td>
                                        <td>{{ $item->jenis_olahan }}</td>
                                        <td class="col-right">{{ number_format($item->jumlah_keping) }}</td>
                                        <td class="col-right">{{ number_format($item->volume, 2) }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row">
                                    <td colspan="6" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($items->sum('jumlah_keping')) }}</td>
                                    <td class="col-right">{{ number_format($items->sum('volume'), 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            @break
                    @endswitch
                </table>
            </div>
        @else
            <div class="empty-state">
                <div style="margin-bottom: 1rem; color: #D1D5DB;">
                    <i class="far fa-folder-open fa-3x"></i>
                </div>
                <h3 style="font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Data Tidak Ditemukan</h3>
                <p style="font-size: 0.875rem;">Tidak ada data laporan untuk periode dan filter yang dipilih.</p>
            </div>
        @endif
    </div>

@endsection