@extends('layouts.admin')

@section('title', 'Detail Rekonsiliasi - ' . $reconciliation->original_filename)
@section('header')
    Detail Rekonsiliasi <span class="text-sm font-normal text-gray-500 ml-2">{{ $reconciliation->original_filename }}</span>
@endsection

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="text-sm text-gray-500 mt-1">
                Tahun {{ $reconciliation->year }} - Triwulan {{ $reconciliation->quarter }}
            </div>
        </div>
        <div>
            <a href="{{ route('reconciliations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition text-sm">
                &larr; Kembali
            </a>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                {{-- Loop Total Kuantitas --}}
                @foreach($totalPerSatuan as $t)
                    {{-- Cek: Jika volume 0, jangan tampilkan kartu --}}
                    @if($t->total_volume <= 0) 
                        @continue 
                    @endif

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-gray-500 text-sm font-medium uppercase">Total {{ $t->satuan == '-' ? 'LAINNYA' : $t->satuan }}</div>
                        <div class="text-2xl font-bold text-gray-900 mt-2">
                            <?= rtrim(rtrim(number_format($t->total_volume, 3, ',', '.'), '0'), ',') ?>
                            <span class="text-sm text-gray-400 font-normal">{{ $t->satuan == '-' ? '' : $t->satuan }}</span>
                        </div>
                    </div>
                @endforeach

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-medium">Total Nilai LHP</div>
                    <div class="text-2xl font-bold text-yellow-600 mt-2">
                        <span class="text-sm text-gray-500 font-normal">Rp</span>
                         {{ number_format($statsJenis->sum('total_nilai'), 0, ',', '.') }}
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm font-medium">Cakupan</div>
                    <div class="mt-2">
                         <span class="text-xl font-bold text-indigo-600">{{ $statsWilayah->count() }}</span> <span class="text-xs text-gray-500">Wilayah</span>
                         <span class="mx-2 text-gray-300">|</span>
                         <span class="text-xl font-bold text-green-600">{{ $statsJenis->count() }}</span> <span class="text-xs text-gray-500">Jenis</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg lg:col-span-1">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="font-bold text-gray-800">Rekap Jenis Hasil Hutan</h3>
                    </div>
                    <div class="p-4 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                             <thead>
                                <tr class="border-b">
                                    <th class="py-2">Jenis</th>
                                    <th class="py-2 text-center">Sat</th> <th class="py-2 text-right">Volume</th>
                                    <th class="py-2 text-right">Count</th>
                                </tr>
                             </thead>
                             <tbody>
                                @foreach($statsJenis as $s)
                                <tr class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="py-2 font-medium">{{ $s->label }}</td>
                                    <td class="py-2 text-center text-xs bg-gray-100 rounded">{{ $s->satuan }}</td>
                                    <td class="py-2 text-right">{{ number_format($s->total_volume, 2, ',', '.') }}</td>
                                    <td class="py-2 text-right text-gray-500">{{ $s->count }}</td>
                                </tr>
                                @endforeach
                             </tbody>
                        </table>
                    </div>
                </div>

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
                                    <td class="py-2 text-right">{{ number_format($s->total_nilai, 0, ',', '.') }}</td>
                                    <td class="py-2 text-right text-gray-500">{{ $s->count }}</td>
                                </tr>
                                @endforeach
                             </tbody>
                        </table>
                    </div>
                </div>
                
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Detail Data Transaksi ({{ $details->total() }} baris)</h3>
                    <div class="flex items-center space-x-2">
                         <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Menampilkan {{ $details->count() }} data per halaman</span>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
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
                        @include('reconciliations.partials.table')
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
                // Add loading indicator
                dataContainer.style.opacity = '0.5';
                
                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    dataContainer.innerHTML = html;
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
                
                // Merge existing sort params from URL if not in form
                const currentUrlParams = new URLSearchParams(window.location.search);
                if (currentUrlParams.has('sort')) params.set('sort', currentUrlParams.get('sort'));
                if (currentUrlParams.has('direction')) params.set('direction', currentUrlParams.get('direction'));
                
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
                
                // Show/Hide reset button
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
                
                // Clear search from URL but keep sort? usually reset clears filter.
                // Let's clear search param.
                const params = new URLSearchParams(window.location.search);
                params.delete('search');
                
                const newUrl = `${window.location.pathname}?${params.toString()}`;
                window.history.pushState({}, '', newUrl);
                
                fetchData(newUrl);
            });

            // Event Delegation for Sort and Pagination Links
            dataContainer.addEventListener('click', function(e) {
                // Find closest anchor tag
                const link = e.target.closest('a');
                if (!link) return;

                // Check if it is a sort link or pagination link (usually logic is: if it's an anchor in this container, we should hijack it unless external)
                // Safest check: Same origin and has query params relevant to us
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
                // Sync search input
                const params = new URLSearchParams(window.location.search);
                filterForm.querySelector('[name="search"]').value = params.get('search') || '';
                if(params.get('search')) resetBtn.classList.remove('hidden'); 
                else resetBtn.classList.add('hidden');
            });
        });
    </script>
@endsection
