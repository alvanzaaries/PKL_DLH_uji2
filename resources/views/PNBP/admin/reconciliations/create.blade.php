@extends('PNBP.layouts.admin')

@section('title', 'Upload Rekonsiliasi Baru - Pelaporan PNBP')
@section('header', 'Upload Rekonsiliasi Baru')

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Card Container --}}
    <div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
        
        {{-- Header Card --}}
        <div class="bg-primary px-6 py-4">
            <h2 class="text-xl font-bold text-white flex items-center">
                <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Upload Data Rekonsiliasi Triwulan
            </h2>
        </div>
        
        <div class="p-8">
            <p class="mb-6 text-gray-600 dark:text-gray-400">
                Silakan unggah file Excel hasil rekonsiliasi. Pastikan file memiliki format yang sesuai sebelum diunggah.
            </p>

            {{-- Error Alert --}}
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

            <form id="uploadForm" action="{{ route('reconciliations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Input Tahun --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tahun Periode</label>
                        <input type="number" name="year" value="{{ old('year', date('Y')) }}" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/50 py-2 px-3 border" 
                            placeholder="Contoh: 2024">
                    </div>

                    {{-- Input Triwulan --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Triwulan</label>
                        <select name="quarter" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/50 py-2 px-3 border bg-white">
                            <option value="1" {{ old('quarter') == 1 ? 'selected' : '' }}>Triwulan I</option>
                            <option value="2" {{ old('quarter') == 2 ? 'selected' : '' }}>Triwulan II</option>
                            <option value="3" {{ old('quarter') == 3 ? 'selected' : '' }}>Triwulan III</option>
                            <option value="4" {{ old('quarter') == 4 ? 'selected' : '' }}>Triwulan IV</option>
                        </select>
                    </div>

                    {{-- Input KPH --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">KPH</label>
                        <input type="text" name="kph" value="{{ old('kph') }}" list="kph-list" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary focus:ring focus:ring-primary/50 py-2 px-3 border" 
                            placeholder="Ketik / pilih KPH" required>
                        <datalist id="kph-list">
                            @foreach(($kphOptions ?? []) as $kph)
                                <option value="{{ $kph }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                {{-- Upload Area --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File Excel (.xlsx, .xls, .csv)</label>
                    
                    {{-- REMOVED: class "transition" --}}
                    <div id="drop-zone" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-md hover:bg-gray-50 dark:hover:bg-gray-800/50 relative cursor-pointer bg-white dark:bg-gray-800">
                        
                        {{-- Empty State --}}
                        <div id="empty-state" class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                <span class="font-medium text-primary hover:text-primary_hover focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                    Klik untuk pilih file
                                </span>
                                <p class="pl-1">atau drag and drop</p>
                                <input id="file-upload" name="file" type="file" class="sr-only" accept=".xlsx,.xls,.csv" required>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Excel hingga 2MB</p>
                        </div>

                        {{-- File Selected State --}}
                        <div id="file-info" class="hidden text-center w-full">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primary/10 mb-3">
                                <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white" id="selected-filename">filename.xlsx</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="selected-filesize">0 KB</p>
                            
                            {{-- REMOVED: class "transition-colors" --}}
                            <button type="button" id="remove-file" class="mt-3 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Ganti File
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    const dropZone = document.getElementById('drop-zone');
                    const fileInput = document.getElementById('file-upload');
                    const emptyState = document.getElementById('empty-state');
                    const fileInfo = document.getElementById('file-info');
                    const filenameDisplay = document.getElementById('selected-filename');
                    const filesizeDisplay = document.getElementById('selected-filesize');
                    const removeBtn = document.getElementById('remove-file');

                    // Classes untuk state aktif/sukses (Tema Primary)
                    const activeClasses = ['border-primary', 'bg-primary/5', 'dark:bg-primary/10'];
                    const defaultBorder = ['border-gray-300', 'dark:border-gray-600'];

                    function updateUI(file) {
                        if (file) {
                            emptyState.classList.add('hidden');
                            fileInfo.classList.remove('hidden');
                            filenameDisplay.textContent = file.name;
                            filesizeDisplay.textContent = (file.size / 1024).toFixed(2) + ' KB';
                            
                            dropZone.classList.add(...activeClasses);
                            dropZone.classList.remove(...defaultBorder);
                        } else {
                            resetUI();
                        }
                    }

                    function resetUI() {
                        fileInput.value = '';
                        emptyState.classList.remove('hidden');
                        fileInfo.classList.add('hidden');
                        
                        dropZone.classList.remove(...activeClasses);
                        dropZone.classList.add(...defaultBorder);
                    }

                    fileInput.addEventListener('change', function(e) {
                        if (this.files.length > 0) {
                            updateUI(this.files[0]);
                        }
                    });

                    removeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        resetUI();
                    });

                    dropZone.addEventListener('click', function(e) {
                        if (e.target && (e.target.id === 'remove-file' || (e.target.closest && e.target.closest('#remove-file')))) {
                            return;
                        }
                        fileInput.click();
                    });

                    // Drag and Drop Events
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        dropZone.addEventListener(eventName, preventDefaults, false);
                    });

                    function preventDefaults(e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    ['dragenter', 'dragover'].forEach(eventName => {
                        dropZone.addEventListener(eventName, highlight, false);
                    });

                    ['dragleave', 'drop'].forEach(eventName => {
                        dropZone.addEventListener(eventName, unhighlight, false);
                    });

                    function highlight(e) {
                        dropZone.classList.add(...activeClasses);
                    }

                    function unhighlight(e) {
                        if (fileInput.files.length === 0) {
                            dropZone.classList.remove(...activeClasses);
                        }
                    }

                    dropZone.addEventListener('drop', handleDrop, false);

                    function handleDrop(e) {
                        const dt = e.dataTransfer;
                        const files = dt.files;
                        if (files.length > 0) {
                            fileInput.files = files;
                            updateUI(files[0]);
                        }
                    }
                </script>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                    {{-- REMOVED: class "transition-colors" --}}
                    <a href="{{ route('reconciliations.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Batal
                    </a>
                    
                    {{-- REMOVED: class "transition-colors" --}}
                    <button id="uploadBtn" type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary_hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg id="uploadSpinner" class="hidden animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="uploadBtnText">Upload Data</span>
                    </button>
                </div>
            </form>

            <script>
                (function () {
                    const form = document.getElementById('uploadForm');
                    const btn = document.getElementById('uploadBtn');
                    const spinner = document.getElementById('uploadSpinner');
                    const text = document.getElementById('uploadBtnText');
                    if (!form || !btn || !spinner || !text) return;

                    form.addEventListener('submit', function () {
                        btn.disabled = true;
                        btn.classList.add('opacity-70', 'pointer-events-none');
                        spinner.classList.remove('hidden');
                        text.textContent = 'Mengupload…';
                        form.setAttribute('aria-busy', 'true');
                    });
                })();
            </script>
        </div>
    </div>
</div>
@endsection