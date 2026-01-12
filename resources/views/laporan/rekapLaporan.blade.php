@extends('laporan.layouts.dashboard')

@section('title', 'Rekap Laporan')

@section('page-title', 'Rekap Laporan Bulanan')

@section('content')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $jenis = request('jenis', 'semua');
    $tampilan = request('tampilan', $jenis === 'semua' ? 'ringkas' : 'detail');
    $showDetail = $tampilan === 'detail';

    $jenisOptions = [
        'semua' => 'Semua Jenis Laporan',
        'penerimaan_kayu_bulat' => 'Penerimaan Kayu Bulat',
        'penerimaan_kayu_olahan' => 'Penerimaan Kayu Olahan',
        'mutasi_kayu_bulat' => 'Mutasi Kayu Bulat (LMKB)',
        'mutasi_kayu_olahan' => 'Mutasi Kayu Olahan (LMKO)',
        'penjualan_kayu_olahan' => 'Penjualan Kayu Olahan',
    ];
@endphp

<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">
            Rekap Laporan Bulanan
        </h1>
        <p class="text-gray-600">Rekapitulasi laporan industri kayu per bulan</p>
    </div>

    <!-- Filter Bulan dan Tahun -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 no-print">
        <form method="GET" action="{{ route('laporan.rekap') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan" id="bulan" class="w-40 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @foreach($namaBulan as $key => $nama)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" id="tahun" class="w-32 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan</label>
                <select name="jenis" id="jenis" class="w-64 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @foreach($jenisOptions as $key => $label)
                        <option value="{{ $key }}" {{ $jenis === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tampilan" class="block text-sm font-medium text-gray-700 mb-1">Tampilan</label>
                <select name="tampilan" id="tampilan" class="w-40 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="ringkas" {{ $tampilan === 'ringkas' ? 'selected' : '' }}>Ringkas</option>
                    <option value="detail" {{ $tampilan === 'detail' ? 'selected' : '' }}>Detail</option>
                </select>
            </div>
            
            <div>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded shadow-sm transition">
                    Tampilkan
                </button>
            </div>

            <div>
                <button type="button" onclick="window.print()" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded shadow-sm transition">
                    Cetak
                </button>
            </div>
        </form>
    </div>

    <!-- Info Periode -->
    <div class="bg-blue-600 rounded-lg shadow p-4 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">
                    Periode: {{ $namaBulan[$bulan] }} {{ $tahun }}
                </h2>
                <p class="text-sm text-blue-100 mt-1">Total: {{ $insights['total_laporan'] }} laporan dari {{ $insights['total_industri_aktif'] }} industri</p>
            </div>
        </div>
    </div>

    <!-- Tabel Rekap per Jenis Laporan -->

    @if($jenis === 'semua' || $jenis === 'penerimaan_kayu_bulat')
    <!-- Penerimaan Kayu Bulat -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="bg-emerald-600 px-4 py-3">
            <h3 class="text-base font-bold text-white">{{ $jenis === 'semua' ? '1. ' : '' }}Laporan Penerimaan Kayu Bulat</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Indikator</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 text-gray-800">Total Dokumen Penerimaan</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($rekap['penerimaan_kayu_bulat']['total_dokumen']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Dokumen</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">Total Jumlah Batang</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($rekap['penerimaan_kayu_bulat']['total_batang']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Batang</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-semibold">Total Volume</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-700">{{ number_format($rekap['penerimaan_kayu_bulat']['total_volume'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    @if($rekap['penerimaan_kayu_bulat']['jenis_kayu_terbanyak'])
                    <tr class="bg-yellow-50">
                        <td class="px-4 py-3 text-gray-800">Jenis Kayu Terbanyak</td>
                        <td class="px-4 py-3 text-right font-semibold" colspan="2">{{ $rekap['penerimaan_kayu_bulat']['jenis_kayu_terbanyak'] }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t">
            <a href="{{ route('laporan.detail', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => 'penerimaan_kayu_bulat']) }}" class="text-sm text-emerald-700 hover:text-emerald-800 font-medium">
                Lihat Detail Data (Tabel)
            </a>
        </div>
    </div>
    @endif

    @if($jenis === 'semua' || $jenis === 'penerimaan_kayu_olahan')
    <!-- Penerimaan Kayu Olahan -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="bg-teal-600 px-4 py-3">
            <h3 class="text-base font-bold text-white">{{ $jenis === 'semua' ? '2. ' : '' }}Laporan Penerimaan Kayu Olahan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Indikator</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 text-gray-800">Total Dokumen Penerimaan</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($rekap['penerimaan_kayu_olahan']['total_dokumen']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Dokumen</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">Total Jumlah Keping</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($rekap['penerimaan_kayu_olahan']['total_keping']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Keping</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-semibold">Total Volume</td>
                        <td class="px-4 py-3 text-right font-bold text-teal-700">{{ number_format($rekap['penerimaan_kayu_olahan']['total_volume'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    @if($rekap['penerimaan_kayu_olahan']['jenis_olahan_terbanyak'])
                    <tr class="bg-yellow-50">
                        <td class="px-4 py-3 text-gray-800">Jenis Olahan Terbanyak</td>
                        <td class="px-4 py-3 text-right font-semibold" colspan="2">{{ $rekap['penerimaan_kayu_olahan']['jenis_olahan_terbanyak'] }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t">
            <a href="{{ route('laporan.detail', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => 'penerimaan_kayu_olahan']) }}" class="text-sm text-teal-700 hover:text-teal-800 font-medium">
                Lihat Detail Data (Tabel)
            </a>
        </div>
    </div>
    @endif

    @if($jenis === 'semua' || $jenis === 'mutasi_kayu_bulat')
    <!-- Mutasi Kayu Bulat (LMKB) -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="bg-amber-600 px-4 py-3">
            <h3 class="text-base font-bold text-white">{{ $jenis === 'semua' ? '3. ' : '' }}Laporan Mutasi Kayu Bulat (LMKB)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Indikator</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Volume</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 text-gray-800">Persediaan Awal</td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-700">{{ number_format($rekap['mutasi_kayu_bulat']['total_persediaan_awal'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">Penggunaan/Pengurangan</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-700">{{ number_format($rekap['mutasi_kayu_bulat']['total_penggunaan'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-semibold">Persediaan Akhir</td>
                        <td class="px-4 py-3 text-right font-bold text-green-700">{{ number_format($rekap['mutasi_kayu_bulat']['total_persediaan_akhir'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    @if($rekap['mutasi_kayu_bulat']['jenis_kayu_terbanyak'])
                    <tr class="bg-yellow-50">
                        <td class="px-4 py-3 text-gray-800">Jenis Kayu Terbanyak</td>
                        <td class="px-4 py-3 text-right font-semibold" colspan="2">{{ $rekap['mutasi_kayu_bulat']['jenis_kayu_terbanyak'] }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t">
            <a href="{{ route('laporan.detail', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => 'mutasi_kayu_bulat']) }}" class="text-sm text-amber-700 hover:text-amber-800 font-medium">
                Lihat Detail Data (Tabel)
            </a>
        </div>
    </div>
    @endif

    @if($jenis === 'semua' || $jenis === 'mutasi_kayu_olahan')
    <!-- Mutasi Kayu Olahan (LMKO) -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="bg-orange-600 px-4 py-3">
            <h3 class="text-base font-bold text-white">{{ $jenis === 'semua' ? '4. ' : '' }}Laporan Mutasi Kayu Olahan (LMKO)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Indikator</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Volume</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 text-gray-800">Persediaan Awal</td>
                        <td class="px-4 py-3 text-right font-semibold text-blue-700">{{ number_format($rekap['mutasi_kayu_olahan']['total_persediaan_awal'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">Penggunaan/Pengurangan</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-700">{{ number_format($rekap['mutasi_kayu_olahan']['total_penggunaan'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-semibold">Persediaan Akhir</td>
                        <td class="px-4 py-3 text-right font-bold text-green-700">{{ number_format($rekap['mutasi_kayu_olahan']['total_persediaan_akhir'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    @if($rekap['mutasi_kayu_olahan']['jenis_olahan_terbanyak'])
                    <tr class="bg-yellow-50">
                        <td class="px-4 py-3 text-gray-800">Jenis Olahan Terbanyak</td>
                        <td class="px-4 py-3 text-right font-semibold" colspan="2">{{ $rekap['mutasi_kayu_olahan']['jenis_olahan_terbanyak'] }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t">
            <a href="{{ route('laporan.detail', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => 'mutasi_kayu_olahan']) }}" class="text-sm text-orange-700 hover:text-orange-800 font-medium">
                Lihat Detail Data (Tabel)
            </a>
        </div>
    </div>
    @endif

    @if($jenis === 'semua' || $jenis === 'penjualan_kayu_olahan')
    <!-- Penjualan Kayu Olahan -->
    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
        <div class="bg-rose-600 px-4 py-3">
            <h3 class="text-base font-bold text-white">{{ $jenis === 'semua' ? '5. ' : '' }}Laporan Penjualan Kayu Olahan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Indikator</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Jumlah</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Satuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="px-4 py-3 text-gray-800">Total Dokumen Penjualan</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($rekap['penjualan_kayu_olahan']['total_dokumen']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Dokumen</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">Total Jumlah Keping</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($rekap['penjualan_kayu_olahan']['total_keping']) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">Keping</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-semibold">Total Volume</td>
                        <td class="px-4 py-3 text-right font-bold text-rose-700">{{ number_format($rekap['penjualan_kayu_olahan']['total_volume'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">m³</td>
                    </tr>
                    @if($rekap['penjualan_kayu_olahan']['tujuan_terbanyak'])
                    <tr class="bg-yellow-50">
                        <td class="px-4 py-3 text-gray-800">Tujuan Kirim Terbanyak</td>
                        <td class="px-4 py-3 text-right font-semibold" colspan="2">{{ $rekap['penjualan_kayu_olahan']['tujuan_terbanyak'] }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-gray-50 border-t">
            <a href="{{ route('laporan.detail', ['bulan' => $bulan, 'tahun' => $tahun, 'jenis' => 'penjualan_kayu_olahan']) }}" class="text-sm text-rose-700 hover:text-rose-800 font-medium">
                Lihat Detail Data (Tabel)
            </a>
        </div>
    </div>
    @endif

    <!-- Footer Info -->
    <div class="mt-6 text-center text-sm text-gray-500 border-t pt-4">
        <p>Data diambil pada: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>
</div>

<style>
    @media print {
        .app-sidebar, .no-print {
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
