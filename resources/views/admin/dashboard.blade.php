@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Overview Statistik')

@section('content')
    {{-- Filter Section --}}
    <div class="mb-8 bg-white p-4 rounded-lg shadow-sm">
        <form method="GET" action="{{ route('dashboard.index') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Data</label>
                <select name="year" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2 border">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">KPH</label>
                <select name="kph" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2 border">
                    <option value="">Semua KPH</option>
                    @foreach(($availableKph ?? []) as $kph)
                        <option value="{{ $kph }}" {{ request('kph') == $kph ? 'selected' : '' }}>{{ $kph }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                <select name="wilayah" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2 border">
                    <option value="">Semua Wilayah</option>
                    @foreach(($availableWilayah ?? []) as $wil)
                        <option value="{{ $wil }}" {{ request('wilayah') == $wil ? 'selected' : '' }}>{{ $wil }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1 w-full md:w-auto">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter Waktu (Triwulan)</label>
                
                <select id="combined_quarter_select" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-2 border">
                    <option value="">Semua Waktu</option>
                    
                    {{-- Grup 1: Triwulan Spesifik --}}
                    <optgroup label="Triwulan Spesifik (Hanya TW itu)">
                        @foreach($availableQuarters as $q)
                            <option value="q-{{ $q }}" {{ request('quarter') == $q ? 'selected' : '' }}>
                                Triwulan {{ $q }}
                            </option>
                        @endforeach
                    </optgroup>

                    {{-- Grup 2: Sampai Dengan --}}
                    <optgroup label="Akumulasi (Sampai Dengan)">
                        @foreach($availableQuarters as $q)
                            <option value="s-{{ $q }}" {{ request('sampai_quarter') == $q ? 'selected' : '' }}>
                                Sampai Dengan TW {{ $q }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>

                <input type="hidden" name="quarter" id="input_quarter" value="{{ request('quarter') }}">
                <input type="hidden" name="sampai_quarter" id="input_sampai_quarter" value="{{ request('sampai_quarter') }}">
            </div>
            <div class="flex-none">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                    <i class="fas fa-filter mr-2"></i> Filter Data
                </button>
                @if(request('year') || request('kph') || request('wilayah') || request('quarter') || request('sampai_quarter'))
                    <a href="{{ route('dashboard.index') }}" class="ml-2 text-gray-600 hover:text-gray-900 border px-3 py-2 rounded-md">Reset</a>
                @endif
            </div>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const comboSelect = document.getElementById('combined_quarter_select');
                const inputQuarter = document.getElementById('input_quarter');
                const inputSampai = document.getElementById('input_sampai_quarter');

                comboSelect.addEventListener('change', function () {
                    const val = this.value; 

                    inputQuarter.value = '';
                    inputSampai.value = '';

                    if (val) {
                        const parts = val.split('-'); 
                        const type = parts[0];
                        const number = parts[1]; 

                        if (type === 'q') {
                            inputQuarter.value = number;
                        } else if (type === 's') {
                            inputSampai.value = number;
                        }
                    }
                });
            });
        </script>
    </div>

    {{-- Infographic Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Files --}}
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3 text-blue-600">
                    <i class="fas fa-file-excel text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 truncate">Total File Rekonsiliasi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalFiles) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Setor --}}
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500 col-span-1 lg:col-span-2">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-full p-3 text-green-600">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Nilai Setor (PNBP)</p>
                    <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($financials->total_setor ?? 0, 0, ',', '.') }}</p>
                    <div class="flex gap-4 mt-1 text-xs text-gray-500">
                        <span><i class="fas fa-receipt mr-1"></i> Billing: {{ number_format($financials->total_billing ?? 0, 0, ',', '.') }}</span>
                        <span><i class="fas fa-file-invoice-dollar mr-1"></i> LHP: {{ number_format($financials->total_lhp ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Volume --}}
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3 text-yellow-600">
                    <i class="fas fa-cubes text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Volume Produksi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($financials->total_volume ?? 0, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Stats Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Chart/List: Top Wilayah --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Total {{ number_format($wilayahCount ?? 0) }} Wilayah (Kabupaten/Kota)</h3>
            </div>
            <div class="p-5">
                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                    @forelse($topWilayah as $idx => $wil)
                        <div class="flex items-center">
                            <span class="flex-shrink-0 h-6 w-6 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold mr-3">
                                {{ $idx + 1 }}
                            </span>
                            <div class="flex-1">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $wil->wilayah }}</span>
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($wil->total, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    {{-- Calculate percentage relative to total_setor for visual bar --}}
                                    @php
                                        $percent = ($financials->total_setor > 0) ? ($wil->total / $financials->total_setor) * 100 : 0;
                                    @endphp
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Belum ada data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Table: Stats by Jenis --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-5 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Statistik per Jenis SDH</h3>
            </div>
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis SDH</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Volume</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Setor</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($statsJenis as $jenis)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $jenis->jenis_sdh }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ number_format($jenis->total_vol, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 font-bold">Rp {{ number_format($jenis->total_setor, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
