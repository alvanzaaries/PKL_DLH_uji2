@extends('laporan/layouts.dashboard')

@section('page-title', 'Upload Laporan')

@section('content')

<style>
    .upload-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .form-section {
        padding: 2rem;
    }

    .preview-section {
        background: #f8fafc;
        border-top: 2px solid #e2e8f0;
        padding: 2rem;
        display: none;
    }

    .preview-section.active {
        display: block;
    }

    .excel-drop-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s;
        cursor: pointer;
    }

    .excel-drop-zone:hover,
    .excel-drop-zone.dragover {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .preview-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        margin-top: 1rem;
    }

    .preview-table th {
        background: #f1f5f9;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        border-bottom: 2px solid #cbd5e1;
    }

    .preview-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .preview-table tbody tr:hover {
        background: #f8fafc;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
    }

    .btn-primary {
        background: #10b981;
        color: white;
        border: none;
    }

    .btn-primary:hover {
        background: #059669;
    }

    .btn-secondary {
        background: #64748b;
        color: white;
        border: none;
    }

    .btn-secondary:hover {
        background: #475569;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border-left: 4px solid #3b82f6;
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border-left: 4px solid #f59e0b;
    }
</style>

<div class="upload-card">
    <div class="card-header">
        <h2 class="text-2xl font-bold flex items-center gap-3">
            <i class="fas fa-file-excel"></i>
            Upload Laporan Industri
        </h2>
        <p class="text-emerald-100 text-sm mt-2">
            Upload file Excel untuk mengirimkan data laporan berkala
        </p>
    </div>

    <form id="uploadForm" action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-section">
            <!-- Step 1: Form Metadata -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Perusahaan/Industri <span class="text-red-500">*</span>
                    </label>
                    <select name="industri_id" id="industri_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach ($industris as $industri)
                            <option value="{{ $industri->id }}">{{ $industri->nama_perusahaan }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Periode Laporan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <select name="bulan" id="bulan" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                            <option value="">-- Bulan --</option>
                            @php
                                $bulanIndo = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            @endphp
                            @foreach($bulanIndo as $index => $namaBulan)
                                <option value="{{ $index + 1 }}">{{ $namaBulan }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" id="tahun" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                            <option value="">-- Tahun --</option>
                            @for($tahun = date('Y') + 1; $tahun >= 2020; $tahun--)
                                <option value="{{ $tahun }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ $tahun }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Jenis Laporan <span class="text-red-500">*</span>
                    </label>
                    <select name="jenis_laporan" id="jenis_laporan" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500" required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach ($jenisLaporans as $jenis)
                            <option value="{{ $jenis }}">{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Step 2: Upload Excel -->
            <div class="mt-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    File Excel <span class="text-red-500">*</span>
                </label>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Template Excel:</strong> Silakan download template Excel sesuai jenis laporan yang dipilih.
                    <a href="#" class="underline font-semibold ml-2" id="downloadTemplate">Download Template</a>
                </div>

                <div class="excel-drop-zone" id="dropZone">
                    <i class="fas fa-cloud-upload-alt text-5xl text-gray-400 mb-3"></i>
                    <p class="text-lg font-semibold text-gray-700 mb-2">Klik atau Drop File Excel Di Sini</p>
                    <p class="text-sm text-gray-500">Format: .xlsx atau .xls (Maks. 5MB)</p>
                    <input type="file" name="excel_file" id="excelFile" accept=".xlsx,.xls" class="hidden" required>
                    <p id="fileName" class="mt-3 text-emerald-600 font-semibold"></p>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="window.history.back()" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i> Batal
                </button>
                <button type="button" id="btnPreview" class="btn btn-primary">
                    <i class="fas fa-eye mr-2"></i> Preview Data
                </button>
            </div>
        </div>

        <!-- Step 3: Preview Section -->
        <div class="preview-section" id="previewSection">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-table mr-2"></i> Preview Data Excel
            </h3>

            <div id="previewTableContainer" class="overflow-x-auto bg-white rounded-lg p-4">
                <!-- Table will be inserted here by JavaScript -->
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" id="btnCancelPreview" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Simpan ke Database
                </button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
let excelData = [];
const dropZone = document.getElementById('dropZone');
const excelFile = document.getElementById('excelFile');
const fileName = document.getElementById('fileName');
const btnPreview = document.getElementById('btnPreview');
const btnCancelPreview = document.getElementById('btnCancelPreview');
const previewSection = document.getElementById('previewSection');
const previewTableContainer = document.getElementById('previewTableContainer');
const formSection = document.querySelector('.form-section');
const jenisLaporanSelect = document.getElementById('jenis_laporan');

// Click to upload
dropZone.addEventListener('click', () => excelFile.click());

// Drag & Drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        excelFile.files = files;
        handleFileSelect(files[0]);
    }
});

// File input change
excelFile.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

function handleFileSelect(file) {
    fileName.textContent = `📄 ${file.name}`;
}

// Preview button
btnPreview.addEventListener('click', async () => {
    const file = excelFile.files[0];
    const jenisLaporan = jenisLaporanSelect.value;

    if (!file) {
        alert('Silakan pilih file Excel terlebih dahulu!');
        return;
    }

    if (!jenisLaporan) {
        alert('Silakan pilih jenis laporan terlebih dahulu!');
        return;
    }

    try {
        const data = await readExcelFile(file);
        excelData = data;
        displayPreview(data, jenisLaporan);
        formSection.style.display = 'none';
        previewSection.classList.add('active');
    } catch (error) {
        alert('Gagal membaca file Excel: ' + error.message);
    }
});

// Cancel preview
btnCancelPreview.addEventListener('click', () => {
    previewSection.classList.remove('active');
    formSection.style.display = 'block';
});

// Read Excel file
function readExcelFile(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(firstSheet);
                resolve(jsonData);
            } catch (error) {
                reject(error);
            }
        };
        reader.onerror = () => reject(new Error('Gagal membaca file'));
        reader.readAsArrayBuffer(file);
    });
}

// Display preview table
function displayPreview(data, jenisLaporan) {
    if (data.length === 0) {
        previewTableContainer.innerHTML = '<p class="text-center text-gray-500">Tidak ada data dalam file Excel</p>';
        return;
    }

    let headers = Object.keys(data[0]);
    let tableHTML = '<table class="preview-table"><thead><tr>';
    
    headers.forEach(header => {
        tableHTML += `<th>${header}</th>`;
    });
    tableHTML += '</tr></thead><tbody>';

    data.forEach(row => {
        tableHTML += '<tr>';
        headers.forEach(header => {
            tableHTML += `<td>${row[header] || ''}</td>`;
        });
        tableHTML += '</tr>';
    });

    tableHTML += '</tbody></table>';
    tableHTML += `<p class="mt-4 text-sm text-gray-600">Total: <strong>${data.length}</strong> baris data</p>`;
    
    previewTableContainer.innerHTML = tableHTML;
}

// Submit form with Excel data
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    // Add excel data as hidden input
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'excel_data';
    hiddenInput.value = JSON.stringify(excelData);
    this.appendChild(hiddenInput);
});
</script>

@endsection
