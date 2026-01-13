@extends('laporan.layouts.dashboard')

@section('title', 'Detail Laporan')

@section('page-title', 'Detail Laporan')

@section('content')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $periodeLabel = ($namaBulan[$bulan] ?? $bulan) . ' ' . $tahun;
@endphp

<div class="container mx-auto px-6 py-8">
    <div class="mb-6 flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 mb-1">{{ $jenisLabel }}</h1>
            <p class="text-gray-600">Periode: {{ $periodeLabel }} • Total data: {{ $items->count() }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('laporan.rekap', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => $jenis]) }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded shadow-sm transition">
                Kembali ke Rekap
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded shadow-sm transition">
                Cetak
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 no-print">
        <form method="GET" action="{{ route('laporan.detail') }}" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="jenis" value="{{ $jenis }}">

            @switch($jenis)
                @case('penerimaan_kayu_bulat')
                    <div>
                        <label for="jenis_kayu" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kayu</label>
                        <select name="jenis_kayu" id="jenis_kayu" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['jenis_kayu'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('jenis_kayu') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="asal_kayu" class="block text-sm font-medium text-gray-700 mb-1">Asal Kayu</label>
                        <select name="asal_kayu" id="asal_kayu" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['asal_kayu'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('asal_kayu') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @break

                @case('penerimaan_kayu_olahan')
                    <div>
                        <label for="jenis_olahan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Olahan</label>
                        <select name="jenis_olahan" id="jenis_olahan" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['jenis_olahan'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="asal_kayu" class="block text-sm font-medium text-gray-700 mb-1">Asal Kayu</label>
                        <select name="asal_kayu" id="asal_kayu" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['asal_kayu'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('asal_kayu') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @break

                @case('mutasi_kayu_bulat')
                    <div>
                        <label for="jenis_kayu" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kayu</label>
                        <select name="jenis_kayu" id="jenis_kayu" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['jenis_kayu'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('jenis_kayu') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @break

                @case('mutasi_kayu_olahan')
                    <div>
                        <label for="jenis_olahan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Olahan</label>
                        <select name="jenis_olahan" id="jenis_olahan" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['jenis_olahan'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    @break

                @case('penjualan_kayu_olahan')
                    <div>
                        <label for="tujuan_kirim" class="block text-sm font-medium text-gray-700 mb-1">Tujuan Kirim</label>
                        <select name="tujuan_kirim" id="tujuan_kirim" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['tujuan_kirim'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('tujuan_kirim') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="jenis_olahan" class="block text-sm font-medium text-gray-700 mb-1">Jenis Olahan</label>
                        <select name="jenis_olahan" id="jenis_olahan" class="w-48 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Semua</option>
                            @foreach($filterOptions['jenis_olahan'] ?? [] as $item)
                                <option value="{{ $item }}" {{ request('jenis_olahan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="ekspor_impor" class="block text-sm font-medium text-gray-700 mb-1">Ekspor/Lokal</label>
                        <select name="ekspor_impor" id="ekspor_impor" class="w-40 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">-- Semua --</option>
                            <option value="ekspor" {{ request('ekspor_impor') == 'ekspor' ? 'selected' : '' }}>Ekspor</option>
                            <option value="lokal" {{ request('ekspor_impor') == 'lokal' ? 'selected' : '' }}>Lokal</option>
                        </select>
                    </div>
                    @break
            @endswitch

            <div class="ml-auto flex gap-2">
                <a href="{{ route('laporan.detail', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => $jenis]) }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded shadow-sm transition">
                    Reset
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded shadow-sm transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                @switch($jenis)
                    @case('penerimaan_kayu_bulat')
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Perusahaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nomor Dokumen</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Asal Kayu</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Jenis Kayu</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah Batang</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Volume (m³)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->nomor_dokumen }}</td>
                                    <td class="px-4 py-3">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                    <td class="px-4 py-3">{{ $item->asal_kayu }}</td>
                                    <td class="px-4 py-3">{{ $item->jenis_kayu }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->jumlah_batang) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->volume, 2) }}</td>
                                    <td class="px-4 py-3">{{ $item->keterangan }}</td>
                                </tr>
                            @endforeach
                            @if($items->count() > 0)
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="6" class="px-4 py-3 text-right">TOTAL:</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('jumlah_batang')) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('volume'), 2) }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            @endif
                        </tbody>
                        @break

                    @case('penerimaan_kayu_olahan')
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Perusahaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nomor Dokumen</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Asal Kayu</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Jenis Olahan</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah Keping</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Volume (m³)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->nomor_dokumen }}</td>
                                    <td class="px-4 py-3">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                    <td class="px-4 py-3">{{ $item->asal_kayu }}</td>
                                    <td class="px-4 py-3">{{ $item->jenis_olahan }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->jumlah_keping) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->volume, 2) }}</td>
                                    <td class="px-4 py-3">{{ $item->keterangan }}</td>
                                </tr>
                            @endforeach
                            @if($items->count() > 0)
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="6" class="px-4 py-3 text-right">TOTAL:</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('jumlah_keping')) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('volume'), 2) }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            @endif
                        </tbody>
                        @break

                    @case('mutasi_kayu_bulat')
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Perusahaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Jenis Kayu</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Persediaan Awal (m³)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Penambahan (m³)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Penggunaan (m³)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Persediaan Akhir (m³)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->jenis_kayu }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                    <td class="px-4 py-3">{{ $item->keterangan }}</td>
                                </tr>
                            @endforeach
                            @if($items->count() > 0)
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="3" class="px-4 py-3 text-right">TOTAL:</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('persediaan_awal_volume'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('penambahan_volume'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('penggunaan_pengurangan_volume'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('persediaan_akhir_volume'), 2) }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            @endif
                        </tbody>
                        @break

                    @case('mutasi_kayu_olahan')
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Perusahaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Jenis Olahan</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Persediaan Awal (m³)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Penambahan (m³)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Penggunaan (m³)</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Persediaan Akhir (m³)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->jenis_olahan }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->persediaan_awal_volume, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->penambahan_volume, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->penggunaan_pengurangan_volume, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->persediaan_akhir_volume, 2) }}</td>
                                    <td class="px-4 py-3">{{ $item->keterangan }}</td>
                                </tr>
                            @endforeach
                            @if($items->count() > 0)
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="3" class="px-4 py-3 text-right">TOTAL:</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('persediaan_awal_volume'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('penambahan_volume'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('penggunaan_pengurangan_volume'), 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('persediaan_akhir_volume'), 2) }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            @endif
                        </tbody>
                        @break

                    @case('penjualan_kayu_olahan')
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Perusahaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Nomor Dokumen</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Tujuan Kirim</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Jenis Olahan</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah Keping</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-700">Volume (m³)</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-700">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->laporan->industri->nama ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item->nomor_dokumen }}</td>
                                    <td class="px-4 py-3">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                                    <td class="px-4 py-3">{{ $item->tujuan_kirim }}</td>
                                    <td class="px-4 py-3">{{ $item->jenis_olahan }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->jumlah_keping) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($item->volume, 2) }}</td>
                                    <td class="px-4 py-3">{{ $item->keterangan }}</td>
                                </tr>
                            @endforeach
                            @if($items->count() > 0)
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="6" class="px-4 py-3 text-right">TOTAL:</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('jumlah_keping')) }}</td>
                                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('volume'), 2) }}</td>
                                    <td class="px-4 py-3"></td>
                                </tr>
                            @endif
                        </tbody>
                        @break
                @endswitch
            </table>
        </div>

        @if($items->count() === 0)
            <div class="p-6 text-center text-gray-500">Tidak ada data untuk periode ini.</div>
        @endif
    </div>
</div>

<style>
    @media print {
        .app-sidebar, .no-print, button:not(.print-only), a {
            display: none !important;
        }
        body {
            background: white !important;
        }
        .container {
            max-width: 100% !important;
            padding: 20px
        }
    }
</style>
@endsection
