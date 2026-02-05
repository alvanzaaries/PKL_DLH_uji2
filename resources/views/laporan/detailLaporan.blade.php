@extends('laporan.layouts.layout')

@section('title', 'Detail Laporan')

@section('page-title', 'Detail Laporan')

@section('content')

    {{-- STYLING DARI REKAP LAPORAN (DIADAPTASI) --}}
    <link rel="stylesheet" href="{{ asset('css/laporan/custom.css') }}">

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
        
        // Ambil user yang upload laporan (dari item pertama)
        $uploaderName = null;
        $uploadedAt = null;
        if(isset($items) && $items->count()) {
            $laporanData = $items->first()->laporan;
            if($laporanData) {
                $uploaderName = $laporanData->user->name ?? null;
                $uploadedAt = $laporanData->created_at;
            }
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
                @if($uploaderName)
                <p style="font-size: 0.85rem; color: #6b7280; margin-top: 4px;">
                    <i class="fas fa-user-upload" style="margin-right: 4px;"></i>
                    Diupload oleh: <strong>{{ $uploaderName }}</strong>
                    @if($uploadedAt)
                        • {{ $uploadedAt->translatedFormat('d F Y, H:i') }}
                    @endif
                </p>
                @endif
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
                                <tr class="total-row text-white">
                                    <td colspan="6" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($grandTotal['jumlah_batang'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['volume'] ?? 0, 2) }}</td>
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
                                <tr class="total-row text-white">
                                    <td colspan="6" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($grandTotal['jumlah_keping'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['volume'] ?? 0, 2) }}</td>
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
                                    <th class="col-right" colspan="2">Persediaan Awal</th>
                                    <th class="col-right" colspan="2">Penambahan</th>
                                    <th class="col-right" colspan="2">Penggunaan</th>
                                    <th class="col-right" colspan="2">Persediaan Akhir</th>
                                    <th>Keterangan</th>
                                </tr>
                                <tr style="background: #f9fafb;">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Btg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Btg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Btg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Btg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="col-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="font-medium text-gray-900">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                        <td>{{ $item->jenis_kayu }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_awal_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penambahan_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penggunaan_pengurangan_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_akhir_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row text-white">
                                    <td colspan="3" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_awal_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_awal_volume'] ?? 0, 2) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penambahan_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penambahan_volume'] ?? 0, 2) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penggunaan_pengurangan_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penggunaan_pengurangan_volume'] ?? 0, 2) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_akhir_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_akhir_volume'] ?? 0, 2) }}</td>
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
                                    <th class="col-right" colspan="2">Persediaan Awal</th>
                                    <th class="col-right" colspan="2">Penambahan</th>
                                    <th class="col-right" colspan="2">Penggunaan</th>
                                    <th class="col-right" colspan="2">Persediaan Akhir</th>
                                    <th>Keterangan</th>
                                </tr>
                                <tr style="background: #f9fafb;">
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Kpg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Kpg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Kpg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">Kpg</th>
                                    <th class="col-right" style="font-size: 0.75rem; font-weight: 500;">m³</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td class="col-center text-gray-400">{{ $index + 1 }}</td>
                                        <td class="font-medium text-gray-900">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                        <td>{{ $item->jenis_olahan }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_awal_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penambahan_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->penggunaan_pengurangan_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_akhir_btg) }}</td>
                                        <td class="col-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                        <td>{{ $item->keterangan }}</td>
                                    </tr>
                                @endforeach
                                <tr class="total-row text-white">
                                    <td colspan="3" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_awal_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_awal_volume'] ?? 0, 2) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penambahan_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penambahan_volume'] ?? 0, 2) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penggunaan_pengurangan_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['penggunaan_pengurangan_volume'] ?? 0, 2) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_akhir_btg'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['persediaan_akhir_volume'] ?? 0, 2) }}</td>
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
                                <tr class="total-row text-white">
                                    <td colspan="6" class="col-right pr-4">TOTAL:</td>
                                    <td class="col-right">{{ number_format($grandTotal['jumlah_keping'] ?? 0) }}</td>
                                    <td class="col-right">{{ number_format($grandTotal['volume'] ?? 0, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tbody>
                            @break
                    @endswitch
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4 px-2">
                {{ $items->appends(request()->query())->links() }}
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