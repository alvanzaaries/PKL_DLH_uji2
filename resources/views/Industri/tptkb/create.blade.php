@extends('layouts.sidebar')

@section('title', 'Tambah TPT-KB')

@push('styles')
<style>
        :root {
            --primary: #0f172a;
            --accent: #15803d;
            --bg-body: #f8fafc;
            --text-main: #334155;
            --white: #ffffff;
            --border: #e2e8f0;
            --error: #dc2626;
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

        /* Header Section */
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

        /* Alert Success */
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
            color: var(--error);
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
            font-family: 'Inter', sans-serif;
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

        .file-info {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
        }

        .error-message {
            color: var(--error);
            font-size: 13px;
            margin-top: 6px;
        }

        /* Grid Layout untuk form */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Buttons */
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
            <h1 class="page-title">Tambah TPT-KB</h1>
            <p class="page-subtitle">Formulir pendaftaran Tempat Penampungan Tebangan Kayu Bulat</p>
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
            <form action="{{ route('tptkb.store') }}" method="POST">
                @csrf

                <!-- Nama Perusahaan -->
                <div class="form-group">
                    <label class="form-label">
                        Nama Perusahaan <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-input" 
                        placeholder="Contoh: PT. Kayu Jaya Makmur"
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
                        placeholder="Masukkan alamat lengkap perusahaan"
                        required
                    >{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Row: Kabupaten & Penanggung Jawab -->
                <div class="form-row">
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
                </div>

                <!-- Row: Kontak & Nomor Izin -->
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
                            Nomor Izin <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nomor_izin" 
                            class="form-input" 
                            placeholder="Masukkan nomor izin"
                            value="{{ old('nomor_izin') }}"
                            required
                        >
                        @error('nomor_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row: Pemberi Izin & Sumber Bahan Baku -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Pemberi Izin <span class="required">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="pemberi_izin" 
                            class="form-input" 
                            placeholder="Contoh: Dinas LHK Jateng"
                            value="{{ old('pemberi_izin') }}"
                            required
                        >
                        @error('pemberi_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Sumber Bahan Baku <span class="required">*</span>
                        </label>
                        <select 
                            name="sumber_bahan_baku" 
                            class="form-select" 
                            required
                        >
                            <option value="">-- Pilih Sumber Bahan Baku --</option>
                            <option value="Hutan Alam" {{ old('sumber_bahan_baku') == 'Hutan Alam' ? 'selected' : '' }}>Hutan Alam</option>
                            <option value="Perhutani" {{ old('sumber_bahan_baku') == 'Perhutani' ? 'selected' : '' }}>Hutan Tanaman</option>
                            <option value="Hutan Rakyat" {{ old('sumber_bahan_baku') == 'Hutan Rakyat' ? 'selected' : '' }}>Hutan Rakyat</option>
                        </select>
                        @error('sumber_bahan_baku')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Row: Kapasitas Izin & Masa Berlaku -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">
                            Kapasitas Izin <span class="required">*</span>
                        </label>
                        <select 
                            name="kapasitas_izin" 
                            class="form-select" 
                            required
                        >
                            <option value="">-- Pilih Kapasitas Izin --</option>
                            <option value="0-1999" {{ old('kapasitas_izin') == '0-1999' ? 'selected' : '' }}>0 - 1999 m³/tahun</option>
                            <option value="2000-5999" {{ old('kapasitas_izin') == '2000-5999' ? 'selected' : '' }}>2000 - 5999 m³/tahun</option>
                            <option value=">= 6000" {{ old('kapasitas_izin') == '>= 6000' ? 'selected' : '' }}>>= 6000 m³/tahun</option>
                        </select>
                        @error('kapasitas_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Masa Berlaku Izin <span class="required">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="masa_berlaku" 
                            class="form-input" 
                            value="{{ old('masa_berlaku') }}"
                            required
                        >
                        @error('masa_berlaku')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                         Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

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

        // Auto-hide success alert after 5 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.3s';
                successAlert.style.opacity = '0';
                setTimeout(() => successAlert.remove(), 300);
            }, 5000);
        }
    </script>
@endpush
