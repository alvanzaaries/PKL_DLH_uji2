@extends('laporan.layouts.dashboard')
@section('title', 'Pratinjau Laporan')
@section('page-title', 'Pratinjau Laporan')

@section('content')

<div class="bg-white rounded-xl shadow-lg border-t-4 border-blue-600 p-8 max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="border-b border-gray-100 pb-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
            <span class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                <i class="fas fa-eye"></i>
            </span>
            Preview Data Laporan
        </h2>
        <p class="text-gray-500 text-sm mt-2 pl-12">
            Periksa data di bawah ini sebelum menyimpan ke database
        </p>
    </div>

    <!-- Metadata Info -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <p class="text-xs text-gray-500 font-medium">Jenis Laporan</p>
                <p class="text-sm font-semibold text-gray-800">{{ $metadata['jenis_laporan'] }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Periode</p>
                <p class="text-sm font-semibold text-gray-800">
                    {{ ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][$metadata['bulan']] }} 
                    {{ $metadata['tahun'] }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Total Data</p>
                <p class="text-sm font-semibold text-emerald-600">{{ $data['total'] ?? 0 }} baris</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium">Status Validasi</p>
                @if(empty($data['errors']))
                    <p class="text-sm font-semibold text-emerald-600">
                        <i class="fas fa-check-circle"></i> Semua Valid
                    </p>
                @else
                    <p class="text-sm font-semibold text-red-600">
                        <i class="fas fa-exclamation-circle"></i> {{ count($data['errors']) }} Error
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Error Messages -->
    @if(!empty($data['errors']))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
                <div class="flex-1">
                    <h3 class="text-red-800 font-semibold mb-2">Ditemukan Error Validasi:</h3>
                    <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                        @foreach($data['errors'] as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Preview Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @foreach($data['headers'] ?? [] as $header)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($data['rows'] ?? [] as $index => $row)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-blue-50 transition-colors">
                        @foreach($row as $cell)
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                {{ $cell }}
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="100" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p>Tidak ada data untuk ditampilkan</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if(($data['total'] ?? 0) > count($data['rows'] ?? []))
            <div class="bg-blue-50 px-4 py-3 text-sm text-blue-800 border-t border-blue-200">
                <i class="fas fa-info-circle mr-2"></i>
                Menampilkan {{ count($data['rows'] ?? []) }} dari <strong>{{ $data['total'] ?? 0 }}</strong> total baris data
            </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <form action="{{ route('laporan.store') }}" method="POST" class="mt-6">
        @csrf
        
        <!-- Hidden inputs untuk metadata -->
        <input type="hidden" name="industri_id" value="{{ $metadata['industri_id'] }}">
        <input type="hidden" name="bulan" value="{{ $metadata['bulan'] }}">
        <input type="hidden" name="tahun" value="{{ $metadata['tahun'] }}">
        <input type="hidden" name="jenis_laporan" value="{{ $metadata['jenis_laporan'] }}">
        <input type="hidden" name="confirmed_preview" value="1">

        <div class="flex justify-end gap-3 pt-4 border-gray-100">
            <a href="{{ route('industri.laporan', $metadata['industri_id']) }}" 
                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Upload
            </a>
            
            @if(empty($data['errors']))
                <button type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 shadow-md hover:shadow-lg transition-all">
                    <i class="fas fa-save mr-2"></i> Konfirmasi & Simpan
                </button>
            @else
                <button type="button" disabled
                    class="px-5 py-2.5 text-sm font-medium text-white bg-gray-400 rounded-lg cursor-not-allowed">
                    <i class="fas fa-ban mr-2"></i> Perbaiki Error Dahulu
                </button>
            @endif
        </div>
    </form>

</div>

@endsection
