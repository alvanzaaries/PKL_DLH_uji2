@extends('Industri.layouts.sidebar')

@section('title', 'Edit Perajin')

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

        .form-card {
            background: var(--white);
            padding: 40px;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 26px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 14px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
            display: flex;
            align-items: center;
            gap: 8px;
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

        .form-hint {
            margin-top: 6px;
            font-size: 13px;
            color: #64748b;
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

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .error-list {
            list-style: none;
            margin-top: 8px;
        }

        .error-list li {
            padding: 4px 0;
        }

        .form-error {
            border-color: #dc2626 !important;
        }

        .error-message {
            color: #dc2626;
            font-size: 13px;
            margin-top: 6px;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terdapat kesalahan:</strong>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-card">
            <div class="page-header">
                <h1 class="page-title">Edit Perajin</h1>
                <p class="page-subtitle">Ubah data Perajin / End User</p>
            </div>

            <form action="{{ route('perajin.update', $perajin) }}" method="POST">
                @csrf
                @method('PUT')

                <h3 class="section-title">Data Perusahaan</h3>

                <div class="form-group">
                    <label class="form-label">Nama Pemilik <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-input @error('nama') form-error @enderror" 
                        value="{{ old('nama', $perajin->industri->nama) }}" 
                        required
                        placeholder="Masukkan nama pemilik">
                    @error('nama')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Lengkap <span class="required">*</span></label>
                    <textarea 
                        name="alamat" 
                        class="form-textarea @error('alamat') form-error @enderror" 
                        required
                        placeholder="Masukkan alamat lengkap perusahaan">{{ old('alamat', $perajin->industri->alamat) }}</textarea>
                    @error('alamat')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kabupaten/Kota <span class="required">*</span></label>
                        <select name="kabupaten" class="form-select @error('kabupaten') form-error @enderror" required id="kabupatenSelect">
                            <option value="">-- Pilih Kabupaten/Kota --</option>
                        </select>
                        <div class="form-hint">Memuat data wilayah...</div>
                        @error('kabupaten')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Penanggung Jawab/Pemilik <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="penanggungjawab" 
                            class="form-input @error('penanggungjawab') form-error @enderror" 
                            value="{{ old('penanggungjawab', $perajin->industri->penanggungjawab) }}" 
                            required
                            placeholder="Nama lengkap pemilik">
                        @error('penanggungjawab')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kontak (Telepon/Email) <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="kontak" 
                            class="form-input @error('kontak') form-error @enderror" 
                            value="{{ old('kontak', $perajin->industri->kontak) }}" 
                            required
                            placeholder="08xx atau email@domain.com">
                        @error('kontak')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nomor SK <span class="required">*</span></label>
                        <input 
                            type="text" 
                            name="nomor_izin" 
                            class="form-input @error('nomor_izin') form-error @enderror" 
                            value="{{ old('nomor_izin', $perajin->industri->nomor_izin) }}" 
                            required
                            placeholder="Nomor izin usaha">
                        @error('nomor_izin')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal SK <span class="required">*</span></label>
                        <input 
                            type="date" 
                            name="tanggal" 
                            class="form-input @error('tanggal') form-error @enderror" 
                            value="{{ old('tanggal', $perajin->industri->tanggal) }}" 
                            required>
                        @error('tanggal')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Update Data
                    </button>
                    <a href="{{ route('perajin.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
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
            const oldValue = "{{ old('kabupaten', $perajin->industri->kabupaten) }}";

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
                    loadingInfo.style.color = '#dc2626';
                });
        });
    </script>
@endpush
