@extends('layouts.admin')

@section('title', 'Detail Rekonsiliasi - ' . $reconciliation->original_filename)
@section('header')
    Detail Rekonsiliasi <span class="text-sm font-normal text-gray-500 ml-2">{{ $reconciliation->original_filename }}</span>
@endsection

@section('content')
    @if (session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6 border-l-4 border-green-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="text-sm text-gray-500 mt-1">
                Tahun {{ $reconciliation->year }} - Triwulan {{ $reconciliation->quarter }}
            </div>
        </div>
        <div class="flex items-center gap-2">
            {{-- Tombol Download File Asli --}}
            <a href="{{ route('reconciliations.file', $reconciliation->id) }}" target="_blank" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Unduh Excel Asli
            </a>
            
            {{-- Tombol Lihat RAW Excel --}}
            <a href="{{ route('reconciliations.raw', $reconciliation->id) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lihat Raw
            </a>
            
            <a href="{{ route('reconciliations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition text-sm">
                &larr; Kembali
            </a>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Kartu Statistik Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- Loop Total Kuantitas --}}
                @foreach($totalPerSatuan as $t)
                    @if($t->total_volume <= 0) @continue @endif

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Total {{ $t->satuan == '-' ? 'LAINNYA' : $t->satuan }}</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">
                            {{-- Tampilkan nilai override jika ada, fallback ke nilai sistem --}}
                            {{ rtrim(rtrim(number_format(($t->total_volume_final ?? $t->total_volume), 3, '.', ','), '0'), ',') }}
                            <span class="text-sm text-gray-400 font-normal">{{ $t->satuan == '-' ? '' : $t->satuan }}</span>
                        </div>
                    </div>
                @endforeach

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium">Total Nilai LHP</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-2">
                        <span class="text-sm text-gray-500 font-normal">Rp</span>
                        {{ number_format(($totalNilaiLhpFinal ?? $statsJenis->sum('total_nilai')), 0, '.', ',') }}
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-medium">Total Nilai Setor</div>
                    <div class="text-2xl font-bold text-green-600 mt-2">
                        <span class="text-sm text-gray-500 font-normal">Rp</span>
                        {{ number_format(($baseTotalNilaiSetor ?? 0), 0, '.', ',') }}
                    </div>
                </div>

                {{-- Hitung Selisih Nilai --}}
                @php
                    $selisihNilai = (float) ($baseTotalNilaiSetor ?? 0) - (float) ($totalNilaiLhpFinal ?? 0);
                    $selisihNilaiAbs = abs($selisihNilai);
                    $selisihNilaiSign = $selisihNilai > 0 ? '+' : ($selisihNilai < 0 ? '-' : '');
                    $selisihNilaiClass = $selisihNilai > 0 ? 'text-green-600' : ($selisihNilai < 0 ? 'text-red-600' : 'text-gray-600');
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-gray-500">
                    <div class="text-gray-500 text-sm font-medium">Selisih Nilai</div>
                    <div class="text-2xl font-bold mt-2 {{ $selisihNilaiClass }}">
                        <span class="text-sm text-gray-500 font-normal">Rp</span>
                        @if ($selisihNilai == 0)
                            0
                        @else
                            {{ $selisihNilaiSign }}{{ number_format($selisihNilaiAbs, 0, '.', ',') }}
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium">Cakupan Data</div>
                    <div class="mt-2">
                         <span class="text-xl font-bold text-indigo-600">{{ $statsWilayah->count() }}</span> <span class="text-xs text-gray-500">Wilayah</span>
                         <span class="mx-2 text-gray-300">|</span>
                         <span class="text-xl font-bold text-green-600">{{ $statsJenis->count() }}</span> <span class="text-xs text-gray-500">HH</span>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <details class="p-6">
                    <summary class="cursor-pointer font-semibold text-gray-800">Edit Ringkasan Total (hanya kartu di atas)</summary>
                    <div class="mt-4">
                        <form method="POST" action="{{ route('reconciliations.summary-overrides', $reconciliation->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @csrf

                            @foreach($totalPerSatuan as $t)
                                @if($t->total_volume <= 0)
                                    @continue
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Total {{ $t->satuan == '-' ? 'LAINNYA' : $t->satuan }}</label>
                                    <input
                                        name="total_volume[{{ $t->satuan }}]"
                                        value="{{ old('total_volume.' . $t->satuan, rtrim(rtrim(number_format($t->total_volume_final ?? $t->total_volume, 3, '.', ','), '0'), ',')) }}"
                                        class="mt-1 w-full border rounded p-2"
                                        placeholder="Kosongkan untuk kembali ke hasil sistem"
                                    />
                                    <div class="text-xs text-gray-400 mt-1">Nilai sistem: {{ rtrim(rtrim(number_format($t->total_volume, 3, '.', ','), '0'), ',') }} {{ $t->satuan }}</div>
                                </div>
                            @endforeach

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Total Nilai LHP (Rp)</label>
                                <input
                                    name="total_nilai_lhp"
                                    value="{{ old('total_nilai_lhp', number_format($totalNilaiLhpFinal ?? $statsJenis->sum('total_nilai'), 0, '.', ',')) }}"
                                    class="mt-1 w-full border rounded p-2"
                                    placeholder="Kosongkan untuk kembali ke hasil sistem"
                                />
                                <div class="text-xs text-gray-400 mt-1">Nilai sistem: Rp {{ number_format($baseTotalNilaiLhp ?? $statsJenis->sum('total_nilai'), 0, '.', ',') }}</div>
                            </div>

                            <div class="md:col-span-2 flex justify-end">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Simpan Ringkasan</button>
                            </div>
                        </form>
                    </div>
                </details>
            </div>

            {{-- Tabel Ringkasan (Jenis & Bank) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Rekap Jenis --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-1">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-bold text-gray-800">Rekap Jenis Hasil Hutan</h3>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                             <thead>
                                <tr class="border-b">
                                    <th class="py-2">Jenis</th>
                                    <th class="py-2 text-right">Volume</th>
                                    <th class="py-2 text-center"></th> 
                                    <th class="py-2 text-right">Nilai LHP (Rp)</th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($statsJenis as $s)
                                <tr class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="py-2 font-medium text-xs">{{ $s->label }}</td>
                                    <td class="py-2 text-right">{{ number_format($s->total_volume, 2, '.', ',') }}</td>
                                    <td class="py-2 text-center">{{ $s->satuan }}</td>
                                    <td class="py-2 text-right text-gray-700">{{ "Rp " . number_format(($s->total_nilai ?? 0), 0, '.', ',') }}</td>
                                </tr>
                                @endforeach
                             </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rekap Bank --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-1">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-bold text-gray-800">Rekap Bank Penyetor</h3>
                    </div>
                    <div class="p-4 overflow-x-auto">
                         <table class="w-full text-sm text-left">
                             <thead>
                                <tr class="border-b">
                                    <th class="py-2">Bank</th>
                                    <th class="py-2 text-right">Total Setor (Rp)</th>
                                    <th class="py-2 text-right">Trx</th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($statsBank as $s)
                                <tr class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="py-2 font-medium truncate max-w-[100px]" title="{{ $s->label }}">{{ $s->label }}</td>
                                    <td class="py-2 text-right">{{ "Rp " . number_format($s->total_nilai, 0, '.', ',') }}</td>
                                    <td class="py-2 text-right text-gray-500">{{ $s->count }}</td>
                                </tr>
                                @endforeach
                             </tbody>
                        </table>
                    </div>
                </div>
                
            </div>

            {{-- Detail Data Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Detail Data Transaksi ({{ $details->total() }} baris)</h3>
                    <div class="flex items-center space-x-2">
                         <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Menampilkan {{ $details->count() }} data per halaman</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    {{-- Form Pencarian --}}
                    <form action="{{ route('reconciliations.show', $reconciliation->id) }}" method="GET" id="filterForm" class="flex gap-2 mb-4 p-1">
                        <input type="text" name="search" value="{{ request('search') }}" class="w-full md:w-1/3 px-3 py-2 border rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Cari data (Wilayah, NTPN, Billing, dll)...">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Cari
                        </button>
                        <button type="button" id="resetBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('search') ? '' : 'hidden' }}">
                            Reset
                        </button>
                    </form>

                    <div id="reconciliation-data" class="relative">
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="min-w-full text-xs text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b">
                                    <tr>
                                        {{-- LOGIKA SORTING 3 ARAH (Asc -> Desc -> Unsort) --}}
                                        @php
                                            $sortLink = function($col) use ($reconciliation) {
                                                $currentSort = request('sort');
                                                $currentDir = request('direction');
                                                $params = request()->all();

                                                if ($currentSort != $col) {
                                                    // Klik 1: Kolom baru -> Asc
                                                    $params['sort'] = $col;
                                                    $params['direction'] = 'asc';
                                                } elseif ($currentDir == 'asc') {
                                                    // Klik 2: Kolom sama, Asc -> Desc
                                                    $params['sort'] = $col;
                                                    $params['direction'] = 'desc';
                                                } else {
                                                    // Klik 3: Kolom sama, Desc -> Reset (Unsort)
                                                    unset($params['sort']);
                                                    unset($params['direction']);
                                                }

                                                return route('reconciliations.show', array_merge(['reconciliation' => $reconciliation->id], $params));
                                            };

                                            $sortIcon = function($col) {
                                                if (request('sort') != $col) return '<span class="text-gray-300 ml-1 text-[10px]">↕</span>';
                                                if (request('direction') == 'asc') return ' <span class="text-blue-600 ml-1 font-bold">↑</span>';
                                                return ' <span class="text-blue-600 ml-1 font-bold">↓</span>';
                                            };
                                        @endphp

                                        <th class="px-3 py-3 whitespace-nowrap sticky left-0 bg-gray-100 z-10 shadow-sm min-w-[60px]">
                                            <a href="{{ $sortLink('no_urut') }}" class="sort-link flex items-center space-x-1 hover:text-black group">
                                                <span>No</span> {!! $sortIcon('no_urut') !!}
                                            </a>
                                        </th>

                                        <th class="px-3 py-3 whitespace-nowrap min-w-[200px]">
                                            <a href="{{ $sortLink('wilayah') }}" class="sort-link flex items-center space-x-1 hover:text-black group">
                                                <span>Wilayah</span> {!! $sortIcon('wilayah') !!}
                                            </a>
                                        </th>
                                        
                                        {{-- LHP Group --}}
                                        <th class="px-3 py-3 whitespace-nowrap bg-blue-50 border-l border-blue-100 text-blue-800 min-w-[120px]">
                                            <a href="{{ $sortLink('lhp_no') }}" class="sort-link flex items-center space-x-1 hover:text-blue-900 group">
                                                <span>LHP No</span> {!! $sortIcon('lhp_no') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-blue-800 min-w-[100px]">
                                            <a href="{{ $sortLink('lhp_tanggal') }}" class="sort-link flex items-center space-x-1 hover:text-blue-900 group">
                                                <span>Tgl LHP</span> {!! $sortIcon('lhp_tanggal') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-blue-800 min-w-[150px]">
                                            <a href="{{ $sortLink('jenis_sdh') }}" class="sort-link flex items-center space-x-1 hover:text-blue-900 group">
                                                <span>Jenis HH</span> {!! $sortIcon('jenis_sdh') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-right text-blue-800">
                                            <a href="{{ $sortLink('volume') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-blue-900 group">
                                                <span>Volume</span> {!! $sortIcon('volume') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-center text-blue-800 w-[60px]">
                                            <a href="{{ $sortLink('satuan') }}" class="sort-link flex items-center justify-center space-x-1 hover:text-blue-900 group">
                                                <span>Sat</span> {!! $sortIcon('satuan') !!}
                                            </a>
                                        </th> 
                                        <th class="px-3 py-3 whitespace-nowrap bg-blue-50 text-right text-blue-800">
                                            <a href="{{ $sortLink('lhp_nilai') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-blue-900 group">
                                                <span>Nilai LHP (Rp)</span> {!! $sortIcon('lhp_nilai') !!}
                                            </a>
                                        </th>
                                        
                                        {{-- Billing Group --}}
                                        <th class="px-3 py-3 whitespace-nowrap bg-green-50 border-l border-green-100 text-green-800 min-w-[120px]">
                                            <a href="{{ $sortLink('billing_no') }}" class="sort-link flex items-center space-x-1 hover:text-green-900 group">
                                                <span>Billing No</span> {!! $sortIcon('billing_no') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-green-50 text-green-800 min-w-[100px]">
                                            <a href="{{ $sortLink('billing_tanggal') }}" class="sort-link flex items-center space-x-1 hover:text-green-900 group">
                                                <span>Tgl Billing</span> {!! $sortIcon('billing_tanggal') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-green-50 text-right text-green-800">
                                            <a href="{{ $sortLink('billing_nilai') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-green-900 group">
                                                <span>Nilai Billing</span> {!! $sortIcon('billing_nilai') !!}
                                            </a>
                                        </th>
                                        
                                        {{-- Setor Group --}}
                                        <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 border-l border-yellow-100 text-yellow-800 min-w-[100px]">
                                            <a href="{{ $sortLink('setor_tanggal') }}" class="sort-link flex items-center space-x-1 hover:text-yellow-900 group">
                                                <span>Tgl Setor</span> {!! $sortIcon('setor_tanggal') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 text-yellow-800 min-w-[120px]">
                                            <a href="{{ $sortLink('setor_bank') }}" class="sort-link flex items-center space-x-1 hover:text-yellow-900 group">
                                                <span>Bank</span> {!! $sortIcon('setor_bank') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 text-yellow-800 min-w-[120px]">
                                            <a href="{{ $sortLink('setor_ntpn') }}" class="sort-link flex items-center space-x-1 hover:text-yellow-900 group">
                                                <span>NTPN</span> {!! $sortIcon('setor_ntpn') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 text-yellow-800 min-w-[120px]">
                                            <a href="{{ $sortLink('setor_ntb') }}" class="sort-link flex items-center space-x-1 hover:text-yellow-900 group">
                                                <span>NTB</span> {!! $sortIcon('setor_ntb') !!}
                                            </a>
                                        </th>
                                        <th class="px-3 py-3 whitespace-nowrap bg-yellow-50 text-right text-yellow-800">
                                            <a href="{{ $sortLink('setor_nilai') }}" class="sort-link flex items-center justify-end space-x-1 hover:text-yellow-900 group">
                                                <span>Nilai Setor</span> {!! $sortIcon('setor_nilai') !!}
                                            </a>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($details as $detail)
                                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-2 sticky left-0 bg-white hover:bg-gray-50 font-medium text-center shadow-sm border-r">{{ $detail->no_urut ?? '-' }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap">{{ $detail->wilayah }}</td>
                                        
                                        <td class="px-3 py-2 bg-blue-50/20 border-l border-blue-50 whitespace-nowrap">{{ $detail->lhp_no }}</td>
                                        <td class="px-3 py-2 bg-blue-50/20 whitespace-nowrap">{{ $detail->lhp_tanggal }}</td>
                                        <td class="px-3 py-2 bg-blue-50/20 whitespace-nowrap font-medium text-blue-600">{{ $detail->jenis_sdh }}</td>
                                        <td class="px-3 py-2 bg-blue-50/20 text-right font-bold text-gray-700">
                                            {{ number_format($detail->volume, 2, '.', ',') }}
                                        </td>
                                        <td class="px-3 py-2 bg-blue-50/20 text-center text-xs font-bold text-gray-500">
                                            {{ $detail->satuan }} 
                                        </td>
                                        <td class="px-3 py-2 bg-blue-50/20 text-right">
                                            {{ number_format($detail->lhp_nilai, 0, '.', ',') }}
                                        </td>
                                        
                                        <td class="px-3 py-2 bg-green-50/20 border-l border-green-50 whitespace-nowrap">{{ $detail->billing_no }}</td>
                                        <td class="px-3 py-2 bg-green-50/20 whitespace-nowrap">{{ $detail->billing_tanggal }}</td>
                                        <td class="px-3 py-2 bg-green-50/20 text-right">
                                            {{ number_format($detail->billing_nilai, 0, '.', ',') }}
                                        </td>
                                        
                                        <td class="px-3 py-2 bg-yellow-50/20 border-l border-yellow-50 whitespace-nowrap">{{ $detail->setor_tanggal }}</td>
                                        <td class="px-3 py-2 bg-yellow-50/20 whitespace-nowrap">{{ $detail->setor_bank }}</td>
                                        <td class="px-3 py-2 bg-yellow-50/20 whitespace-nowrap text-[10px] font-mono">{{ $detail->setor_ntpn }}</td>
                                        <td class="px-3 py-2 bg-yellow-50/20 whitespace-nowrap text-[10px] font-mono">{{ $detail->setor_ntb }}</td>
                                        <td class="px-3 py-2 bg-yellow-50/20 text-right font-medium">
                                            {{ number_format($detail->setor_nilai, 0, '.', ',') }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="16" class="px-6 py-4 text-center text-gray-500">
                                            Tidak ada data yang cocok dengan pencarian Anda.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div id="pagination" class="p-4 border-t border-gray-200 bg-gray-50">
                            {{ $details->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const dataContainer = document.getElementById('reconciliation-data');
            const resetBtn = document.getElementById('resetBtn');

            // Function to fetch data
            const fetchData = (url) => {
                dataContainer.style.opacity = '0.5';
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newSegment = doc.getElementById('reconciliation-data');
                    dataContainer.innerHTML = newSegment ? newSegment.innerHTML : html;
                    dataContainer.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Error:', err);
                    dataContainer.style.opacity = '1';
                });
            };

            // Handle Search Submit
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const params = new URLSearchParams(formData);
                
                const currentUrlParams = new URLSearchParams(window.location.search);
                if (currentUrlParams.has('sort')) params.set('sort', currentUrlParams.get('sort'));
                if (currentUrlParams.has('direction')) params.set('direction', currentUrlParams.get('direction'));
                
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
                
                if (formData.get('search')) {
                    resetBtn.classList.remove('hidden');
                } else {
                    resetBtn.classList.add('hidden');
                }

                fetchData(newUrl);
            });

            // Handle Reset
            resetBtn.addEventListener('click', function() {
                filterForm.querySelector('[name="search"]').value = '';
                resetBtn.classList.add('hidden');
                
                const params = new URLSearchParams(window.location.search);
                params.delete('search');
                
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
                
                fetchData(newUrl);
            });

            // Event Delegation for Sort and Pagination Links
            dataContainer.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;

                if (link.origin === window.location.origin) {
                    e.preventDefault();
                    const url = link.href;
                    window.history.pushState({}, '', url);
                    fetchData(url);
                }
            });

            // Handle Back/Forward Browser Buttons
            window.addEventListener('popstate', function() {
                const url = window.location.href;
                fetchData(url);
                const params = new URLSearchParams(window.location.search);
                filterForm.querySelector('[name="search"]').value = params.get('search') || '';
                if(params.get('search')) resetBtn.classList.remove('hidden'); 
                else resetBtn.classList.add('hidden');
            });
        });
    </script>
@endsection