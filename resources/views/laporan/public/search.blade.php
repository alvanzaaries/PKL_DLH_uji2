@extends('laporan.layouts.layout')
@section('title', 'Download Bukti Laporan')
@section('page-title', 'Download Bukti Laporan')

@section('content')
    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-8">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4">
                    <i class="fas fa-file-invoice text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">Unduh Tanda Terima</h2>
                <p class="text-gray-500 mt-2">Masukkan informasi industri untuk mengakses dan mengunduh bukti tanda terima
                    laporan.</p>
            </div>

            @if($error)
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700 font-medium">
                                {{ $error }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('laporan.bukti') }}" class="space-y-6">
                @csrf

                <div class="space-y-6">
                    <!-- Industri Selection -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <label for="industri_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building text-green-600 mr-2"></i>Pilih Industri
                        </label>
                        <select name="industri_id" id="industri_id"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            required>
                            <option value="">-- Cari Nama Industri --</option>
                            @foreach($industries as $industri)
                                <option value="{{ $industri->id }}" {{ ($selectedIndustri && $selectedIndustri->id == $industri->id) ? 'selected' : '' }}>
                                    {{ $industri->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-2 ml-1">Pilih nama perusahaan atau industri Anda dari daftar.</p>
                    </div>

                    <!-- Verification Input -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <label for="penanggung_jawab" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-green-600 mr-2"></i>Verifikasi Keamanan
                        </label>
                        <div class="relative">
                            <input type="text" name="penanggung_jawab" id="penanggung_jawab"
                                class="w-full pl-4 pr-10 py-3 rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition-all font-medium"
                                placeholder="Masukkan Nama Penanggung Jawab / Direktur"
                                value="{{ old('penanggung_jawab') }}" required autocomplete="off">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                        </div>
                        <div
                            class="flex items-start mt-2 gap-2 text-xs text-gray-500 bg-blue-50 p-2 rounded text-blue-800 border border-blue-100">
                            <i class="fas fa-info-circle mt-0.5"></i>
                            <span>Masukkan nama lengkap Penanggung Jawab atau Direktur sesuai yang terdaftar di sistem
                                sebagai kunci verifikasi.</span>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit"
                        class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                        <i class="fas fa-search mr-2"></i>
                        Cari Bukti Laporan
                    </button>
                </div>
            </form>
        </div>

        @if($verified)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Hasil Pencarian</h3>
                        <p class="text-sm text-gray-500">{{ $selectedIndustri->nama }}</p>
                    </div>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1.5"></i> Terverifikasi
                    </span>
                </div>

                @if($laporans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis
                                        Laporan</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Periode</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Lapor</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($laporans as $laporan)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $laporan->jenis_laporan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('F Y') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $laporan->created_at->translatedFormat('d F Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('laporan.bukti.download', $laporan->id) }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition-all">
                                                <i class="fas fa-download mr-1.5"></i>
                                                Download PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 px-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                            <i class="far fa-folder-open text-xl"></i>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Tidak Ada Bukti Laporan</h3>
                        <p class="text-sm text-gray-500 mt-1">Belum ada laporan dengan bukti tanda terima digital untuk industri
                            ini.</p>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-8 text-center text-sm text-gray-500">
            <a href="{{ route('laporan.landing') }}"
                class="font-medium text-green-600 hover:text-green-500 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Custom Green Theme for Select2 */
        .select2-container--default .select2-selection--single {
            border-color: #D1D5DB;
            border-radius: 0.5rem;
            height: 42px;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single:focus {
            outline: none;
            border-color: #10B981;
            box-shadow: 0 0 0 1px #10B981;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        .select2-dropdown {
            border-color: #D1D5DB;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 0.375rem;
            border-color: #D1D5DB;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #10B981;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#industri_id').select2({
                placeholder: '-- Pilih Industri --',
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endpush