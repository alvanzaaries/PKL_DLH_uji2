@extends('laporan.layouts.layout')
@section('title', 'Unduh Tanda Terima')
@section('page-title', 'Unduh Tanda Terima')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl mb-4">
                    <span class="block text-green-700">Unduh Tanda Terima</span>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
                    Akses bukti pelaporan digital Anda dengan mudah dan cepat.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Left Side: Form -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100 relative">
                        <!-- Decorative top bar -->
                        <div class="h-2 bg-gradient-to-r from-green-500 to-green-700"></div>

                        <div class="p-8">
                            <div class="flex items-center gap-3 mb-8">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-lg">
                                    1
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Cari Data Laporan</h3>
                            </div>

                            @if($error)
                                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-8 rounded-r-lg shadow-sm animate-fade-in-down">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="text-sm font-medium text-red-800">Verifikasi Gagal</h3>
                                            <div class="mt-1 text-sm text-red-700">
                                                {{ $error }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('laporan.bukti') }}" class="space-y-8">
                                @csrf

                                <!-- Step 1: Industry -->
                                <div class="relative group">
                                    <label for="industri_id" class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider text-xs">
                                        Perusahaan / Industri
                                    </label>
                                    <div class="relative">
                                        <select name="industri_id" id="industri_id" class="w-full" required>
                                            <option value="">-- Cari Nama Industri --</option>
                                            @foreach($industries as $industri)
                                                <option value="{{ $industri->id }}" {{ old('industri_id', $selectedIndustri?->id) == $industri->id ? 'selected' : '' }}>
                                                    {{ $industri->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Step 2: Period -->
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider text-xs">Bulan</label>
                                        <select name="bulan" id="bulan"
                                            class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-xl transition-shadow shadow-sm hover:shadow-md"
                                            required>
                                            <option value="">Pilih Bulan</option>
                                            @foreach($months as $num => $nama)
                                                <option value="{{ $num }}" {{ old('bulan', $selectedBulan) == $num ? 'selected' : '' }}>
                                                    {{ $nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="group">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wider text-xs">Tahun</label>
                                        <select name="tahun" id="tahun"
                                            class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-xl transition-shadow shadow-sm hover:shadow-md"
                                            required>
                                            <option value="">Pilih Tahun</option>
                                            @foreach($years as $year)
                                                <option value="{{ $year }}" {{ old('tahun', $selectedTahun) == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Step 3: Verification -->
                                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                                    <label for="nomor_izin" class="block text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wider text-xs">
                                        <i class="fas fa-shield-alt text-green-600 mr-1"></i> Kunci Verifikasi
                                    </label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-id-card text-gray-400"></i>
                                        </div>
                                        <input type="text" name="nomor_izin" id="nomor_izin"
                                            class="focus:ring-green-500 focus:border-green-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-lg py-3 hover:shadow-md transition-shadow"
                                            placeholder="Masukkan Nomor Izin Industri"
                                            value="{{ old('nomor_izin') }}" required autocomplete="off">
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        Masukkan Nomor Izin yang terdaftar pada sistem untuk verifikasi.
                                    </p>
                                </div>

                                <div class="pt-4">
                                    <button type="submit"
                                        class="w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-search mr-2"></i>
                                        Cari Bukti Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Result or Info -->
                <div class="lg:col-span-5">
                    @if($verified)
                        <!-- Success Card: Ticket Style -->
                        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200 transform transition-all duration-500 animate-fade-in-up">
                            <div class="bg-[#1A4030] p-6 text-white relative overflow-hidden">
                                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                                <div class="absolute bottom-0 left-0 -ml-4 -mb-4 w-20 h-20 bg-green-500/20 rounded-full blur-xl"></div>

                                <div class="relative z-10 flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold opacity-90">Tanda Terima Digital</h3>
                                        <p class="text-green-100 text-sm mt-1">Verifikasi Berhasil</p>
                                    </div>
                                    <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                                        <i class="fas fa-check-circle text-2xl text-green-400"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Jagged line effect using CSS or SVG could go here, simulating a receipt -->
                            <div class="relative h-4 bg-[#1A4030]">
                                <svg class="absolute bottom-0 w-full h-3 text-white fill-current" preserveAspectRatio="none" viewBox="0 0 100 10">
                                    <path d="M0 10 L5 0 L10 10 L15 0 L20 10 L25 0 L30 10 L35 0 L40 10 L45 0 L50 10 L55 0 L60 10 L65 0 L70 10 L75 0 L80 10 L85 0 L90 10 L95 0 L100 10 Z"></path>
                                </svg>
                            </div>

                            <div class="p-8 pb-10 bg-white relative">
                                <!-- Dashed vertical lines decoration -->
                                <div class="absolute top-0 bottom-0 left-8 border-l-2 border-dashed border-gray-100"></div>

                                <div class="ml-8 space-y-6">
                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Industri</p>
                                        <h4 class="text-xl font-bold text-gray-900 mt-1">{{ $selectedIndustri->nama }}</h4>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Periode</p>
                                            <p class="text-base font-medium text-gray-800 mt-1">{{ (is_scalar($selectedBulan) && isset($months[$selectedBulan])) ? $months[$selectedBulan] : '-' }} {{ $selectedTahun }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status</p>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                                Selesai
                                            </span>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Dokumen Tercakup</p>
                                        <ul class="text-sm text-gray-600 space-y-2">
                                            @foreach($jenisLaporanList as $jenis)
                                                <li class="flex items-start">
                                                    <i class="fas fa-check text-green-500 mt-1 mr-2 text-xs"></i>
                                                    <span>{{ $jenis }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="pt-6 border-t border-dashed border-gray-200">
                                        <form action="{{ route('laporan.bukti.view') }}" method="POST" target="_blank" id="viewReceiptForm">
                                            @csrf
                                            <input type="hidden" name="industri_id" value="{{ $selectedIndustri->id }}">
                                            <input type="hidden" name="bulan" value="{{ $selectedBulan }}">
                                            <input type="hidden" name="tahun" value="{{ $selectedTahun }}">
                                            <button type="submit"
                                                class="w-full flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-sm font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                                                <i class="fas fa-external-link-alt mr-2 text-green-600"></i>
                                                Buka Tanda Terima
                                            </button>
                                        </form>
                                        <p class="text-center text-xs text-gray-400 mt-3">Gunakan Ctrl + P untuk simpan PDF</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Info / Empty State -->
                        <div class="h-full flex flex-col justify-center items-center text-center p-8 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                            <div class="w-24 h-24 bg-white rounded-full shadow-sm flex items-center justify-center mb-6">
                                <i class="fas fa-file-invoice text-4xl text-gray-300"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Instruksi Pengunduhan</h3>
                            <p class="text-gray-500 max-w-xs mx-auto mb-8">
                                Silakan lengkapi formulir di sebelah kiri untuk mencari dan mengunduh tanda terima laporan Anda.
                            </p>

                            <div class="text-left w-full max-w-xs space-y-4">
                                <div class="flex items-center gap-3 p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">1</div>
                                    <span class="text-sm text-gray-600 font-medium">Pilih Industri & Periode</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">2</div>
                                    <span class="text-sm text-gray-600 font-medium">Masukkan Kunci Verifikasi</span>
                                </div>
                                <div class="flex items-center gap-3 p-3 bg-white rounded-lg shadow-sm border border-gray-100">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs">3</div>
                                    <span class="text-sm text-gray-600 font-medium">Download / Cetak Bukti</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('laporan.landing') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Glassmorphism Theme for Select2 */
        .select2-container--default .select2-selection--single {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 1rem; /* rounded-2xl look */
            height: 54px;
            display: flex;
            align-items: center;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05), 
                0 2px 4px -1px rgba(0, 0, 0, 0.03),
                inset 0 0 0 1px rgba(255, 255, 255, 0.5); /* Inner light border */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hover Effect */
        .select2-container--default .select2-selection--single:hover {
            background: rgba(255, 255, 255, 0.4);
            box-shadow: 
                0 10px 15px -3px rgba(0, 0, 0, 0.08), 
                0 4px 6px -2px rgba(0, 0, 0, 0.04),
                inset 0 0 0 1px rgba(255, 255, 255, 0.6);
            transform: translateY(-1px);
        }

        /* Focus Effect - Glowing Green Ring */
        .select2-container--default .select2-selection--single:focus-within {
            outline: none;
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(16, 185, 129, 0.5);
            box-shadow: 
                0 0 0 4px rgba(16, 185, 129, 0.1),
                0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 52px;
            right: 14px;
        }
        
        /* Rendered Text */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 20px;
            color: #1F2937; /* gray-800 */
            font-weight: 500;
            padding-right: 60px; /* Space for clear btn and arrow */
        }

        /* Clear Button Position */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute;
            right: 40px; /* Position to the left of the arrow */
            top: 50%;
            transform: translateY(-50%);
            margin-right: 0;
            z-index: 10;
            color: #9CA3AF;
            font-size: 18px;
            font-weight: bold;
            height: auto;
            line-height: 1;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #EF4444; /* red-500 */
        }

        .select2-dropdown {
            border-color: #E5E7EB;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 8px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
        }

        .select2-search--dropdown {
            padding: 8px;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-radius: 0.5rem;
            border-color: #D1D5DB;
            padding: 8px 12px;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #10B981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        .select2-results__option {
            padding: 10px 12px;
            border-radius: 0.5rem;
            margin-bottom: 2px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #ECFDF5;
            color: #047857;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #D1FAE5;
            color: #065F46;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translate3d(0, 20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        .animate-fade-in-down {
            animation-name: fadeInDown;
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }

        .animate-fade-in-up {
            animation-name: fadeInUp;
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#industri_id').select2({
                placeholder: '-- Cari dan Pilih Industri --',
                allowClear: true,
                width: '100%'
            });

            // Initialize Select2 for Bulan and Tahun (Glass Style)
            $('#bulan, #tahun').select2({
                minimumResultsForSearch: Infinity, // Hide search box
                width: '100%'
            });
        });
    </script>
@endpush