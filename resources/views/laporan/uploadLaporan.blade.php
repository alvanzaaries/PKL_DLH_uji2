@extends('laporan/layouts.layout')

@section('title', 'Input Laporan')

@section('page-title', 'Input Laporan Baru')

@section('content')

    <style>
        .btn-primary {
            background-color: #1A4030;
            color: white;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .btn-primary:hover { background-color: #2E5444; }
        
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
            border-color: #1A4030;
            --tw-ring-color: rgba(26, 64, 48, 0.2);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(3px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .dropzone-active {
            border-color: #1A4030 !important;
            background-color: #F1FDF4 !important;
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
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('pelaporan.index') }}" class="text-gray-400 hover:text-[#1A4030] transition-colors" title="Kembali">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 font-sans tracking-tight">Upload Laporan Baru</h2>
                    <p class="text-sm text-gray-500">Formulir upload laporan dengan pilihan perusahaan</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 shadow-sm rounded-md mb-8">
            <div class="px-6 py-4 border-bottom border-gray-200 bg-gray-50 rounded-t-md">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-upload text-[#1A4030]"></i>
                    Formulir Upload Laporan
                </h3>
            </div>

            <form id="uploadForm" action="{{ route('laporan.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="p-6">
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Pilih Perusahaan</label>
                        <select name="industri_id" id="industri_id" required class="w-full form-input px-3 py-2 border text-sm">
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach ($industries as $industri)
                                <option value="{{ $industri->id }}" {{ old('industri_id') == $industri->id ? 'selected' : '' }}>
                                    {{ $industri->nama }} - {{ $industri->kabupaten }}
                                </option>
                            @endforeach
                        </select>
                        @error('industri_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Bulan</label>
                            <select name="bulan" id="bulan" required class="w-full form-input px-3 py-2 border text-sm">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ (old('bulan') ?? date('n')) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Tahun</label>
                            <select name="tahun" id="tahun" required class="w-full form-input px-3 py-2 border text-sm">
                                @php
                                    $currentYear = date('Y');
                                    $startYear = 2026;
                                @endphp
                                @for ($y = $currentYear; $y >= $startYear; $y--)
                                    <option value="{{ $y }}" {{ (old('tahun') ?? $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Jenis Dokumen</label>
                            <select name="jenis_laporan" id="jenis_laporan" required class="w-full form-input px-3 py-2 border text-sm">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach (\App\Models\Laporan::JENIS_LAPORAN as $jenis)
                                    <option value="{{ $jenis }}" {{ old('jenis_laporan') == $jenis ? 'selected' : '' }}>
                                        {{ $jenis }}
                                    </option>
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
                            <p id="fileName" class="mt-3 text-[#1A4030] font-bold text-sm"></p>
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

@endsection
