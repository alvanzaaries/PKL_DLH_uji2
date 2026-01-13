@extends('laporan/layouts.dashboard')

@section('title', 'Laporan Perusahaan')

@section('page-title', 'Laporan Perusahaan')

@section('content')

    <style>
        .btn-primary {
            background-color: #1B5E20;
            color: white;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .btn-primary:hover { background-color: #2E7D32; }
        
        .btn-secondary {
            background-color: #F3F4F6;
            color: #1F2937;
            border: 1px solid #E5E7EB;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .btn-secondary:hover { background-color: #E5E7EB; }

        .form-input {
            border-radius: 4px;
            border-color: #D1D5DB;
        }
        .form-input:focus {
            border-color: #1B5E20;
            --tw-ring-color: rgba(27, 94, 32, 0.2);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .dropzone-active {
            border-color: #1B5E20 !important;
            background-color: #F1FDF4 !important;
        }

        /* Institutional Badge Style */
        .badge {
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid transparent;
        }
        .badge-outline {
            background: white;
            border-color: #E5E7EB;
            color: #374151;
        }
        .badge-primary {
            background: #F1FDF4;
            border-color: #1B5E20;
            color: #1B5E20;
        }
    </style>

    <div class="max-w-7xl mx-auto">

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-700 p-4 mb-6 shadow-sm flex items-start">
                <i class="fas fa-exclamation-circle text-red-700 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-bold text-sm text-red-800">Kesalahan</h3>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <div class="bg-white border border-gray-200 shadow-sm rounded-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('data.industri') }}" class="text-gray-400 hover:text-[#1B5E20] transition-colors" title="Kembali">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 font-sans tracking-tight">{{ $industri->nama }}</h2>
                            <p class="text-sm text-gray-500">No Izin: {{ $industri->nomor_izin ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6 border-t border-gray-100 pt-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-gray-50 rounded-sm text-gray-500">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Lokasi</p>
                                <p class="text-sm font-medium text-gray-900">{{ $industri->kabupaten }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-gray-50 rounded-sm text-gray-500">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Arsip</p>
                                <p class="text-sm font-medium text-gray-900">{{ $laporans->total() }} Dokumen</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-gray-50 rounded-sm text-gray-500">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Update Terakhir</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $laporans->first() ? $laporans->first()->created_at->format('d M Y') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-md mb-8">
            <div class="px-6 py-4 border-bottom border-gray-200 bg-gray-50 rounded-t-md">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-upload text-[#1B5E20]"></i>
                    Formulir Upload Laporan
                </h3>
            </div>

            <form id="uploadForm" action="{{ route('laporan.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="industri_id" value="{{ $industri->id }}">

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Bulan</label>
                            <select name="bulan" id="bulan" required class="w-full form-input px-3 py-2 border text-sm">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tahun</label>
                            <select name="tahun" id="tahun" required class="w-full form-input px-3 py-2 border text-sm">
                                @for ($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Jenis Dokumen</label>
                            <select name="jenis_laporan" id="jenis_laporan" required class="w-full form-input px-3 py-2 border text-sm">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach (\App\Models\Laporan::JENIS_LAPORAN as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">File Excel (.xlsx/.xls)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-md p-8 text-center transition-all cursor-pointer hover:bg-gray-50" id="dropZone">
                            <i class="fas fa-file-excel text-3xl text-gray-400 mb-3"></i>
                            <p class="text-sm font-medium text-gray-700 mb-1">Klik untuk memilih file atau tarik file ke sini</p>
                            <p class="text-xs text-gray-500">Maksimal ukuran file 5MB</p>
                            <input type="file" name="file_excel" id="excelFile" accept=".xlsx,.xls" class="hidden" required>
                            <p id="fileName" class="mt-3 text-[#1B5E20] font-bold text-sm"></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-gray-50 border border-gray-200 rounded-sm mb-6">
                        <i class="fas fa-info-circle text-gray-500 mt-0.5 text-sm"></i>
                        <div class="text-xs text-gray-600">
                            <strong>Catatan Sistem:</strong> Pastikan format header Excel sesuai dengan template standar Dinas. Laporan yang sudah diupload tidak dapat diedit secara parsial, harus diupload ulang sepenuhnya.
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2 border-t border-gray-100">
                        <button type="submit" class="btn-primary px-5 py-2.5 text-sm font-semibold flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            Preview & Validasi
                        </button>
                        <button type="reset" class="btn-secondary px-5 py-2.5 text-sm font-semibold">
                            Reset Form
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <script>
            const dropZone = document.getElementById('dropZone');
            const excelFile = document.getElementById('excelFile');
            const fileName = document.getElementById('fileName');

            dropZone.addEventListener('click', () => excelFile.click());

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('dropzone-active');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('dropzone-active');
                });
            });

            dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    excelFile.files = files;
                    updateFileName(files[0]);
                }
            });

            excelFile.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    updateFileName(e.target.files[0]);
                }
            });

            function updateFileName(file) {
                fileName.innerHTML = `<i class="fas fa-check mr-1"></i> ${file.name}`;
            }
        </script>

        <div class="bg-white border border-gray-200 shadow-sm rounded-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-base font-bold text-gray-800">
                    <i class="fas fa-history text-gray-500 mr-2"></i>
                    Riwayat Pelaporan
                </h3>
            </div>

            @if ($laporans->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Perusahaan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis Dokumen</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl Upload</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Volume Data</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($laporans as $laporan)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ ($laporans->currentPage() - 1) * $laporans->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        {{ $laporan->industri->nama ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        {{ $laporan->jenis_laporan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($laporan->tanggal)->translatedFormat('F Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $laporan->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @php
                                            // Menghitung total data (Logic tetap sama, styling disederhanakan)
                                            $totalData = 0;
                                            switch ($laporan->jenis_laporan) {
                                                case 'Laporan Penerimaan Kayu Bulat':
                                                    $totalData = \App\Models\laporan_penerimaan_kayu_bulat::where('laporan_id', $laporan->id)->count(); break;
                                                case 'Laporan Mutasi Kayu Bulat (LMKB)':
                                                    $totalData = \App\Models\laporan_mutasi_kayu_bulat::where('laporan_id', $laporan->id)->count(); break;
                                                case 'Laporan Penerimaan Kayu Olahan':
                                                    $totalData = \App\Models\laporan_penerimaan_kayu_olahan::where('laporan_id', $laporan->id)->count(); break;
                                                case 'Laporan Mutasi Kayu Olahan (LMKO)':
                                                    $totalData = \App\Models\laporan_mutasi_kayu_olahan::where('laporan_id', $laporan->id)->count(); break;
                                                case 'Laporan Penjualan Kayu Olahan':
                                                    $totalData = \App\Models\laporan_penjualan_kayu_olahan::where('laporan_id', $laporan->id)->count(); break;
                                            }
                                        @endphp
                                        <span class="badge badge-outline">
                                            {{ $totalData }} baris
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $laporans->links() }}
                </div>
            @else
                <div class="p-10 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3 text-gray-400">
                        <i class="fas fa-folder-open text-xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Data Tidak Ditemukan</h3>
                    <p class="text-xs text-gray-500 mt-1">Perusahaan ini belum memiliki riwayat pelaporan dokumen.</p>
                </div>
            @endif
        </div>

    </div>

@endsection