@extends('PNBP.layouts.admin')

@section('title', 'Detail Rekonsiliasi - ' . $reconciliation->original_filename)

@section('header')
    Detail Rekonsiliasi <span class="text-sm font-normal text-gray-500 ml-2">{{ $reconciliation->original_filename }}</span>
@endsection

@section('content')
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="rounded-md bg-green-50 dark:bg-green-900/20 p-4 mb-6 border-l-4 border-green-500">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 text-red-700 dark:text-red-300 p-4 mb-6" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <ul class="list-disc pl-5 mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header Action Buttons --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Tahun {{ $reconciliation->year }} - Triwulan {{ $reconciliation->quarter }}
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- REMOVED: class "transition-colors" --}}
            <a href="{{ route('reconciliations.export-pdf', array_merge(['reconciliation' => $reconciliation->id], request()->query())) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-primary hover:bg-primary_hover">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 17v-6m0 0l-3 3m3-3l3 3M6 20h12" />
                </svg>
                Export PDF
            </a>
            
            {{-- REMOVED: class "transition-colors" --}}
            <a href="{{ route('reconciliations.file', $reconciliation->id) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-surface-dark border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Excel Asli
            </a>

            {{-- REMOVED: class "transition-colors" --}}
            <a href="{{ route('reconciliations.raw', $reconciliation->id) }}" target="_blank" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-surface-dark border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Raw Data
            </a>

            {{-- REMOVED: class "transition-colors" --}}
            <a href="{{ route('reconciliations.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-surface-dark border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700">
                &larr; Kembali
            </a>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Kategori: Kayu --}}
                @if(($volumeByCat['HASIL HUTAN KAYU'] ?? 0) > 0)
                <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total Hasil Hutan Kayu</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ number_format($volumeByCat['HASIL HUTAN KAYU'], 2, '.', ',') }}
                        <span class="text-sm text-gray-400 font-normal">m&sup3;</span>
                    </div>
                </div>
                @endif

                {{-- Kategori: HHBK --}}
                @if(($volumeByCat['HASIL HUTAN BUKAN KAYU (HHBK)'] ?? 0) > 0)
                <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total HHBK</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ number_format($volumeByCat['HASIL HUTAN BUKAN KAYU (HHBK)'], 2, '.', ',') }}
                        <span class="text-sm text-gray-400 font-normal">ton / kg</span>
                    </div>
                </div>
                @endif

                {{-- Kategori: Lainnya --}}
                @if(($volumeByCat['HASIL HUTAN LAINNYA'] ?? 0) > 0)
                <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Total HH Lainnya</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ number_format($volumeByCat['HASIL HUTAN LAINNYA'], 2, '.', ',') }}
                        <span class="text-sm text-gray-400 font-normal">unit</span>
                    </div>
                </div>
                @endif
                
                <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Nilai LHP</div>
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-500 mt-2">
                        <span class="text-sm text-gray-500 font-normal">Rp</span>
                        {{ number_format(($totalNilaiLhpFinal ?? $statsJenis->sum('total_nilai')), 0, '.', ',') }}
                    </div>
                </div>
                <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total Nilai Setor</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-500 mt-2">
                        <span class="text-sm text-gray-500 font-normal">Rp</span>
                        {{ number_format(($baseTotalNilaiSetor ?? 0), 0, '.', ',') }}
                    </div>
                </div>
            </div>

    {{-- Edit Summary Overrides (Restored) --}}
    <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 mb-6 p-6">
        <h3 class="font-bold text-gray-800 dark:text-white mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit Ringkasan (Manual Override)
        </h3>
        
        <form action="{{ route('reconciliations.summary-overrides', $reconciliation->id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Override Total Nilai LHP --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Nilai LHP (Rp)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="text" name="total_nilai_lhp" 
                            value="{{ number_format($totalNilaiLhpFinal, 0, '.', ',') }}"
                            class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2.5"
                            placeholder="0">
                    </div>
                </div>

                {{-- Loop per satuan --}}
                @foreach($totalPerSatuan as $t)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Volume ({{ $t->satuan == '-' ? 'LAINNYA' : $t->satuan }})</label>
                        <input type="text" name="total_volume[{{ $t->satuan }}]" 
                            value="{{ number_format(($t->total_volume_final), 3, '.', '') }}"
                            class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full p-2.5"
                            placeholder="0">
                        @if(!empty($t->is_overridden))
                            <p class="text-xs text-yellow-600 mt-1">*Nilai manual (Asli: {{ number_format($t->total_volume, 3) }})</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-between items-center">
                <p class="text-xs text-gray-500 dark:text-gray-400">Kosongkan kolom untuk kembali ke hitungan otomatis.</p>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary_hover text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

            {{-- Rekap Tables --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Rekap Jenis --}}
                <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 lg:col-span-1">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-transparent">
                        <h3 class="font-bold text-gray-800 dark:text-white">Rekap Jenis Hasil Hutan</h3>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b dark:border-gray-700 text-gray-500 dark:text-gray-400">
                                    <th class="py-2 font-medium">Jenis</th>
                                    <th class="py-2 text-right font-medium">Volume</th>
                                    <th class="py-2 text-center font-medium"></th>
                                    <th class="py-2 text-right font-medium text-primary dark:text-primary-400">Nilai LHP (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($statsJenis as $s)
                                    {{-- REMOVED: class "transition-colors" --}}
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-2 font-medium text-xs text-gray-900 dark:text-gray-200">{{ $s->label }}</td>
                                        <td class="py-2 text-right text-gray-700 dark:text-gray-300">{{ number_format($s->total_volume, 2, '.', ',') }}</td>
                                        <td class="py-2 text-center text-gray-500 dark:text-gray-400">{{ $s->satuan }}</td>
                                        <td class="py-2 text-right text-primary dark:text-primary-400 font-semibold">{{ "Rp " . number_format(($s->total_nilai ?? 0), 0, '.', ',') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Rekap Bank --}}
                <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 lg:col-span-1">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-transparent">
                        <h3 class="font-bold text-gray-800 dark:text-white">Rekap Bank Penyetor</h3>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b dark:border-gray-700 text-gray-500 dark:text-gray-400">
                                    <th class="py-2 font-medium">Bank</th>
                                    <th class="py-2 text-right font-medium">Total Setor (Rp)</th>
                                    <th class="py-2 text-right font-medium text-primary dark:text-primary-400">Trx</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($statsBank as $s)
                                    {{-- REMOVED: class "transition-colors" --}}
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="py-2 font-medium truncate max-w-[100px] text-gray-900 dark:text-gray-200" title="{{ $s->label }}">{{ $s->label }}</td>
                                        <td class="py-2 text-right text-gray-900 dark:text-gray-200 font-medium">{{ number_format($s->total_nilai, 0, '.', ',') }}</td>
                                        <td class="py-2 text-right text-primary dark:text-primary-400 font-semibold">{{ $s->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- MAIN TABLE: Detail Data Transaksi --}}
            <div class="bg-white dark:bg-surface-dark overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-surface-dark">
                    <h3 class="font-bold text-gray-800 dark:text-white">Detail Data Transaksi ({{ $details->total() }} baris)</h3>
                    <div class="flex items-center space-x-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded border dark:border-gray-600">
                            Menampilkan {{ $details->count() }} data per halaman
                        </span>
                    </div>
                </div>

                <div class="p-4">
                    {{-- Filter & Search --}}
                    <form action="{{ route('reconciliations.show', $reconciliation->id) }}" method="GET" id="filterForm" class="flex gap-2 mb-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="w-full md:w-1/3 px-3 py-2 border dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-gray-500" placeholder="Cari data (Wilayah, NTPN, Billing, dll)...">
                        
                        {{-- REMOVED: class "transition-colors" --}}
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-gray-800 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-lg text-sm font-medium">Cari</button>
                        
                        {{-- REMOVED: class "transition-colors" --}}
                        <button type="button" id="resetBtn" class="inline-flex items-center px-4 py-2.5 bg-white dark:bg-surface-dark border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium {{ request('search') ? '' : 'hidden' }}">Reset</button>
                    </form>

                    {{-- TABEL NETRAL MULAI DARI SINI --}}
                    <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                        <table class="min-w-full text-xs text-left text-gray-900 dark:text-gray-200">
                            {{-- Header Netral --}}
                            <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-800 border-b dark:border-gray-700 text-gray-700 dark:text-gray-300">
                                <tr>
                                    <th class="px-3 py-3 whitespace-nowrap sticky left-0 bg-gray-100 dark:bg-gray-800 z-10 shadow-sm border-r dark:border-gray-700 min-w-[50px]">No</th>
                                    <th class="px-3 py-3 whitespace-nowrap min-w-[200px]">Wilayah</th>

                                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">LHP No</th>
                                    <th class="px-3 py-3 whitespace-nowrap min-w-[100px]">Tgl LHP</th>
                                    <th class="px-3 py-3 whitespace-nowrap min-w-[150px]">Jenis HH</th>
                                    <th class="px-3 py-3 whitespace-nowrap text-right">Volume</th>
                                    <th class="px-3 py-3 whitespace-nowrap text-center w-[60px]">Sat</th>
                                    <th class="px-3 py-3 whitespace-nowrap text-right">Nilai LHP</th>

                                    <th class="px-3 py-3 whitespace-nowrap border-l dark:border-gray-700 min-w-[120px]">Billing No</th>
                                    <th class="px-3 py-3 whitespace-nowrap min-w-[100px]">Tgl Billing</th>
                                    <th class="px-3 py-3 whitespace-nowrap text-right">Nilai Billing</th>

                                    <th class="px-3 py-3 whitespace-nowrap border-l dark:border-gray-700 min-w-[100px]">Tgl Setor</th>
                                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">Bank</th>
                                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">NTPN</th>
                                    <th class="px-3 py-3 whitespace-nowrap min-w-[120px]">NTB</th>
                                    <th class="px-3 py-3 whitespace-nowrap text-right">Nilai Setor</th>
                                </tr>
                            </thead>

                            {{-- Body Netral --}}
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($details as $detail)
                                    {{-- REMOVED: class "transition-colors" --}}
                                    <tr class="bg-white dark:bg-surface-dark hover:bg-gray-50 dark:hover:bg-gray-700">
                                        {{-- No & Wilayah --}}
                                        <td class="px-3 py-2 sticky left-0 bg-white dark:bg-surface-dark font-medium text-center shadow-sm border-r dark:border-gray-700 text-gray-500 dark:text-gray-400">{{ $detail->no_urut ?? '-' }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-200 whitespace-nowrap">{{ $detail->wilayah }}</td>

                                        {{-- Data LHP --}}
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->lhp_no }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->lhp_tanggal }}</td>
                                        <td class="px-3 py-2 font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $detail->jenis_sdh }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->volume, 2, '.', ',') }}</td>
                                        <td class="px-3 py-2 text-center text-xs text-gray-500 dark:text-gray-400">{{ $detail->satuan }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->lhp_nilai, 0, '.', ',') }}</td>

                                        {{-- Data Billing --}}
                                        <td class="px-3 py-2 border-l dark:border-gray-700 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->billing_no }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->billing_tanggal }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->billing_nilai ?? 0, 0, '.', ',') }}</td>

                                        {{-- Data Setor --}}
                                        <td class="px-3 py-2 border-l dark:border-gray-700 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_tanggal }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_bank }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_ntpn }}</td>
                                        <td class="px-3 py-2 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $detail->setor_ntb }}</td>
                                        <td class="px-3 py-2 text-right font-medium text-gray-800 dark:text-gray-200">{{ number_format($detail->setor_nilai ?? 0, 0, '.', ',') }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="p-6 text-center text-gray-500 dark:text-gray-400" colspan="15">Tidak ada data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $details->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('resetBtn')?.addEventListener('click', function(){
            const f = document.getElementById('filterForm');
            if(!f) return;
            f.querySelector('input[name="search"]').value = '';
            f.submit();
        });
    </script>
@endsection