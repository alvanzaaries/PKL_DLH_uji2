@extends('PNBP.layouts.app')

@section('title', 'Upload Rekonsiliasi - Pelaporan PNBP')

@section('content')
<div class="w-full max-w-3xl mx-auto">
    {{-- Form Upload Rekonsiliasi User --}}
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <div class="bg-green-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload Data Rekonsiliasi Triwulan
            </h2>
        </div>

        <div class="p-8">
            <p class="mb-6 text-gray-600">
                Silakan unggah file Excel hasil rekonsiliasi. Pastikan file memiliki format yang sesuai sebelum diunggah.
                <br>
                <a href="{{ asset('template/PNBP/Format Kertas Kerja Rekon PSDH Triwulan.xlsx') }}" download class="text-green-600 hover:underline font-medium inline-flex items-center mt-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download Format Excel
                </a>
            </p>

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-600 text-green-800 p-4 mb-6" role="alert">
                    <p class="font-bold">Berhasil</p>
                    <p class="text-sm mt-1">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Notifikasi Error --}}
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

            {{-- Form Upload --}}
            <form id="uploadForm" action="{{ route('reconciliations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Periode</label>
                        <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2 px-3 border" placeholder="Contoh: 2024" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Triwulan</label>
                        <select name="quarter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2 px-3 border bg-white" required>
                            <option value="1" {{ old('quarter') == 1 ? 'selected' : '' }}>Triwulan I</option>
                            <option value="2" {{ old('quarter') == 2 ? 'selected' : '' }}>Triwulan II</option>
                            <option value="3" {{ old('quarter') == 3 ? 'selected' : '' }}>Triwulan III</option>
                            <option value="4" {{ old('quarter') == 4 ? 'selected' : '' }}>Triwulan IV</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">KPH/KPS</label>
                        <select name="kph" class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50 py-2 px-3 border bg-white" required>
                            <option value="">-- Pilih KPH/KPS --</option>
                            @foreach(($kphOptions ?? []) as $kph)
                                <option value="{{ $kph }}" {{ old('kph') == $kph ? 'selected' : '' }}>{{ $kph }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Area Dropzone Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Excel (.xlsx, .xls, .csv)</label>

                    <div id="drop-zone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition relative cursor-pointer">
                        <div id="empty-state" class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <span class="font-medium text-green-600">Klik untuk pilih file</span>
                                <p class="pl-1">atau drag and drop</p>
                                <input id="file-upload" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                            </div>
                            <p class="text-xs text-gray-500">Excel hingga 2MB</p>
                        </div>

                        <div id="file-info" class="hidden text-center w-full">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900" id="selected-filename">filename.xlsx</h3>
                            <p class="text-xs text-gray-500 mt-1" id="selected-filesize">0 KB</p>
                            <button type="button" id="remove-file" class="mt-3 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Ganti File
                            </button>
                        </div>
                    </div>
                </div>

                <script src="{{ asset('js/pnbp/user/upload.js') }}"></script>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button id="uploadBtn" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg id="uploadSpinner" class="hidden animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="uploadBtnText">Upload Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
