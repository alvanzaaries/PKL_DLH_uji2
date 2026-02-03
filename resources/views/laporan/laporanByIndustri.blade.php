@extends('laporan/layouts.layout')

@section('title', 'Laporan Perusahaan')

@section('page-title', 'Laporan Perusahaan')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/laporan/custom.css') }}">

    <div class="max-w-7xl mx-auto">

        <div class="bg-white border border-gray-200 shadow-sm rounded-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <a href="{{ route('laporan.monitoring') }}" class="text-gray-400 hover:text-[#1B5E20] transition-colors"
                            title="Kembali">
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

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-md mb-6 shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold">Berhasil!</p>
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-md mb-6 shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold">Terjadi Kesalahan!</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 px-6 py-4 rounded-md mb-6 shadow-sm" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold">Peringatan!</p>
                        <p class="text-sm">{{ session('warning') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white border border-gray-200 shadow-sm rounded-md mb-8">
            <div class="px-6 py-4 border-bottom border-gray-200 bg-gray-50 rounded-t-md">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-file-upload text-[#1B5E20]"></i>
                        Formulir Upload Laporan
                    </h3>
                    <div class="flex gap-2">
                        <button type="button" id="btnExcelMode"
                            class="px-4 py-2 text-xs font-semibold bg-[#1B5E20] text-white rounded-sm transition-all">
                            <i class="fas fa-file-excel mr-1"></i> Upload Excel
                        </button>
                        <button type="button" id="btnManualMode"
                            class="px-4 py-2 text-xs font-semibold bg-gray-200 text-gray-700 rounded-sm hover:bg-gray-300 transition-all">
                            <i class="fas fa-keyboard mr-1"></i> Upload Manual
                        </button>
                    </div>
                </div>
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
                            <select name="jenis_laporan" id="jenis_laporan" required
                                class="w-full form-input px-3 py-2 border text-sm">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach (\App\Models\Laporan::JENIS_LAPORAN as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Excel Upload Section -->
                    <div id="excelSection" class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">File Excel (.xlsx/.xls)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-md p-8 text-center transition-all cursor-pointer hover:bg-gray-50"
                            id="dropZone">
                            <i class="fas fa-file-excel text-3xl text-gray-400 mb-3"></i>
                            <p class="text-sm font-medium text-gray-700 mb-1">Klik untuk memilih file atau tarik file ke
                                sini</p>
                            <p class="text-xs text-gray-500">Maksimal ukuran file 5MB</p>
                            <input type="file" name="file_excel" id="excelFile" accept=".xlsx,.xls" class="hidden">
                            <p id="fileName" class="mt-3 text-[#1B5E20] font-bold text-sm"></p>
                        </div>
                    </div>

                    <!-- Manual Input Section -->
                    <div id="manualSection" class="mb-6 hidden">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Input Data Manual</label>
                            <button type="button" id="btnAddRow"
                                class="px-3 py-1.5 text-xs font-semibold bg-[#1B5E20] text-white rounded-sm hover:bg-[#2d5a47] transition-all">
                                <i class="fas fa-plus mr-1"></i> Tambah Baris
                            </button>
                        </div>

                        <div id="tableNotice" class="p-4 bg-yellow-50 border border-yellow-200 rounded-md text-center">
                            <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                            <span class="text-xs text-yellow-800">Pilih jenis dokumen terlebih dahulu untuk menampilkan
                                tabel input</span>
                        </div>

                        <div id="tableContainer" class="border border-gray-200 rounded-md overflow-x-auto hidden">
                            <table class="w-full text-xs" id="manualTable">
                                <thead class="bg-gray-50 border-b border-gray-200" id="manualTableHead">
                                    <!-- Headers will be generated dynamically -->
                                </thead>
                                <tbody id="manualTableBody">
                                    <!-- Rows will be added dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <p class="text-xs text-gray-500 mt-2" id="tableHint" style="display: none;">
                            <i class="fas fa-info-circle mr-1"></i>
                            Klik "Tambah Baris" untuk menambahkan data baru
                        </p>
                    </div>

                    <div class="flex items-start gap-3 p-3 bg-gray-50 border border-gray-200 rounded-sm mb-6">
                        <i class="fas fa-info-circle text-gray-500 mt-0.5 text-sm"></i>
                        <div class="text-xs text-gray-600">
                            <strong>Catatan Sistem:</strong> <span id="noteText">Pastikan format header Excel sesuai dengan
                                template standar
                                Dinas. Laporan yang sudah diupload tidak dapat diedit secara parsial, harus diupload ulang
                                sepenuhnya.</span>
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
            const btnExcelMode = document.getElementById('btnExcelMode');
            const btnManualMode = document.getElementById('btnManualMode');
            const excelSection = document.getElementById('excelSection');
            const manualSection = document.getElementById('manualSection');
            const manualTableHead = document.getElementById('manualTableHead');
            const manualTableBody = document.getElementById('manualTableBody');
            const btnAddRow = document.getElementById('btnAddRow');
            const noteText = document.getElementById('noteText');
            const uploadForm = document.getElementById('uploadForm');
            const jenisLaporanSelect = document.getElementById('jenis_laporan');
            const tableNotice = document.getElementById('tableNotice');
            const tableContainer = document.getElementById('tableContainer');
            const tableHint = document.getElementById('tableHint');

            let currentMode = 'excel'; // 'excel' or 'manual'
            let rowCounter = 0;
            let currentTableConfig = null;

            // Define table configurations for each report type
            const tableConfigs = {
                'Laporan Mutasi Kayu Bulat (LMKB)': {
                    fields: [
                        { name: 'jenis_kayu', label: 'Jenis Kayu', type: 'text', placeholder: 'Contoh: Meranti' },
                        { name: 'persediaan_awal_btg', label: 'Pers. Awal (Btg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'persediaan_awal_volume', label: 'Pers. Awal (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'penambahan_btg', label: 'Penambahan (Btg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'penambahan_volume', label: 'Penambahan (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'penggunaan_pengurangan_btg', label: 'Pengurangan (Btg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'penggunaan_pengurangan_volume', label: 'Pengurangan (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'persediaan_akhir_btg', label: 'Pers. Akhir (Btg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'persediaan_akhir_volume', label: 'Pers. Akhir (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'keterangan', label: 'Keterangan', type: 'text', placeholder: 'Opsional', required: false }
                    ]
                },
                'Laporan Mutasi Kayu Olahan (LMKO)': {
                    fields: [
                        { name: 'jenis_olahan', label: 'Jenis Olahan', type: 'text', placeholder: 'Contoh: Papan' },
                        { name: 'persediaan_awal_btg', label: 'Pers. Awal (Kpg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'persediaan_awal_volume', label: 'Pers. Awal (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'penambahan_btg', label: 'Penambahan (Kpg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'penambahan_volume', label: 'Penambahan (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'penggunaan_pengurangan_btg', label: 'Pengurangan (Kpg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'penggunaan_pengurangan_volume', label: 'Pengurangan (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'persediaan_akhir_btg', label: 'Pers. Akhir (Kpg)', type: 'number', placeholder: '0', step: '1' },
                        { name: 'persediaan_akhir_volume', label: 'Pers. Akhir (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'keterangan', label: 'Keterangan', type: 'text', placeholder: 'Opsional', required: false }
                    ]
                },
                'Laporan Penerimaan Kayu Bulat': {
                    fields: [
                        { name: 'nomor_dokumen', label: 'Nomor Dokumen', type: 'text', placeholder: 'Nomor dokumen' },
                        { name: 'tanggal', label: 'Tanggal', type: 'date', placeholder: '' },
                        { name: 'asal_kayu', label: 'Asal Kayu', type: 'text', placeholder: 'Asal kayu' },
                        { name: 'jenis_kayu', label: 'Jenis Kayu', type: 'text', placeholder: 'Jenis kayu' },
                        { name: 'jumlah_batang', label: 'Jumlah Batang', type: 'number', placeholder: '0', step: '1' },
                        { name: 'volume', label: 'Volume (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'keterangan', label: 'Keterangan', type: 'text', placeholder: 'Opsional', required: false }
                    ]
                },
                'Laporan Penerimaan Kayu Olahan': {
                    fields: [
                        { name: 'nomor_dokumen', label: 'Nomor Dokumen', type: 'text', placeholder: 'Nomor dokumen' },
                        { name: 'tanggal', label: 'Tanggal', type: 'date', placeholder: '' },
                        { name: 'asal_kayu', label: 'Asal Kayu', type: 'text', placeholder: 'Asal kayu' },
                        { name: 'jenis_olahan', label: 'Jenis Olahan', type: 'text', placeholder: 'Jenis olahan' },
                        { name: 'jumlah_keping', label: 'Jumlah Keping', type: 'number', placeholder: '0', step: '1' },
                        { name: 'volume', label: 'Volume (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'keterangan', label: 'Keterangan', type: 'text', placeholder: 'Opsional', required: false }
                    ]
                },
                'Laporan Penjualan Kayu Olahan': {
                    fields: [
                        { name: 'nomor_dokumen', label: 'Nomor Dokumen', type: 'text', placeholder: 'Nomor dokumen' },
                        { name: 'tanggal', label: 'Tanggal', type: 'date', placeholder: '' },
                        { name: 'tujuan_kirim', label: 'Tujuan Kirim', type: 'text', placeholder: 'Tujuan pengiriman' },
                        { name: 'jenis_olahan', label: 'Jenis Olahan', type: 'text', placeholder: 'Jenis olahan' },
                        { name: 'jumlah_keping', label: 'Jumlah Keping', type: 'number', placeholder: '0', step: '1' },
                        { name: 'volume', label: 'Volume (m³)', type: 'number', placeholder: '0.00', step: '0.01' },
                        { name: 'keterangan', label: 'Keterangan', type: 'text', placeholder: 'Opsional', required: false }
                    ]
                }
            };

            // Generate table headers based on config
            function generateTableHeaders(config) {
                let headerHTML = '<tr><th class="px-3 py-2 text-left font-bold text-gray-700">No</th>';
                config.fields.forEach(field => {
                    headerHTML += `<th class="px-3 py-2 text-left font-bold text-gray-700">${field.label}</th>`;
                });
                headerHTML += '<th class="px-3 py-2 text-center font-bold text-gray-700">Aksi</th></tr>';
                return headerHTML;
            }

            // Generate table row based on config
            function generateTableRow(config, rowNum) {
                let rowHTML = `<tr class="border-b border-gray-100 hover:bg-gray-50">`;
                rowHTML += `<td class="px-3 py-2 text-gray-700">${rowNum}</td>`;

                config.fields.forEach(field => {
                    const isRequired = field.required !== false ? 'required' : '';
                    const stepAttr = field.step ? `step="${field.step}"` : '';
                    const minAttr = field.type === 'number' ? 'min="0"' : '';

                    rowHTML += `
                                                <td class="px-3 py-2">
                                                    <input type="${field.type}" 
                                                        name="manual_data[${rowNum}][${field.name}]" 
                                                        class="w-full px-2 py-1 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-[#1B5E20] focus:border-[#1B5E20]" 
                                                        placeholder="${field.placeholder}" 
                                                        ${stepAttr} ${minAttr} ${isRequired}>
                                                </td>`;
                });

                rowHTML += `
                                            <td class="px-3 py-2 text-center">
                                                <button type="button" onclick="removeRow(this)" 
                                                    class="text-red-600 hover:text-red-800 transition-colors" 
                                                    title="Hapus baris">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>`;

                return rowHTML;
            }

            // Initialize table based on selected report type
            function initializeTable(jenisLaporan) {
                if (!jenisLaporan || !tableConfigs[jenisLaporan]) {
                    tableNotice.classList.remove('hidden');
                    tableContainer.classList.add('hidden');
                    tableHint.style.display = 'none';
                    btnAddRow.disabled = true;
                    btnAddRow.classList.add('opacity-50', 'cursor-not-allowed');
                    currentTableConfig = null;
                    return;
                }

                currentTableConfig = tableConfigs[jenisLaporan];

                // Show table, hide notice
                tableNotice.classList.add('hidden');
                tableContainer.classList.remove('hidden');
                tableHint.style.display = 'block';
                btnAddRow.disabled = false;
                btnAddRow.classList.remove('opacity-50', 'cursor-not-allowed');

                // Generate headers
                manualTableHead.innerHTML = generateTableHeaders(currentTableConfig);

                // Clear existing rows and reset counter
                manualTableBody.innerHTML = '';
                rowCounter = 0;

                // Add initial row
                addRow();
            }

            // Listen to jenis laporan changes
            jenisLaporanSelect.addEventListener('change', (e) => {
                if (currentMode === 'manual') {
                    initializeTable(e.target.value);
                }
            });

            // Mode switching
            btnExcelMode.addEventListener('click', () => {
                currentMode = 'excel';
                excelSection.classList.remove('hidden');
                manualSection.classList.add('hidden');

                btnExcelMode.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                btnExcelMode.classList.add('bg-[#1B5E20]', 'text-white');

                btnManualMode.classList.remove('bg-[#1B5E20]', 'text-white');
                btnManualMode.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');

                excelFile.required = true;
                noteText.textContent = 'Pastikan format header Excel sesuai dengan template standar Dinas. Laporan yang sudah diupload tidak dapat diedit secara parsial, harus diupload ulang sepenuhnya.';
            });

            btnManualMode.addEventListener('click', () => {
                currentMode = 'manual';
                excelSection.classList.add('hidden');
                manualSection.classList.remove('hidden');

                btnManualMode.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                btnManualMode.classList.add('bg-[#1B5E20]', 'text-white');

                btnExcelMode.classList.remove('bg-[#1B5E20]', 'text-white');
                btnExcelMode.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');

                excelFile.required = false;
                excelFile.value = '';
                fileName.innerHTML = '';
                noteText.textContent = 'Masukkan data laporan secara manual pada tabel di atas. Pastikan semua kolom terisi dengan benar sebelum melakukan preview.';

                // Initialize table based on current selection
                initializeTable(jenisLaporanSelect.value);
            });

            // Add row function
            function addRow() {
                if (!currentTableConfig) {
                    alert('Pilih jenis dokumen terlebih dahulu');
                    return;
                }

                rowCounter++;
                const rowHTML = generateTableRow(currentTableConfig, rowCounter);
                manualTableBody.insertAdjacentHTML('beforeend', rowHTML);
            }

            // Remove row function
            window.removeRow = function (button) {
                const row = button.closest('tr');
                row.remove();
                updateRowNumbers();
            };

            // Update row numbers after deletion
            function updateRowNumbers() {
                const rows = manualTableBody.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    row.querySelector('td:first-child').textContent = index + 1;
                    // Update input names to match new row number
                    const inputs = row.querySelectorAll('input');
                    inputs.forEach(input => {
                        const name = input.getAttribute('name');
                        if (name) {
                            const newName = name.replace(/\[\d+\]/, `[${index + 1}]`);
                            input.setAttribute('name', newName);
                        }
                    });
                });
                rowCounter = rows.length;
            }

            // Add row button click
            btnAddRow.addEventListener('click', addRow);

            // Excel file handling
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

            // Form validation before submit
            uploadForm.addEventListener('submit', (e) => {
                if (currentMode === 'manual') {
                    const rows = manualTableBody.querySelectorAll('tr');
                    if (rows.length === 0) {
                        e.preventDefault();
                        alert('Harap tambahkan minimal satu baris data sebelum melakukan preview.');
                        return false;
                    }
                }
            });

            // Reset form handler
            uploadForm.addEventListener('reset', () => {
                fileName.innerHTML = '';
                if (currentMode === 'manual') {
                    manualTableBody.innerHTML = '';
                    rowCounter = 0;
                    initializeTable(jenisLaporanSelect.value);
                }
            });
        </script>

        @include('laporan.partials.riwayatPelaporan', ['laporans' => $laporans])

    </div>

@endsection