@extends('Industri.layouts.sidebar')

@section('title', 'Edit Industri Primer')

@push('styles')
<style>
        :root {
            --primary: #0f172a;
            --accent: #15803d;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
            --border: #e2e8f0;
            --success: #16a34a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            line-height: 1.6;
        }

        .container {
            max-width: 100%;
            margin: 0 ;
            padding: 20px 30px;
        }

        /* Navigation */
        nav {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .nav-content {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .back-link {
            text-decoration: none;
            color: var(--accent);
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: opacity 0.2s;
        }

        .back-link:hover {
            opacity: 0.7;
        }

        /* Form Card */
        .form-card {
            background: var(--white);
            padding: 35px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 26px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .required {
            color: #dc2626;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: border-color 0.2s;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .file-upload-wrapper {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 24px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s;
            cursor: pointer;
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

        .current-file {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px;
            background: #f1f5f9;
            border-radius: 6px;
            margin-top: 8px;
        }

        .current-file a {
            color: var(--accent);
            font-weight: 500;
            text-decoration: none;
        }

        .current-file a:hover {
            text-decoration: underline;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border);
        }

        .btn {
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 25px;
            }
        }
</style>
@endpush

@section('content')
<div class="container">
    <div class="form-card">
        <div class="form-header">
            <h1 class="form-title">Edit Data Industri Primer</h1>
                <p class="form-subtitle">Perbarui informasi perusahaan industri primer (PBPHH)</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 8px 0 0 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('industri-primer.update', $industriPrimer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Data Perusahaan -->
                <h3 style="font-size: 18px; color: var(--primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
                    Data Perusahaan
                </h3>

                <div class="form-group">
                    <label class="form-label">Nama Perusahaan <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input" value="{{ old('nama', $industriPrimer->industri->nama) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                    <textarea name="alamat" class="form-textarea" required>{{ old('alamat', $industriPrimer->industri->alamat) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kabupaten/Kota <span class="required">*</span></label>
                        <select name="kabupaten" id="kabupaten" class="form-select" required>
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Penanggung Jawab <span class="required">*</span></label>
                        <input type="text" name="penanggungjawab" class="form-input" value="{{ old('penanggungjawab', $industriPrimer->industri->penanggungjawab) }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kontak (Telepon/Email) <span class="required">*</span></label>
                        <input type="text" name="kontak" class="form-input" value="{{ old('kontak', $industriPrimer->industri->kontak) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor SK <span class="required">*</span></label>
                        <input type="text" name="nomor_izin" class="form-input" value="{{ old('nomor_izin', $industriPrimer->industri->nomor_izin) }}" required>
                    </div>
                </div>

                <!-- Data Produksi -->
                <h3 style="font-size: 18px; color: var(--primary); margin: 30px 0 20px; padding-bottom: 10px; border-bottom: 2px solid var(--accent);">
                    Data Produksi & Izin
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Pemberi Izin <span class="required">*</span></label>
                        <select name="pemberi_izin" class="form-select" required>
                            <option value="">-- Pilih Pemberi Izin --</option>
                            <option value="Menteri Kehutanan" {{ old('pemberi_izin', $industriPrimer->pemberi_izin) == 'Menteri Kehutanan' ? 'selected' : '' }}>Menteri Kehutanan</option>
                            <option value="BKPM" {{ old('pemberi_izin', $industriPrimer->pemberi_izin) == 'BKPM' ? 'selected' : '' }}>BKPM</option>
                            <option value="Gubernur" {{ old('pemberi_izin', $industriPrimer->pemberi_izin) == 'Gubernur' ? 'selected' : '' }}>Gubernur</option>
                            <option value="Bupati/Walikota" {{ old('pemberi_izin', $industriPrimer->pemberi_izin) == 'Bupati/Walikota' ? 'selected' : '' }}>Bupati/Walikota</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Produksi <span class="required">*</span></label>
                        <select 
                            name="jenis_produksi" 
                            class="form-select" 
                            id="jenis_produksi_select_edit"
                            onchange="toggleJenisProduksiInputEdit()"
                            required
                        >
                            <option value="">-- Pilih Jenis Produksi --</option>
                            <option value="Kayu Gergajian" {{ old('jenis_produksi', $industriPrimer->jenis_produksi) == 'Kayu Gergajian' ? 'selected' : '' }}>Kayu Gergajian</option>
                            <option value="Kayu Lapis" {{ old('jenis_produksi', $industriPrimer->jenis_produksi) == 'Kayu Lapis' ? 'selected' : '' }}>Kayu Lapis</option>
                            <option value="Kayu Veneer" {{ old('jenis_produksi', $industriPrimer->jenis_produksi) == 'Kayu Veneer' ? 'selected' : '' }}>Kayu Veneer</option>
                            <option value="Lainnya" {{ old('jenis_produksi', $industriPrimer->jenis_produksi) == 'Lainnya' || (old('jenis_produksi', $industriPrimer->jenis_produksi) && !in_array(old('jenis_produksi', $industriPrimer->jenis_produksi), ['Kayu Gergajian', 'Kayu Lapis', 'Kayu Veneer'])) ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <input 
                            type="text" 
                            name="jenis_produksi_lainnya" 
                            id="jenis_produksi_lainnya_edit"
                            class="form-input" 
                            placeholder="Sebutkan jenis produksi lainnya"
                            value="{{ !in_array(old('jenis_produksi', $industriPrimer->jenis_produksi), ['Kayu Gergajian', 'Kayu Lapis', 'Kayu Veneer', '']) ? old('jenis_produksi', $industriPrimer->jenis_produksi) : '' }}"
                            style="{{ !in_array(old('jenis_produksi', $industriPrimer->jenis_produksi), ['Kayu Gergajian', 'Kayu Lapis', 'Kayu Veneer', '']) ? '' : 'display: none;' }} margin-top: 10px;"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Kapasitas Izin <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="kapasitas_izin" 
                        class="form-input" 
                        value="{{ old('kapasitas_izin', $industriPrimer->kapasitas_izin) }}" 
                        placeholder="Contoh: 5000 m³/tahun" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal SK<span class="required">*</span></label>
                    <input 
                        type="date" 
                        name="tanggal" 
                        class="form-input" 
                        value="{{ old('tanggal', $industriPrimer->industri->tanggal ?? '') }}" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">Dokumen Izin (PDF)</label>
                    
                    @if($industriPrimer->dokumen_izin)
                        <div class="current-file">
                            <span style="color: #64748b;">File saat ini:</span>
                            <a href="{{ asset('storage/' . $industriPrimer->dokumen_izin) }}" target="_blank">
                                <i class="fas fa-file-pdf" style="color: #dc2626;"></i> Lihat Dokumen
                            </a>
                        </div>
                    @endif

                    <div class="file-upload-wrapper" id="fileUploadWrapper" style="margin-top: 10px;">
                        <div class="file-upload-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="file-upload-text">
                            <strong onclick="document.getElementById('dokumenInput').click()">Klik untuk pilih file baru</strong> atau drag & drop
                        </div>
                        <div class="file-info">
                            Format: PDF | Maksimal: 5 MB | Kosongkan jika tidak ingin mengubah
                        </div>
                        <input 
                            type="file" 
                            id="dokumenInput"
                            name="dokumen_izin" 
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
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                         Simpan Perubahan
                    </button>
                    <a href="{{ route('industri-primer.index') }}" class="btn btn-secondary">
                        ← Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
        // Load kabupaten dari API wilayah.id
        async function loadKabupaten() {
            const kabupatenSelect = document.getElementById('kabupaten');
            const currentKabupaten = "{{ old('kabupaten', $industriPrimer->industri->kabupaten) }}";
            
            try {
                const response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/regencies/33.json');
                const data = await response.json();
                
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.textContent = item.name;
                    
                    if (item.name === currentKabupaten) {
                        option.selected = true;
                    }
                    
                    kabupatenSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading kabupaten:', error);
                // Fallback: set current value
                const option = document.createElement('option');
                option.value = currentKabupaten;
                option.textContent = currentKabupaten;
                option.selected = true;
                kabupatenSelect.appendChild(option);
            }
        }

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

        // Load data saat halaman dibuka
        loadKabupaten();

        // Toggle jenis produksi input
        function toggleJenisProduksiInputEdit() {
            const select = document.getElementById('jenis_produksi_select_edit');
            const input = document.getElementById('jenis_produksi_lainnya_edit');
            
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
        toggleJenisProduksiInputEdit();
    </script>
@endpush
@endsection
