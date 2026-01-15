@extends('Industri.layouts.sidebar')

@section('title', 'Tambah Industri Primer')

@push('styles')
<style>
    .container {
        max-width: 100%;
        margin: 0 ;
        padding: 20px 30px;
    }

    /* Page Header */
    .page-header {
        background: var(--white);
        padding: 30px;
        border-radius: 12px;
        border: 1px solid var(--border);
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 28px;
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #64748b;
        font-size: 14px;
    }

    /* Alert */
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }

    .alert-success {
        background: #dcfce7;
        border: 1px solid #86efac;
        color: #166534;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }

    /* Form Card */
    .form-card {
        background: var(--white);
        padding: 40px;
        border-radius: 12px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-label .required {
        color: #dc2626;
        margin-left: 2px;
    }

    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Segoe UI', Arial, sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(21, 128, 61, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    .form-file {
        width: 100%;
        padding: 12px;
        border: 2px dashed var(--border);
        border-radius: 8px;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
    }

    .form-file:hover {
        border-color: var(--accent);
        background: #f0fdf4;
    }

    .file-upload-wrapper {
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 24px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s;
    }

    .file-upload-wrapper:hover {
        border-color: var(--accent);
        background: #f0fdf4;
    }

    .file-upload-wrapper.has-file {
        border-color: var(--accent);
        background: #f0fdf4;
        border-style: solid;
    }

    .file-upload-icon {
        font-size: 48px;
        color: #94a3b8;
        margin-bottom: 12px;
    }

    .file-upload-icon i {
        display: block;
    }

    .file-upload-text {
        font-size: 14px;
        color: #475569;
        margin-bottom: 8px;
    }

    .file-upload-text strong {
        color: var(--accent);
        cursor: pointer;
    }

    .file-upload-text strong:hover {
        text-decoration: underline;
    }

    .file-preview {
        display: none;
        margin-top: 16px;
        padding: 16px;
        background: white;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    .file-preview.show {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .file-preview-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .file-preview-icon {
        width: 40px;
        height: 40px;
        background: #fee2e2;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .file-preview-icon i {
        color: #dc2626;
    }

    .file-preview-details {
        text-align: left;
    }

    .file-preview-name {
        font-weight: 600;
        color: var(--primary);
        font-size: 14px;
        margin-bottom: 2px;
    }

    .file-preview-size {
        font-size: 12px;
        color: #64748b;
    }

    .file-remove-btn {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .file-remove-btn:hover {
        background: #fecaca;
        transform: scale(1.05);
    }

    .file-info {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .error-message {
        color: #dc2626;
        font-size: 13px;
        margin-top: 6px;
    }

    /* Grid Layout */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
    }

    .btn-primary:hover {
        background: #166534;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(21, 128, 61, 0.3);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: var(--text-main);
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-card {
            padding: 24px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">Tambah Industri Primer (PBPHH)</h1>
        <p class="page-subtitle">Formulir pendaftaran industri primer pengelolaan hasil hutan</p>
    </div>
        <!-- Success Alert -->
        @if(session('success'))
        <div class="alert alert-success">
            <span>✓</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Error Alert -->
        @if($errors->any())
        <div class="alert alert-error">
            <span>⚠</span>
            <span>Terdapat kesalahan pada form. Silakan periksa kembali.</span>
        </div>
        @endif

        <!-- Form Card -->
        <div class="form-card">
            <form action="{{ route('industri-primer.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Nama Industri -->
                <div class="form-group">
                    <label class="form-label">
                        Nama Industri <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-input" 
                        placeholder="Contoh: PT. Kayu Lestari Indonesia"
                        value="{{ old('nama') }}"
                        required
                    >
                    @error('nama')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="form-group">
                    <label class="form-label">
                        Alamat Lengkap <span class="required">*</span>
                    </label>
                    <textarea 
                        name="alamat" 
                        class="form-textarea" 
                        placeholder="Masukkan alamat lengkap industri"
                        required
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row: Penanggung Jawab & Kabupaten -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Penanggung Jawab/Direktur <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="penanggungjawab" 
                            class="form-input" 
                            placeholder="Nama lengkap direktur"
                            value="{{ old('penanggungjawab') }}"
                            required
                        >
                        @error('penanggungjawab')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Kabupaten/Kota <span class="required">*</span>
                        </label>
                        <select 
                            name="kabupaten" 
                            class="form-select" 
                            required
                            id="kabupatenSelect"
                        >
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                        <div class="file-info">Memuat data wilayah...</div>
                        @error('kabupaten')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row: Kontak & Pemberi Izin -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Kontak <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="kontak" 
                            class="form-input" 
                            placeholder="Nomor telepon/email"
                            value="{{ old('kontak') }}"
                            required
                        >
                        @error('kontak')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Pemberi Izin <span class="required">*</span>
                        </label>
                        <select 
                            name="pemberi_izin" 
                            class="form-select" 
                            required
                        >
                            <option value="">-- Pilih Pemberi Izin --</option>
                            <option value="Menteri Kehutanan" {{ old('pemberi_izin') == 'Menteri Kehutanan' ? 'selected' : '' }}>Menteri Kehutanan</option>
                            <option value="BKPM" {{ old('pemberi_izin') == 'BKPM' ? 'selected' : '' }}>BKPM</option>
                            <option value="Gubernur" {{ old('pemberi_izin') == 'Gubernur' ? 'selected' : '' }}>Gubernur</option>
                            <option value="Bupati/Walikota" {{ old('pemberi_izin') == 'Bupati/Walikota' ? 'selected' : '' }}>Bupati/Walikota</option>
                        </select>
                        @error('pemberi_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row: Jenis Produksi & Kapasitas Izin -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Jenis Produksi <span class="required">*</span>
                        </label>
                        <select 
                            name="jenis_produksi" 
                            class="form-select" 
                            id="jenis_produksi_select"
                            onchange="toggleJenisProduksiInput()"
                            required
                        >
                            <option value="">-- Pilih Jenis Produksi --</option>
                            <option value="Kayu Gergajian" {{ old('jenis_produksi') == 'Kayu Gergajian' ? 'selected' : '' }}>Kayu Gergajian</option>
                            <option value="Kayu Lapis" {{ old('jenis_produksi') == 'Kayu Lapis' ? 'selected' : '' }}>Kayu Lapis</option>
                            <option value="Kayu Veneer" {{ old('jenis_produksi') == 'Kayu Veneer' ? 'selected' : '' }}>Kayu Veneer</option>
                            <option value="Lainnya" {{ old('jenis_produksi') == 'Lainnya' || (old('jenis_produksi') && !in_array(old('jenis_produksi'), ['Kayu Gergajian', 'Kayu Lapis', 'Kayu Veneer'])) ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <input 
                            type="text" 
                            name="jenis_produksi_lainnya" 
                            id="jenis_produksi_lainnya"
                            class="form-input" 
                            placeholder="Sebutkan jenis produksi lainnya"
                            value="{{ old('jenis_produksi_lainnya') }}"
                            style="display: none; margin-top: 10px;"
                        >
                        @error('jenis_produksi')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Kapasitas Izin <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="kapasitas_izin" 
                            class="form-input" 
                            placeholder="Contoh: 5000 m³/tahun"
                            value="{{ old('kapasitas_izin') }}"
                            required
                        >
                        @error('kapasitas_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row: Tanggal -->
                <div class="form-group">
                    <label class="form-label">
                        Tanggal <span class="required">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="tanggal" 
                        class="form-input" 
                        value="{{ old('tanggal') }}"
                        required
                    >
                    @error('tanggal')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nomor Izin/NIB/SS -->
                <div class="form-group">
                    <label class="form-label">
                        Nomor Izin/NIB/SS <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nomor_izin" 
                        class="form-input" 
                        placeholder="Masukkan nomor izin/NIB/SS"
                        value="{{ old('nomor_izin') }}"
                        required
                    >
                    @error('nomor_izin')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Upload Dokumen Izin -->
                <div class="form-group">
                    <label class="form-label">
                        Upload Dokumen Izin (PDF)
                    </label>
                    <div class="file-upload-wrapper" id="fileUploadWrapper">
                        <div class="file-upload-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="file-upload-text">
                            <strong onclick="document.getElementById('dokumenInput').click()">Klik untuk pilih file</strong> atau drag & drop
                        </div>
                        <div class="file-info">
                            Format: PDF | Maksimal: 5 MB
                        </div>
                        <input 
                            type="file" 
                            id="dokumenInput"
                            name="dokumen_izin" 
                            class="form-file"
                            accept=".pdf"
                            style="display: none;"
                        >
                    </div>
                    <div class="file-preview" id="filePreview">
                        <div class="file-preview-info">
                            <div class="file-preview-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <div class="file-preview-details">
                                <div class="file-preview-name" id="fileName"></div>
                                <div class="file-preview-size" id="fileSize"></div>
                            </div>
                        </div>
                        <button type="button" class="file-remove-btn" id="removeFileBtn">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                    @error('dokumen_izin')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('industri-primer.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                         Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
        // Load data kabupaten Jawa Tengah dari API wilayah.id
        // ID Provinsi Jawa Tengah = 33
        document.addEventListener('DOMContentLoaded', function() {
            const kabupatenSelect = document.getElementById('kabupatenSelect');
            const loadingInfo = kabupatenSelect.nextElementSibling;
            const oldValue = "{{ old('kabupaten') }}";

            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json')
                .then(response => response.json())
                .then(data => {
                    loadingInfo.textContent = 'Pilih kabupaten/kota di Jawa Tengah';
                    loadingInfo.style.color = '#64748b';
                    
                    data.sort((a, b) => a.name.localeCompare(b.name));
                    
                    data.forEach(kabupaten => {
                        const option = document.createElement('option');
                        option.value = kabupaten.name;
                        option.textContent = kabupaten.name;
                        if (oldValue && oldValue === kabupaten.name) {
                            option.selected = true;
                        }
                        kabupatenSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading wilayah data:', error);
                    loadingInfo.textContent = 'Gagal memuat data wilayah';
                    loadingInfo.style.color = 'var(--error)';
                });
        });

        // File upload preview and validation
        const fileInput = document.getElementById('dokumenInput');
        const fileUploadWrapper = document.getElementById('fileUploadWrapper');
        const filePreview = document.getElementById('filePreview');
        const removeFileBtn = document.getElementById('removeFileBtn');
        const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        function handleFileSelect(file) {
            if (!file) return;

            // Validasi tipe file
            if (file.type !== 'application/pdf') {
                alert('PERINGATAN: Hanya file PDF yang diperbolehkan!');
                fileInput.value = '';
                return;
            }

            // Validasi ukuran file
            if (file.size > MAX_FILE_SIZE) {
                alert('PERINGATAN: Ukuran file melebihi 5 MB!\nUkuran file: ' + formatFileSize(file.size));
                fileInput.value = '';
                return;
            }

            // Tampilkan preview
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('fileSize').textContent = formatFileSize(file.size);
            fileUploadWrapper.classList.add('has-file');
            filePreview.classList.add('show');
        }

        fileInput.addEventListener('change', function(e) {
            handleFileSelect(e.target.files[0]);
        });

        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            fileUploadWrapper.classList.remove('has-file');
            filePreview.classList.remove('show');
        });

        // Drag and drop functionality
        fileUploadWrapper.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = 'var(--accent)';
            this.style.background = '#f0fdf4';
        });

        fileUploadWrapper.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!this.classList.contains('has-file')) {
                this.style.borderColor = '#cbd5e1';
                this.style.background = '#f8fafc';
            }
        });

        fileUploadWrapper.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(files[0]);
            }
        });

        // Click anywhere in the wrapper to open file dialog
        fileUploadWrapper.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('file-upload-icon') || 
                e.target.classList.contains('file-upload-text') || e.target.classList.contains('file-info')) {
                fileInput.click();
            }
        });

        // Auto-hide success alert after 5 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.3s';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }

        // Toggle jenis produksi input
        function toggleJenisProduksiInput() {
            const select = document.getElementById('jenis_produksi_select');
            const input = document.getElementById('jenis_produksi_lainnya');
            
            if (select.value === 'Lainnya') {
                input.style.display = 'block';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
                input.value = '';
            }
        }

        // Call on page load
        toggleJenisProduksiInput();
    </script>
@endpush
@endsection